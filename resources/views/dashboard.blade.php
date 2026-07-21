@extends('layouts.app')

@section('title', 'Tablero Principal')

@section('content')
<div class="space-y-8">
    
    <!-- Welcome Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100">Tablero de Control</h1>
            <p class="text-sm text-slate-400 mt-1">Monitoreo general de bocaminas, contratos, producción y pagos.</p>
        </div>
        <div class="flex space-x-3 no-print">
            <a href="{{ route('anticipos.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-slate-800 border border-slate-700/80 hover:bg-slate-750 text-sm font-medium text-slate-200 transition duration-150">
                <i class="fa-solid fa-money-bill-transfer mr-2 text-amber-500"></i> Registrar Anticipo
            </a>
            <a href="{{ route('pagos.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-sm font-bold text-slate-950 transition duration-150 shadow-lg shadow-orange-500/10">
                <i class="fa-solid fa-receipt mr-2"></i> Procesar Pago
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Stat Card 1 -->
        <div class="glass-card rounded-xl p-6 relative overflow-hidden group hover:border-amber-500/25 transition duration-300">
            <div class="absolute top-0 right-0 p-3 opacity-10 text-6xl text-amber-500 group-hover:opacity-20 transition duration-300">
                <i class="fa-solid fa-user-group"></i>
            </div>
            <p class="text-sm font-medium text-slate-400 uppercase tracking-wider">Trabajadores</p>
            <p class="mt-2 text-3xl font-bold text-slate-100">{{ $totalTrabajadores }}</p>
            <div class="mt-2 text-xs text-slate-500 font-mono">Personas registradas</div>
        </div>

        <!-- Stat Card 2 -->
        <div class="glass-card rounded-xl p-6 relative overflow-hidden group hover:border-amber-500/25 transition duration-300">
            <div class="absolute top-0 right-0 p-3 opacity-10 text-6xl text-amber-500 group-hover:opacity-20 transition duration-300">
                <i class="fa-solid fa-mountain"></i>
            </div>
            <p class="text-sm font-medium text-slate-400 uppercase tracking-wider">Bocaminas</p>
            <p class="mt-2 text-3xl font-bold text-slate-100">{{ $totalBocaminas }}</p>
            <div class="mt-2 text-xs text-slate-500 font-mono">Frentes de trabajo activos</div>
        </div>

        <!-- Stat Card 3 -->
        <div class="glass-card rounded-xl p-6 relative overflow-hidden group hover:border-amber-500/25 transition duration-300">
            <div class="absolute top-0 right-0 p-3 opacity-10 text-6xl text-amber-500 group-hover:opacity-20 transition duration-300">
                <i class="fa-solid fa-file-contract"></i>
            </div>
            <p class="text-sm font-medium text-slate-400 uppercase tracking-wider">Contratos Activos</p>
            <p class="mt-2 text-3xl font-bold text-slate-100">{{ $totalContratosActivos }}</p>
            <div class="mt-2 text-xs text-slate-500 font-mono">Acuerdos vigentes en ejecución</div>
        </div>

        <!-- Stat Card 4 -->
        <div class="glass-card rounded-xl p-6 relative overflow-hidden group hover:border-amber-500/25 transition duration-300">
            <div class="absolute top-0 right-0 p-3 opacity-10 text-6xl text-amber-500 group-hover:opacity-20 transition duration-300">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <p class="text-sm font-medium text-slate-400 uppercase tracking-wider">Anticipos Pendientes</p>
            <p class="mt-2 text-3xl font-bold text-amber-500">Bs. {{ number_format($totalAnticiposPendientes, 2) }}</p>
            <div class="mt-2 text-xs text-slate-500 font-mono">Saldo por descontar</div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Chart 1: Producción por Bocamina -->
        <div class="glass-card rounded-xl p-6">
            <h3 class="text-lg font-semibold text-slate-100 mb-4 flex items-center">
                <i class="fa-solid fa-mountain mr-2 text-amber-500"></i> Producción Total por Bocamina (Bs.)
            </h3>
            <div class="relative h-72">
                <canvas id="bocaminasChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Pagos Históricos -->
        <div class="glass-card rounded-xl p-6">
            <h3 class="text-lg font-semibold text-slate-100 mb-4 flex items-center">
                <i class="fa-solid fa-chart-area mr-2 text-amber-500"></i> Desembolsos de Pagos Netos (Últimos Meses)
            </h3>
            <div class="relative h-72">
                <canvas id="pagosChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Logs Grid -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Recent Advances -->
        <div class="glass-card rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-100 flex items-center">
                    <i class="fa-solid fa-money-bill-transfer mr-2 text-amber-500"></i> Anticipos Recientes
                </h3>
                <a href="{{ route('anticipos.index') }}" class="text-xs text-amber-500 hover:underline">Ver todos</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            <th class="pb-3 font-medium">Trabajador</th>
                            <th class="pb-3 font-medium">Fecha</th>
                            <th class="pb-3 font-medium">Monto Total</th>
                            <th class="pb-3 font-medium">Saldo Pendiente</th>
                            <th class="pb-3 font-medium">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40 text-sm text-slate-300">
                        @forelse($recientesAnticipos as $anticipo)
                            <tr>
                                <td class="py-3 font-medium text-slate-200">{{ $anticipo->trabajador->nombre }}</td>
                                <td class="py-3 font-mono">{{ $anticipo->fecha->format('d/m/Y') }}</td>
                                <td class="py-3 font-mono text-amber-500">Bs. {{ number_format($anticipo->monto, 2) }}</td>
                                <td class="py-3 font-mono text-red-400">Bs. {{ number_format($anticipo->saldo, 2) }}</td>
                                <td class="py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $anticipo->pagado ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/25' : 'bg-amber-500/10 text-amber-400 border border-amber-500/25' }}">
                                        {{ $anticipo->pagado ? 'Pagado' : 'Pendiente' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-slate-500">No hay registros de anticipos recientes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Payments processed -->
        <div class="glass-card rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-100 flex items-center">
                    <i class="fa-solid fa-receipt mr-2 text-amber-500"></i> Pagos Procesados Recientemente
                </h3>
                <a href="{{ route('pagos.index') }}" class="text-xs text-amber-500 hover:underline">Ver todos</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            <th class="pb-3 font-medium">Trabajador</th>
                            <th class="pb-3 font-medium">Fecha</th>
                            <th class="pb-3 font-medium">Anticipos Descontados</th>
                            <th class="pb-3 font-medium">Pago Neto</th>
                            <th class="pb-3 font-medium">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40 text-sm text-slate-300">
                        @forelse($recientesPagos as $pago)
                            <tr>
                                <td class="py-3 font-medium text-slate-200">{{ $pago->trabajador->nombre }}</td>
                                <td class="py-3 font-mono">{{ $pago->fecha->format('d/m/Y') }}</td>
                                <td class="py-3 font-mono text-red-400">-Bs. {{ number_format($pago->anticipos_descontados, 2) }}</td>
                                <td class="py-3 font-mono text-emerald-400 font-semibold">Bs. {{ number_format($pago->neto, 2) }}</td>
                                <td class="py-3">
                                    <a href="{{ route('pagos.show', $pago->id) }}" class="text-amber-500 hover:text-amber-400 text-xs font-medium flex items-center">
                                        <i class="fa-solid fa-print mr-1"></i> Recibo
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-slate-500">No hay pagos procesados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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

        // Chart 1: Bocaminas
        const bocaminasCtx = document.getElementById('bocaminasChart').getContext('2d');
        new Chart(bocaminasCtx, {
            type: 'bar',
            data: {
                labels: produccionBocaminas.map(b => b.nombre),
                datasets: [{
                    label: 'Producción en Bs.',
                    data: produccionBocaminas.map(b => b.total),
                    backgroundColor: 'rgba(245, 158, 11, 0.45)',
                    borderColor: '#f59e0b',
                    borderWidth: 1.5,
                    borderRadius: 6
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
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { color: '#94a3b8' }
                    },
                    y: {
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { color: '#94a3b8' }
                    }
                }
            }
        });

        // Chart 2: Pagos Mensuales
        const pagosCtx = document.getElementById('pagosChart').getContext('2d');
        new Chart(pagosCtx, {
            type: 'line',
            data: {
                labels: pagosMensuales.map(p => p.etiqueta),
                datasets: [{
                    label: 'Neto Desembolsado',
                    data: pagosMensuales.map(p => p.total),
                    backgroundColor: 'rgba(234, 88, 12, 0.15)',
                    borderColor: '#ea580c',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#f59e0b',
                    pointBorderColor: '#020617',
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
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { color: '#94a3b8' }
                    },
                    y: {
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { color: '#94a3b8' }
                    }
                }
            }
        });
    });
</script>
@endsection
