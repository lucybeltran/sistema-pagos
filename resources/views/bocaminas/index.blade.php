@extends('layouts.app')

@section('title', 'Bocaminas')

@section('content')
<div x-data="{ 
    openModal: false, 
    editMode: false, 
    bocaminaId: null,
    nombre: '', 
    descripcion: '', 
    editActionUrl: '',
    openCreate() {
        this.editMode = false;
        this.bocaminaId = null;
        this.nombre = '';
        this.descripcion = '';
        this.openModal = true;
    },
    openEdit(bocamina) {
        this.editMode = true;
        this.bocaminaId = bocamina.id;
        this.nombre = bocamina.nombre;
        this.descripcion = bocamina.descripcion || '';
        this.editActionUrl = '/bocaminas/' + bocamina.id;
        this.openModal = true;
    }
}" class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100">Administración de Bocaminas</h1>
            <p class="text-sm text-slate-400 mt-1">Registra y administra las diferentes bocaminas o frentes de trabajo.</p>
        </div>
        <button @click="openCreate()" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-sm font-bold text-slate-950 transition duration-150 shadow-lg shadow-orange-500/10">
            <i class="fa-solid fa-plus mr-2"></i> Nueva Bocamina
        </button>
    </div>

    <!-- Grid Layout -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($bocaminas as $bocamina)
            <div class="glass-card rounded-xl p-6 relative overflow-hidden group hover:border-amber-500/25 transition duration-300 flex flex-col justify-between h-48">
                <div>
                    <div class="flex justify-between items-start">
                        <h3 class="text-lg font-bold text-slate-100 group-hover:text-amber-500 transition duration-200">{{ $bocamina->nombre }}</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-500 border border-amber-500/20">
                            ID: {{ $bocamina->id }}
                        </span>
                    </div>
                    <p class="text-sm text-slate-400 mt-2 line-clamp-2">{{ $bocamina->descripcion ?: 'Sin descripción' }}</p>
                </div>
                
                <div class="flex items-center justify-between border-t border-slate-800/60 pt-4 mt-4 w-full">
                    <a href="{{ route('trabajadores.index', ['bocamina_id' => $bocamina->id]) }}" class="text-xs text-slate-400 hover:text-amber-500 font-medium flex items-center transition duration-150" title="Ver trabajadores/contratistas de esta bocamina">
                        <i class="fa-solid fa-user-group mr-1.5 text-amber-500/80"></i> {{ $bocamina->trabajadores_count }} trabajadores / contratistas
                    </a>
                    <div class="flex space-x-2">
                        <button @click="openEdit({{ $bocamina }})" class="p-1.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-amber-500 transition duration-150" title="Editar">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                        </button>
                        <form action="{{ route('bocaminas.destroy', $bocamina->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta bocamina?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded bg-slate-800 hover:bg-red-950 text-slate-300 hover:text-red-400 transition duration-150" title="Eliminar">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 flex flex-col items-center justify-center glass-card rounded-xl">
                <i class="fa-solid fa-mountain text-slate-600 text-5xl mb-4"></i>
                <p class="text-slate-400">No hay bocaminas registradas.</p>
                <button @click="openCreate()" class="text-xs text-amber-500 underline font-medium mt-2 hover:text-amber-400">Registrar la primera bocamina</button>
            </div>
        @endforelse
    </div>

    <!-- AlpineJS Modal (Create/Edit) -->
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div @click.away="openModal = false" class="glass-card w-full max-w-md rounded-2xl overflow-hidden shadow-2xl border border-slate-800/80 relative">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-800/80 flex items-center justify-between bg-slate-900/60">
                <h3 class="text-lg font-bold text-slate-100" x-text="editMode ? 'Editar Bocamina' : 'Nueva Bocamina'"></h3>
                <button @click="openModal = false" class="text-slate-400 hover:text-slate-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form :action="editMode ? editActionUrl : '{{ route('bocaminas.store') }}'" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-6 space-y-4">
                    <div>
                        <label for="modal_nombre" class="block text-sm font-medium text-slate-300">Nombre de la Bocamina</label>
                        <input id="modal_nombre" name="nombre" type="text" required x-model="nombre"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm"
                               placeholder="Ej. Bocamina Rosario">
                    </div>
                    <div>
                        <label for="modal_descripcion" class="block text-sm font-medium text-slate-300">Descripción / Ubicación</label>
                        <textarea id="modal_descripcion" name="descripcion" rows="3" x-model="descripcion"
                                  class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm"
                                  placeholder="Detalles sobre la bocamina..."></textarea>
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
