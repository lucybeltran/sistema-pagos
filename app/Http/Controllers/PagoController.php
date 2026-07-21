<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Trabajador;
use App\Models\Trabajo;
use App\Models\Anticipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    public function index()
    {
        $pagos = Pago::with('trabajador.bocamina')->orderBy('fecha', 'desc')->get();
        return view('pagos.index', compact('pagos'));
    }

    public function create()
    {
        $trabajadores = Trabajador::with('bocamina')->where('estado', 'activo')->get();
        $bocaminas = \App\Models\Bocamina::all();
        return view('pagos.create', compact('trabajadores', 'bocaminas'));
    }

    public function getTrabajadorData($id)
    {
        $trabajador = Trabajador::with('bocamina')->findOrFail($id);
        
        // Unpaid works
        $trabajos = Trabajo::where('trabajador_id', $id)
                            ->where('pagado', false)
                            ->orderBy('fecha', 'asc')
                            ->get();

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
            'trabajos' => $trabajos,
            'anticipos' => $anticipos,
            'subtotal' => $trabajos->sum('subtotal'),
            'total_anticipos_pendientes' => $anticipos->sum('saldo'),
            'saldos_pendientes' => $saldosPendientes,
            'total_saldos_pendientes' => $saldosPendientes->sum('saldo_pendiente'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'trabajador_id' => 'required|exists:trabajadores,id',
            'fecha' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
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
        $subtotal = (float) $request->subtotal;
        $bonos = (float) $request->bonos;
        $descuentos = (float) $request->descuentos;
        $montoPagado = $request->has('monto_pagado') && $request->monto_pagado !== null ? (float) $request->monto_pagado : null;
        $tipoCambio = (float) $request->tipo_cambio;

        if ($descuentos > 0.01 && empty(trim($request->observacion))) {
            return back()->withErrors(['observacion' => 'Debe ingresar una observación explicando el motivo del descuento.'])->withInput();
        }

        // Perform the entire payment process inside a transaction
        $pago = DB::transaction(function() use ($trabajadorId, $subtotal, $bonos, $descuentos, $montoPagado, $tipoCambio, $request) {
            
            // Get unpaid works
            $trabajos = Trabajo::where('trabajador_id', $trabajadorId)
                                ->where('pagado', false)
                                ->get();

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

            // Mark works as paid and link to Pago
            foreach ($trabajos as $trabajo) {
                $trabajo->pagado = true;
                $trabajo->pago_id = $pago->id;
                $trabajo->save();
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
            'trabajador.bocamina',
            'trabajos',
            'anticipos' => function($q) {
                $q->withPivot('monto_descontado');
            }
        ]);

        return view('pagos.recibo', compact('pago'));
    }
}
