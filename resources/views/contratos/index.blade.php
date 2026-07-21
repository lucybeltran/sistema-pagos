@extends('layouts.app')

@section('title', 'Contratos')

@section('content')
<div x-data="{ 
    openModal: false, 
    editMode: false, 
    contratoId: null,
    codigo: '', 
    trabajador_id: '', 
    bocamina_id: '', 
    descripcion: '', 
    tipo_pago: 'metro', 
    tipo_pago_select: 'metro',
    tipo_pago_custom: '',
    precio_unitario: '', 
    avance_estimado_semanal: '', 
    monto_total: '', 
    fecha_inicio: '', 
    fecha_fin: '', 
    estado: 'activo', 
    editActionUrl: '',
    init() {
        this.$watch('precio_unitario', () => this.calcularMontoPresupuestado());
        this.$watch('avance_estimado_semanal', () => this.calcularMontoPresupuestado());
        this.$watch('tipo_pago', () => this.calcularMontoPresupuestado());
    },
    calcularMontoPresupuestado() {
        if (this.tipo_pago !== 'monto_fijo') {
            const precio = parseFloat(this.precio_unitario);
            const avance = parseFloat(this.avance_estimado_semanal);
            if (!isNaN(precio) && !isNaN(avance)) {
                this.monto_total = (precio * avance).toFixed(2);
            }
        }
    },
    updateTipoPagoActual() {
        this.tipo_pago = (this.tipo_pago_select === 'otro') ? this.tipo_pago_custom : this.tipo_pago_select;
    },
    openCreate() {
        this.editMode = false;
        this.contratoId = null;
        this.codigo = '';
        this.trabajador_id = '';
        this.bocamina_id = '';
        this.descripcion = '';
        this.tipo_pago_select = 'metro';
        this.tipo_pago_custom = '';
        this.tipo_pago = 'metro';
        this.precio_unitario = '';
        this.avance_estimado_semanal = '';
        this.monto_total = '';
        this.fecha_inicio = '{{ now()->toDateString() }}';
        this.fecha_fin = '';
        this.estado = 'activo';
        this.openModal = true;
    },
    openEdit(contrato) {
        this.editMode = true;
        this.contratoId = contrato.id;
        this.codigo = contrato.codigo;
        this.trabajador_id = contrato.trabajador_id;
        this.bocamina_id = contrato.bocamina_id;
        this.descripcion = contrato.descripcion;
        
        const presets = ['metro', 'volqueta', 'tonelada', 'saco', 'monto_fijo'];
        if (presets.includes(contrato.tipo_pago)) {
            this.tipo_pago_select = contrato.tipo_pago;
            this.tipo_pago_custom = '';
        } else {
            this.tipo_pago_select = 'otro';
            this.tipo_pago_custom = contrato.tipo_pago;
        }
        this.tipo_pago = contrato.tipo_pago;
        
        this.precio_unitario = contrato.precio_unitario || '';
        this.avance_estimado_semanal = contrato.avance_estimado_semanal || '';
        this.monto_total = contrato.monto_total;
        this.fecha_inicio = contrato.fecha_inicio;
        this.fecha_fin = contrato.fecha_fin || '';
        this.estado = contrato.estado;
        this.editActionUrl = '/contratos/' + contrato.id;
        this.openModal = true;
    }
}" class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100">Contratos de Personal</h1>
            <p class="text-sm text-slate-400 mt-1">Administra los acuerdos específicos (por metro, volqueta, destajo) para el personal.</p>
        </div>
        <button @click="openCreate()" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-sm font-bold text-slate-950 transition duration-150 shadow-lg shadow-orange-500/10 self-start">
            <i class="fa-solid fa-file-signature mr-2"></i> Nuevo Contrato
        </button>
    </div>

    <!-- Filters Section -->
    <div class="glass-card rounded-xl p-6 no-print">
        <form action="{{ route('contratos.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-5 items-end">
            <div>
                <label for="buscar" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Buscar Código o Desc.</label>
                <input type="text" name="buscar" id="buscar" value="{{ request('buscar') }}" 
                       class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm"
                       placeholder="Ej. CON-01 / excavación">
            </div>
            
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
                <label for="bocamina_id_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Bocamina</label>
                <select name="bocamina_id" id="bocamina_id_filter" 
                        class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                    <option value="">Todas las Bocaminas</option>
                    @foreach($bocaminas as $bocamina)
                        <option value="{{ $bocamina->id }}" {{ request('bocamina_id') == $bocamina->id ? 'selected' : '' }}>{{ $bocamina->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="tipo_pago_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Tipo de Contrato</label>
                <select name="tipo_pago" id="tipo_pago_filter" 
                        class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                    <option value="">Todos los Tipos</option>
                    @foreach($tiposPago as $tipo)
                        <option value="{{ $tipo }}" {{ request('tipo_pago') === $tipo ? 'selected' : '' }}>
                            @if($tipo === 'monto_fijo')
                                Monto Fijo / Destajo
                            @elseif(in_array($tipo, ['metro', 'volqueta', 'tonelada', 'saco']))
                                Por {{ ucfirst($tipo) }}
                            @else
                                {{ ucfirst($tipo) }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-slate-800 border border-slate-700 hover:bg-slate-700 text-sm font-medium text-slate-200 rounded-lg transition duration-150">
                    <i class="fa-solid fa-magnifying-glass mr-2"></i> Filtrar
                </button>
                <a href="{{ route('contratos.index') }}" class="inline-flex items-center justify-center px-3 py-2 bg-slate-900 border border-slate-800 hover:bg-slate-800 text-sm font-medium text-slate-400 rounded-lg transition duration-150" title="Limpiar Filtros">
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
                        <th class="px-6 py-4 font-semibold">Código</th>
                        <th class="px-6 py-4 font-semibold">Trabajador / Contratista</th>
                        <th class="px-6 py-4 font-semibold">Bocamina</th>
                        <th class="px-6 py-4 font-semibold">Tipo Pago</th>
                        <th class="px-6 py-4 font-semibold">Presupuesto (Total)</th>
                        <th class="px-6 py-4 font-semibold">Progreso Financiero</th>
                        <th class="px-6 py-4 font-semibold">Estado</th>
                        <th class="px-6 py-4 font-semibold no-print">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-sm text-slate-300">
                    @forelse($contratos as $contrato)
                        <tr class="hover:bg-slate-900/10 transition duration-150">
                            <td class="px-6 py-4 font-mono font-medium text-amber-500">
                                <a href="{{ route('contratos.show', $contrato->id) }}" class="hover:underline flex items-center">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] mr-1.5 opacity-80"></i> {{ $contrato->codigo }}
                                </a>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-100">{{ $contrato->trabajador->nombre }}</td>
                            <td class="px-6 py-4">{{ $contrato->bocamina->nombre }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="px-2 py-0.5 rounded text-xs bg-slate-800 text-slate-300 border border-slate-700/60 self-start capitalize">
                                        @if($contrato->tipo_pago === 'monto_fijo')
                                            Monto Fijo / Destajo
                                        @elseif(in_array($contrato->tipo_pago, ['metro', 'volqueta', 'tonelada', 'saco']))
                                            Por {{ ucfirst($contrato->tipo_pago) }}
                                        @else
                                            {{ ucfirst($contrato->tipo_pago) }}
                                        @endif
                                    </span>
                                    @if($contrato->precio_unitario)
                                        <span class="text-[10px] text-slate-400 mt-1 font-mono">
                                            Precio: Bs. {{ number_format($contrato->precio_unitario, 2) }}
                                        </span>
                                    @endif
                                    @if($contrato->avance_estimado_semanal)
                                        <span class="text-[10px] text-amber-500 mt-0.5 font-mono">
                                            Est. Semanal: {{ number_format($contrato->avance_estimado_semanal, 1) }} 
                                            {{ $contrato->tipo_pago === 'metro' ? 'm' : ($contrato->tipo_pago === 'volqueta' ? 'vq.' : ($contrato->tipo_pago === 'tonelada' ? 't' : ($contrato->tipo_pago === 'saco' ? 's' : $contrato->tipo_pago))) }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono font-medium text-slate-200">Bs. {{ number_format($contrato->monto_total, 2) }}</td>
                            <td class="px-6 py-4">
                                <div class="w-full bg-slate-800 rounded-full h-2 relative">
                                    <div class="bg-gradient-to-r from-amber-500 to-orange-500 h-2 rounded-full" style="width: {{ $contrato->avance_porcentaje }}%"></div>
                                </div>
                                <div class="flex justify-between items-center text-[10px] text-slate-400 mt-1 font-mono">
                                    <span>Bs. {{ number_format($contrato->avance_monto, 2) }}</span>
                                    <span>{{ $contrato->avance_porcentaje }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold 
                                    @if($contrato->estado === 'activo') bg-emerald-500/10 text-emerald-400 border border-emerald-500/25
                                    @elseif($contrato->estado === 'finalizado') bg-blue-500/10 text-blue-400 border border-blue-500/25
                                    @else bg-slate-850 text-slate-400 border border-slate-700 @endif">
                                    {{ ucfirst($contrato->estado) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 no-print">
                                <div class="flex space-x-2">
                                    <a href="{{ route('contratos.show', $contrato->id) }}" class="p-1.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-amber-500 transition duration-150" title="Ver Detalles y Avance">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <button @click="openEdit({{ $contrato }})" class="p-1.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-amber-500 transition duration-150" title="Editar">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    <form action="{{ route('contratos.destroy', $contrato->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este contrato?')">
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
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-file-prescription text-4xl mb-3 block text-slate-600"></i>
                                No se encontraron contratos de trabajo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- AlpineJS Modal (Create/Edit) -->
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div @click.away="openModal = false" class="glass-card w-full max-w-xl rounded-2xl overflow-hidden shadow-2xl border border-slate-800/80 relative">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-800/80 flex items-center justify-between bg-slate-900/60">
                <h3 class="text-lg font-bold text-slate-100" x-text="editMode ? 'Editar Contrato' : 'Nuevo Contrato'"></h3>
                <button @click="openModal = false" class="text-slate-400 hover:text-slate-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form :action="editMode ? editActionUrl : '{{ route('contratos.store') }}'" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div>
                        <label for="modal_codigo" class="block text-sm font-medium text-slate-300">Código del Contrato (Opcional)</label>
                        <input id="modal_codigo" name="codigo" type="text" x-model="codigo"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono"
                               placeholder="Ej. CON-01 (Auto-generado si queda vacío)">
                    </div>

                    <div>
                        <label for="modal_trabajador" class="block text-sm font-medium text-slate-300">Trabajador / Contratista Responsable</label>
                        <select id="modal_trabajador" name="trabajador_id" required x-model="trabajador_id"
                                class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                            <option value="">Seleccione trabajador / contratista...</option>
                            @foreach($trabajadores as $trabajador)
                                <option value="{{ $trabajador->id }}">{{ $trabajador->nombre }} (CI: {{ $trabajador->ci }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="modal_bocamina" class="block text-sm font-medium text-slate-300">Bocamina</label>
                        <select id="modal_bocamina" name="bocamina_id" required x-model="bocamina_id"
                                class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                            <option value="">Seleccione bocamina...</option>
                            @foreach($bocaminas as $bocamina)
                                <option value="{{ $bocamina->id }}">{{ $bocamina->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="modal_descripcion" class="block text-sm font-medium text-slate-300">Descripción del Trabajo</label>
                        <textarea id="modal_descripcion" name="descripcion" required rows="2" x-model="descripcion"
                                  class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm"
                                  placeholder="Detalles del acuerdo (ej. excavación de túnel, traslado de mineral, destajo)"></textarea>
                    </div>

                    <div class="grid gap-4 transition-all duration-200" :class="tipo_pago_select === 'otro' ? 'grid-cols-3' : 'grid-cols-2'">
                        <div>
                            <label for="modal_tipo_pago_select" class="block text-sm font-medium text-slate-300">Tipo de Contrato</label>
                            <div class="flex items-center space-x-2 mt-1">
                                <select id="modal_tipo_pago_select" x-model="tipo_pago_select" @change="updateTipoPagoActual()" required
                                        class="block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                                    @foreach($tiposPago as $tipo)
                                        <option value="{{ $tipo }}">
                                            @if($tipo === 'monto_fijo')
                                                Monto Fijo (Destajo)
                                            @elseif(in_array($tipo, ['metro', 'volqueta', 'tonelada', 'saco']))
                                                Por {{ ucfirst($tipo) }}
                                            @else
                                                {{ ucfirst($tipo) }}
                                            @endif
                                        </option>
                                    @endforeach
                                    <option value="otro">Personalizado (Escribir nuevo...)</option>
                                </select>
                                <button type="button" @click="tipo_pago_select = 'otro'; tipo_pago_custom = ''; updateTipoPagoActual(); $nextTick(() => $refs.tipoPagoCustomInput.focus())" 
                                        class="p-2.5 rounded-lg bg-slate-800 border border-slate-700 hover:border-amber-500 text-slate-300 hover:text-amber-500 transition duration-150 flex-shrink-0"
                                        title="Agregar nuevo tipo de contrato">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Custom Type Input (Show if 'otro' is selected) -->
                        <div x-show="tipo_pago_select === 'otro'" class="transition-all duration-200" x-cloak>
                            <label for="modal_tipo_pago_custom" class="block text-sm font-medium text-slate-300">Nombre del Tipo</label>
                            <input id="modal_tipo_pago_custom" type="text" x-model="tipo_pago_custom" x-ref="tipoPagoCustomInput" @input="updateTipoPagoActual()"
                                   class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm"
                                   placeholder="Ej. Por Caja, Por Rueda">
                        </div>

                        <div>
                            <label for="modal_precio_unitario" class="block text-sm font-medium text-slate-300">Precio Unitario (Bs.)</label>
                            <input id="modal_precio_unitario" name="precio_unitario" type="number" step="0.01" x-model="precio_unitario"
                                   class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono"
                                   placeholder="Ej. 150.00 (Vacío si es fijo)">
                        </div>
                    </div>

                    <!-- Hidden input to submit the actual value -->
                    <input type="hidden" name="tipo_pago" :value="tipo_pago">

                    <div x-show="tipo_pago !== 'monto_fijo'" class="transition-all duration-200">
                        <label for="modal_avance_estimado_semanal" class="block text-sm font-medium text-slate-300">
                            Estimación Semanal de Avance (<span x-text="tipo_pago === 'metro' ? 'Metros' : (tipo_pago === 'volqueta' ? 'Volquetas' : (tipo_pago === 'tonelada' ? 'Toneladas' : (tipo_pago === 'saco' ? 'Sacos' : (tipo_pago_custom ? tipo_pago_custom : 'Unidades'))))"></span> por semana)
                        </label>
                        <input id="modal_avance_estimado_semanal" name="avance_estimado_semanal" type="number" step="0.1" x-model="avance_estimado_semanal"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono"
                               placeholder="Ej. 5.5">
                    </div>

                    <div>
                        <label for="modal_monto_total" class="block text-sm font-medium text-slate-300">Monto Presupuestado Total (Bs.)</label>
                        <input id="modal_monto_total" name="monto_total" type="number" step="0.01" required x-model="monto_total"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono"
                               placeholder="Ej. 10000.00">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="modal_fecha_inicio" class="block text-sm font-medium text-slate-300">Fecha de Inicio</label>
                            <input id="modal_fecha_inicio" name="fecha_inicio" type="date" required x-model="fecha_inicio"
                                   class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono">
                        </div>
                        <div>
                            <label for="modal_fecha_fin" class="block text-sm font-medium text-slate-300">Fecha Fin (Opcional)</label>
                            <input id="modal_fecha_fin" name="fecha_fin" type="date" x-model="fecha_fin"
                                   class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono">
                        </div>
                    </div>

                    <div>
                        <label for="modal_estado" class="block text-sm font-medium text-slate-300">Estado</label>
                        <select id="modal_estado" name="estado" required x-model="estado"
                                class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                            <option value="activo">Activo</option>
                            <option value="finalizado">Finalizado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
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
