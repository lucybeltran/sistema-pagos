<?php

namespace App\Http\Controllers;

use App\Models\Bocamina;
use App\Models\Trabajador;
use App\Models\Contrato;
use App\Models\Trabajo;
use App\Models\Anticipo;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function dashboard()
    {
        $totalTrabajadores = Trabajador::count();
        $totalBocaminas = Bocamina::count();
        $totalContratosActivos = Contrato::where('estado', 'activo')->count();
        $totalAnticiposPendientes = Anticipo::where('saldo', '>', 0)->sum('saldo');
        
        $recientesAnticipos = Anticipo::with('trabajador')->orderBy('fecha', 'desc')->take(5)->get();
        $recientesPagos = Pago::with('trabajador')->orderBy('fecha', 'desc')->take(5)->get();

        // Chart data: Production (subtotal of jobs) by Bocamina
        $produccionBocaminas = Bocamina::with(['trabajadores.trabajos'])
            ->get()
            ->map(function($bocamina) {
                $total = 0;
                foreach ($bocamina->trabajadores as $trabajador) {
                    $total += $trabajador->trabajos->sum('subtotal');
                }
                return [
                    'nombre' => $bocamina->nombre,
                    'total' => $total
                ];
            });

        // Chart data: Payments by month (last 6 months)
        $pagosMensuales = Pago::select(
            DB::raw("strftime('%m', fecha) as mes"),
            DB::raw("strftime('%Y', fecha) as anio"),
            DB::raw("SUM(neto) as total")
        )
        ->groupBy('anio', 'mes')
        ->orderBy('anio', 'desc')
        ->orderBy('mes', 'desc')
        ->take(6)
        ->get()
        ->reverse()
        ->map(function($item) {
            $date = Carbon::createFromDate($item->anio, $item->mes, 1);
            return [
                'etiqueta' => $date->translatedFormat('F Y'),
                'total' => $item->total
            ];
        });

        return view('dashboard', compact(
            'totalTrabajadores',
            'totalBocaminas',
            'totalContratosActivos',
            'totalAnticiposPendientes',
            'recientesAnticipos',
            'recientesPagos',
            'produccionBocaminas',
            'pagosMensuales'
        ));
    }

    public function index(Request $request)
    {
        $trabajadores = Trabajador::orderBy('nombre', 'asc')->get();
        $bocaminas = Bocamina::orderBy('nombre', 'asc')->get();
        $tab = $request->input('tab', 'trabajador');

        // Common dates for tabs
        $filtroFecha = $request->input('filtro_fecha', 'personalizado');
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');

        if ($request->filled('filtro_fecha') && $filtroFecha !== 'personalizado') {
            $hoy = Carbon::today();
            if ($filtroFecha === 'esta_semana') {
                $fechaDesde = $hoy->copy()->startOfWeek()->toDateString();
                $fechaHasta = $hoy->copy()->endOfWeek()->toDateString();
            } elseif ($filtroFecha === 'semana_pasada') {
                $fechaDesde = $hoy->copy()->subWeek()->startOfWeek()->toDateString();
                $fechaHasta = $hoy->copy()->subWeek()->endOfWeek()->toDateString();
            } elseif ($filtroFecha === 'este_mes') {
                $fechaDesde = $hoy->copy()->startOfMonth()->toDateString();
                $fechaHasta = $hoy->copy()->endOfMonth()->toDateString();
            } elseif ($filtroFecha === 'mes_pasado') {
                $fechaDesde = $hoy->copy()->subMonth()->startOfMonth()->toDateString();
                $fechaHasta = $hoy->copy()->subMonth()->endOfMonth()->toDateString();
            }
        }

        // 1. Reporte por Trabajador
        $reporteTrabajador = null;
        if ($request->filled('trabajador_id')) {
            $t = Trabajador::findOrFail($request->trabajador_id);
            
            $trabajosQuery = $t->trabajos();
            $anticiposQuery = $t->anticipos();
            $pagosQuery = $t->pagos();
            
            if ($fechaDesde) {
                $trabajosQuery->where('fecha', '>=', $fechaDesde);
                $anticiposQuery->where('fecha', '>=', $fechaDesde);
                $pagosQuery->where('fecha', '>=', $fechaDesde);
            }
            if ($fechaHasta) {
                $trabajosQuery->where('fecha', '<=', $fechaHasta);
                $anticiposQuery->where('fecha', '<=', $fechaHasta);
                $pagosQuery->where('fecha', '<=', $fechaHasta);
            }
            
            $trabajos = $trabajosQuery->get();
            $anticipos = $anticiposQuery->get();
            $pagos = $pagosQuery->get();
            
            $subtotalTrabajos = $trabajos->sum('subtotal');
            $trabajosPendientes = $trabajos->where('pagado', false)->sum('subtotal');
            $anticiposPendientes = $anticipos->sum('saldo');
            $pagosRecibidos = $pagos->sum('neto');

            $reporteTrabajador = [
                'trabajador' => $t,
                'trabajos' => $trabajos->sortByDesc('fecha'),
                'anticipos' => $anticipos->sortByDesc('fecha'),
                'pagos' => $pagos->sortByDesc('fecha'),
                'subtotal_trabajos' => $subtotalTrabajos,
                'trabajos_pendientes' => $trabajosPendientes,
                'anticipos_pendientes' => $anticiposPendientes,
                'pagos_recibidos' => $pagosRecibidos,
                'desde' => $fechaDesde,
                'hasta' => $fechaHasta,
            ];
        }

        // 2. Reporte por Bocamina (General list and Specific details)
        $reporteBocamina = [];
        foreach ($bocaminas as $bocamina) {
            $trabajadoresIds = Trabajador::where('bocamina_id', $bocamina->id)->pluck('id');
            
            $cantTrabajadores = count($trabajadoresIds);
            
            $pagosQuery = Pago::whereIn('trabajador_id', $trabajadoresIds);
            $trabajosQuery = Trabajo::whereIn('trabajador_id', $trabajadoresIds);
            
            if ($fechaDesde) {
                $pagosQuery->where('fecha', '>=', $fechaDesde);
                $trabajosQuery->where('fecha', '>=', $fechaDesde);
            }
            if ($fechaHasta) {
                $pagosQuery->where('fecha', '<=', $fechaHasta);
                $trabajosQuery->where('fecha', '<=', $fechaHasta);
            }
            
            $totalPagado = $pagosQuery->sum('neto');
            $totalProduccion = $trabajosQuery->sum('subtotal');
            
            $metrosTotales = Trabajo::whereIn('trabajador_id', $trabajadoresIds)
                                    ->where('tipo', 'like', '%metro%');
                                    
            $volquetasTotales = Trabajo::whereIn('trabajador_id', $trabajadoresIds)
                                       ->where('tipo', 'like', '%volqueta%');

            if ($fechaDesde) {
                $metrosTotales->where('fecha', '>=', $fechaDesde);
                $volquetasTotales->where('fecha', '>=', $fechaDesde);
            }
            if ($fechaHasta) {
                $metrosTotales->where('fecha', '<=', $fechaHasta);
                $volquetasTotales->where('fecha', '<=', $fechaHasta);
            }

            $reporteBocamina[] = [
                'bocamina' => $bocamina,
                'cantidad_trabajadores' => $cantTrabajadores,
                'total_pagado' => $totalPagado,
                'total_produccion' => $totalProduccion,
                'metros' => $metrosTotales->sum('cantidad'),
                'volquetas' => $volquetasTotales->sum('cantidad'),
            ];
        }

        $reporteBocaminaDetalle = null;
        if ($request->filled('bocamina_id')) {
            $b = Bocamina::findOrFail($request->bocamina_id);
            $workers = Trabajador::where('bocamina_id', $b->id)->get();
            $workersIds = $workers->pluck('id');

            $totalPagadoQuery = Pago::whereIn('trabajador_id', $workersIds);
            $totalProduccionQuery = Trabajo::whereIn('trabajador_id', $workersIds);
            $totalAnticiposQuery = Anticipo::whereIn('trabajador_id', $workersIds);
            $saldoAnticiposQuery = Anticipo::whereIn('trabajador_id', $workersIds);

            if ($fechaDesde) {
                $totalPagadoQuery->where('fecha', '>=', $fechaDesde);
                $totalProduccionQuery->where('fecha', '>=', $fechaDesde);
                $totalAnticiposQuery->where('fecha', '>=', $fechaDesde);
                $saldoAnticiposQuery->where('fecha', '>=', $fechaDesde);
            }
            if ($fechaHasta) {
                $totalPagadoQuery->where('fecha', '<=', $fechaHasta);
                $totalProduccionQuery->where('fecha', '<=', $fechaHasta);
                $totalAnticiposQuery->where('fecha', '<=', $fechaHasta);
                $saldoAnticiposQuery->where('fecha', '<=', $fechaHasta);
            }

            $totalPagado = $totalPagadoQuery->sum('neto');
            $totalProduccion = $totalProduccionQuery->sum('subtotal');
            $totalAnticipos = $totalAnticiposQuery->sum('monto');
            $saldoAnticipos = $saldoAnticiposQuery->sum('saldo');

            $metrosQuery = Trabajo::whereIn('trabajador_id', $workersIds)->where('tipo', 'like', '%metro%');
            $volquetasQuery = Trabajo::whereIn('trabajador_id', $workersIds)->where('tipo', 'like', '%volqueta%');

            if ($fechaDesde) {
                $metrosQuery->where('fecha', '>=', $fechaDesde);
                $volquetasQuery->where('fecha', '>=', $fechaDesde);
            }
            if ($fechaHasta) {
                $metrosQuery->where('fecha', '<=', $fechaHasta);
                $volquetasQuery->where('fecha', '<=', $fechaHasta);
            }

            $metros = $metrosQuery->sum('cantidad');
            $volquetas = $volquetasQuery->sum('cantidad');

            $recentTrabajosQuery = Trabajo::whereIn('trabajador_id', $workersIds)->with('trabajador');
            $recentPagosQuery = Pago::whereIn('trabajador_id', $workersIds)->with('trabajador');

            if ($fechaDesde) {
                $recentTrabajosQuery->where('fecha', '>=', $fechaDesde);
                $recentPagosQuery->where('fecha', '>=', $fechaDesde);
            }
            if ($fechaHasta) {
                $recentTrabajosQuery->where('fecha', '<=', $fechaHasta);
                $recentPagosQuery->where('fecha', '<=', $fechaHasta);
            }

            $recentTrabajos = $recentTrabajosQuery->orderBy('fecha', 'desc')->take(15)->get();
            $recentPagos = $recentPagosQuery->orderBy('fecha', 'desc')->take(15)->get();

            // Calculate workers detail
            $workersData = [];
            foreach ($workers as $worker) {
                $wTrabajos = $worker->trabajos();
                $wPagos = $worker->pagos();

                if ($fechaDesde) {
                    $wTrabajos->where('fecha', '>=', $fechaDesde);
                    $wPagos->where('fecha', '>=', $fechaDesde);
                }
                if ($fechaHasta) {
                    $wTrabajos->where('fecha', '<=', $fechaHasta);
                    $wPagos->where('fecha', '<=', $fechaHasta);
                }

                $workersData[] = [
                    'worker' => $worker,
                    'total_produccion' => $wTrabajos->sum('subtotal'),
                    'total_pagado' => $wPagos->sum('neto'),
                ];
            }

            $reporteBocaminaDetalle = [
                'bocamina' => $b,
                'trabajadores_data' => $workersData,
                'total_pagado' => $totalPagado,
                'total_produccion' => $totalProduccion,
                'total_anticipos' => $totalAnticipos,
                'saldo_anticipos' => $saldoAnticipos,
                'metros' => $metros,
                'volquetas' => $volquetas,
                'recientes_trabajos' => $recentTrabajos,
                'recientes_pagos' => $recentPagos,
                'desde' => $fechaDesde,
                'hasta' => $fechaHasta,
            ];
        }

        // 3. Reporte de Anticipos (Filtrable por fechas y estado de saldo)
        $antEstado = $request->input('ant_estado', 'todos');
        $anticiposQuery = Anticipo::with('trabajador.bocamina')->orderBy('fecha', 'desc');

        if ($fechaDesde) {
            $anticiposQuery->where('fecha', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $anticiposQuery->where('fecha', '<=', $fechaHasta);
        }
        
        if ($antEstado === 'pendiente') {
            $anticiposQuery->where('saldo', '>', 0);
        } elseif ($antEstado === 'pagado') {
            $anticiposQuery->where('saldo', '<=', 0);
        }

        $reporteAnticipos = $anticiposQuery->get();

        // 4. Reporte General (Weekly breakdown / Dates range)
        $genFechaDesde = $request->gen_fecha_desde;
        $genFechaHasta = $request->gen_fecha_hasta;
        $genFiltro = $request->gen_filtro_fecha ?: 'personalizado';

        if ($request->filled('gen_filtro_fecha')) {
            $hoy = Carbon::today();
            if ($genFiltro === 'esta_semana') {
                $genFechaDesde = $hoy->copy()->startOfWeek()->toDateString();
                $genFechaHasta = $hoy->copy()->endOfWeek()->toDateString();
            } elseif ($genFiltro === 'semana_pasada') {
                $genFechaDesde = $hoy->copy()->subWeek()->startOfWeek()->toDateString();
                $genFechaHasta = $hoy->copy()->subWeek()->endOfWeek()->toDateString();
            } elseif ($genFiltro === 'este_mes') {
                $genFechaDesde = $hoy->copy()->startOfMonth()->toDateString();
                $genFechaHasta = $hoy->copy()->endOfMonth()->toDateString();
            } elseif ($genFiltro === 'mes_pasado') {
                $genFechaDesde = $hoy->copy()->subMonth()->startOfMonth()->toDateString();
                $genFechaHasta = $hoy->copy()->subMonth()->endOfMonth()->toDateString();
            }
        }

        $reporteGeneral = null;
        if ($genFechaDesde && $genFechaHasta) {
            $pagosRango = Pago::with('trabajador.bocamina')
                              ->whereBetween('fecha', [$genFechaDesde, $genFechaHasta])
                              ->orderBy('fecha', 'desc')
                              ->get();

            $trabajosRango = Trabajo::with('trabajador.bocamina')
                                    ->whereBetween('fecha', [$genFechaDesde, $genFechaHasta])
                                    ->orderBy('fecha', 'desc')
                                    ->get();

            $anticiposRango = Anticipo::with('trabajador.bocamina')
                                      ->whereBetween('fecha', [$genFechaDesde, $genFechaHasta])
                                      ->orderBy('fecha', 'desc')
                                      ->get();

            // Calculate weekly summaries inside range
            $semanasResumen = [];
            $start = Carbon::parse($genFechaDesde)->startOfWeek();
            $end = Carbon::parse($genFechaHasta)->endOfWeek();

            $current = $start->copy();
            while ($current->lte($end)) {
                $weekStart = $current->copy()->startOfWeek();
                $weekEnd = $current->copy()->endOfWeek();

                // Group transactions that fall within this week and the query range
                $wStartStr = $weekStart->copy()->max(Carbon::parse($genFechaDesde))->toDateString();
                $wEndStr = $weekEnd->copy()->min(Carbon::parse($genFechaHasta))->toDateString();

                $pSem = Pago::whereBetween('fecha', [$wStartStr, $wEndStr])->get();
                $tSem = Trabajo::whereBetween('fecha', [$wStartStr, $wEndStr])->get();
                $aSem = Anticipo::whereBetween('fecha', [$wStartStr, $wEndStr])->get();

                if ($pSem->count() > 0 || $tSem->count() > 0 || $aSem->count() > 0) {
                    $semanasResumen[] = [
                        'semana_nombre' => 'Semana ' . $weekStart->weekOfYear . ' (' . Carbon::parse($wStartStr)->format('d/m/Y') . ' al ' . Carbon::parse($wEndStr)->format('d/m/Y') . ')',
                        'total_pagado' => $pSem->sum('neto'),
                        'total_produccion' => $tSem->sum('subtotal'),
                        'total_anticipos' => $aSem->sum('monto'),
                        'cantidad_pagos' => $pSem->count(),
                        'cantidad_trabajos' => $tSem->count(),
                    ];
                }

                $current->addWeek();
            }

            $reporteGeneral = [
                'desde' => $genFechaDesde,
                'hasta' => $genFechaHasta,
                'pagos' => $pagosRango,
                'trabajos' => $trabajosRango,
                'anticipos' => $anticiposRango,
                'semanas' => array_reverse($semanasResumen),
                'total_pagos' => $pagosRango->sum('neto'),
                'total_trabajos' => $trabajosRango->sum('subtotal'),
                'total_anticipos' => $anticiposRango->sum('monto'),
            ];
        }

        return view('reportes.index', compact(
            'trabajadores',
            'bocaminas',
            'tab',
            'reporteTrabajador',
            'reporteBocamina',
            'reporteBocaminaDetalle',
            'reporteAnticipos',
            'reporteGeneral',
            'filtroFecha',
            'fechaDesde',
            'fechaHasta',
            'genFiltro',
            'genFechaDesde',
            'genFechaHasta',
            'antEstado'
        ));
    }
}
