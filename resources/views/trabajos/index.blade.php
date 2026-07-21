@extends('layouts.app')

@section('title', 'Registro de Trabajos')

@section('content')
<div x-data='{ 
    openModal: false, 
    editMode: false, 
    trabajoId: null,
    trabajador_id: '', 
    contrato_id: '', 
    fecha: "{{ now()->toDateString() }}", 
    tipo: "", 
    cantidad: "", 
    precio_unitario: "", 
    observacion: "",
    editActionUrl: "",
    
    // Contracts array from backend
    contratos: @json($contratos),
    
    // Filter contracts based on selected worker
    get filteredContratos() {
        if (!this.trabajador_id) return [];
        return this.contratos.filter(c => c.trabajador_id == this.trabajador_id);
    },
    
    // Update fields when contract is selected
    onContractChange() {
        if (!this.contrato_id) return;
        const selected = this.contratos.find(c => c.id == this.contrato_id);
        if (selected) {
            this.tipo = selected.tipo_pago;
            this.precio_unitario = selected.precio_unitario || "";
        }
    },
    
    openCreate() {
        this.editMode = false;
        this.trabajoId = null;
        this.trabajador_id = "";
        this.contrato_id = "";
        this.fecha = "{{ now()->toDateString() }}";
        this.tipo = "";
        this.cantidad = "";
        this.precio_unitario = "";
        this.observacion = "";
        this.openModal = true;
    },
    
    openEdit(trabajo) {
        if (trabajo.pagado) {
            alert("No se puede editar un trabajo que ya ha sido pagado.");
            return;
        }
        this.editMode = true;
        this.trabajoId = trabajo.id;
        this.trabajador_id = trabajo.trabajador_id;
        this.contrato_id = trabajo.contrato_id || "";
        this.fecha = trabajo.fecha.split("T")[0];
        this.tipo = trabajo.tipo;
        this.cantidad = trabajo.cantidad;
        this.precio_unitario = trabajo.precio_unitario;
        this.observacion = trabajo.observacion || "";
        this.editActionUrl = "/trabajos/" + trabajo.id;
        this.openModal = true;
    }
}' class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100">Registro de Trabajos Realizados</h1>
            <p class="text-sm text-slate-400 mt-1">Registra la producción del personal (metros avanzados, volquetas cargadas, destajos, etc.).</p>
        </div>
        <button @click="openCreate()" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-sm font-bold text-slate-950 transition duration-150 shadow-lg shadow-orange-500/10 self-start">
            <i class="fa-solid fa-plus mr-2"></i> Registrar Trabajo
        </button>
    </div>

    <!-- Filters Section -->
    <div class="glass-card rounded-xl p-6 no-print">
        <form action="{{ route('trabajos.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-4 items-end">
            <div>
                <label for="trabajador_id_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Trabajador / Contratista</label>
                <select name="trabajador_id" id="trabajador_id_filter" 
                        class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                    <option value="">Todos los Trabajadores / Contratistas</option>
                    @foreach($trabajadores as $trabajador)
                        <option value="{{ $trabajador->id }}" {{ request('trabajador_id') == $trabajador->id ? 'selected' : '' }}>{{ $trabajador->nombre }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="fecha_desde" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Fecha Desde</label>
                <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}" 
                       class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono">
            </div>

            <div>
                <label for="fecha_hasta" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Fecha Hasta</label>
                <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ request('fecha_hasta') }}" 
                       class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono">
            </div>

            <div>
                <label for="pagado_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Estado Pago</label>
                <select name="pagado" id="pagado_filter" 
                        class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                    <option value="">Todos</option>
                    <option value="si" {{ request('pagado') === 'si' ? 'selected' : '' }}>Pagado</option>
                    <option value="no" {{ request('pagado') === 'no' ? 'selected' : '' }}>Pendiente</option>
                </select>
            </div>

            <div class="col-span-full sm:col-span-1 flex space-x-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-slate-800 border border-slate-700 hover:bg-slate-700 text-sm font-medium text-slate-200 rounded-lg transition duration-150">
                    <i class="fa-solid fa-magnifying-glass mr-2"></i> Filtrar
                </button>
                <a href="{{ route('trabajos.index') }}" class="inline-flex items-center justify-center px-3 py-2 bg-slate-900 border border-slate-800 hover:bg-slate-800 text-sm font-medium text-slate-400 rounded-lg transition duration-150" title="Limpiar Filtros">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="glass-card rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800">
                <thead>
                    <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900/40">
                        <th class="px-6 py-4 font-semibold">Fecha</th>
                        <th class="px-6 py-4 font-semibold">Trabajador / Contratista</th>
                        <th class="px-6 py-4 font-semibold">Bocamina</th>
                        <th class="px-6 py-4 font-semibold">Contrato / Tipo</th>
                        <th class="px-6 py-4 font-semibold">Cant. × P. Unit.</th>
                        <th class="px-6 py-4 font-semibold">Subtotal</th>
                        <th class="px-6 py-4 font-semibold">Estado</th>
                        <th class="px-6 py-4 font-semibold no-print">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-sm text-slate-300">
                    @forelse($trabajos as $trabajo)
                        <tr class="hover:bg-slate-900/10 transition duration-150">
                            <td class="px-6 py-4 font-mono text-xs">{{ $trabajo->fecha->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 font-medium text-slate-100">{{ $trabajo->trabajador->nombre }}</td>
                            <td class="px-6 py-4 text-xs font-medium">{{ $trabajo->trabajador->bocamina->nombre }}</td>
                            <td class="px-6 py-4">
                                @if($trabajo->contrato)
                                    <span class="text-xs text-amber-500 font-mono block">{{ $trabajo->contrato->codigo }}</span>
                                @endif
                                <span class="capitalize text-slate-300">{{ $trabajo->tipo }}</span>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs">
                                {{ number_format($trabajo->cantidad, 2) }} × Bs. {{ number_format($trabajo->precio_unitario, 2) }}
                            </td>
                            <td class="px-6 py-4 font-mono font-medium text-amber-500">Bs. {{ number_format($trabajo->subtotal, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $trabajo->pagado ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/25' : 'bg-amber-500/10 text-amber-400 border border-amber-500/25' }}">
                                    {{ $trabajo->pagado ? 'Pagado' : 'Pendiente' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 no-print">
                                @if(!$trabajo->pagado)
                                    <div class="flex space-x-2">
                                        <button @click="openEdit({{ $trabajo }})" class="p-1.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-amber-500 transition duration-150" title="Editar">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </button>
                                        <form action="{{ route('trabajos.destroy', $trabajo->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este trabajo?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded bg-slate-800 hover:bg-red-950 text-slate-300 hover:text-red-400 transition duration-150" title="Eliminar">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-500 font-mono">ID Pago: {{ $trabajo->pago_id }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-hammer text-4xl mb-3 block text-slate-600"></i>
                                No se encontraron trabajos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- AlpineJS Modal (Create/Edit) -->
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div @click.away="openModal = false" class="glass-card w-full max-w-md rounded-2xl overflow-hidden shadow-2xl border border-slate-800/80 relative">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-800/80 flex items-center justify-between bg-slate-900/60">
                <h3 class="text-lg font-bold text-slate-100" x-text="editMode ? 'Editar Registro de Trabajo' : 'Registrar Nuevo Trabajo'"></h3>
                <button @click="openModal = false" class="text-slate-400 hover:text-slate-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form :action="editMode ? editActionUrl : '{{ route('trabajos.store') }}'" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-6 space-y-4">
                    <div>
                        <label for="modal_trabajador" class="block text-sm font-medium text-slate-300">Trabajador / Contratista</label>
                        <select id="modal_trabajador" name="trabajador_id" required x-model="trabajador_id" @change="contrato_id = ''"
                                :disabled="editMode"
                                class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                            <option value="">Seleccione trabajador / contratista...</option>
                            @foreach($trabajadores as $trabajador)
                                <option value="{{ $trabajador->id }}">{{ $trabajador->nombre }} (CI: {{ $trabajador->ci }})</option>
                            @endforeach
                        </select>
                        <template x-if="editMode">
                            <input type="hidden" name="trabajador_id" :value="trabajador_id">
                        </template>
                    </div>

                    <!-- Associated Contract (Conditional based on filteredContratos) -->
                    <div x-show="filteredContratos.length > 0">
                        <label for="modal_contrato" class="block text-sm font-medium text-slate-300">Asociar a Contrato de Avance</label>
                        <select id="modal_contrato" name="contrato_id" x-model="contrato_id" @change="onContractChange()"
                                class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                            <option value="">No asociar (trabajo por fuera de contrato)</option>
                            <template x-for="c in filteredContratos" :key="c.id">
                                <option :value="c.id" x-text="c.codigo + ' - ' + c.descripcion.substring(0,25) + '...'"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label for="modal_fecha" class="block text-sm font-medium text-slate-300">Fecha</label>
                        <input id="modal_fecha" name="fecha" type="date" required x-model="fecha"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="modal_tipo" class="block text-sm font-medium text-slate-300">Tipo / Unidad</label>
                            <input id="modal_tipo" name="tipo" type="text" required x-model="tipo"
                                   class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm"
                                   placeholder="Ej. metro, volqueta">
                        </div>
                        <div>
                            <label for="modal_cantidad" class="block text-sm font-medium text-slate-300">Cantidad</label>
                            <input id="modal_cantidad" name="cantidad" type="number" step="0.01" required x-model="cantidad"
                                   class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono"
                                   placeholder="Ej. 15.00">
                        </div>
                    </div>

                    <div>
                        <label for="modal_precio_unitario" class="block text-sm font-medium text-slate-300">Precio Unitario (Bs.)</label>
                        <input id="modal_precio_unitario" name="precio_unitario" type="number" step="0.01" required x-model="precio_unitario"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono"
                               placeholder="Ej. 500.00">
                    </div>

                    <div>
                        <label for="modal_observacion" class="block text-sm font-medium text-slate-300">Observación</label>
                        <input id="modal_observacion" name="observacion" type="text" x-model="observacion"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm"
                               placeholder="Ej. Sector galería 3">
                    </div>
                    
                    <!-- Live Subtotal Calculation -->
                    <div class="p-3 bg-slate-900/60 border border-slate-800 rounded-lg flex justify-between items-center text-sm">
                        <span class="text-slate-400 font-medium">Subtotal Estimado:</span>
                        <span class="text-amber-500 font-bold font-mono text-base" x-text="isNaN(cantidad * precio_unitario) ? 'Bs. 0.00' : 'Bs. ' + (cantidad * precio_unitario).toFixed(2)"></span>
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
