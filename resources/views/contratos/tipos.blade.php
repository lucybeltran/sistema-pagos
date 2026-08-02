@extends('layouts.app')

@section('title', 'Tipos de Contrato')

@section('content')
<div x-data="{ 
    openModal: false, 
    editMode: false, 
    contratoId: null,
    nombre: '', 
    estado: 'activo', 
    editActionUrl: '',
    
    openCreate() {
        this.editMode = false;
        this.contratoId = null;
        this.nombre = '';
        this.estado = 'activo';
        this.openModal = true;
    },
    openEdit(contrato) {
        this.editMode = true;
        this.contratoId = contrato.id;
        this.nombre = contrato.nombre;
        this.estado = contrato.estado;
        this.editActionUrl = '/tipos-contrato/' + contrato.id;
        this.openModal = true;
    }
}" class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100">Catálogo de Tipos de Contrato</h1>
            <p class="text-sm text-slate-400 mt-1">Administra los tipos de contrato (Por saco, Por volqueta, Por viaje, Mensual, etc.) asignables a los trabajadores.</p>
        </div>
        <button @click="openCreate()" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-sm font-bold text-white transition duration-150 shadow-lg shadow-sky-500/10 self-start">
            <i class="fa-solid fa-plus mr-2"></i> Nuevo Tipo de Contrato
        </button>
    </div>

    <!-- Filters Section -->
    <div class="glass-card rounded-xl p-6 no-print">
        <form action="{{ route('tipos-contrato.index') }}" method="GET" onsubmit="event.preventDefault(); submitFilterRealTime(this);" class="grid grid-cols-1 gap-4 sm:grid-cols-3 items-end">
            <div>
                <label for="buscar" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Buscar por Nombre</label>
                <input type="text" name="buscar" id="buscar" value="{{ request('buscar') }}" 
                       oninput="clearTimeout(searchDebounceTimeout); searchDebounceTimeout = setTimeout(() => submitFilterRealTime(this.form), 250)"
                       class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-sm"
                       placeholder="Ej. Por saco, por viaje...">
            </div>

            <div>
                <label for="estado_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Estado</label>
                <select name="estado" id="estado_filter" 
                        onchange="submitFilterRealTime(this.form)"
                        class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-sm">
                    <option value="">Todos los Estados</option>
                    <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activos</option>
                    <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="button" onclick="document.getElementById('buscar').value = ''; document.getElementById('estado_filter').value = ''; submitFilterRealTime(this.form);" class="btn-vibrant-warm flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-bold rounded-lg shadow-lg" title="Limpiar Filtros">
                    <i class="fa-solid fa-rotate-left mr-2"></i> Limpiar
                </button>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div id="table-container" class="glass-card rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800">
                <thead>
                    <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900/40">
                        <th class="px-6 py-4 font-semibold w-24">ID</th>
                        <th class="px-6 py-4 font-semibold">Nombre del Tipo de Contrato</th>
                        <th class="px-6 py-4 font-semibold text-center w-40">Estado</th>
                        <th class="px-6 py-4 font-semibold no-print text-center w-40">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-sm text-slate-300">
                    @forelse($contratos as $contrato)
                        <tr class="hover:bg-slate-900/10 transition duration-150">
                            <td class="px-6 py-4 font-mono text-slate-400 font-bold text-xs">{{ str_pad($contrato->id, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-4 font-medium text-slate-100">{{ $contrato->nombre }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $contrato->estado === 'activo' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/25' : 'bg-slate-800 text-slate-450 border border-slate-700' }}">
                                    {{ $contrato->estado === 'activo' ? 'Activo' : 'Inactivo (Desactivado)' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 no-print text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="relative group/btn">
                                        <button @click="openEdit({{ $contrato }})" 
                                            class="w-8 h-8 rounded-xl flex items-center justify-center bg-gradient-to-br from-indigo-500 to-violet-600 hover:from-indigo-400 hover:to-violet-500 text-white shadow-md shadow-indigo-500/25 hover:shadow-indigo-500/50 hover:scale-110 active:scale-95 transition-all duration-200">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </button>
                                        <span class="absolute -bottom-7 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-lg bg-slate-900 text-[10px] font-bold text-slate-200 whitespace-nowrap opacity-0 group-hover/btn:opacity-100 transition-all duration-150 pointer-events-none border border-slate-700/60 shadow-xl z-50">Editar</span>
                                    </div>
                                    <div class="relative group/del">
                                        <form action="{{ route('tipos-contrato.destroy', $contrato->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                class="w-8 h-8 rounded-xl flex items-center justify-center bg-gradient-to-br from-rose-500 to-red-600 hover:from-rose-400 hover:to-red-500 text-white shadow-md shadow-rose-500/25 hover:shadow-rose-500/50 hover:scale-110 active:scale-95 transition-all duration-200">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                        <span class="absolute -bottom-7 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-lg bg-slate-900 text-[10px] font-bold text-rose-400 whitespace-nowrap opacity-0 group-hover/del:opacity-100 transition-all duration-150 pointer-events-none border border-rose-500/30 shadow-xl z-50">Eliminar</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-file-contract text-4xl mb-3 block text-slate-600"></i>
                                No se encontraron tipos de contrato registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- AlpineJS Modal (Create/Edit - ERP Design) -->
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div @click.away="openModal = false" class="glass-card w-full max-w-md rounded-2xl overflow-hidden shadow-2xl border border-slate-800/80 relative">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-800/60 flex items-center justify-between bg-slate-900/60">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center">
                        <i class="fa-solid fa-file-signature text-sm"></i>
                    </div>
                    <h3 class="text-md font-bold text-slate-100" x-text="editMode ? 'Editar Tipo de Contrato' : 'Nuevo Tipo de Contrato'"></h3>
                </div>
                <button @click="openModal = false" class="text-slate-400 hover:text-slate-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form :action="editMode ? editActionUrl : '{{ route('tipos-contrato.store') }}'" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-6 space-y-4">
                    <div>
                        <label for="modal_nombre" class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Nombre del Tipo de Contrato <span class="text-red-500">*</span></label>
                        <input id="modal_nombre" name="nombre" type="text" required x-model="nombre"
                               class="block w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-sm"
                               placeholder="Ej. Por saco, Por viaje...">
                    </div>

                    <div>
                        <label for="modal_estado" class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Estado <span class="text-red-500">*</span></label>
                        <select id="modal_estado" name="estado" required x-model="estado"
                                class="block w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-sm">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Desactivado (Inactivo)</option>
                        </select>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-slate-800/60 bg-slate-900/40 flex justify-end space-x-3">
                    <button type="button" @click="openModal = false" class="px-4 py-2.5 text-xs font-bold uppercase tracking-wider rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-350 border border-slate-700/60 transition-all duration-150">
                        Cancelar
                    </button>
                    <button type="submit" 
                            :disabled="!nombre"
                            :class="(!nombre) ? 'opacity-50 cursor-not-allowed' : ''"
                            class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-xs font-bold uppercase tracking-wider text-white transition duration-150 shadow-lg shadow-sky-500/10">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
