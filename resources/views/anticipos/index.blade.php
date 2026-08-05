@extends('layouts.app')

@section('title', 'Anticipos')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100">Historial de Anticipos (Adelantos)</h1>
            <p class="text-sm text-slate-400 mt-1">Historial de adelantos de dinero registrados. Se descuentan automáticamente en las liquidaciones semanales.</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="glass-card rounded-xl p-6 no-print">
        <form id="filterFormAnticipos" action="{{ route('anticipos.index') }}" method="GET" onsubmit="event.preventDefault(); submitFilterRealTime(this);" class="grid grid-cols-1 gap-4 sm:grid-cols-4 items-end">
            <div>
                <label for="bocamina_id_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Bocamina</label>
                <select name="bocamina_id" id="bocamina_id_filter" 
                        onchange="submitFilterRealTime(this.form)"
                        class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 text-sm">
                    <option value="">Todas las Bocaminas</option>
                    @foreach($bocaminas as $bocamina)
                        <option value="{{ $bocamina->id }}" {{ request('bocamina_id') == $bocamina->id ? 'selected' : '' }}>{{ $bocamina->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="trabajador_id_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Trabajador / Contratista</label>
                <select name="trabajador_id" id="trabajador_id_filter" 
                        onchange="submitFilterRealTime(this.form)"
                        class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 text-sm">
                    <option value="">Todos los Trabajadores / Contratistas</option>
                    @foreach($trabajadores as $trabajador)
                        <option value="{{ $trabajador->id }}" {{ request('trabajador_id') == $trabajador->id ? 'selected' : '' }}>{{ $trabajador->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="estado_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Estado de Saldo</label>
                <select name="estado" id="estado_filter" 
                        onchange="submitFilterRealTime(this.form)"
                        class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 text-sm">
                    <option value="">Todos los Anticipos</option>
                    <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Con Saldo Pendiente</option>
                    <option value="pagado" {{ request('estado') === 'pagado' ? 'selected' : '' }}>Totalmente Descontados</option>
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="button" onclick="limpiarFiltrosAnticipos()" class="btn-vibrant-warm flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-bold rounded-lg shadow-lg" title="Limpiar Filtros">
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
                        <th class="px-6 py-4 font-semibold">ID</th>
                        <th class="px-6 py-4 font-semibold">Fecha y Hora</th>
                        <th class="px-6 py-4 font-semibold">Trabajador / Contratista</th>
                        <th class="px-6 py-4 font-semibold">Bocamina</th>
                        <th class="px-6 py-4 font-semibold">Monto Original</th>
                        <th class="px-6 py-4 font-semibold">Saldo Restante</th>
                        <th class="px-6 py-4 font-semibold">Estado</th>
                        <th class="px-6 py-4 font-semibold no-print">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-sm text-slate-300">
                    @forelse($anticipos as $anticipo)
                        <tr class="hover:bg-slate-900/10 transition duration-150">
                            <td class="px-6 py-4 font-mono text-xs">{{ $anticipo->id }}</td>
                            <td class="px-6 py-4 font-mono text-xs">
                                {{ $anticipo->fecha->format('d/m/Y') }}
                                <span class="text-[10px] text-slate-500 block">{{ $anticipo->created_at->format('H:i:s') }}</span>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-100">{{ $anticipo->trabajador->nombre }}</td>
                            <td class="px-6 py-4 text-xs">{{ $anticipo->trabajador->bocamina ? $anticipo->trabajador->bocamina->nombre : 'Sin Bocamina' }}</td>
                            <td class="px-6 py-4 font-mono font-medium text-slate-200">Bs. {{ number_format($anticipo->monto, 2) }}</td>
                            <td class="px-6 py-4 font-mono font-bold {{ $anticipo->saldo > 0 ? 'text-rose-450' : 'text-slate-400' }}">Bs. {{ number_format($anticipo->saldo, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $anticipo->saldo == 0 ? 'bg-slate-800 text-slate-400 border border-slate-700' : 'bg-red-500/10 text-red-400 border border-red-500/25' }}">
                                    {{ $anticipo->saldo == 0 ? 'Descontado' : 'Pendiente' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 no-print">
                                <div class="flex space-x-2">
                                    <a href="{{ route('anticipos.recibo', $anticipo->id) }}" target="_blank"
                                       class="p-1.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-emerald-500 transition duration-150" title="Imprimir Recibo de Anticipo">
                                        <i class="fa-solid fa-print text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-user-slash text-4xl mb-3 block text-slate-600"></i>
                                No se encontraron anticipos registrados con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function submitFilterRealTime(form) {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();
        const url = form.action + '?' + params;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTable = doc.getElementById('table-container');
            if (newTable) {
                document.getElementById('table-container').innerHTML = newTable.innerHTML;
            }
            window.history.replaceState({}, '', url);
        })
        .catch(err => console.error('Error al filtrar en tiempo real:', err));
    }

    function limpiarFiltrosAnticipos() {
        document.getElementById('bocamina_id_filter').value = '';
        document.getElementById('trabajador_id_filter').value = '';
        document.getElementById('estado_filter').value = '';
        const form = document.getElementById('filterFormAnticipos');
        submitFilterRealTime(form);
    }
</script>
@endpush
