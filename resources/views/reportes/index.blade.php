@extends('layouts.app')

@section('title', 'Reportes del Personal')

@section('content')
<style>
/* ===========================================================
   REPORTES DEL PERSONAL — ERP PREMIUM DESIGN SYSTEM
   =========================================================== */
.rpt-page { --rpt-bg-card: #ffffff; --rpt-border: #e2e8f0; --rpt-text: #0f172a; --rpt-text-sub: #475569; --rpt-text-muted: #94a3b8; --rpt-row-hover: #f8fafc; --rpt-th-bg: #f1f5f9; --rpt-th-color: #334155; }
html:not(.light-theme) .rpt-page { --rpt-bg-card: rgba(15,23,42,0.65); --rpt-border: rgba(255,255,255,0.08); --rpt-text: #f1f5f9; --rpt-text-sub: #94a3b8; --rpt-text-muted: #64748b; --rpt-row-hover: rgba(255,255,255,0.03); --rpt-th-bg: rgba(15,23,42,0.7); --rpt-th-color: #94a3b8; }

.rpt-card {
    background: var(--rpt-bg-card);
    border: 1px solid var(--rpt-border);
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    backdrop-filter: blur(12px);
    overflow: hidden;
}

/* ── Barra de Pestañas ── */
.rpt-tabs-bar {
    display: flex;
    gap: 6px;
    padding: 6px;
    border-radius: 16px;
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
    padding: 12px 14px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 0.02em;
    transition: all 0.2s ease;
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
    box-shadow: 0 4px 16px rgba(245,158,11,0.35);
}

/* ── Inputs de Filtro ── */
.rpt-filter-input {
    width: 100%;
    padding: 10px 14px;
    border-radius: 10px;
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

/* ── Tablas ── */
.rpt-section {
    border-radius: 14px;
    border: 1.5px solid var(--rpt-border);
    overflow: hidden;
    background: var(--rpt-bg-card);
}
.rpt-section-header {
    padding: 14px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1.5px solid var(--rpt-border);
    background: var(--rpt-th-bg);
    font-size: 12px; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.07em;
    color: var(--rpt-text-sub);
    font-family: 'Outfit', sans-serif;
}

table.rpt-tbl { width: 100%; border-collapse: collapse; }
table.rpt-tbl thead tr th {
    padding: 12px 18px;
    text-align: left;
    font-size: 11px; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.05em;
    color: var(--rpt-th-color);
    background: var(--rpt-th-bg);
    border-bottom: 1.5px solid var(--rpt-border);
    white-space: nowrap;
    font-family: 'Outfit', sans-serif;
    cursor: pointer;
}
table.rpt-tbl tbody tr td {
    padding: 12px 18px;
    font-size: 13px;
    color: var(--rpt-text-sub);
    border-bottom: 1px solid var(--rpt-border);
    white-space: nowrap;
    font-family: 'Outfit', sans-serif;
}
table.rpt-tbl tbody tr:hover td { background: var(--rpt-row-hover); }

/* Print Styles */
@media print {
    .no-print { display: none !important; }
    .rpt-card, .rpt-section { box-shadow: none !important; border: 1px solid #000 !important; }
    body { background: #fff !important; color: #000 !important; }
}
</style>

<div class="rpt-page space-y-6"
     x-data="{
        tab: '{{ $tab }}',
        searchTerm: '',
        sortCol: '',
        sortAsc: true,

        sortBy(col) {
            if (this.sortCol === col) {
                this.sortAsc = !this.sortAsc;
            } else {
                this.sortCol = col;
                this.sortAsc = true;
            }
        }
     }">

    {{-- ═══════════════ HEADER & GLOBAL ACTIONS ═══════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 no-print">
        <div>
            <h1 class="text-3xl font-extrabold flex items-center gap-3 tracking-tight text-slate-100">
                <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <i class="fa-solid fa-chart-line text-lg"></i>
                </span>
                Reportes del Personal
            </h1>
            <p class="text-xs text-slate-400 mt-1 ml-13">Monitoreo de planillas, desglose de anticipos, balance de bocaminas e historial de contratistas.</p>
        </div>
        <div class="flex flex-wrap gap-2.5">
            <button class="rpt-export-btn btn-excel" onclick="window.doExportExcel()">
                <i class="fa-solid fa-file-excel"></i> Excel
            </button>
            <button class="rpt-export-btn btn-pdf" onclick="window.doExportPDF()">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </button>
            <button class="rpt-export-btn btn-print" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    {{-- ═══════════════ TAB NAVIGATION ═══════════════ --}}
    <div class="rpt-tabs-bar no-print">
        <button class="rpt-tab-btn" :class="{ 'rpt-active': tab === 'general' }" @click="tab = 'general'; window.history.replaceState({}, '', '?tab=general')">
            <i class="fa-solid fa-chart-pie text-amber-500"></i> 📈 Resumen General
        </button>
        <button class="rpt-tab-btn" :class="{ 'rpt-active': tab === 'trabajador' }" @click="tab = 'trabajador'; window.history.replaceState({}, '', '?tab=trabajador')">
            <i class="fa-solid fa-user-group text-blue-500"></i> 👷 Trabajadores
        </button>
        <button class="rpt-tab-btn" :class="{ 'rpt-active': tab === 'bocamina' }" @click="tab = 'bocamina'; window.history.replaceState({}, '', '?tab=bocamina')">
            <i class="fa-solid fa-mountain text-emerald-500"></i> ⛏️ Bocaminas
        </button>
        <button class="rpt-tab-btn" :class="{ 'rpt-active': tab === 'anticipos' }" @click="tab = 'anticipos'; window.history.replaceState({}, '', '?tab=anticipos')">
            <i class="fa-solid fa-hand-holding-dollar text-rose-500"></i> 💵 Anticipos
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TAB 1: 📈 RESUMEN GENERAL --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'general'" space-y-6 x-cloak>
        
        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            
            {{-- Card 1: Total Pagado --}}
            <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-indigo-500 to-purple-650 text-white shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
                <div class="absolute right-8 -top-8 w-16 h-16 bg-white/10 rounded-full pointer-events-none"></div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-100/90">Total Pagado (Planillas)</span>
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20">
                        <i class="fa-solid fa-receipt text-xs text-white"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-extrabold tracking-tight font-mono">Bs. {{ number_format($genTotalPagado, 2) }}</h2>
                <p class="text-[10px] text-indigo-100/80 font-medium mt-2">Sumatoria de planillas liquidadas</p>
            </div>

            {{-- Card 2: Total Anticipos --}}
            <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-rose-500 to-red-650 text-white shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
                <div class="absolute right-8 -top-8 w-16 h-16 bg-white/10 rounded-full pointer-events-none"></div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-rose-100/90">Total Anticipos (Adelantos)</span>
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20">
                        <i class="fa-solid fa-hand-holding-dollar text-xs text-white"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-extrabold tracking-tight font-mono">Bs. {{ number_format($genTotalAnticipos, 2) }}</h2>
                <p class="text-[10px] text-rose-100/80 font-medium mt-2">Egresado por adelantos otorgados</p>
            </div>

            {{-- Card 3: Saldo de Caja del Personal --}}
            @php
                $posGen = $genSaldoCaja >= 0;
                $gradGen = $posGen ? 'from-emerald-500 to-teal-600' : 'from-amber-500 to-orange-650';
            @endphp
            <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br {{ $gradGen }} text-white shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
                <div class="absolute right-8 -top-8 w-16 h-16 bg-white/10 rounded-full pointer-events-none"></div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-white/90">Saldo de la Caja del Personal</span>
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20">
                        <i class="fa-solid fa-vault text-xs text-white"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-extrabold tracking-tight font-mono">Bs. {{ number_format(abs($genSaldoCaja), 2) }}</h2>
                <p class="text-[10px] text-emerald-100/80 font-medium mt-2">
                    {{ $posGen ? '● Efectivo disponible para planillas' : '▲ Caja en déficit' }}
                </p>
            </div>

            {{-- Card 4: Trabajadores Activos --}}
            <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-cyan-500 to-blue-650 text-white shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
                <div class="absolute right-8 -top-8 w-16 h-16 bg-white/10 rounded-full pointer-events-none"></div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-100/90">Trabajadores Activos</span>
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20">
                        <i class="fa-solid fa-users text-xs text-white"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-extrabold tracking-tight font-mono">{{ $genTrabajadoresActivos }}</h2>
                <p class="text-[10px] text-cyan-100/80 font-medium mt-2">Personal registrado activo</p>
            </div>
        </div>

        {{-- Interactive Charts Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pt-4">
            
            {{-- Chart 1: Gastos por Semana --}}
            <div class="rpt-card p-6 lg:col-span-2 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-chart-column text-amber-500"></i> Gastos por Semana (Planillas vs Anticipos)
                    </h3>
                    <span class="text-[10px] font-mono text-slate-400">Últimas 8 semanas</span>
                </div>
                <div class="h-64 relative">
                    <canvas id="chartGastosSemana"></canvas>
                </div>
            </div>

            {{-- Chart 2 & 3: Doughnut breakdown --}}
            <div class="rpt-card p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-pie-chart text-emerald-500"></i> Gastos por Bocamina
                    </h3>
                </div>
                <div class="h-64 relative flex items-center justify-center">
                    <canvas id="chartGastosBocamina"></canvas>
                </div>
            </div>

        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TAB 2: 👷 TRABAJADORES --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'trabajador'" space-y-6 x-cloak>
        
        {{-- Filters Section --}}
        <div class="rpt-card p-6 no-print">
            <form action="{{ route('reportes.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5 items-end">
                <input type="hidden" name="tab" value="trabajador">

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-user"></i> Trabajador</label>
                    <select name="trabajador_id" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="">Todos los Trabajadores</option>
                        @foreach($allTrabajadores as $t)
                            <option value="{{ $t->id }}" {{ $trabId == $t->id ? 'selected' : '' }}>{{ $t->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-user-tag"></i> Tipo de Trabajador</label>
                    <select name="rol" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="">Todos los Cargos</option>
                        @foreach($roles as $r)
                            <option value="{{ $r }}" {{ $trabRol == $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-file-contract"></i> Tipo de Contrato</label>
                    <select name="tipo_contrato_id" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="">Todos los Contratos</option>
                        @foreach($tiposContrato as $tc)
                            <option value="{{ $tc->id }}" {{ $trabContratoId == $tc->id ? 'selected' : '' }}>{{ $tc->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-mountain"></i> Bocamina</label>
                    <select name="bocamina_id" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="">Todas las Bocaminas</option>
                        @foreach($bocaminas as $b)
                            <option value="{{ $b->id }}" {{ $trabBocaminaId == $b->id ? 'selected' : '' }}>{{ $b->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="rpt-export-btn btn-print flex-1 justify-center">
                        <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>

        {{-- Workers KPI Summary Cards --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <div class="rpt-card p-5 border-l-4 border-l-indigo-500">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Pagado</span>
                <h3 class="text-2xl font-black font-mono text-indigo-400 mt-1">Bs. {{ number_format($totPagadoTrabajador, 2) }}</h3>
            </div>
            <div class="rpt-card p-5 border-l-4 border-l-rose-500">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Anticipos</span>
                <h3 class="text-2xl font-black font-mono text-rose-400 mt-1">Bs. {{ number_format($totAnticiposTrabajador, 2) }}</h3>
            </div>
            <div class="rpt-card p-5 border-l-4 border-l-emerald-500">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Neto Recibido en Planillas</span>
                <h3 class="text-2xl font-black font-mono text-emerald-400 mt-1">Bs. {{ number_format($netoRecibidoTrabajador, 2) }}</h3>
            </div>
        </div>

        {{-- Tables Section: Pagos & Anticipos --}}
        <div class="rpt-section">
            <div class="rpt-section-header">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-amber-500"></i> Historial de Liquidaciones y Planillas de Pago
                </div>
                <input type="text" x-model="searchTerm" placeholder="🔍 Buscar en tabla..." 
                       class="px-3 py-1 bg-slate-900 border border-slate-700/80 rounded-lg text-xs text-slate-100 font-sans focus:outline-none focus:border-amber-500">
            </div>
            <div class="overflow-x-auto">
                <table class="rpt-tbl">
                    <thead>
                        <tr>
                            <th @click="sortBy('id')">ID ⇕</th>
                            <th @click="sortBy('fecha')">Fecha ⇕</th>
                            <th @click="sortBy('nombre')">Trabajador ⇕</th>
                            <th>Bocamina</th>
                            <th>Subtotal Trabajos</th>
                            <th>Bonos (+)</th>
                            <th>Descuentos (-)</th>
                            <th>Anticipos (-)</th>
                            <th @click="sortBy('neto')">Pago Neto ⇕</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($listPagosTrabajador as $pago)
                            <tr>
                                <td class="td-mono">{{ $pago->id }}</td>
                                <td class="td-mono">{{ $pago->fecha->format('d/m/Y') }}</td>
                                <td class="td-name">{{ $pago->trabajador->nombre }}</td>
                                <td>{{ $pago->trabajador->bocamina->nombre ?? 'N/A' }}</td>
                                <td class="td-mono">Bs. {{ number_format($pago->subtotal, 2) }}</td>
                                <td class="td-mono text-emerald-400">+Bs. {{ number_format($pago->bonos, 2) }}</td>
                                <td class="td-mono text-red-400">-Bs. {{ number_format($pago->descuentos, 2) }}</td>
                                <td class="td-mono text-red-400">-Bs. {{ number_format($pago->anticipos_descontados, 2) }}</td>
                                <td class="td-mono text-emerald-400 font-bold">Bs. {{ number_format($pago->neto, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-8 text-slate-500">No se encontraron pagos registrados con los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TAB 3: ⛏️ BOCAMINAS --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'bocamina'" space-y-6 x-cloak>
        
        {{-- Filters Section --}}
        <div class="rpt-card p-6 no-print">
            <form action="{{ route('reportes.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 items-end">
                <input type="hidden" name="tab" value="bocamina">

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-mountain"></i> Bocamina</label>
                    <select name="boc_bocamina_id" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="">Todas las Bocaminas</option>
                        @foreach($bocaminas as $b)
                            <option value="{{ $b->id }}" {{ $bocFiltroId == $b->id ? 'selected' : '' }}>{{ $b->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-user-tag"></i> Tipo de Trabajador</label>
                    <select name="boc_rol" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="">Todos los Cargos</option>
                        @foreach($roles as $r)
                            <option value="{{ $r }}" {{ $bocRol == $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-file-contract"></i> Tipo de Contrato</label>
                    <select name="boc_tipo_contrato_id" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="">Todos los Contratos</option>
                        @foreach($tiposContrato as $tc)
                            <option value="{{ $tc->id }}" {{ $bocContratoId == $tc->id ? 'selected' : '' }}>{{ $tc->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="rpt-export-btn btn-print flex-1 justify-center">
                        <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>

        {{-- Bocamina Details Grid --}}
        <div class="space-y-6">
            @foreach($bocaminasResumen as $bRes)
                <div class="rpt-card p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-3 gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold">
                                <i class="fa-solid fa-mountain"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-slate-100">{{ $bRes['bocamina']->nombre }}</h2>
                                <p class="text-xs text-slate-400">{{ $bRes['cant_trabajadores'] }} trabajador(es) asignado(s)</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-4 font-mono text-xs">
                            <div class="bg-slate-900/60 px-3 py-1.5 rounded-lg border border-slate-800">
                                <span class="text-slate-500 block text-[9px]">TOTAL GASTADO:</span>
                                <span class="font-bold text-slate-100 text-sm">Bs. {{ number_format($bRes['total_gastado'], 2) }}</span>
                            </div>
                            <div class="bg-indigo-500/10 px-3 py-1.5 rounded-lg border border-indigo-500/20">
                                <span class="text-indigo-400 block text-[9px]">PAGOS PLANILLAS:</span>
                                <span class="font-bold text-indigo-400 text-sm">Bs. {{ number_format($bRes['total_pagos'], 2) }}</span>
                            </div>
                            <div class="bg-rose-500/10 px-3 py-1.5 rounded-lg border border-rose-500/20">
                                <span class="text-rose-400 block text-[9px]">ANTICIPOS:</span>
                                <span class="font-bold text-rose-400 text-sm">Bs. {{ number_format($bRes['total_anticipos'], 2) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Workers table --}}
                    <div class="overflow-x-auto">
                        <table class="rpt-tbl">
                            <thead>
                                <tr>
                                    <th>Trabajador</th>
                                    <th>Cargo</th>
                                    <th>Contrato</th>
                                    <th>Total Pagos</th>
                                    <th>Total Anticipos</th>
                                    <th>Total Egresado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bRes['trabajadores_detalle'] as $wd)
                                    <tr>
                                        <td class="td-name">{{ $wd['trabajador']->nombre }}</td>
                                        <td><span class="badge badge-gray">{{ ucfirst($wd['trabajador']->rol ?? 'Trabajador') }}</span></td>
                                        <td>{{ $wd['trabajador']->tipoContrato->nombre ?? 'N/A' }}</td>
                                        <td class="td-mono">Bs. {{ number_format($wd['pagos'], 2) }}</td>
                                        <td class="td-mono text-rose-400">Bs. {{ number_format($wd['anticipos'], 2) }}</td>
                                        <td class="td-mono text-emerald-400 font-bold">Bs. {{ number_format($wd['total'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TAB 4: 💵 ANTICIPOS --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'anticipos'" space-y-6 x-cloak>
        
        {{-- Filters Section --}}
        <div class="rpt-card p-6 no-print">
            <form action="{{ route('reportes.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5 items-end">
                <input type="hidden" name="tab" value="anticipos">

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-user"></i> Trabajador</label>
                    <select name="ant_trabajador_id" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="">Todos los Trabajadores</option>
                        @foreach($allTrabajadores as $t)
                            <option value="{{ $t->id }}" {{ $antTrabId == $t->id ? 'selected' : '' }}>{{ $t->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-user-tag"></i> Tipo de Trabajador</label>
                    <select name="ant_rol" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="">Todos los Cargos</option>
                        @foreach($roles as $r)
                            <option value="{{ $r }}" {{ $antRol == $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-mountain"></i> Bocamina</label>
                    <select name="ant_bocamina_id" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="">Todas las Bocaminas</option>
                        @foreach($bocaminas as $b)
                            <option value="{{ $b->id }}" {{ $antBocaminaId == $b->id ? 'selected' : '' }}>{{ $b->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-toggle-on"></i> Estado de Saldo</label>
                    <select name="ant_estado" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="todos" {{ $antEstado === 'todos' ? 'selected' : '' }}>Todos los Anticipos</option>
                        <option value="pendiente" {{ $antEstado === 'pendiente' ? 'selected' : '' }}>Pendientes por Descontar</option>
                        <option value="descontado" {{ $antEstado === 'descontado' ? 'selected' : '' }}>Totalmente Descontados</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="rpt-export-btn btn-print flex-1 justify-center">
                        <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>

        {{-- Anticipos KPI Summary Cards --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rpt-card p-5 border-l-4 border-l-cyan-500">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Vales Emitidos</span>
                <h3 class="text-2xl font-black font-mono text-cyan-400 mt-1">{{ $antConteo }}</h3>
            </div>
            <div class="rpt-card p-5 border-l-4 border-l-rose-500">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Monto Total Anticipado</span>
                <h3 class="text-2xl font-black font-mono text-rose-400 mt-1">Bs. {{ number_format($antMontoTotal, 2) }}</h3>
            </div>
            <div class="rpt-card p-5 border-l-4 border-l-amber-500">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Anticipos Pendientes</span>
                <h3 class="text-2xl font-black font-mono text-amber-400 mt-1">Bs. {{ number_format($antMontoPendiente, 2) }}</h3>
            </div>
            <div class="rpt-card p-5 border-l-4 border-l-emerald-500">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Anticipos Descontados</span>
                <h3 class="text-2xl font-black font-mono text-emerald-400 mt-1">Bs. {{ number_format($antMontoDescontado, 2) }}</h3>
            </div>
        </div>

        {{-- Anticipos History Table --}}
        <div class="rpt-section">
            <div class="rpt-section-header">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-hand-holding-dollar text-rose-500"></i> Historial de Vales de Anticipo
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="rpt-tbl">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Trabajador</th>
                            <th>Bocamina</th>
                            <th>Monto Original</th>
                            <th>Saldo Restante</th>
                            <th>Estado</th>
                            <th class="no-print">Comprobante</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($listAnticiposTab as $ant)
                            <tr>
                                <td class="td-mono">{{ $ant->id }}</td>
                                <td class="td-mono">{{ $ant->fecha->format('d/m/Y') }}</td>
                                <td class="td-name">{{ $ant->trabajador->nombre }}</td>
                                <td>{{ $ant->trabajador->bocamina->nombre ?? 'N/A' }}</td>
                                <td class="td-mono">Bs. {{ number_format($ant->monto, 2) }}</td>
                                <td class="td-mono text-rose-400 font-bold">Bs. {{ number_format($ant->saldo, 2) }}</td>
                                <td>
                                    <span class="badge {{ $ant->saldo == 0 ? 'badge-gray' : 'badge-red' }}">
                                        {{ $ant->saldo == 0 ? 'Descontado' : 'Pendiente' }}
                                    </span>
                                </td>
                                <td class="no-print">
                                    <a href="{{ route('anticipos.recibo', $ant->id) }}" target="_blank"
                                       class="p-1.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-emerald-400 transition" title="Imprimir Vale">
                                        <i class="fa-solid fa-print text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-8 text-slate-500">No se encontraron anticipos registrados con los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Chart 1: Gastos por Semana
    const ctxSemana = document.getElementById('chartGastosSemana');
    if (ctxSemana) {
        const dataSemana = @json($semanasChart);
        new Chart(ctxSemana, {
            type: 'bar',
            data: {
                labels: dataSemana.map(d => d.label),
                datasets: [
                    {
                        label: 'Planillas (Pagos)',
                        data: dataSemana.map(d => d.pagos),
                        backgroundColor: '#6366f1',
                        borderRadius: 6
                    },
                    {
                        label: 'Anticipos',
                        data: dataSemana.map(d => d.anticipos),
                        backgroundColor: '#f43f5e',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { color: '#94a3b8', font: { family: 'Outfit', weight: 'bold' } } }
                },
                scales: {
                    x: { ticks: { color: '#94a3b8' }, grid: { display: false } },
                    y: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255,255,255,0.05)' } }
                }
            }
        });
    }

    // Chart 2: Gastos por Bocamina
    const ctxBocamina = document.getElementById('chartGastosBocamina');
    if (ctxBocamina) {
        const dataBocamina = @json($bocaminasChart);
        new Chart(ctxBocamina, {
            type: 'doughnut',
            data: {
                labels: dataBocamina.map(d => d.nombre),
                datasets: [{
                    data: dataBocamina.map(d => d.total),
                    backgroundColor: ['#10b981', '#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#94a3b8', font: { family: 'Outfit', weight: 'bold' } } }
                }
            }
        });
    }
});

function doExportExcel() {
    window.print();
}

function doExportPDF() {
    window.print();
}
</script>
@endpush
