@extends('layouts.app')

@section('title', 'Caja Chica y Recargas')

@section('content')
@php $positivo = $saldo_caja >= 0; @endphp

<div class="space-y-6">

    {{-- ══ HEADER ══ --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center shadow-lg shadow-cyan-500/30 flex-shrink-0">
                <i class="fa-solid fa-vault text-white text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-100">
                    Caja Chica <span class="text-cyan-400 font-extrabold">/</span> Recargas
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Registra ingresos del banco · Monitorea el saldo disponible para planillas</p>
            </div>
        </div>
    </div>

    {{-- ══ SUMMARY CARDS ══ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

        {{-- Total Recargado --}}
        <div class="glass-card rounded-2xl p-5 border-l-4 border-l-cyan-500/80 shadow-md hover:shadow-cyan-500/5 hover:-translate-y-0.5 transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-450">Total Recargado (Banco)</span>
                <div class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-500 flex items-center justify-center">
                    <i class="fa-solid fa-building-columns text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-100">Bs. {{ number_format($total_recargado, 2) }}</p>
            <p class="text-[10px] text-slate-500 mt-2 font-mono">Fondos ingresados a caja chica</p>
        </div>

        {{-- Total Gastado --}}
        <div class="glass-card rounded-2xl p-5 border-l-4 border-l-rose-500/80 shadow-md hover:shadow-rose-500/5 hover:-translate-y-0.5 transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-450">Gastado (Pagos y Anticipos)</span>
                <div class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-500 flex items-center justify-center">
                    <i class="fa-solid fa-hand-holding-dollar text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-100">Bs. {{ number_format($total_gastado, 2) }}</p>
            <p class="text-[10px] text-slate-500 mt-2 font-mono">Egresado por planillas y adelantos</p>
        </div>

        {{-- Saldo --}}
        @php
            $accentColor = $positivo ? 'emerald' : 'amber';
            $accentBorder = $positivo ? 'border-l-emerald-500/80' : 'border-l-amber-500/80';
            $accentBg = $positivo ? 'bg-emerald-500/10 text-emerald-500' : 'bg-amber-500/10 text-amber-500';
            $accentText = $positivo ? 'text-emerald-500' : 'text-amber-500';
        @endphp
        <div class="glass-card rounded-2xl p-5 border-l-4 {{ $accentBorder }} shadow-md hover:shadow-{{ $accentColor }}-500/5 hover:-translate-y-0.5 transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-450">Saldo Sobrante en Caja</span>
                <div class="w-8 h-8 rounded-lg {{ $accentBg }} flex items-center justify-center">
                    <i class="fa-solid fa-vault text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-100">Bs. {{ number_format(abs($saldo_caja), 2) }}</p>
            <p class="text-[10px] text-slate-500 mt-2 font-mono {{ $accentText }}">
                {{ $positivo ? '● Efectivo disponible' : '▲ Caja en déficit' }}
            </p>
        </div>
    </div>

    {{-- ══ PROGRESS BAR ══ --}}
    @php
        $pct = $total_recargado > 0 ? min(100, round(($total_gastado / $total_recargado) * 100)) : 0;
        $barColor = $pct >= 90 ? 'from-rose-500 to-red-600' : ($pct >= 70 ? 'from-amber-500 to-orange-500' : 'from-emerald-500 to-teal-500');
        $txtColor = $pct >= 90 ? 'text-rose-400' : ($pct >= 70 ? 'text-amber-400' : 'text-emerald-400');
    @endphp
    <div class="glass-card rounded-2xl px-6 py-4">
        <div class="flex items-center justify-between mb-2.5">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-gauge-high text-cyan-400 text-xs"></i>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Uso del Fondo de Caja</span>
            </div>
            <span class="text-sm font-black {{ $txtColor }}">{{ $pct }}% <span class="text-slate-500 font-normal text-xs">utilizado</span></span>
        </div>
        <div class="h-2 w-full bg-slate-800/80 rounded-full overflow-hidden">
            <div class="h-full bg-gradient-to-r {{ $barColor }} rounded-full transition-all duration-700" style="width: {{ $pct }}%"></div>
        </div>
        <div class="flex justify-between mt-2 text-[10px] font-mono text-slate-600">
            <span>Bs. 0.00</span>
            <span>Bs. {{ number_format($total_recargado, 2) }}</span>
        </div>
    </div>

    {{-- ══ FORM + TABLE ══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- ── Formulario ── --}}
        <div class="glass-card rounded-2xl overflow-hidden">
            {{-- Form Header --}}
            <div class="bg-gradient-to-r from-cyan-500/10 to-blue-500/10 border-b border-slate-800/60 px-5 py-4 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center shadow-md shadow-cyan-500/25 flex-shrink-0">
                    <i class="fa-solid fa-money-bill-trend-up text-white text-xs"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-200">Registrar Recarga de Caja</h3>
                    <p class="text-[10px] text-slate-500">Nuevo ingreso del banco</p>
                </div>
            </div>

            <form action="{{ route('fondos-pagos.store') }}" method="POST" class="p-5 space-y-4">
                @csrf

                {{-- Fecha --}}
                <div>
                    <label for="fecha" class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                        <i class="fa-regular fa-calendar mr-1"></i> Fecha de Retiro
                    </label>
                    <input id="fecha" name="fecha" type="date" required value="{{ now()->toDateString() }}"
                           class="block w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500 text-sm font-mono transition-colors">
                </div>

                {{-- Monto --}}
                <div>
                    <label for="monto" class="block text-[10px] font-bold uppercase tracking-widest text-cyan-400 mb-1.5">
                        <i class="fa-solid fa-coins mr-1"></i> Monto Recarga (Bs.)
                    </label>
                    <div class="relative">
                        <input id="monto" name="monto" type="number" step="0.01" required min="0.01"
                               placeholder="0.00"
                               class="block w-full pl-3 pr-12 py-3 bg-slate-800 border border-cyan-500/40 rounded-xl text-slate-100 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 text-sm font-mono font-bold transition-all">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-black text-cyan-400 pointer-events-none font-mono">Bs.</span>
                    </div>
                </div>

                {{-- Observación --}}
                <div>
                    <label for="observacion" class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                        <i class="fa-solid fa-note-sticky mr-1"></i> Observación / Origen
                    </label>
                    <input id="observacion" name="observacion" type="text"
                           placeholder="Ej. Retiro Banco Unión - Fondeo semanal"
                           class="block w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500 text-sm transition-colors">
                </div>

                {{-- Botón --}}
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-black text-sm shadow-lg shadow-cyan-500/25 hover:shadow-cyan-500/40 transition-all duration-200 hover:scale-[1.02] active:scale-95">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    Confirmar Recarga
                </button>

                {{-- Tip --}}
                <div class="flex gap-2 bg-cyan-500/5 border border-cyan-500/15 rounded-xl px-3 py-2.5 text-[10px] text-slate-500 leading-relaxed">
                    <i class="fa-solid fa-circle-info text-cyan-500 mt-0.5 flex-shrink-0"></i>
                    <span>Cada recarga sube el saldo disponible. Registra cada retiro del banco por separado.</span>
                </div>
            </form>
        </div>

        {{-- ── Tabla Historial ── --}}
        <div class="glass-card rounded-2xl overflow-hidden lg:col-span-2">
            {{-- Table Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800/60 bg-gradient-to-r from-slate-800/20 to-transparent">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-clock-rotate-left text-cyan-400 text-sm"></i>
                    <div>
                        <h3 class="text-sm font-bold text-slate-200">Historial de Movimientos</h3>
                        <p class="text-[10px] text-slate-500">Recargas desde el banco</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-slate-400 bg-slate-800/60 border border-slate-700/40 px-3 py-1 rounded-full">
                    {{ count($fondos) }} {{ count($fondos) === 1 ? 'registro' : 'registros' }}
                </span>
            </div>

            @if(count($fondos) === 0)
                <div class="flex flex-col items-center justify-center py-14 text-slate-500 gap-3">
                    <div class="w-14 h-14 rounded-2xl bg-slate-800/50 flex items-center justify-center">
                        <i class="fa-solid fa-building-columns text-2xl text-slate-600"></i>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-semibold text-slate-400">Sin recargas registradas</p>
                        <p class="text-xs text-slate-600 mt-0.5">Usa el formulario para registrar tu primer fondeo</p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-800/40">
                                <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-slate-500">#</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-slate-500">Fecha</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-slate-500">Monto Ingresado</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-slate-500">Origen / Glosa</th>
                                <th class="px-5 py-3 text-center text-[10px] font-bold uppercase tracking-widest text-slate-500 no-print">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/20">
                            @foreach($fondos as $i => $fondo)
                            <tr class="hover:bg-slate-800/10 transition-colors duration-150 group">
                                {{-- # --}}
                                <td class="px-5 py-4 text-xs font-mono text-slate-500 font-bold">
                                    {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                                </td>
                                {{-- Fecha --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-regular fa-calendar text-slate-500 text-[11px]"></i>
                                        <span class="font-mono text-xs text-slate-350">{{ $fondo->fecha->format('d/m/Y') }}</span>
                                    </div>
                                </td>
                                {{-- Monto --}}
                                <td class="px-5 py-4">
                                    <span class="font-bold text-slate-100 font-mono text-sm">
                                        <span class="text-cyan-500 font-normal mr-1">↑</span>Bs. {{ number_format($fondo->monto, 2) }}
                                    </span>
                                </td>
                                {{-- Glosa --}}
                                <td class="px-5 py-4 text-slate-400 text-xs">
                                    {{ $fondo->observacion ?: 'Fondeo de Caja' }}
                                </td>
                                {{-- Acción --}}
                                <td class="px-5 py-4 text-center no-print">
                                    <div class="relative group/del inline-block">
                                        <form action="{{ route('fondos-pagos.destroy', $fondo->id) }}" method="POST"
                                              class="inline" onsubmit="return confirm('¿Estás seguro?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-8 h-8 rounded-xl flex items-center justify-center bg-gradient-to-br from-rose-500 to-red-600 hover:from-rose-400 hover:to-red-500 text-white shadow-md shadow-rose-500/25 hover:shadow-rose-500/50 hover:scale-110 active:scale-95 transition-all duration-200">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                        <span class="absolute -bottom-7 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-lg bg-slate-900 text-[10px] font-bold text-rose-400 whitespace-nowrap opacity-0 group-hover/del:opacity-100 transition-all duration-150 pointer-events-none border border-rose-500/30 shadow-xl z-50">Eliminar</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t border-b border-slate-700/30">
                                <td colspan="2" class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-500">
                                    Total Acumulado
                                </td>
                                <td class="px-5 py-4 font-black text-slate-100 font-mono text-sm" colspan="3">
                                    Bs. {{ number_format($total_recargado, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
