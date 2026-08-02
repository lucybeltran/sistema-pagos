<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Trabajador;
use App\Models\Anticipo;
use App\Models\Contrato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pago::with(['trabajador']);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('trabajador', function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        $pagos = $query->orderBy('fecha', 'desc')->get();

        // Calculate totals for summary cards
        $total_recargado = \App\Models\FondoPago::sum('monto');
        $total_gastado_pagos = Pago::sum('monto_pagado');
        $total_gastado_anticipos = \App\Models\Anticipo::sum('monto');
        $total_gastado = $total_gastado_pagos + $total_gastado_anticipos;
        $saldo_caja = $total_recargado - $total_gastado;

        return view('pagos.index', compact('pagos', 'total_recargado', 'total_gastado', 'saldo_caja'));
    }

    public function fondosIndex()
    {
        $fondos = \App\Models\FondoPago::orderBy('fecha', 'desc')->get();
        
        $total_recargado = \App\Models\FondoPago::sum('monto');
        $total_gastado_pagos = Pago::sum('monto_pagado');
        $total_gastado_anticipos = \App\Models\Anticipo::sum('monto');
        $total_gastado = $total_gastado_pagos + $total_gastado_anticipos;
        $saldo_caja = $total_recargado - $total_gastado;

        return view('pagos.fondos', compact(
            'fondos',
            'total_recargado',
            'total_gastado',
            'saldo_caja'
        ));
    }

    public function storeFondo(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'monto' => 'required|numeric|min:0.01',
            'observacion' => 'nullable|string|max:255',
        ]);

        \App\Models\FondoPago::create($request->all());

        return redirect()->route('fondos-caja.index')->with('success', 'Recarga de fondo de caja registrada con éxito.');
    }

    public function destroyFondo($id)
    {
        $fondo = \App\Models\FondoPago::findOrFail($id);
        $fondo->delete();

        return redirect()->route('fondos-caja.index')->with('success', 'Registro de recarga eliminado con éxito.');
    }

    public function create()
    {
        $trabajadores = Trabajador::with(['bocamina', 'tipoContrato'])
                                  ->where('estado', 'activo')
                                  ->orderBy('nombre')
                                  ->get();
        $bocaminas = \App\Models\Bocamina::all();
        
        $total_recargado = \App\Models\FondoPago::sum('monto');
        $total_gastado_pagos = Pago::sum('monto_pagado');
        $total_gastado_anticipos = Anticipo::sum('monto');
        $saldo_caja = $total_recargado - ($total_gastado_pagos + $total_gastado_anticipos);

        return view('pagos.create', compact('trabajadores', 'bocaminas', 'saldo_caja'));
    }

    public function getTrabajadorData($id)
    {
        $trabajador = Trabajador::with(['bocamina', 'tipoContrato'])->findOrFail($id);
        
        // Pending advances
        $anticipos = Anticipo::where('trabajador_id', $id)
                              ->where('saldo', '>', 0)
                              ->orderBy('fecha', 'asc')
                              ->get();

        // Pending balances from previous payments (where owner paid less)
        $saldosPendientes = Pago::where('trabajador_id', $id)
                                ->where('saldo_pendiente', '>', 0)
                                ->where('saldo_liquidado', false)
                                ->get();

        return response()->json([
            'trabajador' => $trabajador,
            'bocamina_nombre' => $trabajador->bocamina ? $trabajador->bocamina->nombre : 'Sin Bocamina Asignada',
            'cargo' => $trabajador->rol ?: 'ayudante',
            'tipo_contrato_nombre' => $trabajador->tipoContrato ? $trabajador->tipoContrato->nombre : 'Sin Tipo de Contrato',
            'tarifa_acordada' => $trabajador->tarifa_acordada ? (float)$trabajador->tarifa_acordada : 0.00,
            'anticipos' => $anticipos,
            'total_anticipos_pendientes' => (float)$anticipos->sum('saldo'),
            'saldos_pendientes' => $saldosPendientes,
            'total_saldos_pendientes' => (float)$saldosPendientes->sum('saldo_pendiente'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'trabajador_id' => 'required|exists:trabajadores,id',
            'fecha' => 'required|date',
            'tarifa_pago' => 'required|numeric|min:0',
            'cantidad_trabajada' => 'required|numeric|min:0',
            'tipo_contrato_nombre' => 'required|string|max:255',
            'bonos' => 'required|numeric|min:0',
            'descuentos' => 'required|numeric|min:0',
            'monto_pagado' => 'nullable|numeric|min:0',
            'tipo_cambio' => 'required|numeric|min:0.01',
            'observacion' => 'nullable|string',
            'deducciones_anticipos' => 'nullable|array',
            'deducciones_anticipos.*' => 'nullable|numeric|min:0',
            'metodo_pago' => 'nullable|string|in:efectivo,cheque,transferencia',
            'entregado_por' => 'nullable|string|max:255',
        ]);

        $trabajadorId = $request->trabajador_id;
        $tarifaPago = (float) $request->tarifa_pago;
        $cantidadTrabajada = (float) $request->cantidad_trabajada;
        
        // Calculate subtotal = tarifa * cantidad
        $subtotal = $tarifaPago * $cantidadTrabajada;
        
        $bonos = (float) $request->bonos;
        $descuentos = (float) $request->descuentos;
        $montoPagado = $request->has('monto_pagado') && $request->monto_pagado !== null ? (float) $request->monto_pagado : null;
        $tipoCambio = (float) $request->tipo_cambio;

        if ($descuentos > 0.01 && empty(trim($request->observacion))) {
            return back()->withErrors(['observacion' => 'Debe ingresar una observación explicando el motivo del descuento.'])->withInput();
        }

        // Perform the entire payment process inside a transaction
        $pago = DB::transaction(function() use ($trabajadorId, $subtotal, $tarifaPago, $cantidadTrabajada, $bonos, $descuentos, $montoPagado, $tipoCambio, $request) {
            
            // Load outstanding pending balances from previous payments
            $prevSaldos = Pago::where('trabajador_id', $trabajadorId)
                              ->where('saldo_pendiente', '>', 0)
                              ->where('saldo_liquidado', false)
                              ->get();
            
            $totalSaldosPrev = (float) $prevSaldos->sum('saldo_pendiente');

            // Capacidad de pago includes previous week's pending balances!
            $capacidadPago = $subtotal + $bonos - $descuentos + $totalSaldosPrev;
            
            if ($capacidadPago < 0) {
                throw new \Exception('La capacidad de pago total no puede ser negativa.');
            }

            // Get outstanding advances
            $anticipos = Anticipo::where('trabajador_id', $trabajadorId)
                                  ->where('saldo', '>', 0)
                                  ->orderBy('fecha', 'asc')
                                  ->get();

            $totalDeducido = 0.0;
            $deduccionesDetalle = [];
            $customDeducciones = $request->input('deducciones_anticipos', []);

            foreach ($anticipos as $anticipo) {
                $descuentoDeseado = isset($customDeducciones[$anticipo->id]) ? (float)$customDeducciones[$anticipo->id] : 0.0;
                
                if ($descuentoDeseado <= 0) {
                    continue;
                }

                // Make sure we don't deduct more than the advance's balance
                $descuento = min((float)$anticipo->saldo, $descuentoDeseado);

                // Enforce that the sum of deductions does not exceed capacity
                if ($totalDeducido + $descuento > $capacidadPago) {
                    $descuento = $capacidadPago - $totalDeducido;
                    if ($descuento <= 0) {
                        break;
                    }
                }
                
                $anticipo->saldo = (float)$anticipo->saldo - $descuento;
                if ($anticipo->saldo <= 0.01) {
                    $anticipo->saldo = 0;
                    $anticipo->pagado = true;
                }
                $anticipo->save();

                $totalDeducido += $descuento;

                $deduccionesDetalle[$anticipo->id] = ['monto_descontado' => $descuento];
            }

            // Calculated net payout due: subtotal + bonos - descuentos - anticipos + previous unpaid balances
            $neto = $subtotal + $bonos - $descuentos - $totalDeducido + $totalSaldosPrev;

            $actualMontoPagado = $montoPagado === null ? $neto : $montoPagado;

            // Calculate the difference between calculated net due and actual cash paid
            $diferencia = $neto - $actualMontoPagado;
            
            $saldoPendiente = 0.0;
            $saldoLiquidado = true;

            if ($diferencia > 0.01) {
                // Owner paid less: remaining is saved as a debt to the worker
                $saldoPendiente = $diferencia;
                $saldoLiquidado = false;
            } elseif ($diferencia < -0.01) {
                // Owner paid more: extra cash becomes a new advance (anticipo)
                $extra = abs($diferencia);
                Anticipo::create([
                    'trabajador_id' => $trabajadorId,
                    'fecha' => $request->fecha,
                    'monto' => $extra,
                    'saldo' => $extra,
                    'pagado' => false,
                ]);
            }

            // Create Pago record
            $pago = Pago::create([
                'trabajador_id' => $trabajadorId,
                'fecha' => $request->fecha,
                'tarifa_pago' => $tarifaPago,
                'cantidad_trabajada' => $cantidadTrabajada,
                'tipo_contrato_nombre' => $request->tipo_contrato_nombre,
                'subtotal' => $subtotal,
                'bonos' => $bonos,
                'descuentos' => $descuentos,
                'anticipos_descontados' => $totalDeducido,
                'neto' => $neto,
                'monto_pagado' => $actualMontoPagado,
                'saldo_pendiente' => $saldoPendiente,
                'saldo_liquidado' => $saldoLiquidado,
                'tipo_cambio' => $tipoCambio,
                'observacion' => $request->observacion,
                'metodo_pago' => $request->input('metodo_pago', 'efectivo'),
                'entregado_por' => $request->input('entregado_por') ?: (auth()->user()->name ?? 'Administración TORMAN'),
            ]);

            // Mark previous week pending balances as liquidated
            if ($totalSaldosPrev > 0) {
                Pago::where('trabajador_id', $trabajadorId)
                    ->where('saldo_pendiente', '>', 0)
                    ->where('saldo_liquidado', false)
                    ->update(['saldo_liquidado' => true]);
            }

            // Link advances to Pago in pivot table
            if (!empty($deduccionesDetalle)) {
                $pago->anticipos()->attach($deduccionesDetalle);
            }

            return $pago;
        });

        return redirect()->route('pagos.show', $pago->id)->with('success', 'Pago procesado exitosamente.');
    }

    public function show(Pago $pago)
    {
        $pago->load([
            'trabajador.tipoContrato',
            'anticipos' => function($q) {
                $q->withPivot('monto_descontado');
            }
        ]);

        return view('pagos.recibo', compact('pago'));
    }
}
