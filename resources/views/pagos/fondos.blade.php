@extends('layouts.app')

@section('title', 'Caja Chica y Recargas')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100">Caja Chica / Recargas</h1>
            <p class="text-sm text-slate-400 mt-1">Registra los ingresos de efectivo retirados del banco y haz un seguimiento del saldo disponible para planillas.</p>
        </div>
        <div class="flex flex-wrap gap-3 self-start">
            <a href="{{ route('pagos.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-slate-800 border border-slate-700 hover:bg-slate-750 text-sm font-bold text-slate-200 transition duration-150">
                <i class="fa-solid fa-receipt mr-2 text-amber-500"></i> Historial de Pagos
            </a>
            <a href="{{ route('pagos.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-sm font-bold text-slate-950 transition duration-150 shadow-lg shadow-orange-500/10">
                <i class="fa-solid fa-receipt mr-2"></i> Procesar Nuevo Pago
            </a>
        </div>
    </div>

    <!-- Ledger Summary Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <!-- Total Loaded (Banco) -->
        <div class="glass-card rounded-xl p-6 relative overflow-hidden group border-cyan-500/10 hover:border-cyan-500/25 transition duration-300">
            <div class="absolute top-0 right-0 p-3 opacity-10 text-6xl text-cyan-500 group-hover:opacity-20 transition duration-300">
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

    <!-- Main Content Split Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- COL 1: Recharge Form -->
        <div class="glass-card rounded-xl p-6 h-fit space-y-4">
            <h3 class="text-md font-bold text-slate-200 border-b border-slate-800 pb-3 flex items-center">
                <i class="fa-solid fa-money-bill-trend-up mr-2 text-cyan-500"></i> Registrar Recarga de Caja
            </h3>
            
            <form action="{{ route('fondos-pagos.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="fecha" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Fecha de Retiro</label>
                    <input id="fecha" name="fecha" type="date" required value="{{ now()->toDateString() }}"
                           class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500 text-sm font-mono">
                </div>

                <div>
                    <label for="monto" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 font-bold text-cyan-400">Monto Recarga (Bs.)</label>
                    <div class="relative mt-1">
                        <input id="monto" name="monto" type="number" step="0.01" required min="0.01"
                               placeholder="Ej. 1000.00"
                               class="block w-full px-3 py-2.5 bg-slate-900 border border-cyan-500/30 rounded-lg text-slate-100 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm font-mono pr-8 font-bold">
                        <span class="absolute right-3 top-3 text-xs text-cyan-500 font-bold font-mono">Bs.</span>
                    </div>
                </div>

                <div>
                    <label for="observacion" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Observación / Origen</label>
                    <input id="observacion" name="observacion" type="text"
                           placeholder="Ej. Retiro Banco Unión - Fondeo semanal"
                           class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                </div>

                <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 rounded-lg bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-sm font-bold text-white transition duration-150 shadow-lg shadow-cyan-500/10">
                    <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Confirmar Recarga
                </button>
            </form>
        </div>

        <!-- COL 2: Reload History List -->
        <div class="glass-card rounded-xl p-6 lg:col-span-2 space-y-4">
            <h3 class="text-md font-bold text-slate-200 border-b border-slate-800 pb-3 flex items-center">
                <i class="fa-solid fa-list-check mr-2 text-cyan-500"></i> Historial de Movimientos de Recarga
            </h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900/40">
                            <th class="px-4 py-3 font-semibold">ID</th>
                            <th class="px-4 py-3 font-semibold">Fecha</th>
                            <th class="px-4 py-3 font-semibold">Monto</th>
                            <th class="px-4 py-3 font-semibold">Origen / Glosa</th>
                            <th class="px-4 py-3 font-semibold no-print">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40 text-sm text-slate-300">
                        @forelse($fondos as $fondo)
                            <tr class="hover:bg-slate-900/10 transition duration-150">
                                <td class="px-4 py-3 font-mono text-xs">{{ $fondo->id }}</td>
                                <td class="px-4 py-3 font-mono text-xs">{{ $fondo->fecha->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 font-mono font-bold text-cyan-400">Bs. {{ number_format($fondo->monto, 2) }}</td>
                                <td class="px-4 py-3 text-slate-350">{{ $fondo->observacion ?: 'Fondeo de Caja' }}</td>
                                <td class="px-4 py-3 no-print">
                                    <form action="{{ route('fondos-pagos.destroy', $fondo->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este registro de recarga?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 rounded bg-slate-800 hover:bg-red-950 text-slate-300 hover:text-red-400 border border-slate-700/60 hover:border-red-500/30 transition duration-150" title="Eliminar">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                    <i class="fa-solid fa-building-columns text-3xl mb-2 block text-slate-600"></i>
                                    No se registran recargas de fondos desde el banco.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
