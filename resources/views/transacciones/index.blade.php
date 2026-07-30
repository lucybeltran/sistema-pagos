@extends('layouts.app')

@section('title', 'Compra y Venta de Mineral')

@section('content')
<div x-data="{
    openModal: false,
    editMode: false,
    actionUrl: '',
    
    // Form fields
    fecha: '{{ now()->toDateString() }}',
    tipo: 'compra',
    presentacion: 'saco',
    cliente_proveedor: '',
    bocamina_id: '',
    peso_bruto: '',
    humedad_porcentaje: '',
    peso_neto_seco: '',
    ley: '',
    precio_unidad: '',
    monto_total: '',
    observacion: '',

    // Methods
    openCreate() {
        this.editMode = false;
        this.actionUrl = '{{ route('transacciones-minerales.store') }}';
        this.fecha = '{{ now()->toDateString() }}';
        this.tipo = 'compra';
        this.presentacion = 'saco';
        this.cliente_proveedor = '';
        this.bocamina_id = '';
        this.peso_bruto = '';
        this.humedad_porcentaje = '';
        this.peso_neto_seco = '';
        this.ley = '';
        this.precio_unidad = '';
        this.monto_total = '';
        this.observacion = '';
        this.openModal = true;
    },

    openEdit(transaccion) {
        this.editMode = true;
        this.actionUrl = '/transacciones-minerales/' + transaccion.id;
        this.fecha = transaccion.fecha.split('T')[0];
        this.tipo = transaccion.tipo;
        this.presentacion = transaccion.presentacion;
        this.cliente_proveedor = transaccion.cliente_proveedor;
        this.bocamina_id = transaccion.bocamina_id || '';
        this.peso_bruto = transaccion.peso_bruto || '';
        this.humedad_porcentaje = transaccion.humedad_porcentaje || '';
        this.peso_neto_seco = transaccion.peso_neto_seco || '';
        this.ley = transaccion.ley || '';
        this.precio_unidad = transaccion.precio_unidad || '';
        this.monto_total = transaccion.monto_total;
        this.observacion = transaccion.observacion || '';
        this.openModal = true;
    },

    calculateNetWeight() {
        let bruto = parseFloat(this.peso_bruto);
        let humedad = parseFloat(this.humedad_porcentaje);
        
        if (!isNaN(bruto)) {
            if (!isNaN(humedad) && humedad >= 0 && humedad <= 100) {
                this.peso_neto_seco = (bruto * (1 - (humedad / 100))).toFixed(2);
            } else {
                this.peso_neto_seco = bruto.toFixed(2);
            }
            this.calculateTotal();
        }
    },

    calculateTotal() {
        let peso = parseFloat(this.peso_neto_seco);
        let precio = parseFloat(this.precio_unidad);

        if (!isNaN(peso) && !isNaN(precio)) {
            this.monto_total = (peso * precio).toFixed(2);
        }
    }
}" class="space-y-8">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100">Compra y Venta de Minerales</h1>
            <p class="text-sm text-slate-400 mt-1">Control de transacciones de mineral (concentrados, cargas y sacos) medidos por peso y ley.</p>
        </div>
        <button @click="openCreate()" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-sm font-bold text-slate-950 transition duration-150 shadow-lg shadow-orange-500/10 self-start">
            <i class="fa-solid fa-scale-balanced mr-2"></i> Registrar Movimiento
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <!-- Income (Ventas) -->
        <div class="glass-card rounded-xl p-6 relative overflow-hidden group border-emerald-500/10 hover:border-emerald-500/25 transition duration-300">
            <div class="absolute top-0 right-0 p-3 opacity-10 text-6xl text-emerald-500 group-hover:opacity-20 transition duration-300">
                <i class="fa-solid fa-circle-arrow-down"></i>
            </div>
            <p class="text-sm font-medium text-slate-400 uppercase tracking-wider">Ingresos Totales (Ventas)</p>
            <p class="mt-2 text-3xl font-bold text-emerald-500">Bs. {{ number_format($total_ingresos, 2) }}</p>
            <div class="mt-2 text-xs text-slate-500 font-mono">Entradas por ventas de mineral</div>
        </div>

        <!-- Expense (Compras) -->
        <div class="glass-card rounded-xl p-6 relative overflow-hidden group border-rose-500/10 hover:border-rose-500/25 transition duration-300">
            <div class="absolute top-0 right-0 p-3 opacity-10 text-6xl text-rose-500 group-hover:opacity-20 transition duration-300">
                <i class="fa-solid fa-circle-arrow-up"></i>
            </div>
            <p class="text-sm font-medium text-slate-400 uppercase tracking-wider">Egresos Totales (Compras)</p>
            <p class="mt-2 text-3xl font-bold text-rose-450">Bs. {{ number_format($total_egresos, 2) }}</p>
            <div class="mt-2 text-xs text-slate-500 font-mono">Salidas por adquisición de mineral</div>
        </div>

        <!-- Net Balance -->
        <div class="glass-card rounded-xl p-6 relative overflow-hidden group border-amber-500/10 hover:border-amber-500/25 transition duration-300">
            <div class="absolute top-0 right-0 p-3 opacity-10 text-6xl text-amber-500 group-hover:opacity-20 transition duration-300">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <p class="text-sm font-medium text-slate-400 uppercase tracking-wider">Balance de Caja</p>
            <p class="mt-2 text-3xl font-bold {{ $balance >= 0 ? 'text-amber-500' : 'text-rose-500' }}">
                Bs. {{ number_format($balance, 2) }}
            </p>
            <div class="mt-2 text-xs text-slate-500 font-mono">Diferencia neta (Ventas - Compras)</div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="glass-card rounded-xl p-6 no-print">
        <form action="{{ route('transacciones-minerales.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-5 items-end">
            <div>
                <label for="tipo_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Tipo de Movimiento</label>
                <select name="tipo" id="tipo_filter" 
                        class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                    <option value="">Todos los Tipos</option>
                    <option value="compra" {{ request('tipo') === 'compra' ? 'selected' : '' }}>Compra (Egreso)</option>
                    <option value="venta" {{ request('tipo') === 'venta' ? 'selected' : '' }}>Venta (Ingreso)</option>
                </select>
            </div>

            <div>
                <label for="presentacion_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Presentación / Formato</label>
                <select name="presentacion" id="presentacion_filter" 
                        class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                    <option value="">Todas las Presentaciones</option>
                    <option value="saco" {{ request('presentacion') === 'saco' ? 'selected' : '' }}>Saco</option>
                    <option value="concentrado" {{ request('presentacion') === 'concentrado' ? 'selected' : '' }}>Concentrado</option>
                    <option value="volqueta" {{ request('presentacion') === 'volqueta' ? 'selected' : '' }}>Volqueta</option>
                    <option value="tonelada" {{ request('presentacion') === 'tonelada' ? 'selected' : '' }}>Tonelada</option>
                    <option value="otro" {{ request('presentacion') === 'otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>

            <div>
                <label for="bocamina_id_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Bocamina de Origen</label>
                <select name="bocamina_id" id="bocamina_id_filter" 
                        class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                    <option value="">Todas las Bocaminas</option>
                    @foreach($bocaminas as $bocamina)
                        <option value="{{ $bocamina->id }}" {{ request('bocamina_id') == $bocamina->id ? 'selected' : '' }}>{{ $bocamina->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="fecha_desde_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Fecha Desde</label>
                <input type="date" name="fecha_desde" id="fecha_desde_filter" value="{{ request('fecha_desde') }}"
                       class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
            </div>

            <div class="flex space-x-2">
                <div class="flex-grow">
                    <label for="fecha_hasta_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" id="fecha_hasta_filter" value="{{ request('fecha_hasta') }}"
                           class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                </div>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-slate-800 border border-slate-700 hover:bg-slate-700 text-sm font-medium text-slate-200 rounded-lg transition duration-150 self-end mb-0.5">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                <a href="{{ route('transacciones-minerales.index') }}" class="inline-flex items-center justify-center px-3 py-2 bg-slate-900 border border-slate-800 hover:bg-slate-800 text-sm font-medium text-slate-400 rounded-lg transition duration-150 self-end mb-0.5" title="Limpiar Filtros">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table List -->
    <div class="glass-card rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800">
                <thead>
                    <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900/40">
                        <th class="px-6 py-4">Fecha</th>
                        <th class="px-6 py-4">Tipo</th>
                        <th class="px-6 py-4">Cliente / Proveedor</th>
                        <th class="px-6 py-4">Presentación</th>
                        <th class="px-6 py-4">Bocamina</th>
                        <th class="px-6 py-4">Peso Neto Seco</th>
                        <th class="px-6 py-4">Ley (Calidad)</th>
                        <th class="px-6 py-4">Precio Unit. (Bs.)</th>
                        <th class="px-6 py-4">Total (Bs.)</th>
                        <th class="px-6 py-4 no-print">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-sm text-slate-300">
                    @forelse($transacciones as $transaccion)
                        <tr class="hover:bg-slate-900/10 transition duration-150">
                            <td class="px-6 py-4 font-mono text-xs">{{ $transaccion->fecha->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $transaccion->tipo === 'venta' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/25' : 'bg-rose-500/10 text-rose-400 border border-rose-500/25' }}">
                                    {{ $transaccion->tipo === 'venta' ? 'Venta' : 'Compra' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-100">{{ $transaccion->cliente_proveedor }}</td>
                            <td class="px-6 py-4 capitalize">{{ $transaccion->presentacion }}</td>
                            <td class="px-6 py-4 text-xs">{{ $transaccion->bocamina->nombre ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-mono text-xs">
                                {{ $transaccion->peso_neto_seco ? number_format($transaccion->peso_neto_seco, 2) . ' TN' : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-amber-500 font-semibold">{{ $transaccion->ley ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-mono text-xs">
                                {{ $transaccion->precio_unidad ? 'Bs. ' . number_format($transaccion->precio_unidad, 2) : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-100">
                                Bs. {{ number_format($transaccion->monto_total, 2) }}
                            </td>
                            <td class="px-6 py-4 no-print">
                                <div class="flex space-x-2">
                                    <button @click="openEdit({{ $transaccion }})" class="p-1.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-amber-500 transition duration-150" title="Editar">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    <form action="{{ route('transacciones-minerales.destroy', $transaccion->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este registro de compra/venta?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded bg-slate-800 hover:bg-red-950 text-slate-300 hover:text-red-400 transition duration-150" title="Eliminar">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-scale-balanced text-4xl mb-3 block text-slate-600"></i>
                                No se encontraron registros de compra o venta de mineral.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- AlpineJS Modal (Create/Edit) -->
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div @click.away="openModal = false" class="glass-card w-full max-w-2xl rounded-2xl overflow-hidden shadow-2xl border border-slate-800/80 relative" style="background: rgba(15, 23, 42, 0.95);">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-800/80 flex items-center justify-between bg-slate-900/60">
                <h3 class="text-lg font-bold text-slate-100" x-text="editMode ? 'Editar Registro de Mineral' : 'Registrar Compra / Venta de Mineral'"></h3>
                <button @click="openModal = false" class="text-slate-400 hover:text-slate-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form :action="actionUrl" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[75vh] overflow-y-auto">
                    <!-- Date -->
                    <div>
                        <label for="modal_fecha" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Fecha de la Operación</label>
                        <input id="modal_fecha" name="fecha" type="date" required x-model="fecha"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono">
                    </div>

                    <!-- Type Toggle -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Tipo de Transacción</label>
                        <div class="grid grid-cols-2 gap-2 mt-1">
                            <button type="button" @click="tipo = 'compra'" 
                                    :class="tipo === 'compra' ? 'bg-rose-500/25 border-rose-500 text-rose-400 font-bold' : 'bg-slate-900 border-slate-700 text-slate-400'"
                                    class="py-2 text-xs rounded-lg border text-center transition-all duration-150">
                                <i class="fa-solid fa-circle-arrow-up mr-1.5"></i>Compra (Egreso)
                            </button>
                            <button type="button" @click="tipo = 'venta'" 
                                    :class="tipo === 'venta' ? 'bg-emerald-500/25 border-emerald-500 text-emerald-400 font-bold' : 'bg-slate-900 border-slate-700 text-slate-400'"
                                    class="py-2 text-xs rounded-lg border text-center transition-all duration-150">
                                <i class="fa-solid fa-circle-arrow-down mr-1.5"></i>Venta (Ingreso)
                            </button>
                            <input type="hidden" name="tipo" :value="tipo">
                        </div>
                    </div>

                    <!-- Client / Provider -->
                    <div>
                        <label for="modal_cliente_proveedor" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Comprador / Vendedor</label>
                        <input id="modal_cliente_proveedor" name="cliente_proveedor" type="text" required x-model="cliente_proveedor"
                               placeholder="Ej. Comercializadora San Juan o Nombre de Proveedor"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none text-sm">
                    </div>

                    <!-- Presentation -->
                    <div>
                        <label for="modal_presentacion" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Presentación / Formato</label>
                        <select id="modal_presentacion" name="presentacion" required x-model="presentacion"
                                class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none text-sm">
                            <option value="saco">Saco</option>
                            <option value="concentrado">Concentrado</option>
                            <option value="volqueta">Volqueta</option>
                            <option value="tonelada">Tonelada</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <!-- Bocamina of Origin -->
                    <div>
                        <label for="modal_bocamina_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Bocamina de Origen (Opcional)</label>
                        <select id="modal_bocamina_id" name="bocamina_id" x-model="bocamina_id"
                                class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none text-sm">
                            <option value="">Ninguna / No aplica</option>
                            @foreach($bocaminas as $bocamina)
                                <option value="{{ $bocamina->id }}">{{ $bocamina->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Ley / Grade -->
                    <div>
                        <label for="modal_ley" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Ley / Grado de Concentración</label>
                        <input id="modal_ley" name="ley" type="text" x-model="ley"
                               placeholder="Ej. 52% Zn, 1.2% Pb, 65 marcos Ag"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none text-sm font-mono">
                    </div>

                    <!-- Weights Calculations -->
                    <div class="col-span-1 md:col-span-2 border-t border-slate-800/80 pt-4 mt-2">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-amber-500 mb-3">Pesaje y Valoración Económica</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Peso Bruto -->
                            <div>
                                <label for="modal_peso_bruto" class="block text-xs font-semibold uppercase tracking-wider text-slate-450">Peso Bruto</label>
                                <div class="relative mt-1">
                                    <input id="modal_peso_bruto" name="peso_bruto" type="number" step="0.01" x-model="peso_bruto" @input="calculateNetWeight"
                                           placeholder="Ej. 10.00"
                                           class="block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none text-sm font-mono pr-8">
                                    <span class="absolute right-3 top-2.5 text-xs text-slate-500 font-bold font-mono">TN</span>
                                </div>
                            </div>

                            <!-- Humedad % -->
                            <div>
                                <label for="modal_humedad" class="block text-xs font-semibold uppercase tracking-wider text-slate-450">Humedad (%)</label>
                                <div class="relative mt-1">
                                    <input id="modal_humedad" name="humedad_porcentaje" type="number" step="0.01" min="0" max="100" x-model="humedad_porcentaje" @input="calculateNetWeight"
                                           placeholder="Ej. 8.50"
                                           class="block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none text-sm font-mono pr-8">
                                    <span class="absolute right-3 top-2.5 text-xs text-slate-500 font-bold font-mono">%</span>
                                </div>
                            </div>

                            <!-- Peso Neto Seco -->
                            <div>
                                <label for="modal_peso_neto" class="block text-xs font-semibold uppercase tracking-wider text-slate-450">Peso Neto Seco</label>
                                <div class="relative mt-1">
                                    <input id="modal_peso_neto" name="peso_neto_seco" type="number" step="0.01" x-model="peso_neto_seco" @input="calculateTotal"
                                           placeholder="Calculado automáticamente"
                                           class="block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none text-sm font-mono pr-8">
                                    <span class="absolute right-3 top-2.5 text-xs text-slate-500 font-bold font-mono">TN</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Finance Calculations -->
                    <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Precio Unitario -->
                        <div>
                            <label for="modal_precio_unidad" class="block text-xs font-semibold uppercase tracking-wider text-slate-450">Precio por Unidad (Bs.)</label>
                            <div class="relative mt-1">
                                <input id="modal_precio_unidad" name="precio_unitario" type="number" step="0.01" x-model="precio_unitario" @input="calculateTotal"
                                       placeholder="Ej. 1200.00"
                                       class="block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none text-sm font-mono pr-10">
                                <span class="absolute right-3 top-2.5 text-xs text-slate-500 font-bold font-mono">Bs/TN</span>
                            </div>
                        </div>

                        <!-- Monto Total -->
                        <div>
                            <label for="modal_total" class="block text-xs font-semibold uppercase tracking-wider text-amber-500 font-bold">Monto Total a Liquidar (Bs.)</label>
                            <div class="relative mt-1">
                                <input id="modal_total" name="monto_total" type="number" step="0.01" required x-model="monto_total"
                                       placeholder="Calculado automáticamente"
                                       class="block w-full px-3 py-2 bg-slate-900 border border-amber-500/40 rounded-lg text-slate-100 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm font-mono pr-8 font-bold">
                                <span class="absolute right-3 top-2.5 text-xs text-amber-500 font-bold font-mono">Bs.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="modal_observacion" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Observaciones / Detalles Especiales</label>
                        <textarea id="modal_observacion" name="observacion" rows="2" x-model="observacion"
                                  placeholder="Ej. Descuentos por impurezas, bonos o penalidades adicionales."
                                  class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none text-sm"></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-slate-800/80 bg-slate-900/40 flex justify-end space-x-3">
                    <button type="button" @click="openModal = false" class="px-4 py-2 text-sm font-medium rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-bold rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-slate-950">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
