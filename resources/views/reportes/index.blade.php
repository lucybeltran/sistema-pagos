@extends('layouts.app')

@section('title', 'Reportes del Sistema')

@section('content')
<div x-data="{ 
    tab: '{{ $tab === 'fecha' ? 'trabajador' : $tab }}',
    filtroFechaTrab: '{{ request('tab') === 'trabajador' ? $filtroFecha : 'personalizado' }}',
    filtroFechaBoc: '{{ request('tab') === 'bocamina' ? $filtroFecha : 'personalizado' }}',
    filtroFechaAnt: '{{ request('tab') === 'anticipos' ? $filtroFecha : 'personalizado' }}'
}" class="space-y-6">

    <!-- Header (no-print) -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0 no-print">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100">Reportes y Consultas</h1>
            <p class="text-sm text-slate-400 mt-1">Genera y consulta reportes detallados por trabajadores y bocaminas con filtros de fechas, y balances semanales.</p>
        </div>
        <button onclick="window.print()" class="btn-vibrant-warm inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-bold shadow-lg">
            <i class="fa-solid fa-print mr-2"></i> Imprimir Reporte Activo
        </button>
    </div>

    <!-- Print Only Header -->
    <div class="hidden print-only mb-6 text-slate-900">
        <div class="text-center">
            <h1 class="text-2xl font-bold uppercase tracking-wider">Reporte SCPM - Control Pagos Minería</h1>
            <p class="text-sm font-mono mt-1">Generado el {{ now()->format('d/m/Y H:i') }}</p>
        </div>
        <hr class="border-slate-300 my-4">
    </div>

    <!-- Tab Bar (no-print) -->
    <div class="no-print border-b border-slate-800/80">
        <nav class="flex space-x-8 -mb-px overflow-x-auto pb-1" aria-label="Tabs">
            <button @click="tab = 'trabajador'; history.replaceState(null, '', '?tab=trabajador')" 
                    :class="tab === 'trabajador' ? 'border-amber-500 text-amber-500' : 'border-transparent text-slate-400 hover:text-slate-200'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center transition duration-155">
                <i class="fa-solid fa-user mr-2 text-base"></i> 1. Por Trabajador
            </button>
            
            <button @click="tab = 'bocamina'; history.replaceState(null, '', '?tab=bocamina')" 
                    :class="tab === 'bocamina' ? 'border-amber-500 text-amber-500' : 'border-transparent text-slate-400 hover:text-slate-200'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center transition duration-155">
                <i class="fa-solid fa-mountain mr-2 text-base"></i> 2. Por Bocamina
            </button>

            <button @click="tab = 'general'; history.replaceState(null, '', '?tab=general')" 
                    :class="tab === 'general' ? 'border-amber-500 text-amber-500' : 'border-transparent text-slate-400 hover:text-slate-200'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center transition duration-155">
                <i class="fa-solid fa-list-check mr-2 text-base"></i> 3. General y Semanal
            </button>

            <button @click="tab = 'anticipos'; history.replaceState(null, '', '?tab=anticipos')" 
                    :class="tab === 'anticipos' ? 'border-amber-500 text-amber-500' : 'border-transparent text-slate-400 hover:text-slate-200'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center transition duration-155">
                <i class="fa-solid fa-hand-holding-dollar mr-2 text-base"></i> 4. Anticipos
            </button>
        </nav>
    </div>

    <!-- Reports Container -->
    <div class="space-y-6">

        <!-- 1. Reporte por Trabajador -->
        <div x-show="tab === 'trabajador'" class="space-y-6">
            <!-- Selector (no-print) -->
            <div class="glass-card rounded-xl p-6 no-print">
                <form action="{{ route('reportes.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-4 items-end">
                    <input type="hidden" name="tab" value="trabajador">
                    
                    <div class="sm:col-span-2">
                        <label for="trabajador_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Seleccionar Trabajador</label>
                        <select name="trabajador_id" id="trabajador_id" required
                                class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                            <option value="">-- Seleccionar Trabajador --</option>
                            @foreach($trabajadores as $t)
                                <option value="{{ $t->id }}" {{ request('trabajador_id') == $t->id ? 'selected' : '' }}>{{ $t->nombre }} (CI: {{ $t->ci }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="filtro_fecha_trab" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Filtro Rápido de Fecha</label>
                        <select name="filtro_fecha" id="filtro_fecha_trab" x-model="filtroFechaTrab"
                                class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                            <option value="personalizado">Personalizado (Elegir Fechas)</option>
                            <option value="esta_semana">Esta Semana</option>
                            <option value="semana_pasada">Semana Pasada</option>
                            <option value="este_mes">Este Mes</option>
                            <option value="mes_pasado">Mes Pasado</option>
                        </select>
                    </div>
                    
                    <div x-show="filtroFechaTrab === 'personalizado'" class="sm:col-span-2 transition-all duration-300">
                        <label for="fecha_desde" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Desde (Opcional)</label>
                        <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono">
                    </div>
                    
                    <div x-show="filtroFechaTrab === 'personalizado'" class="sm:col-span-2 transition-all duration-300">
                        <label for="fecha_hasta" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Hasta (Opcional)</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ request('fecha_hasta') }}"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono">
                    </div>
                    
                    <div class="sm:col-span-4 flex justify-end">
                        <button type="submit" class="btn-vibrant-amber w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 text-slate-950 font-bold rounded-lg shadow-lg">
                            <i class="fa-solid fa-magnifying-glass mr-2"></i> Generar Reporte
                        </button>
                    </div>
                </form>
            </div>

            <!-- Report Details -->
            @if($reporteTrabajador)
                <div class="glass-card rounded-xl p-6 md:p-8 space-y-8 print:border print:border-slate-300 print:text-slate-900 print:bg-white">
                    <!-- Worker Details Header -->
                    <div class="border-b border-slate-800 pb-4 flex justify-between items-start print:border-slate-200">
                        <div>
                            <h2 class="text-xl font-bold text-slate-100 print:text-slate-900">{{ $reporteTrabajador['trabajador']->nombre }}</h2>
                            <p class="text-xs text-slate-400 font-mono mt-0.5">
                                CI: {{ $reporteTrabajador['trabajador']->ci }} | Tel: {{ $reporteTrabajador['trabajador']->telefono ?: '-' }}
                                @if($reporteTrabajador['desde'] || $reporteTrabajador['hasta'])
                                    <span class="text-amber-500 font-bold block mt-1 font-sans">
                                        Rango: 
                                        @if($reporteTrabajador['desde'] && $reporteTrabajador['hasta'])
                                            {{ Carbon\Carbon::parse($reporteTrabajador['desde'])->format('d/m/Y') }} al {{ Carbon\Carbon::parse($reporteTrabajador['hasta'])->format('d/m/Y') }}
                                        @elseif($reporteTrabajador['desde'])
                                            Desde {{ Carbon\Carbon::parse($reporteTrabajador['desde'])->format('d/m/Y') }}
                                        @else
                                            Hasta {{ Carbon\Carbon::parse($reporteTrabajador['hasta'])->format('d/m/Y') }}
                                        @endif
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-slate-500 uppercase block font-mono">Bocamina Asignada</span>
                            <span class="text-sm font-bold text-amber-500 font-sans">{{ $reporteTrabajador['trabajador']->bocamina->nombre }}</span>
                        </div>
                    </div>

                    <!-- Payout/Pending Stats Grid -->
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                        <div class="p-4 bg-slate-900/40 rounded-lg border border-slate-800/40 print:bg-slate-55 print:border-slate-200">
                            <span class="text-[10px] text-slate-500 uppercase font-mono block">Trabajo Total</span>
                            <span class="text-base font-bold text-slate-200 font-mono print:text-slate-900">Bs. {{ number_format($reporteTrabajador['subtotal_trabajos'], 2) }}</span>
                        </div>
                        <div class="p-4 bg-slate-900/40 rounded-lg border border-slate-800/40 print:bg-slate-55 print:border-slate-200">
                            <span class="text-[10px] text-slate-500 uppercase font-mono block">Pagado Neto</span>
                            <span class="text-base font-bold text-emerald-400 font-mono">Bs. {{ number_format($reporteTrabajador['pagos_recibidos'], 2) }}</span>
                        </div>
                        <div class="p-4 bg-slate-900/40 rounded-lg border border-slate-800/40 print:bg-slate-55 print:border-slate-200">
                            <span class="text-[10px] text-slate-500 uppercase font-mono block">Trabajo Pendiente</span>
                            <span class="text-base font-bold text-amber-500 font-mono">Bs. {{ number_format($reporteTrabajador['trabajos_pendientes'], 2) }}</span>
                        </div>
                        <div class="p-4 bg-slate-900/40 rounded-lg border border-slate-800/40 print:bg-slate-55 print:border-slate-200">
                            <span class="text-[10px] text-slate-500 uppercase font-mono block">Anticipos Entregados (Rango)</span>
                            <span class="text-base font-bold text-red-400 font-mono">Bs. {{ number_format($reporteTrabajador['anticipos_pendientes'], 2) }}</span>
                        </div>
                    </div>

                    <!-- Detailed Tables (Works, Advances, Payments) -->
                    <div class="space-y-6">
                        <!-- Works Table -->
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-3 border-b border-slate-800/50 pb-1.5 print:text-slate-600 print:border-slate-200">1. Historial de Trabajos</h3>
                            <div class="overflow-x-auto font-sans">
                                <table class="w-full text-left text-xs">
                                    <thead>
                                        <tr class="font-bold text-slate-500 border-b border-slate-800 print:border-slate-300">
                                            <th class="py-2">Fecha</th>
                                            <th class="py-2">Detalle / Tipo</th>
                                            <th class="py-2">Cantidad × Precio</th>
                                            <th class="py-2">Subtotal</th>
                                            <th class="py-2 text-right">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/30 print:divide-slate-100 text-slate-300 print:text-slate-800 font-mono">
                                        @forelse($reporteTrabajador['trabajos'] as $t)
                                            <tr>
                                                <td class="py-2.5">{{ $t->fecha->format('d/m/Y') }}</td>
                                                <td class="py-2.5 capitalize font-sans text-slate-200 print:text-slate-900">
                                                    {{ $t->tipo }}
                                                    @if($t->contrato)
                                                        <span class="text-[10px] text-slate-500 font-mono block">Contrato: {{ $t->contrato->codigo }}</span>
                                                    @endif
                                                </td>
                                                <td class="py-2.5">{{ number_format($t->cantidad, 2) }} × Bs. {{ number_format($t->precio_unitario, 2) }}</td>
                                                <td class="py-2.5 font-bold text-amber-500">Bs. {{ number_format($t->subtotal, 2) }}</td>
                                                <td class="py-2.5 text-right font-sans">
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] {{ $t->pagado ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }}">
                                                        {{ $t->pagado ? 'Pagado' : 'Pendiente' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="py-3 text-center text-slate-500 font-sans">No hay trabajos registrados.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Advances Table -->
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-3 border-b border-slate-800/50 pb-1.5 print:text-slate-600 print:border-slate-200">2. Historial de Anticipos</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead>
                                        <tr class="font-bold text-slate-500 border-b border-slate-800 print:border-slate-300">
                                            <th class="py-2">Fecha</th>
                                            <th class="py-2">Monto Adelantado</th>
                                            <th class="py-2">Saldo Pendiente</th>
                                            <th class="py-2 text-right">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/30 print:divide-slate-100 text-slate-300 print:text-slate-800 font-mono">
                                        @forelse($reporteTrabajador['anticipos'] as $a)
                                            <tr>
                                                <td class="py-2.5">{{ $a->fecha->format('d/m/Y') }}</td>
                                                <td>Bs. {{ number_format($a->monto, 2) }}</td>
                                                <td class="font-bold text-amber-500 font-mono">Bs. {{ number_format($a->saldo, 2) }}</td>
                                                <td class="text-right font-sans">
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] {{ $a->saldo == 0 ? 'bg-slate-850 text-slate-400' : 'bg-red-500/10 text-red-400 border border-red-500/20' }}">
                                                        {{ $a->saldo == 0 ? 'Descontado' : 'Pendiente' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="py-3 text-center text-slate-500 font-sans">No hay anticipos registrados.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Payments Table -->
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-3 border-b border-slate-800/50 pb-1.5 print:text-slate-600 print:border-slate-200">3. Pagos Netos Recibidos</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead>
                                        <tr class="font-bold text-slate-500 border-b border-slate-800 print:border-slate-300">
                                            <th class="py-2">Fecha</th>
                                            <th class="py-2">Subtotal Trabajos</th>
                                            <th class="py-2">Bonos (+)</th>
                                            <th class="py-2">Descuentos (-)</th>
                                            <th class="py-2">Anticipos Cobrados (-)</th>
                                            <th class="py-2 text-right">Pago Neto</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/30 print:divide-slate-100 text-slate-300 print:text-slate-800 font-mono">
                                        @forelse($reporteTrabajador['pagos'] as $p)
                                            <tr>
                                                <td class="py-2.5">{{ $p->fecha->format('d/m/Y') }}</td>
                                                <td>Bs. {{ number_format($p->subtotal, 2) }}</td>
                                                <td class="text-emerald-450">+Bs. {{ number_format($p->bonos, 2) }}</td>
                                                <td class="text-red-400">-Bs. {{ number_format($p->descuentos, 2) }}</td>
                                                <td class="text-red-400">-Bs. {{ number_format($p->anticipos_descontados, 2) }}</td>
                                                <td class="text-right text-emerald-400 font-bold text-sm">Bs. {{ number_format($p->neto, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-3 text-center text-slate-500 font-sans">No hay pagos registrados.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            @else
                <div class="glass-card rounded-xl p-12 text-center text-slate-500 no-print">
                    <i class="fa-solid fa-user-check text-4xl text-slate-700 block mb-3"></i>
                    Selecciona un trabajador y opcionalmente un rango de fechas para generar su estado de cuentas.
                </div>
            @endif
        </div>

        <!-- 2. Reporte por Bocamina -->
        <div x-show="tab === 'bocamina'" class="space-y-6">
            <!-- Selector (no-print) -->
            <div class="glass-card rounded-xl p-6 no-print">
                <form action="{{ route('reportes.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-4 items-end">
                    <input type="hidden" name="tab" value="bocamina">
                    
                    <div class="sm:col-span-2">
                        <label for="bocamina_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Seleccionar Bocamina</label>
                        <select name="bocamina_id" id="bocamina_id"
                                class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                            <option value="">-- Ver Todas las Bocaminas (Resumen General) --</option>
                            @foreach($bocaminas as $b)
                                <option value="{{ $b->id }}" {{ request('bocamina_id') == $b->id ? 'selected' : '' }}>{{ $b->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="filtro_fecha_boc" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Filtro Rápido de Fecha</label>
                        <select name="filtro_fecha" id="filtro_fecha_boc" x-model="filtroFechaBoc"
                                class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                            <option value="personalizado">Personalizado (Elegir Fechas)</option>
                            <option value="esta_semana">Esta Semana</option>
                            <option value="semana_pasada">Semana Pasada</option>
                            <option value="este_mes">Este Mes</option>
                            <option value="mes_pasado">Mes Pasado</option>
                        </select>
                    </div>
                    
                    <div x-show="filtroFechaBoc === 'personalizado'" class="sm:col-span-2 transition-all duration-300">
                        <label for="fecha_desde_boc" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Desde (Opcional)</label>
                        <input type="date" name="fecha_desde" id="fecha_desde_boc" value="{{ request('fecha_desde') }}"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono">
                    </div>
                    
                    <div x-show="filtroFechaBoc === 'personalizado'" class="sm:col-span-2 transition-all duration-300">
                        <label for="fecha_hasta_boc" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Hasta (Opcional)</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta_boc" value="{{ request('fecha_hasta') }}"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono">
                    </div>
                    
                    <div class="sm:col-span-4 flex justify-end">
                        <button type="submit" class="btn-vibrant-amber w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 text-slate-950 font-bold rounded-lg shadow-lg">
                            <i class="fa-solid fa-magnifying-glass mr-2"></i> Generar Reporte
                        </button>
                    </div>
                </form>
            </div>

            @if($reporteBocaminaDetalle)
                <!-- Specific Bocamina Details -->
                <div class="glass-card rounded-xl p-6 md:p-8 space-y-8 print:border print:border-slate-300 print:text-slate-900 print:bg-white font-sans">
                    <div class="border-b border-slate-800 pb-4 flex justify-between items-start print:border-slate-200">
                        <div>
                            <h2 class="text-xl font-bold text-slate-100 print:text-slate-900">Reporte Detallado: {{ $reporteBocaminaDetalle['bocamina']->nombre }}</h2>
                            <p class="text-xs text-slate-400 font-mono mt-0.5">
                                Ubicación: {{ $reporteBocaminaDetalle['bocamina']->descripcion ?: 'No especificada' }}
                                @if($reporteBocaminaDetalle['desde'] || $reporteBocaminaDetalle['hasta'])
                                    <span class="text-amber-500 font-bold block mt-1 font-sans">
                                        Rango: 
                                        @if($reporteBocaminaDetalle['desde'] && $reporteBocaminaDetalle['hasta'])
                                            {{ Carbon\Carbon::parse($reporteBocaminaDetalle['desde'])->format('d/m/Y') }} al {{ Carbon\Carbon::parse($reporteBocaminaDetalle['hasta'])->format('d/m/Y') }}
                                        @elseif($reporteBocaminaDetalle['desde'])
                                            Desde {{ Carbon\Carbon::parse($reporteBocaminaDetalle['desde'])->format('d/m/Y') }}
                                        @else
                                            Hasta {{ Carbon\Carbon::parse($reporteBocaminaDetalle['hasta'])->format('d/m/Y') }}
                                        @endif
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div class="text-right font-sans">
                            <span class="text-xs text-slate-500 uppercase block font-mono">Personal Asignado</span>
                            <span class="text-lg font-bold text-amber-500">{{ count($reporteBocaminaDetalle['trabajadores_data']) }} Trabajadores</span>
                        </div>
                    </div>

                    <!-- Bocamina Stats Grid -->
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6 font-mono">
                        <div class="p-4 bg-slate-900/40 rounded-lg border border-slate-800/40 print:bg-slate-55 print:border-slate-200">
                            <span class="text-[10px] text-slate-500 uppercase font-mono block font-sans">Producción {{ ($reporteBocaminaDetalle['desde'] || $reporteBocaminaDetalle['hasta']) ? 'Rango' : 'Total' }}</span>
                            <span class="text-sm font-bold text-slate-200 print:text-slate-900">Bs. {{ number_format($reporteBocaminaDetalle['total_produccion'], 2) }}</span>
                        </div>
                        <div class="p-4 bg-slate-900/40 rounded-lg border border-slate-800/40 print:bg-slate-55 print:border-slate-200">
                            <span class="text-[10px] text-slate-500 uppercase font-mono block font-sans">Pagos Desembolsados</span>
                            <span class="text-sm font-bold text-emerald-400">Bs. {{ number_format($reporteBocaminaDetalle['total_pagado'], 2) }}</span>
                        </div>
                        <div class="p-4 bg-slate-900/40 rounded-lg border border-slate-800/40 print:bg-slate-55 print:border-slate-200">
                            <span class="text-[10px] text-slate-500 uppercase font-mono block font-sans">Adelantos {{ ($reporteBocaminaDetalle['desde'] || $reporteBocaminaDetalle['hasta']) ? 'Rango' : 'Totales' }}</span>
                            <span class="text-sm font-bold text-amber-500">Bs. {{ number_format($reporteBocaminaDetalle['total_anticipos'], 2) }}</span>
                        </div>
                        <div class="p-4 bg-slate-900/40 rounded-lg border border-slate-800/40 print:bg-slate-55 print:border-slate-200">
                            <span class="text-[10px] text-slate-500 uppercase font-mono block font-sans">Saldo Adelantos</span>
                            <span class="text-sm font-bold text-red-400">Bs. {{ number_format($reporteBocaminaDetalle['saldo_anticipos'], 2) }}</span>
                        </div>
                        <div class="p-4 bg-slate-900/40 rounded-lg border border-slate-800/40 print:bg-slate-55 print:border-slate-200">
                            <span class="text-[10px] text-slate-500 uppercase font-mono block font-sans">Metros Avanzados</span>
                            <span class="text-sm font-bold text-amber-500 font-mono">{{ number_format($reporteBocaminaDetalle['metros'], 2) }} m</span>
                        </div>
                        <div class="p-4 bg-slate-900/40 rounded-lg border border-slate-800/40 print:bg-slate-55 print:border-slate-200">
                            <span class="text-[10px] text-slate-500 uppercase font-mono block font-sans">Volquetas Cargadas</span>
                            <span class="text-sm font-bold text-amber-500 font-mono">{{ number_format($reporteBocaminaDetalle['volquetas'], 2) }} vq.</span>
                        </div>
                    </div>

                    <!-- Workers List Table -->
                    <div class="space-y-3 font-sans">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800 pb-1.5 print:text-slate-600 print:border-slate-200">Personal de la Bocamina y Totales {{ ($reporteBocaminaDetalle['desde'] || $reporteBocaminaDetalle['hasta']) ? 'del Rango' : '' }}</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="font-bold text-slate-500 border-b border-slate-800 print:border-slate-300">
                                        <th class="py-2">C.I.</th>
                                        <th class="py-2">Nombre</th>
                                        <th class="py-2">Estado</th>
                                        <th class="py-2">Producción {{ ($reporteBocaminaDetalle['desde'] || $reporteBocaminaDetalle['hasta']) ? 'Rango' : 'Total' }}</th>
                                        <th class="py-2">Pagos {{ ($reporteBocaminaDetalle['desde'] || $reporteBocaminaDetalle['hasta']) ? 'Rango' : 'Totales' }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/30 print:divide-slate-100 text-slate-300 print:text-slate-800 font-mono">
                                    @foreach($reporteBocaminaDetalle['trabajadores_data'] as $item)
                                        <tr>
                                            <td class="py-2.5">{{ $item['worker']->ci }}</td>
                                            <td class="py-2.5 font-sans font-medium text-slate-200 print:text-slate-900">{{ $item['worker']->nombre }}</td>
                                            <td class="py-2.5 font-sans">
                                                <span class="px-1.5 py-0.5 rounded text-[10px] {{ $item['worker']->estado === 'activo' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-850 text-slate-400' }}">
                                                    {{ ucfirst($item['worker']->estado) }}
                                                </span>
                                            </td>
                                            <td class="py-2.5 font-bold text-amber-500">Bs. {{ number_format($item['total_produccion'], 2) }}</td>
                                            <td class="py-2.5 font-bold text-emerald-400">Bs. {{ number_format($item['total_pagado'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Recent Activity grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Recent works -->
                        <div class="space-y-3">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800 pb-1.5 print:text-slate-600 print:border-slate-200">Trabajos Registrados {{ ($reporteBocaminaDetalle['desde'] || $reporteBocaminaDetalle['hasta']) ? '(Rango)' : '' }}</h3>
                            <div class="overflow-x-auto max-h-60 overflow-y-auto">
                                <table class="w-full text-left text-xs">
                                    <thead>
                                        <tr class="font-bold text-slate-500 border-b border-slate-800 print:border-slate-300">
                                            <th class="py-2">Fecha</th>
                                            <th class="py-2">Trabajador</th>
                                            <th class="py-2">Detalle</th>
                                            <th class="py-2 text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/30 print:divide-slate-100 text-slate-300 print:text-slate-800 font-mono">
                                        @forelse($reporteBocaminaDetalle['recientes_trabajos'] as $t)
                                            <tr>
                                                <td class="py-2">{{ $t->fecha->format('d/m/Y') }}</td>
                                                <td class="font-sans">{{ $t->trabajador->nombre }}</td>
                                                <td class="capitalize font-sans">{{ $t->tipo }} ({{ $t->cantidad }})</td>
                                                <td class="text-right font-bold text-amber-500">Bs. {{ number_format($t->subtotal, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="py-3 text-center text-slate-500 font-sans">No hay trabajos registrados.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Recent payments -->
                        <div class="space-y-3">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800 pb-1.5 print:text-slate-600 print:border-slate-200">Pagos Desembolsados {{ ($reporteBocaminaDetalle['desde'] || $reporteBocaminaDetalle['hasta']) ? '(Rango)' : '' }}</h3>
                            <div class="overflow-x-auto max-h-60 overflow-y-auto">
                                <table class="w-full text-left text-xs">
                                    <thead>
                                        <tr class="font-bold text-slate-500 border-b border-slate-800 print:border-slate-300">
                                            <th class="py-2">Fecha</th>
                                            <th class="py-2">Trabajador</th>
                                            <th class="py-2 text-right">Pago Neto</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/30 print:divide-slate-100 text-slate-300 print:text-slate-800 font-mono">
                                        @forelse($reporteBocaminaDetalle['recientes_pagos'] as $p)
                                            <tr>
                                                <td class="py-2">{{ $p->fecha->format('d/m/Y') }}</td>
                                                <td class="font-sans">{{ $p->trabajador->nombre }}</td>
                                                <td class="text-right text-emerald-400 font-bold">Bs. {{ number_format($p->neto, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="py-3 text-center text-slate-500 font-sans">No hay pagos registrados.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Comparative table (Resumen General) -->
                <div class="glass-card rounded-xl p-6 print:border print:border-slate-300 print:text-slate-900 print:bg-white font-sans">
                    <h3 class="text-lg font-semibold text-slate-100 mb-4 print:text-slate-900">Resumen Comparativo de Bocaminas</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-800 print:divide-slate-300">
                            <thead>
                                <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900/40 print:bg-slate-55 print:text-slate-655">
                                    <th class="px-6 py-4">Bocamina</th>
                                    <th class="px-6 py-4">Trabajadores Asignados</th>
                                    <th class="px-6 py-4">Total Desembolsado (Neto)</th>
                                    <th class="px-6 py-4">Total Producción (Bruto)</th>
                                    <th class="px-6 py-4">Metros Ejecutados</th>
                                    <th class="px-6 py-4">Volquetas Registradas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/40 print:divide-slate-200 text-sm text-slate-300 print:text-slate-850">
                                @foreach($reporteBocamina as $item)
                                    <tr class="hover:bg-slate-900/10">
                                        <td class="px-6 py-4 font-bold text-slate-200 print:text-slate-900">{{ $item['bocamina']->nombre }}</td>
                                        <td class="px-6 py-4 font-mono">{{ $item['cantidad_trabajadores'] }}</td>
                                        <td class="px-6 py-4 font-mono font-medium text-emerald-400">Bs. {{ number_format($item['total_pagado'], 2) }}</td>
                                        <td class="px-6 py-4 font-mono text-slate-200 print:text-slate-900">Bs. {{ number_format($item['total_produccion'], 2) }}</td>
                                        <td class="px-6 py-4 font-mono">{{ number_format($item['metros'], 2) }} m</td>
                                        <td class="px-6 py-4 font-mono">{{ number_format($item['volquetas'], 2) }} volq.</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- 3. Reporte General (Semanal / Rangos de Fechas) -->
        <div x-show="tab === 'general'" class="space-y-6">
            <!-- Filter Bar (no-print) -->
            <div class="glass-card rounded-xl p-6 no-print">
                <form action="{{ route('reportes.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-4 items-end">
                    <input type="hidden" name="tab" value="general">
                    
                    <div>
                        <label for="gen_filtro_fecha" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Rango Rápido</label>
                        <select name="gen_filtro_fecha" id="gen_filtro_fecha" 
                                class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                            <option value="personalizado" {{ $genFiltro === 'personalizado' ? 'selected' : '' }}>Personalizado</option>
                            <option value="esta_semana" {{ $genFiltro === 'esta_semana' ? 'selected' : '' }}>Esta Semana</option>
                            <option value="semana_pasada" {{ $genFiltro === 'semana_pasada' ? 'selected' : '' }}>Semana Pasada</option>
                            <option value="este_mes" {{ $genFiltro === 'este_mes' ? 'selected' : '' }}>Este Mes</option>
                            <option value="mes_pasado" {{ $genFiltro === 'mes_pasado' ? 'selected' : '' }}>Mes Pasado</option>
                        </select>
                    </div>

                    <div>
                        <label for="gen_fecha_desde" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Fecha Inicial</label>
                        <input type="date" name="gen_fecha_desde" id="gen_fecha_desde" value="{{ $genFechaDesde }}"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono">
                    </div>

                    <div>
                        <label for="gen_fecha_hasta" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Fecha Final</label>
                        <input type="date" name="gen_fecha_hasta" id="gen_fecha_hasta" value="{{ $genFechaHasta }}"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono">
                    </div>

                    <button type="submit" class="btn-vibrant-amber inline-flex items-center justify-center px-4 py-2 text-slate-950 font-bold rounded-lg shadow-lg">
                        <i class="fa-solid fa-magnifying-glass mr-2"></i> Generar Balance
                    </button>
                </form>
            </div>

            <!-- Report Details -->
            @if($reporteGeneral)
                <div class="glass-card rounded-xl p-6 md:p-8 space-y-8 print:border print:border-slate-300 print:text-slate-900 print:bg-white font-sans">
                    <div class="border-b border-slate-800 pb-4 print:border-slate-200">
                        <h2 class="text-xl font-bold text-slate-100 print:text-slate-900">Balance General y Desglose Semanal</h2>
                        <p class="text-xs text-slate-400 font-mono mt-0.5">Rangos de Auditoría: {{ Carbon\Carbon::parse($reporteGeneral['desde'])->format('d/m/Y') }} al {{ Carbon\Carbon::parse($reporteGeneral['hasta'])->format('d/m/Y') }}</p>
                    </div>

                    <!-- Aggregated stats -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 font-mono">
                        <div class="p-4 bg-slate-900/40 rounded-lg border border-slate-800/40 print:bg-slate-55 print:border-slate-200">
                            <span class="text-[10px] text-slate-500 uppercase font-mono block font-sans">Producción Total Rango</span>
                            <span class="text-base font-bold text-amber-500">Bs. {{ number_format($reporteGeneral['total_trabajos'], 2) }}</span>
                        </div>
                        <div class="p-4 bg-slate-900/40 rounded-lg border border-slate-800/40 print:bg-slate-55 print:border-slate-200">
                            <span class="text-[10px] text-slate-500 uppercase font-mono block font-sans">Efectivo Neto Liquidado</span>
                            <span class="text-base font-bold text-emerald-400">Bs. {{ number_format($reporteGeneral['total_pagos'], 2) }}</span>
                        </div>
                        <div class="p-4 bg-slate-900/40 rounded-lg border border-slate-800/40 print:bg-slate-55 print:border-slate-200">
                            <span class="text-[10px] text-slate-500 uppercase font-mono block font-sans">Anticipos Desembolsados</span>
                            <span class="text-base font-bold text-red-400">Bs. {{ number_format($reporteGeneral['total_anticipos'], 2) }}</span>
                        </div>
                    </div>

                    <!-- Weekly summaries -->
                    <div class="space-y-3">
                        <h3 class="text-sm font-semibold text-slate-100 print:text-slate-900">Resumen Desglosado por Semana</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-800 print:divide-slate-300">
                                <thead>
                                    <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900/40 print:bg-slate-55 print:text-slate-655">
                                        <th class="px-6 py-3.5">Semana</th>
                                        <th class="px-6 py-3.5">Cantidad Trabajos</th>
                                        <th class="px-6 py-3.5">Valor Producción (Bruto)</th>
                                        <th class="px-6 py-3.5">Cantidad Recibos</th>
                                        <th class="px-6 py-3.5">Total Pagado (Neto)</th>
                                        <th class="px-6 py-3.5">Anticipos Entregados</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/40 print:divide-slate-200 text-xs text-slate-350 print:text-slate-850 font-mono">
                                    @forelse($reporteGeneral['semanas'] as $sem)
                                        <tr class="hover:bg-slate-900/5">
                                            <td class="px-6 py-3 font-medium text-slate-200 print:text-slate-900 font-sans">{{ $sem['semana_nombre'] }}</td>
                                            <td class="px-6 py-3">{{ $sem['cantidad_trabajos'] }}</td>
                                            <td class="px-6 py-3 text-amber-500">Bs. {{ number_format($sem['total_produccion'], 2) }}</td>
                                            <td class="px-6 py-3">{{ $sem['cantidad_pagos'] }}</td>
                                            <td class="px-6 py-3 text-emerald-400 font-bold">Bs. {{ number_format($sem['total_pagado'], 2) }}</td>
                                            <td class="px-6 py-3 text-red-400">Bs. {{ number_format($sem['total_anticipos'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-8 text-center text-slate-500 font-sans">
                                                No se encontraron registros agrupados por semanas en el rango seleccionado.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Items tables -->
                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                        <!-- Works -->
                        <div class="space-y-3">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800 pb-1.5 print:text-slate-600 print:border-slate-200">Detalle de Trabajos</h3>
                            <div class="overflow-x-auto max-h-80 overflow-y-auto">
                                <table class="w-full text-left text-xs">
                                    <thead>
                                        <tr class="font-bold text-slate-500 border-b border-slate-800 print:border-slate-300">
                                            <th class="py-2">Fecha</th>
                                            <th class="py-2">Trabajador</th>
                                            <th class="py-2">Concepto</th>
                                            <th class="py-2 text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/30 print:divide-slate-100 text-slate-300 print:text-slate-800 font-mono">
                                        @forelse($reporteGeneral['trabajos'] as $t)
                                            <tr>
                                                <td class="py-2">{{ $t->fecha->format('d/m/Y') }}</td>
                                                <td class="font-sans">{{ $t->trabajador->nombre }}</td>
                                                <td class="capitalize font-sans">{{ $t->tipo }} ({{ $t->cantidad }})</td>
                                                <td class="text-right font-bold text-amber-500">Bs. {{ number_format($t->subtotal, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="py-3 text-center text-slate-500 font-sans">No hay trabajos registrados.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Payments -->
                        <div class="space-y-3">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800 pb-1.5 print:text-slate-600 print:border-slate-200">Detalle de Pagos</h3>
                            <div class="overflow-x-auto max-h-80 overflow-y-auto">
                                <table class="w-full text-left text-xs">
                                    <thead>
                                        <tr class="font-bold text-slate-500 border-b border-slate-800 print:border-slate-300">
                                            <th class="py-2">Fecha</th>
                                            <th class="py-2">Trabajador</th>
                                            <th class="py-2">Recibo ID</th>
                                            <th class="py-2 text-right">Neto Pagado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/30 print:divide-slate-100 text-slate-300 print:text-slate-800 font-mono">
                                        @forelse($reporteGeneral['pagos'] as $p)
                                            <tr>
                                                <td class="py-2">{{ $p->fecha->format('d/m/Y') }}</td>
                                                <td class="font-sans">{{ $p->trabajador->nombre }}</td>
                                                <td>REC-{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }}</td>
                                                <td class="text-right text-emerald-400 font-bold">Bs. {{ number_format($p->neto, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="py-3 text-center text-slate-500 font-sans">No hay pagos procesados.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="glass-card rounded-xl p-12 text-center text-slate-500 no-print">
                    <i class="fa-solid fa-list-check text-4xl text-slate-700 block mb-3"></i>
                    Selecciona un rango de fechas o filtro rápido y presiona Generar Balance para obtener el desglose por semanas.
                </div>
            @endif
        </div>

        <!-- 4. Reporte de Anticipos -->
        <div x-show="tab === 'anticipos'" class="space-y-6">
            
            <!-- Filters Card (no-print) -->
            <div class="glass-card rounded-xl p-6 no-print">
                <form action="{{ route('reportes.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-4 items-end">
                    <input type="hidden" name="tab" value="anticipos">
                    
                    <div>
                        <label for="filtro_fecha_ant" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Filtro Rápido de Fecha</label>
                        <select name="filtro_fecha" id="filtro_fecha_ant" x-model="filtroFechaAnt"
                                class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                            <option value="personalizado">Personalizado (Elegir Fechas)</option>
                            <option value="esta_semana">Esta Semana</option>
                            <option value="semana_pasada">Semana Pasada</option>
                            <option value="este_mes">Este Mes</option>
                            <option value="mes_pasado">Mes Pasado</option>
                        </select>
                    </div>
                    
                    <div x-show="filtroFechaAnt === 'personalizado'" class="transition-all duration-300">
                        <label for="fecha_desde_ant" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Desde (Opcional)</label>
                        <input type="date" name="fecha_desde" id="fecha_desde_ant" value="{{ request('fecha_desde') }}"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono">
                    </div>
                    
                    <div x-show="filtroFechaAnt === 'personalizado'" class="transition-all duration-300">
                        <label for="fecha_hasta_ant" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Hasta (Opcional)</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta_ant" value="{{ request('fecha_hasta') }}"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono">
                    </div>

                    <div>
                        <label for="ant_estado" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Estado del Anticipo</label>
                        <select name="ant_estado" id="ant_estado" 
                                class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-bold text-amber-500">
                            <option value="todos" {{ $antEstado === 'todos' ? 'selected' : '' }}>Todos los Anticipos</option>
                            <option value="pendiente" {{ $antEstado === 'pendiente' ? 'selected' : '' }}>Con Saldo Pendiente (Activos)</option>
                            <option value="pagado" {{ $antEstado === 'pagado' ? 'selected' : '' }}>Totalmente Descontados (Pagados)</option>
                        </select>
                    </div>
                    
                    <div class="sm:col-span-4 flex justify-end">
                        <button type="submit" class="btn-vibrant-amber w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 text-slate-950 font-bold rounded-lg shadow-lg">
                            <i class="fa-solid fa-magnifying-glass mr-2"></i> Generar Reporte
                        </button>
                    </div>
                </form>
            </div>

            <!-- Details Card -->
            <div class="glass-card rounded-xl p-6 print:border print:border-slate-300 print:text-slate-900 print:bg-white font-sans">
                <h3 class="text-lg font-semibold text-slate-100 mb-4 print:text-slate-900">
                    @if($antEstado === 'pendiente')
                        Detalle de Anticipos Activos con Saldo Pendiente
                    @elseif($antEstado === 'pagado')
                        Detalle de Anticipos Totalmente Descontados (Pagados)
                    @else
                        Detalle General de Anticipos Registrados
                    @endif
                </h3>
                
                @if($fechaDesde || $fechaHasta)
                    <div class="mb-4 text-xs text-slate-400 font-mono print:text-slate-650">
                        Rango de Fechas: 
                        @if($fechaDesde) desde <strong>{{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }}</strong>@endif
                        @if($fechaHasta) hasta <strong>{{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}</strong>@endif
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-800 print:divide-slate-300">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900/40 print:bg-slate-50 print:text-slate-600">
                                <th class="px-6 py-4">ID Anticipo</th>
                                <th class="px-6 py-4">Fecha Entrega</th>
                                <th class="px-6 py-4">Trabajador / Contratista</th>
                                <th class="px-6 py-4">Bocamina</th>
                                <th class="px-6 py-4">Monto Original</th>
                                <th class="px-6 py-4">Saldo Restante</th>
                                <th class="px-6 py-4">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/40 print:divide-slate-200 text-sm text-slate-300 print:text-slate-850 font-mono">
                            @forelse($reporteAnticipos as $a)
                                <tr class="hover:bg-slate-900/10">
                                    <td class="px-6 py-4 text-xs">ANT-{{ str_pad($a->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-6 py-4 text-xs">{{ $a->fecha->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 font-sans font-bold text-slate-200 print:text-slate-900">{{ $a->trabajador->nombre }}</td>
                                    <td class="px-6 py-4 font-sans text-xs">{{ $a->trabajador->bocamina->nombre }}</td>
                                    <td class="px-6 py-4">Bs. {{ number_format($a->monto, 2) }}</td>
                                    <td class="px-6 py-4 font-bold text-amber-500">Bs. {{ number_format($a->saldo, 2) }}</td>
                                    <td class="px-6 py-4 font-sans">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $a->saldo <= 0 ? 'bg-slate-800 text-slate-450 border border-slate-700/60' : 'bg-red-500/10 text-red-400 border border-red-500/25' }}">
                                            {{ $a->saldo <= 0 ? 'Descontado' : 'Pendiente' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-slate-500 font-sans">
                                        No hay anticipos registrados para los criterios seleccionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
