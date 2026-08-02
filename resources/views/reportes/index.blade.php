@extends('layouts.app')

@section('title', 'Reportes y Balance')

@section('content')
<style>
/* ===========================================================
   REPORTES PREMIUM — Diseño profesional para cliente final
   Legible en modo claro Y oscuro
   =========================================================== */

/* ── Variables adaptativas ── */
.rpt-page { --rpt-bg-card: #ffffff; --rpt-border: #e2e8f0; --rpt-text: #0f172a; --rpt-text-sub: #475569; --rpt-text-muted: #94a3b8; --rpt-row-hover: #f8fafc; --rpt-th-bg: #f1f5f9; --rpt-th-color: #334155; }
html:not(.light-theme) .rpt-page { --rpt-bg-card: rgba(15,23,42,0.55); --rpt-border: rgba(255,255,255,0.08); --rpt-text: #f1f5f9; --rpt-text-sub: #94a3b8; --rpt-text-muted: #64748b; --rpt-row-hover: rgba(255,255,255,0.02); --rpt-th-bg: rgba(15,23,42,0.6); --rpt-th-color: #94a3b8; }

/* ── Card base ── */
.rpt-card {
    background: var(--rpt-bg-card);
    border: 1px solid var(--rpt-border);
    border-radius: 14px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    backdrop-filter: blur(12px);
    overflow: hidden;
}
html:not(.light-theme) .rpt-card {
    box-shadow: 0 4px 24px rgba(0,0,0,0.2);
}

/* ── Barra de Pestañas ── */
.rpt-tabs-bar {
    display: flex;
    gap: 6px;
    padding: 6px;
    border-radius: 14px;
    border: 1.5px solid var(--rpt-border);
    background: var(--rpt-bg-card);
    backdrop-filter: blur(12px);
}
.rpt-tab-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 11px 12px;
    border-radius: 9px;
    border: none;
    cursor: pointer;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.02em;
    transition: all 0.18s ease;
    color: var(--rpt-text-sub);
    background: transparent;
    font-family: 'Outfit', sans-serif;
    white-space: nowrap;
}
.rpt-tab-btn:hover {
    background: rgba(245,158,11,0.08);
    color: #d97706;
}
.rpt-tab-btn.rpt-active {
    background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(245,158,11,0.35);
}
html:not(.light-theme) .rpt-tab-btn.rpt-active {
    box-shadow: 0 4px 16px rgba(245,158,11,0.4);
}
.rpt-tab-btn i { font-size: 14px; }

/* ── Inputs de Filtro ── */
.rpt-filter-input {
    width: 100%;
    padding: 10px 14px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    font-family: 'Outfit', sans-serif;
    border: 1.5px solid var(--rpt-border);
    background: var(--rpt-bg-card);
    color: var(--rpt-text);
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
    appearance: none;
    cursor: pointer;
}
.rpt-filter-input:focus {
    border-color: #f59e0b;
    box-shadow: 0 0 0 3px rgba(245,158,11,0.15);
}
.rpt-filter-input option {
    background: #ffffff; color: #0f172a;
}
html:not(.light-theme) .rpt-filter-input option {
    background: #0f172a; color: #f1f5f9;
}

/* ── Label de Filtro ── */
.rpt-label {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--rpt-text-sub);
    margin-bottom: 6px;
    font-family: 'Outfit', sans-serif;
}
.rpt-label i { color: #f59e0b; font-size: 11px; }

/* ── Botones de Exportar ── */
.rpt-export-btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 10px 18px; border-radius: 10px;
    font-size: 12px; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.06em;
    border: none; cursor: pointer;
    font-family: 'Outfit', sans-serif;
    transition: all 0.18s ease;
}
.rpt-export-btn:hover { transform: translateY(-2px); }
.btn-excel { background: #16a34a; color: #fff; box-shadow: 0 4px 12px rgba(22,163,74,0.3); }
.btn-excel:hover { background: #15803d; box-shadow: 0 6px 18px rgba(22,163,74,0.45); }
.btn-pdf { background: #dc2626; color: #fff; box-shadow: 0 4px 12px rgba(220,38,38,0.3); }
.btn-pdf:hover { background: #b91c1c; box-shadow: 0 6px 18px rgba(220,38,38,0.45); }
.btn-print { background: #d97706; color: #fff; box-shadow: 0 4px 12px rgba(217,119,6,0.3); }
.btn-print:hover { background: #b45309; box-shadow: 0 6px 18px rgba(217,119,6,0.45); }

/* ── Tarjetas de Estadísticas ── */
.rpt-stat-card {
    padding: 18px 20px;
    border-radius: 12px;
    border: 1.5px solid var(--rpt-border);
    background: var(--rpt-bg-card);
    position: relative;
    overflow: hidden;
}
.rpt-stat-card::after {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 4px; height: 100%;
}
.stat-amber::after { background: #f59e0b; }
.stat-green::after { background: #16a34a; }
.stat-indigo::after { background: #4f46e5; }
.stat-red::after { background: #dc2626; }
.stat-violet::after { background: #7c3aed; }
.stat-sky::after { background: #0284c7; }

.rpt-stat-label {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.07em; color: var(--rpt-text-sub);
    display: block; margin-bottom: 6px;
    font-family: 'Outfit', sans-serif;
}
.rpt-stat-value {
    font-size: 20px; font-weight: 900;
    font-family: 'Courier New', monospace;
    display: block; line-height: 1.1;
}
.rpt-stat-icon {
    position: absolute; right: 14px; top: 50%;
    transform: translateY(-50%);
    font-size: 28px; opacity: 0.08;
}

/* ── Tablas ── */
.rpt-section {
    border-radius: 12px;
    border: 1.5px solid var(--rpt-border);
    overflow: hidden;
    background: var(--rpt-bg-card);
}
.rpt-section-header {
    padding: 12px 18px;
    display: flex;
    align-items: center;
    gap: 8px;
    border-bottom: 1.5px solid var(--rpt-border);
    background: var(--rpt-th-bg);
    font-size: 11px; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.07em;
    color: var(--rpt-text-sub);
    font-family: 'Outfit', sans-serif;
}
.rpt-section-header i { font-size: 13px; }

table.rpt-tbl { width: 100%; border-collapse: collapse; }
table.rpt-tbl thead tr th {
    padding: 10px 18px;
    text-align: left;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.05em;
    color: var(--rpt-th-color);
    background: var(--rpt-th-bg);
    border-bottom: 1.5px solid var(--rpt-border);
    white-space: nowrap;
    font-family: 'Outfit', sans-serif;
}
table.rpt-tbl tbody tr td {
    padding: 11px 18px;
    font-size: 13px;
    color: var(--rpt-text-sub);
    border-bottom: 1px solid var(--rpt-border);
    white-space: nowrap;
    font-family: 'Outfit', sans-serif;
}
table.rpt-tbl tbody tr:last-child td { border-bottom: none; }
table.rpt-tbl tbody tr:hover td { background: var(--rpt-row-hover); }
.td-name { color: var(--rpt-text) !important; font-weight: 700 !important; }
.td-mono { font-family: 'Courier New', monospace !important; font-size: 12px !important; }
.td-amber { color: #d97706 !important; font-weight: 700 !important; }
.td-green { color: #16a34a !important; font-weight: 700 !important; }
.td-red { color: #dc2626 !important; font-weight: 700 !important; }
.td-indigo { color: #4f46e5 !important; font-weight: 700 !important; }
html:not(.light-theme) .td-amber { color: #fbbf24 !important; }
html:not(.light-theme) .td-green { color: #34d399 !important; }
html:not(.light-theme) .td-red { color: #f87171 !important; }
html:not(.light-theme) .td-indigo { color: #818cf8 !important; }

/* ── Badges ── */
.badge {
    display: inline-block; padding: 3px 10px;
    border-radius: 99px; font-size: 10px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.05em;
    font-family: 'Outfit', sans-serif;
}
.badge-green { background: #dcfce7; color: #15803d; }
.badge-amber { background: #fef3c7; color: #b45309; }
.badge-red { background: #fee2e2; color: #b91c1c; }
.badge-gray { background: #f1f5f9; color: #475569; }
html:not(.light-theme) .badge-green { background: rgba(16,185,129,0.15); color: #34d399; }
html:not(.light-theme) .badge-amber { background: rgba(245,158,11,0.15); color: #fbbf24; }
html:not(.light-theme) .badge-red { background: rgba(220,38,38,0.15); color: #f87171; }
html:not(.light-theme) .badge-gray { background: rgba(148,163,184,0.1); color: #94a3b8; }

/* ── Banner info ── */
.rpt-banner {
    display: flex; align-items: center; gap: 16px;
    padding: 16px 20px; border-radius: 12px;
    background: rgba(79,70,229,0.08);
    border: 1.5px solid rgba(79,70,229,0.18);
}
.light-theme .rpt-banner { background: #eef2ff; border-color: #c7d2fe; }
.rpt-banner-icon {
    width: 42px; height: 42px; border-radius: 10px; flex-shrink: 0;
    background: rgba(79,70,229,0.12); color: #4f46e5;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
}
.rpt-banner-title { font-size: 13px; font-weight: 800; color: #3730a3; margin-bottom: 2px; }
html:not(.light-theme) .rpt-banner-title { color: #a5b4fc; }
.rpt-banner-text { font-size: 12px; color: var(--rpt-text-sub); line-height: 1.5; }

/* ── Empty state ── */
.rpt-empty {
    padding: 48px 20px; text-align: center;
}
.rpt-empty i { font-size: 36px; color: var(--rpt-text-muted); margin-bottom: 12px; display: block; opacity: 0.5; }
.rpt-empty p { font-size: 13px; color: var(--rpt-text-muted); font-weight: 500; }

/* ── Print ── */
@media print {
    .no-print { display: none !important; }
    .rpt-card, .rpt-section { box-shadow: none !important; }
    body { background: #fff !important; }
}
</style>

<div class="rpt-page space-y-6"
     x-data="{
        tab: '{{ $tab === 'fecha' ? 'trabajador' : $tab }}',
        filtroFechaTrab: '{{ request('tab') === 'trabajador' ? $filtroFecha : 'personalizado' }}',
        filtroFechaBoc: '{{ request('tab') === 'bocamina' ? $filtroFecha : 'personalizado' }}',
        filtroFechaAnt: '{{ request('tab') === 'anticipos' ? $filtroFecha : 'personalizado' }}'
     }">

    {{-- ═══════════════ HEADER ═══════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 no-print">
        <div>
            <h1 class="text-2xl font-black flex items-center gap-2" style="color:var(--text-main)">
                <span style="width:32px;height:32px;background:linear-gradient(135deg,#f59e0b,#ea580c);border-radius:8px;display:inline-flex;align-items:center;justify-content:center;">
                    <i class="fa-solid fa-chart-bar text-white text-sm"></i>
                </span>
                Reportes y Balance
            </h1>
            <p class="text-sm mt-1 ml-10" style="color:var(--text-muted)">Consulta balances, exporta planillas e imprime comprobantes de pago.</p>
        </div>
        <div class="flex flex-wrap gap-2 ml-10 sm:ml-0">
            <button class="rpt-export-btn btn-excel" onclick="window.doExportExcel()">
                <i class="fa-solid fa-file-excel"></i> Exportar Excel
            </button>
            <button class="rpt-export-btn btn-pdf" onclick="window.doExportPDF()">
                <i class="fa-solid fa-file-pdf"></i> Exportar PDF
            </button>
            <button class="rpt-export-btn btn-print" onclick="window.doPrint()">
                <i class="fa-solid fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    {{-- ═══════════════ TABS ═══════════════ --}}
    <div class="rpt-tabs-bar no-print">
        <button class="rpt-tab-btn" id="tab-btn-trabajador"
                :class="tab === 'trabajador' ? 'rpt-active' : ''"
                @click="tab='trabajador'; syncTabBtn('trabajador'); history.replaceState(null,'','?tab=trabajador')">
            <i class="fa-solid fa-user"></i>
            <span>Por Trabajador</span>
        </button>
        <button class="rpt-tab-btn" id="tab-btn-bocamina"
                :class="tab === 'bocamina' ? 'rpt-active' : ''"
                @click="tab='bocamina'; syncTabBtn('bocamina'); history.replaceState(null,'','?tab=bocamina')">
            <i class="fa-solid fa-mountain"></i>
            <span>Por Bocamina</span>
        </button>
        <button class="rpt-tab-btn" id="tab-btn-general"
                :class="tab === 'general' ? 'rpt-active' : ''"
                @click="tab='general'; syncTabBtn('general'); history.replaceState(null,'','?tab=general')">
            <i class="fa-solid fa-chart-pie"></i>
            <span>General y Semanal</span>
        </button>
        <button class="rpt-tab-btn" id="tab-btn-anticipos"
                :class="tab === 'anticipos' ? 'rpt-active' : ''"
                @click="tab='anticipos'; syncTabBtn('anticipos'); history.replaceState(null,'','?tab=anticipos')">
            <i class="fa-solid fa-hand-holding-dollar"></i>
            <span>Anticipos</span>
        </button>
    </div>

    {{-- ═══════════════ TAB 1: POR TRABAJADOR ═══════════════ --}}
    <div x-show="tab === 'trabajador'"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5">

        {{-- Filtros --}}
        <div class="rpt-card p-5 no-print">
            <form action="{{ route('reportes.index') }}" method="GET"
                  class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <input type="hidden" name="tab" value="trabajador">
                <div class="lg:col-span-2">
                    <label class="rpt-label"><i class="fa-solid fa-user"></i> Contratista / Trabajador</label>
                    <select name="trabajador_id"
                            onchange="submitFilterRealTime(this.form,'report-trabajador-output')"
                            class="rpt-filter-input">
                        <option value="">— Seleccionar trabajador —</option>
                        @foreach($trabajadores as $t)
                            <option value="{{ $t->id }}" {{ request('trabajador_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->nombre }} — CI: {{ $t->ci }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="rpt-label"><i class="fa-solid fa-calendar"></i> Período</label>
                    <select name="filtro_fecha" x-model="filtroFechaTrab"
                            onchange="submitFilterRealTime(this.form,'report-trabajador-output')"
                            class="rpt-filter-input">
                        <option value="personalizado">Fechas personalizadas</option>
                        <option value="esta_semana">Esta semana</option>
                        <option value="semana_pasada">Semana pasada</option>
                        <option value="este_mes">Este mes</option>
                        <option value="mes_pasado">Mes pasado</option>
                    </select>
                </div>
                <div x-show="filtroFechaTrab === 'personalizado'">
                    <label class="rpt-label"><i class="fa-regular fa-calendar-plus"></i> Desde</label>
                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
                           oninput="debounce(()=>submitFilterRealTime(this.form,'report-trabajador-output'))"
                           class="rpt-filter-input">
                </div>
                <div x-show="filtroFechaTrab === 'personalizado'">
                    <label class="rpt-label"><i class="fa-regular fa-calendar-minus"></i> Hasta</label>
                    <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
                           oninput="debounce(()=>submitFilterRealTime(this.form,'report-trabajador-output'))"
                           class="rpt-filter-input">
                </div>
            </form>
        </div>

        {{-- Resultado del reporte --}}
        <div id="report-trabajador-output" class="space-y-5">
            @if($reporteTrabajador)
                {{-- Tarjeta resumen del trabajador --}}
                <div class="rpt-card p-6">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 pb-5 mb-5"
                         style="border-bottom:1.5px solid var(--rpt-border)">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#ea580c);display:flex;align-items:center;justify-content:center;">
                                    <i class="fa-solid fa-hard-hat text-white text-sm"></i>
                                </div>
                                <h2 class="text-xl font-black" style="color:var(--rpt-text)">{{ $reporteTrabajador['trabajador']->nombre }}</h2>
                            </div>
                            <p class="text-sm ml-12" style="color:var(--rpt-text-sub)">
                                C.I. <strong>{{ $reporteTrabajador['trabajador']->ci }}</strong>
                                @if($reporteTrabajador['trabajador']->telefono)
                                    &nbsp;·&nbsp; Tel: {{ $reporteTrabajador['trabajador']->telefono }}
                                @endif
                            </p>
                            @if($reporteTrabajador['desde'] || $reporteTrabajador['hasta'])
                                <p class="text-xs ml-12 mt-1 font-semibold" style="color:#d97706">
                                    <i class="fa-regular fa-calendar mr-1"></i>
                                    @if($reporteTrabajador['desde'] && $reporteTrabajador['hasta'])
                                        {{ Carbon\Carbon::parse($reporteTrabajador['desde'])->format('d/m/Y') }} al {{ Carbon\Carbon::parse($reporteTrabajador['hasta'])->format('d/m/Y') }}
                                    @elseif($reporteTrabajador['desde'])
                                        Desde {{ Carbon\Carbon::parse($reporteTrabajador['desde'])->format('d/m/Y') }}
                                    @else
                                        Hasta {{ Carbon\Carbon::parse($reporteTrabajador['hasta'])->format('d/m/Y') }}
                                    @endif
                                </p>
                            @endif
                        </div>
                        <div class="ml-12 sm:ml-0 text-sm sm:text-right">
                            <span class="block text-xs font-bold uppercase tracking-widest mb-1" style="color:var(--rpt-text-muted)">Bocamina Asignada</span>
                            <span class="font-black text-base" style="color:#d97706">{{ $reporteTrabajador['trabajador']->bocamina->nombre }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="rpt-stat-card stat-amber">
                            <span class="rpt-stat-label">Producción Bruta</span>
                            <span class="rpt-stat-value td-amber">Bs. {{ number_format($reporteTrabajador['subtotal_trabajos'], 2) }}</span>
                            <i class="fa-solid fa-hammer rpt-stat-icon"></i>
                        </div>
                        <div class="rpt-stat-card stat-green">
                            <span class="rpt-stat-label">Neto Cobrado</span>
                            <span class="rpt-stat-value td-green">Bs. {{ number_format($reporteTrabajador['pagos_recibidos'], 2) }}</span>
                            <i class="fa-solid fa-circle-check rpt-stat-icon"></i>
                        </div>
                        <div class="rpt-stat-card stat-indigo">
                            <span class="rpt-stat-label">Trabajo Pendiente</span>
                            <span class="rpt-stat-value td-indigo">Bs. {{ number_format($reporteTrabajador['trabajos_pendientes'], 2) }}</span>
                            <i class="fa-solid fa-clock rpt-stat-icon"></i>
                        </div>
                        <div class="rpt-stat-card stat-red">
                            <span class="rpt-stat-label">Anticipos Activos</span>
                            <span class="rpt-stat-value td-red">Bs. {{ number_format($reporteTrabajador['anticipos_pendientes'], 2) }}</span>
                            <i class="fa-solid fa-hand-holding-dollar rpt-stat-icon"></i>
                        </div>
                    </div>
                </div>

                {{-- Tabla: Trabajos --}}
                <div class="rpt-section">
                    <div class="rpt-section-header">
                        <i class="fa-solid fa-list-check td-amber" style="color:#d97706"></i>
                        Historial de Trabajos Registrados
                    </div>
                    <div class="overflow-x-auto">
                        <table class="rpt-tbl">
                            <thead><tr>
                                <th>Fecha</th><th>Tipo de Trabajo</th><th>Cantidad</th><th>Precio Unit.</th><th>Subtotal</th><th class="text-right">Estado</th>
                            </tr></thead>
                            <tbody>
                                @forelse($reporteTrabajador['trabajos'] as $t)
                                <tr>
                                    <td class="td-mono">{{ $t->fecha->format('d/m/Y') }}</td>
                                    <td class="td-name">{{ ucfirst($t->tipo) }}
                                        @if($t->contrato)<span class="block text-xs font-mono" style="color:var(--rpt-text-muted)">{{ $t->contrato->codigo }}</span>@endif
                                    </td>
                                    <td class="td-mono">{{ number_format($t->cantidad, 2) }}</td>
                                    <td class="td-mono">Bs. {{ number_format($t->precio_unitario, 2) }}</td>
                                    <td class="td-mono td-amber">Bs. {{ number_format($t->subtotal, 2) }}</td>
                                    <td class="text-right">
                                        @if($t->pagado)<span class="badge badge-green">Pagado</span>
                                        @else<span class="badge badge-amber">Pendiente</span>@endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="rpt-empty"><i class="fa-solid fa-inbox"></i><p>No hay trabajos registrados en este período.</p></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tabla: Anticipos --}}
                <div class="rpt-section">
                    <div class="rpt-section-header">
                        <i class="fa-solid fa-hand-holding-dollar" style="color:#dc2626"></i>
                        Historial de Anticipos Entregados
                    </div>
                    <div class="overflow-x-auto">
                        <table class="rpt-tbl">
                            <thead><tr>
                                <th>Fecha</th><th>Monto Original</th><th>Saldo Pendiente</th><th class="text-right">Estado</th>
                            </tr></thead>
                            <tbody>
                                @forelse($reporteTrabajador['anticipos'] as $a)
                                <tr>
                                    <td class="td-mono">{{ $a->fecha->format('d/m/Y') }}</td>
                                    <td class="td-mono td-name">Bs. {{ number_format($a->monto, 2) }}</td>
                                    <td class="td-mono td-red">Bs. {{ number_format($a->saldo, 2) }}</td>
                                    <td class="text-right">
                                        @if($a->saldo == 0)<span class="badge badge-gray">Descontado</span>
                                        @else<span class="badge badge-red">Con saldo</span>@endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="rpt-empty"><i class="fa-solid fa-inbox"></i><p>No hay anticipos registrados.</p></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tabla: Pagos --}}
                <div class="rpt-section">
                    <div class="rpt-section-header">
                        <i class="fa-solid fa-wallet" style="color:#16a34a"></i>
                        Pagos Netos Recibidos
                    </div>
                    <div class="overflow-x-auto">
                        <table class="rpt-tbl">
                            <thead><tr>
                                <th>Fecha</th><th>Subtotal Trab.</th><th>Bonos (+)</th><th>Descuentos (−)</th><th>Anticipos (−)</th><th class="text-right">Pago Neto</th>
                            </tr></thead>
                            <tbody>
                                @forelse($reporteTrabajador['pagos'] as $p)
                                <tr>
                                    <td class="td-mono">{{ $p->fecha->format('d/m/Y') }}</td>
                                    <td class="td-mono">Bs. {{ number_format($p->subtotal, 2) }}</td>
                                    <td class="td-mono td-green">+Bs. {{ number_format($p->bonos, 2) }}</td>
                                    <td class="td-mono td-red">−Bs. {{ number_format($p->descuentos, 2) }}</td>
                                    <td class="td-mono td-red">−Bs. {{ number_format($p->anticipos_descontados, 2) }}</td>
                                    <td class="td-mono td-green text-right font-black text-base">Bs. {{ number_format($p->neto, 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="rpt-empty"><i class="fa-solid fa-inbox"></i><p>No hay pagos registrados.</p></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @else
                {{-- Estado inicial --}}
                <div class="rpt-banner">
                    <div class="rpt-banner-icon"><i class="fa-solid fa-circle-info"></i></div>
                    <div>
                        <p class="rpt-banner-title">Selecciona un trabajador para ver su reporte</p>
                        <p class="rpt-banner-text">Usa el filtro de arriba para elegir el contratista y el período. A continuación se muestran los movimientos más recientes del sistema.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <div class="rpt-section">
                        <div class="rpt-section-header">
                            <i class="fa-solid fa-wallet" style="color:#16a34a"></i>
                            Últimos Pagos Realizados
                            <span class="ml-auto badge badge-green">Egresos</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="rpt-tbl">
                                <thead><tr><th>Fecha</th><th>Trabajador</th><th class="text-right">Neto Pagado</th></tr></thead>
                                <tbody>
                                    @forelse($recentPagos as $p)
                                    <tr>
                                        <td class="td-mono">{{ $p->fecha->format('d/m/Y') }}</td>
                                        <td class="td-name">{{ $p->trabajador->nombre }}</td>
                                        <td class="td-mono td-green text-right">Bs. {{ number_format($p->neto, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="rpt-empty"><i class="fa-solid fa-inbox"></i><p>Sin pagos recientes.</p></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="rpt-section">
                        <div class="rpt-section-header">
                            <i class="fa-solid fa-hand-holding-dollar" style="color:#dc2626"></i>
                            Últimos Anticipos Entregados
                            <span class="ml-auto badge badge-red">Adelantos</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="rpt-tbl">
                                <thead><tr><th>Fecha</th><th>Trabajador</th><th class="text-right">Monto</th></tr></thead>
                                <tbody>
                                    @forelse($recentAnticipos as $a)
                                    <tr>
                                        <td class="td-mono">{{ $a->fecha->format('d/m/Y') }}</td>
                                        <td class="td-name">{{ $a->trabajador->nombre }}</td>
                                        <td class="td-mono td-red text-right">Bs. {{ number_format($a->monto, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="rpt-empty"><i class="fa-solid fa-inbox"></i><p>Sin anticipos recientes.</p></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ═══════════════ TAB 2: POR BOCAMINA ═══════════════ --}}
    <div x-show="tab === 'bocamina'"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5">

        <div class="rpt-card p-5 no-print">
            <form action="{{ route('reportes.index') }}" method="GET"
                  class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <input type="hidden" name="tab" value="bocamina">
                <div class="lg:col-span-2">
                    <label class="rpt-label"><i class="fa-solid fa-mountain"></i> Bocamina</label>
                    <select name="bocamina_id" onchange="submitFilterRealTime(this.form,'report-bocamina-output')" class="rpt-filter-input">
                        <option value="">— Todas las bocaminas (resumen comparativo) —</option>
                        @foreach($bocaminas as $b)
                            <option value="{{ $b->id }}" {{ request('bocamina_id') == $b->id ? 'selected' : '' }}>{{ $b->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="rpt-label"><i class="fa-solid fa-calendar"></i> Período</label>
                    <select name="filtro_fecha" x-model="filtroFechaBoc" onchange="submitFilterRealTime(this.form,'report-bocamina-output')" class="rpt-filter-input">
                        <option value="personalizado">Fechas personalizadas</option>
                        <option value="esta_semana">Esta semana</option>
                        <option value="semana_pasada">Semana pasada</option>
                        <option value="este_mes">Este mes</option>
                        <option value="mes_pasado">Mes pasado</option>
                    </select>
                </div>
                <div x-show="filtroFechaBoc === 'personalizado'">
                    <label class="rpt-label">Desde</label>
                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
                           oninput="debounce(()=>submitFilterRealTime(this.form,'report-bocamina-output'))" class="rpt-filter-input">
                </div>
                <div x-show="filtroFechaBoc === 'personalizado'">
                    <label class="rpt-label">Hasta</label>
                    <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
                           oninput="debounce(()=>submitFilterRealTime(this.form,'report-bocamina-output'))" class="rpt-filter-input">
                </div>
            </form>
        </div>

        <div id="report-bocamina-output" class="space-y-5">
            @if($reporteBocaminaDetalle)
                <div class="rpt-card p-6">
                    <div class="flex flex-col sm:flex-row sm:justify-between gap-3 pb-5 mb-5" style="border-bottom:1.5px solid var(--rpt-border)">
                        <div>
                            <h2 class="text-xl font-black" style="color:var(--rpt-text)">{{ $reporteBocaminaDetalle['bocamina']->nombre }}</h2>
                            <p class="text-sm mt-1" style="color:var(--rpt-text-sub)">{{ $reporteBocaminaDetalle['bocamina']->descripcion ?: 'Sin descripción registrada' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="block text-xs font-bold uppercase tracking-widest mb-1" style="color:var(--rpt-text-muted)">Personal Activo</span>
                            <span class="text-2xl font-black" style="color:#d97706">{{ count($reporteBocaminaDetalle['trabajadores_data']) }}</span>
                            <span class="text-sm font-semibold ml-1" style="color:var(--rpt-text-sub)">trabajadores</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                        <div class="rpt-stat-card stat-amber"><span class="rpt-stat-label">Producción</span><span class="rpt-stat-value td-amber" style="font-size:14px">Bs. {{ number_format($reporteBocaminaDetalle['total_produccion'],2) }}</span></div>
                        <div class="rpt-stat-card stat-green"><span class="rpt-stat-label">Liquidado</span><span class="rpt-stat-value td-green" style="font-size:14px">Bs. {{ number_format($reporteBocaminaDetalle['total_pagado'],2) }}</span></div>
                        <div class="rpt-stat-card stat-violet"><span class="rpt-stat-label">Anticipos</span><span class="rpt-stat-value" style="font-size:14px;color:#7c3aed">Bs. {{ number_format($reporteBocaminaDetalle['total_anticipos'],2) }}</span></div>
                        <div class="rpt-stat-card stat-red"><span class="rpt-stat-label">Saldo Deuda</span><span class="rpt-stat-value td-red" style="font-size:14px">Bs. {{ number_format($reporteBocaminaDetalle['saldo_anticipos'],2) }}</span></div>
                        <div class="rpt-stat-card stat-sky"><span class="rpt-stat-label">Metros Av.</span><span class="rpt-stat-value" style="font-size:14px;color:#0284c7">{{ number_format($reporteBocaminaDetalle['metros'],2) }} m</span></div>
                        <div class="rpt-stat-card stat-indigo"><span class="rpt-stat-label">Volquetas</span><span class="rpt-stat-value td-indigo" style="font-size:14px">{{ number_format($reporteBocaminaDetalle['volquetas'],2) }}</span></div>
                    </div>
                </div>

                <div class="rpt-section">
                    <div class="rpt-section-header"><i class="fa-solid fa-users" style="color:#d97706"></i> Personal de la Bocamina</div>
                    <div class="overflow-x-auto">
                        <table class="rpt-tbl">
                            <thead><tr><th>C.I.</th><th>Nombre Completo</th><th>Estado</th><th>Producción Total</th><th>Neto Liquidado</th></tr></thead>
                            <tbody>
                                @foreach($reporteBocaminaDetalle['trabajadores_data'] as $item)
                                <tr>
                                    <td class="td-mono">{{ $item['worker']->ci }}</td>
                                    <td class="td-name">{{ $item['worker']->nombre }}</td>
                                    <td>
                                        @if($item['worker']->estado === 'activo')<span class="badge badge-green">Activo</span>
                                        @else<span class="badge badge-gray">{{ ucfirst($item['worker']->estado) }}</span>@endif
                                    </td>
                                    <td class="td-mono td-amber">Bs. {{ number_format($item['total_produccion'],2) }}</td>
                                    <td class="td-mono td-green">Bs. {{ number_format($item['total_pagado'],2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <div class="rpt-section">
                        <div class="rpt-section-header"><i class="fa-solid fa-list-check" style="color:#4f46e5"></i> Trabajos del Período</div>
                        <div class="overflow-x-auto" style="max-height:260px;overflow-y:auto">
                            <table class="rpt-tbl">
                                <thead><tr><th>Fecha</th><th>Trabajador</th><th>Tipo</th><th class="text-right">Subtotal</th></tr></thead>
                                <tbody>
                                    @forelse($reporteBocaminaDetalle['recientes_trabajos'] as $t)
                                    <tr>
                                        <td class="td-mono">{{ $t->fecha->format('d/m/Y') }}</td>
                                        <td class="td-name">{{ $t->trabajador->nombre }}</td>
                                        <td class="capitalize">{{ $t->tipo }}</td>
                                        <td class="td-mono td-amber text-right">Bs. {{ number_format($t->subtotal,2) }}</td>
                                    </tr>
                                    @empty<tr><td colspan="4" class="rpt-empty"><i class="fa-solid fa-inbox"></i><p>Sin registros.</p></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="rpt-section">
                        <div class="rpt-section-header"><i class="fa-solid fa-wallet" style="color:#16a34a"></i> Pagos del Período</div>
                        <div class="overflow-x-auto" style="max-height:260px;overflow-y:auto">
                            <table class="rpt-tbl">
                                <thead><tr><th>Fecha</th><th>Trabajador</th><th class="text-right">Pago Neto</th></tr></thead>
                                <tbody>
                                    @forelse($reporteBocaminaDetalle['recientes_pagos'] as $p)
                                    <tr>
                                        <td class="td-mono">{{ $p->fecha->format('d/m/Y') }}</td>
                                        <td class="td-name">{{ $p->trabajador->nombre }}</td>
                                        <td class="td-mono td-green text-right">Bs. {{ number_format($p->neto,2) }}</td>
                                    </tr>
                                    @empty<tr><td colspan="3" class="rpt-empty"><i class="fa-solid fa-inbox"></i><p>Sin registros.</p></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="rpt-section">
                    <div class="rpt-section-header"><i class="fa-solid fa-mountain" style="color:#d97706"></i> Resumen Comparativo de Bocaminas</div>
                    <div class="overflow-x-auto">
                        <table class="rpt-tbl">
                            <thead><tr><th>Bocamina</th><th>Trabajadores</th><th>Neto Liquidado</th><th>Valor Producción</th><th>Avance (m)</th><th>Volquetas</th></tr></thead>
                            <tbody>
                                @foreach($reporteBocamina as $item)
                                <tr>
                                    <td class="td-name">{{ $item['bocamina']->nombre }}</td>
                                    <td>{{ $item['cantidad_trabajadores'] }}</td>
                                    <td class="td-mono td-green">Bs. {{ number_format($item['total_pagado'],2) }}</td>
                                    <td class="td-mono">Bs. {{ number_format($item['total_produccion'],2) }}</td>
                                    <td class="td-mono td-indigo">{{ number_format($item['metros'],2) }}</td>
                                    <td class="td-mono td-amber">{{ number_format($item['volquetas'],2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ═══════════════ TAB 3: GENERAL Y SEMANAL ═══════════════ --}}
    <div x-show="tab === 'general'"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5">

        <div class="rpt-card p-5 no-print">
            <form action="{{ route('reportes.index') }}" method="GET"
                  class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <input type="hidden" name="tab" value="general">
                <div>
                    <label class="rpt-label"><i class="fa-solid fa-bolt"></i> Acceso Rápido</label>
                    <select name="gen_filtro_fecha" onchange="submitFilterRealTime(this.form,'report-general-output')" class="rpt-filter-input">
                        <option value="personalizado" {{ $genFiltro === 'personalizado' ? 'selected' : '' }}>Fechas personalizadas</option>
                        <option value="esta_semana" {{ $genFiltro === 'esta_semana' ? 'selected' : '' }}>Esta semana</option>
                        <option value="semana_pasada" {{ $genFiltro === 'semana_pasada' ? 'selected' : '' }}>Semana pasada</option>
                        <option value="este_mes" {{ $genFiltro === 'este_mes' ? 'selected' : '' }}>Este mes</option>
                        <option value="mes_pasado" {{ $genFiltro === 'mes_pasado' ? 'selected' : '' }}>Mes pasado</option>
                    </select>
                </div>
                <div>
                    <label class="rpt-label"><i class="fa-regular fa-calendar-plus"></i> Fecha Inicial</label>
                    <input type="date" name="gen_fecha_desde" value="{{ $genFechaDesde }}"
                           oninput="debounce(()=>submitFilterRealTime(this.form,'report-general-output'))" class="rpt-filter-input">
                </div>
                <div>
                    <label class="rpt-label"><i class="fa-regular fa-calendar-minus"></i> Fecha Final</label>
                    <input type="date" name="gen_fecha_hasta" value="{{ $genFechaHasta }}"
                           oninput="debounce(()=>submitFilterRealTime(this.form,'report-general-output'))" class="rpt-filter-input">
                </div>
            </form>
        </div>

        <div id="report-general-output" class="space-y-5">
            @if($reporteGeneral)
                <div class="rpt-card p-6">
                    <h2 class="text-lg font-black mb-1" style="color:var(--rpt-text)">Balance General y Planilla Semanal</h2>
                    <p class="text-sm mb-5" style="color:var(--rpt-text-sub)">
                        Período: <strong>{{ Carbon\Carbon::parse($reporteGeneral['desde'])->format('d/m/Y') }}</strong>
                        al <strong>{{ Carbon\Carbon::parse($reporteGeneral['hasta'])->format('d/m/Y') }}</strong>
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="rpt-stat-card stat-amber"><span class="rpt-stat-label">Producción Total (Bruto)</span><span class="rpt-stat-value td-amber">Bs. {{ number_format($reporteGeneral['total_trabajos'],2) }}</span><i class="fa-solid fa-industry rpt-stat-icon"></i></div>
                        <div class="rpt-stat-card stat-green"><span class="rpt-stat-label">Efectivo Liquidado (Neto)</span><span class="rpt-stat-value td-green">Bs. {{ number_format($reporteGeneral['total_pagos'],2) }}</span><i class="fa-solid fa-money-bill-wave rpt-stat-icon"></i></div>
                        <div class="rpt-stat-card stat-red"><span class="rpt-stat-label">Anticipos Entregados</span><span class="rpt-stat-value td-red">Bs. {{ number_format($reporteGeneral['total_anticipos'],2) }}</span><i class="fa-solid fa-hand-holding-dollar rpt-stat-icon"></i></div>
                    </div>
                </div>

                <div class="rpt-section">
                    <div class="rpt-section-header"><i class="fa-solid fa-chart-line" style="color:#d97706"></i> Desglose por Semana</div>
                    <div class="overflow-x-auto">
                        <table class="rpt-tbl">
                            <thead><tr><th>Semana</th><th># Trabajos</th><th>Valor Producción</th><th># Recibos</th><th>Total Pagado (Neto)</th><th>Anticipos</th></tr></thead>
                            <tbody>
                                @forelse($reporteGeneral['semanas'] as $sem)
                                <tr>
                                    <td class="td-name">{{ $sem['semana_nombre'] }}</td>
                                    <td>{{ $sem['cantidad_trabajos'] }}</td>
                                    <td class="td-mono td-amber">Bs. {{ number_format($sem['total_produccion'],2) }}</td>
                                    <td>{{ $sem['cantidad_pagos'] }}</td>
                                    <td class="td-mono td-green font-black">Bs. {{ number_format($sem['total_pagado'],2) }}</td>
                                    <td class="td-mono td-red">Bs. {{ number_format($sem['total_anticipos'],2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="rpt-empty"><i class="fa-solid fa-inbox"></i><p>Sin datos para el rango seleccionado.</p></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <div class="rpt-section">
                        <div class="rpt-section-header"><i class="fa-solid fa-hammer" style="color:#4f46e5"></i> Detalle de Trabajos</div>
                        <div class="overflow-x-auto" style="max-height:300px;overflow-y:auto">
                            <table class="rpt-tbl">
                                <thead><tr><th>Fecha</th><th>Trabajador</th><th>Concepto</th><th class="text-right">Subtotal</th></tr></thead>
                                <tbody>
                                    @forelse($reporteGeneral['trabajos'] as $t)
                                    <tr>
                                        <td class="td-mono">{{ $t->fecha->format('d/m/Y') }}</td>
                                        <td class="td-name">{{ $t->trabajador->nombre }}</td>
                                        <td class="capitalize">{{ $t->tipo }} ({{ $t->cantidad }})</td>
                                        <td class="td-mono td-amber text-right">Bs. {{ number_format($t->subtotal,2) }}</td>
                                    </tr>
                                    @empty<tr><td colspan="4" class="rpt-empty"><i class="fa-solid fa-inbox"></i><p>Sin registros.</p></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="rpt-section">
                        <div class="rpt-section-header"><i class="fa-solid fa-wallet" style="color:#16a34a"></i> Detalle de Pagos (Egresos)</div>
                        <div class="overflow-x-auto" style="max-height:300px;overflow-y:auto">
                            <table class="rpt-tbl">
                                <thead><tr><th>Fecha</th><th>Trabajador</th><th># Recibo</th><th class="text-right">Neto Pagado</th></tr></thead>
                                <tbody>
                                    @forelse($reporteGeneral['pagos'] as $p)
                                    <tr>
                                        <td class="td-mono">{{ $p->fecha->format('d/m/Y') }}</td>
                                        <td class="td-name">{{ $p->trabajador->nombre }}</td>
                                        <td class="td-mono" style="color:var(--rpt-text-muted)">REC-{{ str_pad($p->id,4,'0',STR_PAD_LEFT) }}</td>
                                        <td class="td-mono td-green text-right font-black">Bs. {{ number_format($p->neto,2) }}</td>
                                    </tr>
                                    @empty<tr><td colspan="4" class="rpt-empty"><i class="fa-solid fa-inbox"></i><p>Sin registros.</p></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="rpt-card p-12 text-center no-print">
                    <i class="fa-solid fa-chart-pie" style="font-size:48px;color:var(--rpt-text-muted);opacity:0.3;display:block;margin-bottom:16px"></i>
                    <p class="text-sm font-semibold" style="color:var(--rpt-text-sub)">Selecciona un período en el filtro superior para visualizar el balance semanal.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ═══════════════ TAB 4: ANTICIPOS ═══════════════ --}}
    <div x-show="tab === 'anticipos'"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5">

        <div class="rpt-card p-5 no-print">
            <form action="{{ route('reportes.index') }}" method="GET"
                  class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <input type="hidden" name="tab" value="anticipos">
                <div>
                    <label class="rpt-label"><i class="fa-solid fa-calendar"></i> Período</label>
                    <select name="filtro_fecha" x-model="filtroFechaAnt" onchange="submitFilterRealTime(this.form,'report-anticipos-output')" class="rpt-filter-input">
                        <option value="personalizado">Fechas personalizadas</option>
                        <option value="esta_semana">Esta semana</option>
                        <option value="semana_pasada">Semana pasada</option>
                        <option value="este_mes">Este mes</option>
                        <option value="mes_pasado">Mes pasado</option>
                    </select>
                </div>
                <div x-show="filtroFechaAnt === 'personalizado'">
                    <label class="rpt-label">Desde</label>
                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
                           oninput="debounce(()=>submitFilterRealTime(this.form,'report-anticipos-output'))" class="rpt-filter-input">
                </div>
                <div x-show="filtroFechaAnt === 'personalizado'">
                    <label class="rpt-label">Hasta</label>
                    <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
                           oninput="debounce(()=>submitFilterRealTime(this.form,'report-anticipos-output'))" class="rpt-filter-input">
                </div>
                <div>
                    <label class="rpt-label"><i class="fa-solid fa-filter"></i> Estado del Anticipo</label>
                    <select name="ant_estado" onchange="submitFilterRealTime(this.form,'report-anticipos-output')" class="rpt-filter-input">
                        <option value="todos" {{ $antEstado === 'todos' ? 'selected' : '' }}>Todos los anticipos</option>
                        <option value="pendiente" {{ $antEstado === 'pendiente' ? 'selected' : '' }}>Con saldo pendiente</option>
                        <option value="pagado" {{ $antEstado === 'pagado' ? 'selected' : '' }}>Totalmente descontados</option>
                    </select>
                </div>
            </form>
        </div>

        <div id="report-anticipos-output">
            <div class="rpt-section">
                <div class="rpt-section-header">
                    <i class="fa-solid fa-hand-holding-dollar" style="color:#dc2626"></i>
                    @if($antEstado === 'pendiente') Anticipos con Saldo Pendiente
                    @elseif($antEstado === 'pagado') Anticipos Totalmente Descontados
                    @else Historial Completo de Anticipos
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="rpt-tbl">
                        <thead><tr>
                            <th>ID</th><th>Fecha de Entrega</th><th>Trabajador / Contratista</th><th>Bocamina</th><th>Monto Original</th><th>Saldo Restante</th><th class="text-right">Estado</th>
                        </tr></thead>
                        <tbody>
                            @forelse($reporteAnticipos as $a)
                            <tr>
                                <td class="td-mono" style="color:var(--rpt-text-muted);font-size:11px">ANT-{{ str_pad($a->id,5,'0',STR_PAD_LEFT) }}</td>
                                <td class="td-mono">{{ $a->fecha->format('d/m/Y') }}</td>
                                <td class="td-name">{{ $a->trabajador->nombre }}</td>
                                <td style="color:var(--rpt-text-muted);font-size:12px">{{ $a->trabajador->bocamina->nombre }}</td>
                                <td class="td-mono">Bs. {{ number_format($a->monto,2) }}</td>
                                <td class="td-mono td-red font-black">Bs. {{ number_format($a->saldo,2) }}</td>
                                <td class="text-right">
                                    @if($a->saldo <= 0)<span class="badge badge-gray">Descontado</span>
                                    @else<span class="badge badge-red">Pendiente</span>@endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="rpt-empty"><i class="fa-solid fa-inbox"></i><p>No hay anticipos para los criterios seleccionados.</p></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
/* ─── Debounce helper ─────────────────────────────────────── */
let _rptTimer;
function debounce(fn, ms = 420) { clearTimeout(_rptTimer); _rptTimer = setTimeout(fn, ms); }

/* ─── Sync tab active class (for exports) ─────────────────── */
window._currentTab = '{{ $tab === "fecha" ? "trabajador" : $tab }}';
function syncTabBtn(tabName) {
    window._currentTab = tabName;
    document.querySelectorAll('.rpt-tab-btn').forEach(b => b.classList.remove('rpt-active'));
    const btn = document.getElementById('tab-btn-' + tabName);
    if (btn) btn.classList.add('rpt-active');
}
document.addEventListener('DOMContentLoaded', () => syncTabBtn(window._currentTab));

/* ─── Real-time AJAX filter ───────────────────────────────── */
function submitFilterRealTime(form, containerId) {
    const url = new URL(form.action || window.location.href, window.location.origin);
    url.search = '';
    new FormData(form).forEach((v, k) => { if (v && String(v).trim()) url.searchParams.set(k, v); });
    window.history.pushState({}, '', url.toString());

    const el = document.getElementById(containerId);
    if (!el) { form.submit(); return; }
    el.style.opacity = '0.4';
    el.style.pointerEvents = 'none';

    fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text())
        .then(html => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const fresh = doc.getElementById(containerId);
            if (fresh) el.innerHTML = fresh.innerHTML;
            else window.location.reload();
        })
        .catch(() => form.submit())
        .finally(() => { el.style.opacity = '1'; el.style.pointerEvents = ''; });
}

/* ─── Generador de HTML Elegante para PDF e Impresión ──────── */
window.getElegantHTML = function() {
    const tabIdMap = {
        trabajador: 'report-trabajador-output',
        bocamina:   'report-bocamina-output',
        general:    'report-general-output',
        anticipos:  'report-anticipos-output'
    };
    const tabTitles = {
        trabajador: 'Reporte por Trabajador / Contratista',
        bocamina:   'Reporte de Producción por Bocamina',
        general:    'Reporte Balance General y Semanal',
        anticipos:  'Reporte de Anticipos y Saldos'
    };
    const sectionHeaders = {
        trabajador: ['Historial de Trabajos Registrados', 'Historial de Anticipos Entregados', 'Pagos Netos Recibidos (Egresos)'],
        bocamina:   ['Personal de la Bocamina y Producción', 'Trabajos Registrados del Período', 'Pagos Realizados del Período'],
        general:    ['Resumen Desglosado por Semana', 'Detalle de Trabajos Registrados', 'Detalle de Egresos Realizados'],
        anticipos:  ['Historial Completo de Anticipos y Saldos']
    };

    const sourceEl = document.getElementById(tabIdMap[window._currentTab]);
    if (!sourceEl) return null;

    const tables = Array.from(sourceEl.querySelectorAll('table'));
    const now = new Date();
    const fecha = now.toLocaleDateString('es-BO', { day:'2-digit', month:'long', year:'numeric' });
    const hora = now.toLocaleTimeString('es-BO', { hour:'2-digit', minute:'2-digit' });

    // Helper: badge style
    const bStyle = (txt) => {
        const t = (txt || '').toLowerCase();
        if (['pagado','descontado','activo'].includes(t))
            return 'background:#dcfce7;color:#15803d;padding:2px 8px;border-radius:99px;font-size:10px;font-weight:700;display:inline-block;border:1px solid #bbf7d0;';
        if (['pendiente','con saldo'].includes(t))
            return 'background:#fee2e2;color:#b91c1c;padding:2px 8px;border-radius:99px;font-size:10px;font-weight:700;display:inline-block;border:1px solid #fecaca;';
        return 'background:#f1f5f9;color:#475569;padding:2px 8px;border-radius:99px;font-size:10px;font-weight:700;display:inline-block;border:1px solid #e2e8f0;';
    };

    // Helper: cell color mapping
    const getCellColor = (td) => {
        if (td.classList.contains('td-green'))  return '#16a34a';
        if (td.classList.contains('td-red'))    return '#dc2626';
        if (td.classList.contains('td-amber'))  return '#d97706';
        if (td.classList.contains('td-indigo')) return '#4f46e5';
        if (td.classList.contains('td-name'))   return '#0f172a';
        return '#334155';
    };

    // Construir Tablas
    let tablesHtml = '';
    tables.forEach((tbl, idx) => {
        const title = (sectionHeaders[window._currentTab] || [])[idx] || ('Tabla ' + (idx+1));
        let s = `<div style="margin-bottom:24px;border-radius:8px;overflow:hidden;border:1.5px solid #e2e8f0;background:#ffffff;">`;
        s += `<div style="background:#1e293b;color:#f8fafc;padding:10px 14px;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;">`;
        s += `<span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:#f59e0b;margin-right:8px;vertical-align:middle;"></span>${title}</div>`;
        s += `<table style="width:100%;border-collapse:collapse;font-family:Arial,sans-serif;font-size:11.5px;">`;

        const ths = tbl.querySelectorAll('thead th');
        if (ths.length) {
            s += `<thead><tr>`;
            ths.forEach(th => {
                s += `<th style="background:#475569;color:#ffffff;padding:8px 12px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;border:1px solid #475569;white-space:nowrap;">${th.innerText.trim()}</th>`;
            });
            s += `</tr></thead>`;
        }

        s += `<tbody>`;
        tbl.querySelectorAll('tbody tr').forEach((row, ri) => {
            const bg = ri % 2 === 0 ? '#ffffff' : '#f8fafc';
            s += `<tr style="background:${bg};">`;
            row.querySelectorAll('td').forEach(td => {
                const badge = td.querySelector('.badge');
                let inner = badge 
                    ? `<span style="${bStyle(badge.innerText.trim())}">${badge.innerText.trim()}</span>`
                    : td.innerText.trim().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
                const color = getCellColor(td);
                const weight = (td.classList.contains('td-name') || td.classList.contains('td-green') || td.classList.contains('td-red') || td.classList.contains('td-amber')) ? '700' : '400';
                const font = td.classList.contains('td-mono') ? 'Courier New, monospace' : 'Arial, sans-serif';
                s += `<td style="padding:9px 12px;border:1px solid #e2e8f0;color:${color};font-weight:${weight};font-family:${font};vertical-align:middle;">${inner}</td>`;
            });
            s += `</tr>`;
        });
        s += `</tbody></table></div>`;
        tablesHtml += s;
    });

    // Construir Tarjetas de Estadísticas
    let statsHtml = '';
    const cards = sourceEl.querySelectorAll('.rpt-stat-card');
    if (cards.length) {
        statsHtml = `<div style="display:flex;gap:12px;margin-bottom:24px;flex-wrap:wrap;">`;
        cards.forEach(card => {
            const lbl = card.querySelector('.rpt-stat-label')?.innerText || '';
            const val = card.querySelector('.rpt-stat-value')?.innerText || '';
            const c = card.classList.contains('stat-green') ? '#16a34a'
                    : card.classList.contains('stat-red')   ? '#dc2626'
                    : card.classList.contains('stat-indigo')? '#4f46e5'
                    : card.classList.contains('stat-violet')? '#7c3aed'
                    : '#d97706';
            statsHtml += `<div style="flex:1;min-width:140px;padding:14px 16px;border-radius:8px;border-left:5px solid ${c};background:#ffffff;border-top:1.5px solid #e2e8f0;border-right:1.5px solid #e2e8f0;border-bottom:1.5px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.02);">`;
            statsHtml += `<div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#64748b;margin-bottom:6px;">${lbl}</div>`;
            statsHtml += `<div style="font-size:16px;font-weight:900;color:${c};font-family:'Courier New',monospace;">${val}</div>`;
            statsHtml += `</div>`;
        });
        statsHtml += `</div>`;
    }

    // Construir Datos de Sujeto (Trabajador / Bocamina)
    let workerHtml = '';
    const h2el = sourceEl.querySelector('h2');
    if (h2el) {
        const subEl = sourceEl.querySelector('.rpt-card > .flex p, .rpt-card p');
        const bocEl = sourceEl.querySelector('.font-black.text-base');
        const rangeEl = sourceEl.querySelector('.text-amber-400, .text-xs.font-bold');
        workerHtml = `<div style="display:flex;justify-content:space-between;align-items:flex-start;padding-bottom:14px;margin-bottom:20px;border-bottom:2px solid #cbd5e1;">`;
        workerHtml += `<div>`;
        workerHtml += `<div style="font-size:20px;font-weight:900;color:#0f172a;margin-bottom:4px;font-family:Arial,sans-serif;">${h2el.innerText.trim()}</div>`;
        if (subEl) workerHtml += `<div style="font-size:12px;color:#475569;font-family:Arial,sans-serif;">${subEl.innerText.trim()}</div>`;
        if (rangeEl && rangeEl.innerText.includes('/')) workerHtml += `<div style="font-size:11px;color:#b45309;font-weight:700;margin-top:4px;font-family:Arial,sans-serif;">${rangeEl.innerText.trim()}</div>`;
        workerHtml += `</div>`;
        if (bocEl) {
            workerHtml += `<div style="text-align:right;"><div style="font-size:9px;text-transform:uppercase;color:#94a3b8;font-weight:700;margin-bottom:2px;font-family:Arial,sans-serif;">Bocamina Asignada</div>`;
            workerHtml += `<div style="font-size:14px;font-weight:900;color:#d97706;font-family:Arial,sans-serif;">${bocEl.innerText.trim()}</div></div>`;
        }
        workerHtml += `</div>`;
    }

    const fullHtml = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>${tabTitles[window._currentTab]}</title>
            <style>
                * { box-sizing: border-box; margin: 0; padding: 0; }
                body { font-family: Arial, sans-serif; background: #ffffff; color: #1e293b; padding: 30px; font-size: 12px; line-height: 1.4; }
                @media print {
                    body { padding: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <!-- ENCABEZADO DE LA EMPRESA -->
            <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:16px;margin-bottom:24px;border-bottom:3.5px solid #1e293b;">
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="width:52px;height:52px;border-radius:10px;background:linear-gradient(135deg,#f59e0b,#ea580c);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-size:22px;font-weight:900;color:#0f172a;line-height:1.1;">${tabTitles[window._currentTab]}</div>
                        <div style="font-size:11px;color:#475569;font-weight:600;margin-top:2px;">Sistema de Control de Pagos Mineros — SCPM</div>
                        <div style="font-size:10px;color:#94a3b8;margin-top:1px;">Generado el ${fecha} a las ${hora}</div>
                    </div>
                </div>
                <div style="text-align:right;padding:10px 16px;background:#f8fafc;border-radius:8px;border:1.5px solid #e2e8f0;flex-shrink:0;">
                    <div style="font-size:9px;text-transform:uppercase;font-weight:700;color:#94a3b8;letter-spacing:.06em;">Planilla Oficial</div>
                    <div style="font-size:12px;font-weight:800;color:#0f172a;margin-top:3px;">${window._currentTab.toUpperCase()}</div>
                </div>
            </div>

            <!-- DATOS SUJETO -->
            ${workerHtml}

            <!-- ESTADISTICAS -->
            ${statsHtml}

            <!-- TABLAS -->
            ${tablesHtml}

            <!-- FOOTER -->
            <div style="margin-top:24px;padding-top:12px;border-top:1.5px solid #cbd5e1;display:flex;justify-content:space-between;align-items:center;">
                <div style="font-size:9px;color:#94a3b8;">SCPM · Reportes Financieros Oficiales · Confidencial</div>
                <div style="font-size:9px;color:#94a3b8;font-family:'Courier New',monospace;">${now.toISOString()}</div>
            </div>
        </body>
        </html>
    `;
    return { fullHtml, title: tabTitles[window._currentTab], count: tables.length };
};

/* ─── PDF Export — Generador de PDF profesional ───────────── */
window.doExportPDF = function() {
    const data = window.getElegantHTML();
    if (!data) { alert('No hay datos para exportar. Selecciona un filtro primero.'); return; }

    // Creamos un iframe invisible para forzar el layout antes de la captura
    const iframe = document.createElement('iframe');
    iframe.style.cssText = 'position:fixed;right:0;bottom:0;width:1024px;height:768px;border:0;z-index:-9999;visibility:hidden;';
    document.body.appendChild(iframe);
    
    const doc = iframe.contentWindow.document;
    doc.open();
    doc.write(data.fullHtml);
    doc.close();

    // Damos un tiempo breve para que el DOM interno del iframe se layout-ee
    setTimeout(() => {
        const fname = 'SCPM_' + data.title.replace(/\s+/g,'_') + '_' + new Date().toLocaleDateString('es-BO').replace(/\//g,'-') + '.pdf';
        html2pdf().set({
            margin:      [0.35, 0.3, 0.35, 0.3],
            filename:    fname,
            image:       { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true, backgroundColor: '#ffffff', logging: false },
            jsPDF:       { unit: 'in', format: 'letter', orientation: data.count > 1 ? 'landscape' : 'portrait' }
        }).from(doc.body).save().finally(() => {
            document.body.removeChild(iframe);
        });
    }, 150);
};

/* ─── Direct Print — Impresión directa sin interfaz fea ───── */
window.doPrint = function() {
    const data = window.getElegantHTML();
    if (!data) { alert('No hay datos para imprimir. Selecciona un filtro primero.'); return; }

    const iframe = document.createElement('iframe');
    iframe.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;z-index:-9999;visibility:hidden;';
    document.body.appendChild(iframe);

    const doc = iframe.contentWindow.document;
    doc.open();
    doc.write(data.fullHtml);
    doc.close();

    setTimeout(() => {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
        setTimeout(() => {
            document.body.removeChild(iframe);
        }, 1000);
    }, 350);
};

/* ─── Excel Export ────────────────────────────────────────── */
window.doExportExcel = function() {
    const tabIdMap = {
        trabajador: 'report-trabajador-output',
        bocamina: 'report-bocamina-output',
        general: 'report-general-output',
        anticipos: 'report-anticipos-output'
    };
    const el = document.getElementById(tabIdMap[window._currentTab]);
    if (!el) { alert('No hay datos para exportar en esta pestaña.'); return; }
    if (typeof XLSX === 'undefined') { alert('Cargando librería Excel, intenta nuevamente en un segundo.'); return; }

    const tables = el.querySelectorAll('table');
    if (!tables.length) { alert('No se encontraron tablas para exportar en el reporte actual.'); return; }

    const sheetNames = {
        trabajador: ['Historial Trabajos', 'Historial Anticipos', 'Pagos Netos'],
        bocamina: ['Personal Bocamina', 'Trabajos del Período', 'Pagos del Período'],
        general: ['Resumen Semanal', 'Detalle Trabajos', 'Detalle Pagos'],
        anticipos: ['Historial Anticipos']
    };
    const wb = XLSX.utils.book_new();
    const names = sheetNames[window._currentTab] || [];
    tables.forEach((t, i) => {
        const ws = XLSX.utils.table_to_sheet(t);
        XLSX.utils.book_append_sheet(wb, ws, names[i] || ('Tabla ' + (i+1)));
    });
    XLSX.writeFile(wb, 'Reporte_' + window._currentTab.toUpperCase() + '_' + new Date().toLocaleDateString('es-BO').replace(/\//g,'-') + '.xlsx');
};
</script>
@endpush
