@extends('layouts.app')

@section('title', 'Historial de Pagos')

@section('content')
<div x-data="{ 
    activeTab: 'pagos',
    openReloadModal: false,
    fecha: '{{ now()->toDateString() }}',
    monto: '',
    observacion: ''
}" class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100">Control de Caja y Pagos</h1>
            <p class="text-sm text-slate-400 mt-1">Monitorea los fondos retirados del banco, gestiona los pagos semanales y consulta el saldo disponible.</p>
        </div>
        <div class="flex flex-wrap gap-3 self-start">
            <button @click="openReloadModal = true" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-slate-800 border border-slate-700 hover:bg-slate-750 text-sm font-bold text-slate-200 transition duration-150">
                <i class="fa-solid fa-money-bill-trend-up mr-2 text-amber-500"></i> Recargar Caja
            </button>
            <a href="{{ route('pagos.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-sm font-bold text-slate-950 transition duration-150 shadow-lg shadow-orange-500/10">
                <i class="fa-solid fa-receipt mr-2"></i> Procesar Nuevo Pago
            </a>
        </div>
    </div>

    <!-- Cash Pool Ledger Summary Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <!-- Total Loaded (Banco) -->
        <div class="glass-card rounded-xl p-6 relative overflow-hidden group border-amber-500/10 hover:border-amber-500/25 transition duration-300">
            <div class="absolute top-0 right-0 p-3 opacity-10 text-6xl text-amber-500 group-hover:opacity-20 transition duration-300">
                <i class="fa-solid fa-building-columns"></i>
            </div>
            <p class="text-sm font-medium text-slate-400 uppercase tracking-wider">Total Recargado (Banco)</p>
            <p class="mt-2 text-3xl font-bold text-slate-100">Bs. {{ number_format($total_recargado, 2) }}</p>
            <div class="mt-2 text-xs text-slate-500 font-mono">Fondos ingresados a caja chica</div>
        </div>

        <!-- Total Spent (Personal) -->
        <div class="glass-card rounded-xl p-6 relative overflow-hidden group border-rose-500/10 hover:border-rose-500/25 transition duration-300">
            <div class="absolute top-0 right-0 p-3 opacity-10 text-6xl text-rose-500 group-hover:opacity-20 transition duration-300">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <p class="text-sm font-medium text-slate-400 uppercase tracking-wider">Gastado (Pagos y Anticipos)</p>
            <p class="mt-2 text-3xl font-bold text-rose-450">Bs. {{ number_format($total_gastado, 2) }}</p>
            <div class="mt-2 text-xs text-slate-500 font-mono">Egresado por planillas y adelantos</div>
        </div>

        <!-- Remaining Cash -->
        <div class="glass-card rounded-xl p-6 relative overflow-hidden group border-emerald-500/10 hover:border-emerald-500/25 transition duration-300">
            <div class="absolute top-0 right-0 p-3 opacity-10 text-6xl text-emerald-500 group-hover:opacity-20 transition duration-300">
                <i class="fa-solid fa-vault"></i>
            </div>
            <p class="text-sm font-medium text-slate-400 uppercase tracking-wider">Saldo Sobrante en Caja</p>
            <p class="mt-2 text-3xl font-bold {{ $saldo_caja >= 0 ? 'text-emerald-400' : 'text-rose-450' }}">
                Bs. {{ number_format($saldo_caja, 2) }}
            </p>
            <div class="mt-2 text-xs text-slate-500 font-mono">Efectivo físico disponible</div>
        </div>
    </div>

    <!-- Tabs Header -->
    <div class="border-b border-slate-800/60 no-print flex space-x-6">
        <button @click="activeTab = 'pagos'" 
                :class="activeTab === 'pagos' ? 'border-amber-500 text-amber-500 font-bold' : 'border-transparent text-slate-400 hover:text-slate-200'"
                class="py-3 px-1 border-b-2 text-sm font-medium transition duration-150">
            <i class="fa-solid fa-receipt mr-2"></i> Historial de Pagos
        </button>
        <button @click="activeTab = 'fondos'" 
                :class="activeTab === 'fondos' ? 'border-amber-500 text-amber-500 font-bold' : 'border-transparent text-slate-400 hover:text-slate-200'"
                class="py-3 px-1 border-b-2 text-sm font-medium transition duration-150">
            <i class="fa-solid fa-building-columns mr-2"></i> Historial de Recargas (Banco)
        </button>
    </div>

    <!-- TAB 1: Payouts List -->
    <div x-show="activeTab === 'pagos'" class="glass-card rounded-xl overflow-hidden" x-cloak>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800">
                <thead>
                    <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900/40">
                        <th class="px-6 py-4 font-semibold">ID</th>
                        <th class="px-6 py-4 font-semibold">Fecha</th>
                        <th class="px-6 py-4 font-semibold">Trabajador / Contratista</th>
                        <th class="px-6 py-4 font-semibold">Bocamina</th>
                        <th class="px-6 py-4 font-semibold">Trabajos (Subtotal)</th>
                        <th class="px-6 py-4 font-semibold">Bonos (+)</th>
                        <th class="px-6 py-4 font-semibold">Descuentos (-)</th>
                        <th class="px-6 py-4 font-semibold">Anticipos (-)</th>
                        <th class="px-6 py-4 font-semibold">Pago Neto</th>
                        <th class="px-6 py-4 font-semibold no-print">Comprobante</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-sm text-slate-300">
                    @forelse($pagos as $pago)
                        <tr class="hover:bg-slate-900/10 transition duration-150">
                            <td class="px-6 py-4 font-mono text-xs">{{ $pago->id }}</td>
                            <td class="px-6 py-4 font-mono text-xs">{{ $pago->fecha->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 font-medium text-slate-100">{{ $pago->trabajador->nombre }}</td>
                            <td class="px-6 py-4 text-xs font-medium">{{ $pago->trabajador->bocamina->nombre }}</td>
                            <td class="px-6 py-4 font-mono text-xs">Bs. {{ number_format($pago->subtotal, 2) }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-emerald-400">+Bs. {{ number_format($pago->bonos, 2) }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-red-400">-Bs. {{ number_format($pago->descuentos, 2) }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-red-400">-Bs. {{ number_format($pago->anticipos_descontados, 2) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-mono font-bold text-slate-100 text-sm">Bs. {{ number_format($pago->neto, 2) }}</span>
                                    <span class="text-[10px] text-slate-400 mt-1 font-mono">
                                        Pagado: Bs. {{ number_format($pago->monto_pagado, 2) }}
                                    </span>
                                    @if($pago->saldo_pendiente > 0)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold mt-1 self-start 
                                            @if($pago->saldo_liquidado) bg-slate-800 text-slate-400 border border-slate-700
                                            @else bg-amber-500/10 text-amber-400 border border-amber-500/20 @endif font-mono">
                                            @if($pago->saldo_liquidado)
                                                Adeudado: Bs. {{ number_format($pago->saldo_pendiente, 2) }} (Completado)
                                            @else
                                                Adeudado: Bs. {{ number_format($pago->saldo_pendiente, 2) }} (Pendiente)
                                            @endif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mt-1 self-start font-mono">
                                            Completado
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 no-print">
                                <a href="{{ route('pagos.show', $pago->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-amber-500 text-xs font-medium transition duration-150">
                                    <i class="fa-solid fa-print mr-1.5"></i> Imprimir Recibo
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-receipt text-4xl mb-3 block text-slate-600"></i>
                                No se encontraron registros de pagos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 2: Cash Fund Reloads -->
    <div x-show="activeTab === 'fondos'" class="glass-card rounded-xl overflow-hidden" x-cloak>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800">
                <thead>
                    <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900/40">
                        <th class="px-6 py-4 font-semibold">ID</th>
                        <th class="px-6 py-4 font-semibold">Fecha de Recarga</th>
                        <th class="px-6 py-4 font-semibold">Monto Recargado</th>
                        <th class="px-6 py-4 font-semibold">Observación / Origen</th>
                        <th class="px-6 py-4 font-semibold no-print">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-sm text-slate-300">
                    @forelse($fondos as $fondo)
                        <tr class="hover:bg-slate-900/10 transition duration-150">
                            <td class="px-6 py-4 font-mono text-xs">{{ $fondo->id }}</td>
                            <td class="px-6 py-4 font-mono text-xs">{{ $fondo->fecha->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-100">Bs. {{ number_format($fondo->monto, 2) }}</td>
                            <td class="px-6 py-4 text-slate-300">{{ $fondo->observacion ?: 'Fondeo de Caja' }}</td>
                            <td class="px-6 py-4 no-print">
                                <form action="{{ route('fondos-pagos.destroy', $fondo->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este registro de recarga de caja?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded bg-slate-800 hover:bg-red-950 text-slate-350 hover:text-red-400 border border-slate-700/60 hover:border-red-500/30 transition duration-150">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-building-columns text-4xl mb-3 block text-slate-600"></i>
                                No se registran recargas de fondos desde el banco.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Reload Cash Pool Modal -->
    <div x-show="openReloadModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div @click.away="openReloadModal = false" class="glass-card w-full max-w-md rounded-2xl overflow-hidden shadow-2xl border border-slate-800/80 relative" style="background: rgba(15, 23, 42, 0.95);">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-800/80 flex items-center justify-between bg-slate-900/60">
                <h3 class="text-lg font-bold text-slate-100">Recargar Fondos de Caja (Banco)</h3>
                <button @click="openReloadModal = false" class="text-slate-400 hover:text-slate-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Form -->
            <form action="{{ route('fondos-pagos.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label for="reload_fecha" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Fecha de Retiro</label>
                        <input id="reload_fecha" name="fecha" type="date" required x-model="fecha"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono">
                    </div>

                    <div>
                        <label for="reload_monto" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 font-bold text-amber-500">Monto Retirado (Bs.)</label>
                        <div class="relative mt-1">
                            <input id="reload_monto" name="monto" type="number" step="0.01" required x-model="monto" min="0.01"
                                   placeholder="Ej. 1000.00"
                                   class="block w-full px-3 py-2 bg-slate-900 border border-amber-500/40 rounded-lg text-slate-100 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm font-mono pr-8 font-bold">
                            <span class="absolute right-3 top-2.5 text-xs text-amber-500 font-bold font-mono">Bs.</span>
                        </div>
                    </div>

                    <div>
                        <label for="reload_observacion" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Observación / Origen</label>
                        <input id="reload_observacion" name="observacion" type="text" x-model="observacion"
                               placeholder="Ej. Retiro Banco Unión - Fondeo semanal"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none text-sm">
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 border-t border-slate-800/80 bg-slate-900/40 flex justify-end space-x-3">
                    <button type="button" @click="openReloadModal = false" class="px-4 py-2 text-sm font-medium rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-bold rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-slate-950">
                        Guardar Recarga
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
