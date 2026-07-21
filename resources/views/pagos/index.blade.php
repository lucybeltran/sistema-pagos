@extends('layouts.app')

@section('title', 'Historial de Pagos')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100">Historial de Pagos</h1>
            <p class="text-sm text-slate-400 mt-1">Consulta los pagos históricos realizados y descarga sus recibos de pago.</p>
        </div>
        <a href="{{ route('pagos.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-sm font-bold text-slate-950 transition duration-150 shadow-lg shadow-orange-500/10">
            <i class="fa-solid fa-receipt mr-2"></i> Procesar Nuevo Pago
        </a>
    </div>

    <!-- Table Section -->
    <div class="glass-card rounded-xl overflow-hidden">
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
                                            @else bg-amber-500/10 text-amber-400 border border-amber-500/20 @endif">
                                            @if($pago->saldo_liquidado)
                                                Adeudado: Bs. {{ number_format($pago->saldo_pendiente, 2) }} (Completado)
                                            @else
                                                Adeudado: Bs. {{ number_format($pago->saldo_pendiente, 2) }} (Pendiente)
                                            @endif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mt-1 self-start">
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

</div>
@endsection
