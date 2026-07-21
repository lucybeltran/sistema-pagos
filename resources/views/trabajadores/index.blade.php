@extends('layouts.app')

@section('title', 'Trabajadores')

@section('content')
<div x-data="{ 
    openModal: false, 
    editMode: false, 
    trabajadorId: null,
    ci: '', 
    nombre: '', 
    telefono: '', 
    bocamina_id: '',
    estado: 'activo', 
    editActionUrl: '',
    
    isNombreValido() {
        if (!this.nombre) return true;
        const words = this.nombre.trim().split(/\s+/);
        return words.every(word => /^[A-ZÁÉÍÓÚÑ]/.test(word));
    },
    isTelefonoValido() {
        if (!this.telefono) return true;
        return /^[0-9]{8}$/.test(this.telefono);
    },

    openCreate() {
        this.editMode = false;
        this.trabajadorId = null;
        this.ci = '';
        this.nombre = '';
        this.telefono = '';
        this.bocamina_id = '';
        this.estado = 'activo';
        this.openModal = true;
    },
    openEdit(trabajador) {
        this.editMode = true;
        this.trabajadorId = trabajador.id;
        this.ci = trabajador.ci;
        this.nombre = trabajador.nombre;
        this.telefono = trabajador.telefono || '';
        this.bocamina_id = trabajador.bocamina_id;
        this.estado = trabajador.estado;
        this.editActionUrl = '/trabajadores/' + trabajador.id;
        this.openModal = true;
    }
}" class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100">Registro de Trabajadores / Contratistas</h1>
            <p class="text-sm text-slate-400 mt-1">Administra el personal de la empresa asignado a cada bocamina.</p>
        </div>
        <button @click="openCreate()" class="btn-vibrant-amber inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-bold shadow-lg self-start">
            <i class="fa-solid fa-user-plus mr-2"></i> Nuevo Trabajador / Contratista
        </button>
    </div>

    <!-- Filters Section -->
    <div class="glass-card rounded-xl p-6 no-print">
        <form action="{{ route('trabajadores.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-4 items-end">
            <div>
                <label for="buscar" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Buscar por Nombre o CI</label>
                <input type="text" name="buscar" id="buscar" value="{{ request('buscar') }}" 
                       class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm"
                       placeholder="Ej. Juan Pérez / 483920">
            </div>
            
            <div>
                <label for="bocamina_id_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Filtrar por Bocamina</label>
                <select name="bocamina_id" id="bocamina_id_filter" 
                        class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                    <option value="">Todas las Bocaminas</option>
                    @foreach($bocaminas as $bocamina)
                        <option value="{{ $bocamina->id }}" {{ request('bocamina_id') == $bocamina->id ? 'selected' : '' }}>{{ $bocamina->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="estado_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Estado</label>
                <select name="estado" id="estado_filter" 
                        class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                    <option value="">Todos los Estados</option>
                    <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="btn-vibrant-warm flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-bold rounded-lg shadow-lg">
                    <i class="fa-solid fa-magnifying-glass mr-2"></i> Filtrar
                </button>
                <a href="{{ route('trabajadores.index') }}" class="inline-flex items-center justify-center px-3 py-2 bg-slate-900 border border-slate-800 hover:bg-slate-800 text-sm font-medium text-slate-400 rounded-lg transition duration-150" title="Limpiar Filtros">
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
                        <th class="px-6 py-4 font-semibold">C.I.</th>
                        <th class="px-6 py-4 font-semibold">Nombre Completo</th>
                        <th class="px-6 py-4 font-semibold">Teléfono</th>
                        <th class="px-6 py-4 font-semibold">Bocamina Asignada</th>
                        <th class="px-6 py-4 font-semibold">Estado</th>
                        <th class="px-6 py-4 font-semibold no-print">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-sm text-slate-300">
                    @forelse($trabajadores as $trabajador)
                        <tr class="hover:bg-slate-900/10 transition duration-150">
                            <td class="px-6 py-4 font-mono font-medium text-slate-200">{{ $trabajador->ci }}</td>
                            <td class="px-6 py-4 font-medium text-slate-100">{{ $trabajador->nombre }}</td>
                            <td class="px-6 py-4 font-mono">{{ $trabajador->telefono ?: '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-800 text-slate-300 border border-slate-700">
                                    <i class="fa-solid fa-mountain mr-1.5 text-amber-500"></i> {{ $trabajador->bocamina->nombre }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $trabajador->estado === 'activo' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/25' : 'bg-slate-800 text-slate-400 border border-slate-700' }}">
                                    {{ ucfirst($trabajador->estado) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 no-print">
                                <div class="flex space-x-2">
                                    <button @click="openEdit({{ $trabajador }})" class="p-2 rounded-lg bg-slate-800/80 hover:bg-amber-500/20 text-slate-300 hover:text-amber-400 border border-slate-700/60 hover:border-amber-500/40 transition-all duration-300 hover:scale-105 active:scale-95 shadow-sm" title="Editar">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    <form action="{{ route('trabajadores.destroy', $trabajador->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este trabajador?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg bg-slate-800/80 hover:bg-red-500/20 text-slate-300 hover:text-red-400 border border-slate-700/60 hover:border-red-500/40 transition-all duration-300 hover:scale-105 active:scale-95 shadow-sm" title="Eliminar">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-user-slash text-4xl mb-3 block text-slate-600"></i>
                                No se encontraron trabajadores.
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
                <h3 class="text-lg font-bold text-slate-100" x-text="editMode ? 'Editar Trabajador / Contratista' : 'Nuevo Trabajador / Contratista'"></h3>
                <button @click="openModal = false" class="text-slate-400 hover:text-slate-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form :action="editMode ? editActionUrl : '{{ route('trabajadores.store') }}'" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-6 space-y-4">
                    <div>
                        <label for="modal_ci" class="block text-sm font-medium text-slate-300">Cédula de Identidad (C.I.)</label>
                        <input id="modal_ci" name="ci" type="text" required x-model="ci"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono"
                               placeholder="Ej. 1029384-LP">
                    </div>
                    <div>
                        <label for="modal_nombre" class="block text-sm font-medium text-slate-300">Nombre Completo</label>
                        <input id="modal_nombre" name="nombre" type="text" required x-model="nombre"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm"
                               placeholder="Ej. Juan Carlos Pérez">
                        <span class="text-[10px] text-slate-450 block mt-1"><i class="fa-solid fa-info-circle mr-1"></i> Cada nombre/apellido debe iniciar con <strong>MAYÚSCULA</strong> (Ej. Juan Carlos Pérez).</span>
                        <div x-show="nombre && !isNombreValido()" class="text-red-400 text-[10px] mt-1 font-semibold flex items-center" x-cloak>
                            <i class="fa-solid fa-circle-xmark mr-1"></i> Cada palabra debe comenzar con mayúscula.
                        </div>
                    </div>
                    <div>
                        <label for="modal_telefono" class="block text-sm font-medium text-slate-300">Teléfono / Celular (Opcional)</label>
                        <input id="modal_telefono" name="telefono" type="text" x-model="telefono"
                               @input="telefono = telefono.replace(/[^0-9]/g, '')"
                               maxlength="8"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono"
                               placeholder="Ej. 71234567">
                        <span class="text-[10px] text-slate-450 block mt-1"><i class="fa-solid fa-info-circle mr-1"></i> Solo números, exactamente 8 dígitos.</span>
                        <div x-show="telefono && !isTelefonoValido()" class="text-red-400 text-[10px] mt-1 font-semibold flex items-center" x-cloak>
                            <i class="fa-solid fa-circle-xmark mr-1"></i> Debe tener exactamente 8 números.
                        </div>
                    </div>
                    <div>
                        <label for="modal_bocamina" class="block text-sm font-medium text-slate-300">Bocamina Asignada</label>
                        <select id="modal_bocamina" name="bocamina_id" required x-model="bocamina_id"
                                class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                            <option value="">Seleccione una bocamina...</option>
                            @foreach($bocaminas as $bocamina)
                                <option value="{{ $bocamina->id }}">{{ $bocamina->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="modal_estado" class="block text-sm font-medium text-slate-300">Estado</label>
                        <select id="modal_estado" name="estado" required x-model="estado"
                                class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-slate-800/80 bg-slate-900/40 flex justify-end space-x-3">
                    <button type="button" @click="openModal = false" class="px-4 py-2 text-sm font-medium rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-350 border border-slate-700/60 hover:border-slate-600 transition-all duration-200">
                        Cancelar
                    </button>
                    <button type="submit" 
                            :disabled="!nombre || !ci || !bocamina_id || !isNombreValido() || !isTelefonoValido()"
                            :class="(!nombre || !ci || !bocamina_id || !isNombreValido() || !isTelefonoValido()) ? 'opacity-50 cursor-not-allowed' : ''"
                            class="btn-vibrant-amber px-4 py-2 text-sm font-bold rounded-lg shadow-lg transition-all duration-150">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
