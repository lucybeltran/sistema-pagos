@extends('layouts.app')

@section('title', 'Tablero Principal')

@section('content')
<div class="space-y-8">
    
    <!-- Welcome Header -->
    <div class="welcome-header mb-6">
        <h1 class="text-3xl font-bold tracking-tight text-slate-100">Tablero de Control</h1>
        <p class="text-sm text-slate-400 mt-1">Monitoreo general de bocaminas, contratos, producción y pagos.</p>
    </div>
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
        <!-- Card 1: Trabajadores -->
        <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-amber-500 to-orange-650 text-white shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="absolute right-8 -top-8 w-16 h-16 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-100/90">Trabajadores</span>
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20">
                    <i class="fa-solid fa-user-group text-xs"></i>
                </div>
            </div>
            <h2 class="text-3xl font-extrabold tracking-tight">{{ $totalTrabajadores }}</h2>
            <p class="text-[10px] text-amber-100/80 font-medium mt-2">Personas registradas</p>
        </div>

        <!-- Card 2: Bocaminas -->
        <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="absolute right-8 -top-8 w-16 h-16 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-100/90">Bocaminas</span>
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20">
                    <i class="fa-solid fa-mountain text-xs"></i>
                </div>
            </div>
            <h2 class="text-3xl font-extrabold tracking-tight">{{ $totalBocaminas }}</h2>
            <p class="text-[10px] text-blue-100/80 font-medium mt-2">Frentes activos</p>
        </div>

        <!-- Card 3: Saldo Caja Chica -->
        <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-cyan-500 to-blue-650 text-white shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="absolute right-8 -top-8 w-16 h-16 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-100/90">Caja Chica</span>
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20">
                    <i class="fa-solid fa-vault text-xs"></i>
                </div>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight">Bs. {{ number_format($saldo_caja, 0) }}</h2>
            <p class="text-[10px] text-cyan-100/80 font-medium mt-2">Efectivo disponible</p>
        </div>

        <!-- Card 4: Ventas Mineral -->
        <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="absolute right-8 -top-8 w-16 h-16 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-100/90">Ventas Mineral</span>
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20">
                    <i class="fa-solid fa-coins text-xs"></i>
                </div>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight">Bs. {{ number_format($totalVentasMineral, 0) }}</h2>
            <p class="text-[10px] text-emerald-100/80 font-medium mt-2">Ingresos por mineral</p>
        </div>

        <!-- Card 5: Compras Mineral -->
        <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-rose-500 to-red-600 text-white shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="absolute right-8 -top-8 w-16 h-16 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-100/90">Compras Mineral</span>
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20">
                    <i class="fa-solid fa-truck-ramp-box text-xs"></i>
                </div>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight">Bs. {{ number_format($totalComprasMineral, 0) }}</h2>
            <p class="text-[10px] text-rose-100/80 font-medium mt-2">Egresos por compra</p>
        </div>
    </div>

    <!-- Main Analytical Dashboard Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Column 1: Line Chart (Flujo de Pagos) -->
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-200">
                    <i class="fa-solid fa-chart-line mr-2 text-teal-400"></i> Flujo de Pagos (Bs.)
                </h3>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-500/10 text-teal-400 border border-teal-500/20">Mensual</span>
            </div>
            <div class="relative h-64 w-full">
                <canvas id="pagosChart"></canvas>
            </div>
        </div>

        <!-- Column 2: Doughnut Chart (Producción por Bocamina) -->
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-200">
                    <i class="fa-solid fa-chart-pie mr-2 text-emerald-400"></i> Producción por Bocamina
                </h3>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Distribución</span>
            </div>
            <div class="relative h-64 w-full flex items-center justify-center">
                <canvas id="bocaminasChart"></canvas>
            </div>
        </div>

        <!-- Column 3: Top Trabajadores -->
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-200">
                    <i class="fa-solid fa-trophy mr-2 text-amber-400"></i> Top Trabajadores (Producción)
                </h3>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">Líderes</span>
            </div>
            <div class="space-y-3 mt-2">
                @forelse($topTrabajadores as $index => $trabajador)
                    @php
                        $rankColorClass = match($index) {
                            0 => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                            1 => 'bg-slate-400/20 text-slate-350 border-slate-400/30',
                            2 => 'bg-amber-700/20 text-amber-600 border-amber-700/30',
                            default => 'bg-slate-800/40 text-slate-400 border-slate-700/40'
                        };
                        $accentColorClass = match($index) {
                            0 => 'border-l-2 border-amber-500',
                            1 => 'border-l-2 border-slate-400',
                            2 => 'border-l-2 border-amber-700',
                            default => 'border-l-2 border-transparent'
                        };
                    @endphp
                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-900/15 border border-slate-850 hover:border-slate-800/50 hover:bg-slate-900/30 transition-all duration-200 {{ $accentColorClass }}">
                        <div class="flex items-center space-x-3">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold border {{ $rankColorClass }}">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <div class="text-xs font-bold text-slate-200 leading-tight">{{ $trabajador->nombre }}</div>
                                <div class="text-[9px] font-semibold text-slate-450 mt-0.5 uppercase tracking-wider">{{ $trabajador->rol }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-extrabold text-sky-400">Bs. {{ number_format($trabajador->trabajos_sum_subtotal ?? 0, 2) }}</div>
                            <div class="text-[8px] text-slate-500 font-mono mt-0.5">({{ $trabajador->trabajos_count ?? 0 }} trabajos)</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-slate-500 text-xs font-mono">
                        No se registran trabajos de producción.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Activity Feeds (Modulo 1 vs Modulo 2) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column: Personal y Pagos -->
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-800/30">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-200 flex items-center">
                    <i class="fa-solid fa-users mr-2 text-indigo-400"></i> Movimientos al Personal (Caja/Pagos)
                </h3>
            </div>
            
            <div class="space-y-4">
                <!-- Sub-section: Pagos Procesados -->
                <div>
                    <h4 class="text-[10px] font-bold uppercase text-slate-450 tracking-wider mb-2 flex items-center justify-between">
                        <span>Últimos Pagos Procesados</span>
                        <a href="{{ route('pagos.index') }}" class="text-[9px] text-teal-500 hover:underline capitalize font-semibold">Ver todos</a>
                    </h4>
                    <div class="space-y-2">
                        @forelse($recientesPagos as $pago)
                            <div class="flex items-center justify-between p-2 rounded-lg bg-slate-900/10 border border-slate-850 hover:bg-slate-900/25 transition duration-150">
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center justify-center w-7 h-7 rounded-full bg-emerald-500/10 text-emerald-400">
                                        <i class="fa-solid fa-receipt text-[10px]"></i>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-200">{{ $pago->trabajador->nombre }}</div>
                                        <div class="text-[9px] text-slate-500 mt-0.5">{{ $pago->fecha->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-xs font-extrabold text-emerald-400">Bs. {{ number_format($pago->neto, 2) }}</span>
                                    <a href="{{ route('pagos.show', $pago->id) }}" class="p-1 rounded bg-slate-800 hover:bg-slate-700 border border-slate-700/40 text-slate-300 text-[10px]" title="Ver Recibo">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-slate-500 text-xs font-mono">No hay pagos procesados recientemente.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Sub-section: Anticipos Registrados -->
                <div>
                    <h4 class="text-[10px] font-bold uppercase text-slate-450 tracking-wider mb-2 flex items-center justify-between">
                        <span>Últimos Anticipos Registrados</span>
                        <a href="{{ route('anticipos.index') }}" class="text-[9px] text-rose-500 hover:underline capitalize font-semibold">Ver todos</a>
                    </h4>
                    <div class="space-y-2">
                        @forelse($recientesAnticipos as $anticipo)
                            <div class="flex items-center justify-between p-2 rounded-lg bg-slate-900/10 border border-slate-850 hover:bg-slate-900/25 transition duration-150">
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center justify-center w-7 h-7 rounded-full bg-rose-500/10 text-rose-400">
                                        <i class="fa-solid fa-hand-holding-dollar text-[10px]"></i>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-200">{{ $anticipo->trabajador->nombre }}</div>
                                        <div class="text-[9px] text-slate-500 mt-0.5">{{ $anticipo->fecha->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-extrabold text-rose-455">Bs. {{ number_format($anticipo->monto, 2) }}</div>
                                    <div class="text-[8px] text-red-400 mt-0.5">Saldo: Bs. {{ number_format($anticipo->saldo, 2) }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-slate-500 text-xs font-mono">No hay anticipos registrados recientemente.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Compra y Venta de Mineral -->
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-800/30">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-200 flex items-center">
                    <i class="fa-solid fa-dolly mr-2 text-orange-400"></i> Movimientos de Compra y Venta (Mineral)
                </h3>
                <a href="{{ route('transacciones-minerales.index') }}" class="text-[9px] text-orange-500 hover:underline capitalize font-semibold">Ver todos</a>
            </div>

            <div class="space-y-2">
                @forelse($recientesTransaccionesMineral as $transaccion)
                    @php
                        $isVenta = $transaccion->tipo === 'venta';
                        $iconClass = $isVenta ? 'fa-solid fa-arrow-trend-up text-emerald-400 bg-emerald-500/10' : 'fa-solid fa-arrow-trend-down text-rose-400 bg-rose-500/10';
                        $typeLabel = $isVenta ? 'Venta' : 'Compra';
                        $typeColor = $isVenta ? 'text-emerald-400' : 'text-rose-400';
                    @endphp
                    <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-900/10 border border-slate-850 hover:bg-slate-900/25 transition duration-150">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $isVenta ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                                <i class="{{ $isVenta ? 'fa-solid fa-arrow-trend-up' : 'fa-solid fa-arrow-trend-down' }} text-xs"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-200">{{ $transaccion->cliente_proveedor }}</div>
                                <div class="text-[9px] text-slate-500 mt-0.5 flex items-center space-x-2">
                                    <span class="font-semibold {{ $typeColor }}">{{ $typeLabel }}</span>
                                    <span>•</span>
                                    <span>{{ $transaccion->presentacion }}</span>
                                    <span>•</span>
                                    <span>{{ $transaccion->peso_neto_seco }} TN Seco</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-extrabold text-slate-200">Bs. {{ number_format($transaccion->monto_total, 2) }}</div>
                            <div class="text-[8px] text-slate-500 font-mono mt-0.5">{{ $transaccion->fecha->format('d/m/Y') }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 text-slate-500 text-xs font-mono">
                        No hay registros de transacciones de mineral recientemente.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<!-- Render Charts Scripts -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Data from backend
        const produccionBocaminas = @json($produccionBocaminas);
        const pagosMensuales = @json($pagosMensuales);

        // Helper to get colors based on theme
        function getChartThemeColors() {
            const isLight = document.documentElement.classList.contains('light-theme');
            return {
                gridColor: isLight ? 'rgba(15, 23, 42, 0.08)' : 'rgba(255, 255, 255, 0.05)',
                tickColor: isLight ? '#475569' : '#94a3b8'
            };
        }

        let colors = getChartThemeColors();

        // Chart 1: Bocaminas (Doughnut Chart)
        const bocaminasCtx = document.getElementById('bocaminasChart').getContext('2d');
        const bocaminasChart = new Chart(bocaminasCtx, {
            type: 'doughnut',
            data: {
                labels: produccionBocaminas.map(b => b.nombre),
                datasets: [{
                    data: produccionBocaminas.map(b => b.total),
                    backgroundColor: [
                        'rgba(245, 158, 11, 0.75)',  // Amber
                        'rgba(14, 165, 233, 0.75)',  // Sky
                        'rgba(16, 185, 129, 0.75)',  // Emerald
                        'rgba(139, 92, 246, 0.75)',  // Violet
                        'rgba(244, 63, 94, 0.75)'    // Rose
                    ],
                    borderColor: document.documentElement.classList.contains('light-theme') ? '#ffffff' : '#0f172a',
                    borderWidth: 2,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 8,
                            padding: 10,
                            color: colors.tickColor,
                            font: { family: 'Outfit', size: 10 }
                        }
                    }
                },
                cutout: '65%'
            }
        });

        // Chart 2: Pagos Mensuales (Line Chart)
        const pagosCtx = document.getElementById('pagosChart').getContext('2d');
        const pagosChart = new Chart(pagosCtx, {
            type: 'line',
            data: {
                labels: pagosMensuales.map(p => p.etiqueta),
                datasets: [{
                    label: 'Neto Desembolsado (Bs.)',
                    data: pagosMensuales.map(p => p.total),
                    backgroundColor: 'rgba(20, 184, 166, 0.15)',
                    borderColor: '#14b8a6',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#0d9488',
                    pointBorderColor: document.documentElement.classList.contains('light-theme') ? '#f8fafc' : '#020617',
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { color: colors.gridColor },
                        ticks: { color: colors.tickColor, font: { family: 'Outfit', size: 10 } }
                    },
                    y: {
                        grid: { color: colors.gridColor },
                        ticks: { color: colors.tickColor, font: { family: 'Outfit', size: 10 } }
                    }
                }
            }
        });

        // Listen for theme change events to dynamically update chart colors
        window.addEventListener('theme-changed', function() {
            const newColors = getChartThemeColors();
            
            // Update Bocaminas Chart
            bocaminasChart.options.plugins.legend.labels.color = newColors.tickColor;
            bocaminasChart.data.datasets[0].borderColor = document.documentElement.classList.contains('light-theme') ? '#ffffff' : '#0f172a';
            bocaminasChart.update();

            // Update Pagos Chart
            pagosChart.options.scales.x.grid.color = newColors.gridColor;
            pagosChart.options.scales.x.ticks.color = newColors.tickColor;
            pagosChart.options.scales.y.grid.color = newColors.gridColor;
            pagosChart.options.scales.y.ticks.color = newColors.tickColor;
            
            const isLight = document.documentElement.classList.contains('light-theme');
            pagosChart.data.datasets[0].pointBorderColor = isLight ? '#f8fafc' : '#020617';
            
            pagosChart.update();
        });
    });
</script>
@endsection
