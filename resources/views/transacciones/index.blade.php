@extends('layouts.app')

@section('title', 'Compra y Venta de Minerales')

@section('content')
<style>
/* ============================================================
   DISEÑO ERP PREMIUM - MÓDULO DE MINERALES (COMPRAS Y VENTAS)
   ============================================================ */

.erp-container {
    --erp-border: rgba(255, 255, 255, 0.06);
    --erp-bg-card: rgba(15, 23, 42, 0.45);
    --erp-text-main: #f8fafc;
    --erp-text-muted: #64748b;
    --erp-glow-amber: 0 0 20px rgba(245, 158, 11, 0.12);
    --erp-glow-emerald: 0 0 20px rgba(16, 185, 129, 0.12);
    --erp-depth-3d: 0 10px 25px -5px rgba(0,0,0,0.3), 0 8px 10px -6px rgba(0,0,0,0.3);
}

.light-theme .erp-container {
    --erp-border: rgba(15, 23, 42, 0.08);
    --erp-bg-card: #ffffff;
    --erp-text-main: #0f172a;
    --erp-text-muted: #64748b;
    --erp-glow-amber: 0 0 20px rgba(245, 158, 11, 0.06);
    --erp-glow-emerald: 0 0 20px rgba(16, 185, 129, 0.06);
    --erp-depth-3d: 0 10px 20px -3px rgba(0,0,0,0.06), 0 4px 6px -2px rgba(0,0,0,0.04);
}

/* Card 3D Sutil */
.erp-card {
    background: var(--erp-bg-card);
    border: 1.5px solid var(--erp-border);
    border-radius: 18px;
    box-shadow: var(--erp-depth-3d);
    transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}
.erp-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 20px 35px -8px rgba(0,0,0,0.35), 0 10px 20px -6px rgba(0,0,0,0.3);
}
.light-theme .erp-card:hover {
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.08), 0 10px 10px -5px rgba(0,0,0,0.04);
}

/* Resplandores temáticos sutiles */
.compra-glow:hover {
    border-color: rgba(245, 158, 11, 0.35);
    box-shadow: var(--erp-glow-amber), var(--erp-depth-3d);
}
.venta-glow:hover {
    border-color: rgba(16, 185, 129, 0.35);
    box-shadow: var(--erp-glow-emerald), var(--erp-depth-3d);
}

/* Tab Navigation */
.erp-tabs-bar {
    display: flex;
    gap: 8px;
    padding: 8px;
    border-radius: 20px;
    border: 1.5px solid var(--erp-border);
    background: var(--erp-bg-card);
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
}
.erp-tab-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 14px;
    border-radius: 14px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--erp-text-muted);
    transition: all 0.25s ease;
    background: transparent;
    border: none;
    cursor: pointer;
}
.erp-tab-btn:hover {
    color: var(--erp-text-main);
    background: rgba(255,255,255,0.04);
}
.light-theme .erp-tab-btn:hover {
    background: rgba(0,0,0,0.03);
}
.erp-tab-btn.active-compra {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #ffffff !important;
    box-shadow: 0 4px 15px rgba(245,158,11,0.3);
}
.erp-tab-btn.active-venta {
    background: linear-gradient(135deg, #10b981, #059669);
    color: #ffffff !important;
    box-shadow: 0 4px 15px rgba(16,185,129,0.3);
}
.erp-tab-btn.active-stock {
    background: linear-gradient(135deg, #06b6d4, #0891b2);
    color: #ffffff !important;
    box-shadow: 0 4px 15px rgba(6,182,212,0.3);
}
.erp-tab-btn.active-reportes {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    color: #ffffff !important;
    box-shadow: 0 4px 15px rgba(99,102,241,0.3);
}

/* Indicators */
.badge-in { background: rgba(245,158,11,0.12); color: #f59e0b; border: 1.5px solid rgba(245,158,11,0.22); }
.badge-out { background: rgba(16,185,129,0.12); color: #10b981; border: 1.5px solid rgba(16,185,129,0.22); }

/* Custom Form Styles */
.erp-label {
    display: block; font-size: 10px; font-weight: 800; text-transform: uppercase;
    letter-spacing: 0.08em; color: var(--erp-text-muted); margin-bottom: 8px;
}
.erp-input {
    width: 100%; padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 600;
    border: 1.5px solid var(--erp-border); background: var(--erp-bg-card); color: var(--erp-text-main);
    outline: none; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.erp-input:focus {
    border-color: #f59e0b;
    box-shadow: 0 0 0 3px rgba(245,158,11,0.15);
}
.erp-input-select {
    appearance: none;
    cursor: pointer;
}
.erp-input-select option {
    background: #0f172a; color: #f8fafc;
}
.light-theme .erp-input-select option {
    background: #ffffff; color: #0f172a;
}

/* 3D Button style */
.btn-3d-amber {
    box-shadow: 0 4px 0 #b45309, 0 8px 15px rgba(245,158,11,0.25);
    transform: translateY(0);
    transition: all 0.15s ease;
}
.btn-3d-amber:active {
    transform: translateY(3px);
    box-shadow: 0 1px 0 #b45309, 0 4px 6px rgba(245,158,11,0.2);
}
.btn-3d-emerald {
    box-shadow: 0 4px 0 #047857, 0 8px 15px rgba(16,185,129,0.25);
    transform: translateY(0);
    transition: all 0.15s ease;
}
.btn-3d-emerald:active {
    transform: translateY(3px);
    box-shadow: 0 1px 0 #047857, 0 4px 6px rgba(16,185,129,0.2);
}

/* Progress bar stock */
.stock-bar-bg { width: 100%; height: 7px; background: rgba(255,255,255,0.06); border-radius: 99px; overflow: hidden; }
.light-theme .stock-bar-bg { background: #e2e8f0; }
.stock-bar-fill { height: 100%; border-radius: 99px; transition: width 0.4s ease; }
</style>

<div class="erp-container space-y-6"
     x-data="{
        activeTab: '{{ request('tab', 'compras') }}',
        openCompraModal: false,
        openVentaModal: false,
        openDetailModal: false,
        isEditMode: false,
        actionUrl: '',
        
        // Form Compra (Lote) Fields
        compraId: '',
        compraFecha: '{{ now()->toDateString() }}',
        compraProveedor: '',
        compraBocaminaId: '',
        compraPresentacion: 'Sacos',
        compraPresentacionOtro: '',
        compraCantidad: '',
        compraPeso: '',
        compraPrecio: '',
        compraTotal: 0,
        compraObservacion: '',
        compraAnalisis: [], // [{mineral: '', ley: ''}]

        // Form Venta Fields
        ventaId: '',
        ventaFecha: '{{ now()->toDateString() }}',
        ventaCliente: '',
        ventaDestino: '',
        ventaObservacion: '',
        ventaLotes: [], // [{ lote_id: '', cantidad: '', peso_neto_seco: '', precio_unidad: '', monto_total: 0, info: null }]

        // Fields used ONLY for edit mode (single-row update fallback):
        ventaLoteId: '',
        ventaCantidad: '',
        ventaPeso: '',
        ventaPrecio: '',
        ventaTotal: 0,
        loteInfo: null,

        // Modal de Ficha/Detalle
        fichaLote: null,

        // Methods Compra
        initCompra() {
            this.isEditMode = false;
            this.actionUrl = '{{ route('transacciones-minerales.store') }}';
            this.compraId = '';
            this.compraFecha = '{{ now()->toDateString() }}';
            this.compraProveedor = '';
            this.compraBocaminaId = '';
            this.compraPresentacion = 'Sacos';
            this.compraPresentacionOtro = '';
            this.compraCantidad = '';
            this.compraPeso = '';
            this.compraPrecio = '';
            this.compraTotal = 0;
            this.compraObservacion = '';
            this.compraAnalisis = [{ mineral: 'Zinc', ley: 48.50 }, { mineral: 'Plomo', ley: 12.30 }, { mineral: 'Plata', ley: 1.80 }];
            this.openCompraModal = true;
        },

        editCompra(item) {
            this.isEditMode = true;
            this.actionUrl = '/transacciones-minerales/' + item.id;
            this.compraId = item.id;
            this.compraFecha = item.fecha.split('T')[0];
            this.compraProveedor = item.cliente_proveedor;
            this.compraBocaminaId = item.bocamina_id || '';
            this.compraPresentacion = item.presentacion;
            this.compraPresentacionOtro = item.presentacion_otro || '';
            this.compraCantidad = item.cantidad;
            this.compraPeso = item.peso_neto_seco;
            this.compraPrecio = item.precio_unidad;
            this.compraTotal = item.monto_total;
            this.compraObservacion = item.observacion || '';
            this.compraAnalisis = item.analisis.map(a => ({ mineral: a.mineral, ley: parseFloat(a.ley) }));
            this.openCompraModal = true;
        },

        addMineral() {
            this.compraAnalisis.push({ mineral: '', ley: '' });
        },

        removeMineral(index) {
            this.compraAnalisis.splice(index, 1);
        },

        calcCompraTotal() {
            let p = parseFloat(this.compraPeso) || 0;
            let pr = parseFloat(this.compraPrecio) || 0;
            this.compraTotal = (p * pr).toFixed(2);
        },

        // Methods Venta (Multi-Lote Creation & Single-Lote Edit)
        initVenta() {
            this.isEditMode = false;
            this.actionUrl = '{{ route('transacciones-minerales.store') }}';
            this.ventaId = '';
            this.ventaFecha = '{{ now()->toDateString() }}';
            this.ventaCliente = '';
            this.ventaDestino = '';
            this.ventaObservacion = '';
            this.ventaLotes = [{ lote_id: '', cantidad: '', peso_neto_seco: '', precio_unidad: '', monto_total: 0, info: null }];
            this.openVentaModal = true;
        },

        editVenta(item) {
            this.isEditMode = true;
            this.actionUrl = '/transacciones-minerales/' + item.id;
            this.ventaId = item.id;
            this.ventaFecha = item.fecha.split('T')[0];
            this.ventaCliente = item.cliente_proveedor;
            this.ventaDestino = item.destino || '';
            this.ventaLoteId = item.lote_id;
            this.ventaCantidad = item.cantidad;
            this.ventaPeso = item.peso_neto_seco;
            this.ventaPrecio = item.precio_unidad;
            this.ventaTotal = item.monto_total;
            this.ventaObservacion = item.observacion || '';
            
            // Load lote details for the single edited sale record
            if (item.lote) {
                this.loteInfo = {
                    presentacion: item.lote.presentacion,
                    presentacion_otro: item.lote.presentacion_otro,
                    peso_disponible: parseFloat(item.lote.peso_disponible) + parseFloat(item.peso_neto_seco),
                    cantidad_disponible: parseFloat(item.lote.cantidad_disponible) + parseFloat(item.cantidad),
                    analisis: item.lote.analisis
                };
            }
            this.openVentaModal = true;
        },

        addVentaLote() {
            this.ventaLotes.push({ lote_id: '', cantidad: '', peso_neto_seco: '', precio_unidad: '', monto_total: 0, info: null });
        },

        removeVentaLote(index) {
            this.ventaLotes.splice(index, 1);
        },

        onVentaLoteSelected(index) {
            const lotId = this.ventaLotes[index].lote_id;
            if (!lotId) {
                this.ventaLotes[index].info = null;
                return;
            }
            // Fetch Lote data
            fetch('/transacciones-minerales/' + lotId)
                .then(r => r.json())
                .then(data => {
                    this.ventaLotes[index].info = {
                        presentacion: data.presentacion,
                        presentacion_otro: data.presentacion_otro,
                        peso_disponible: parseFloat(data.peso_disponible),
                        cantidad_disponible: parseFloat(data.cantidad_disponible),
                        analisis: data.analisis
                    };
                });
        },

        calcVentaItemTotal(index) {
            let p = parseFloat(this.ventaLotes[index].peso_neto_seco) || 0;
            let pr = parseFloat(this.ventaLotes[index].precio_unidad) || 0;
            this.ventaLotes[index].monto_total = (p * pr).toFixed(2);
        },

        calcVentaTotal() {
            let p = parseFloat(this.ventaPeso) || 0;
            let pr = parseFloat(this.ventaPrecio) || 0;
            this.ventaTotal = (p * pr).toFixed(2);
        },

        // Ficha Detalles Lote
        showFicha(id) {
            fetch('/transacciones-minerales/' + id)
                .then(r => r.json())
                .then(data => {
                    this.fichaLote = data;
                    this.openDetailModal = true;
                });
        }
     }"
     x-init="
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('create') === '1' || urlParams.get('open_modal') === '1') {
            if (activeTab === 'compras') initCompra();
            if (activeTab === 'ventas') initVenta();
        }
     ">

    {{-- ═══════════════ HEADER ═══════════════ --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black flex items-center gap-2.5" style="color:var(--text-main)">
                <span style="width:36px;height:36px;background:linear-gradient(135deg,#6366f1,#4f46e5);border-radius:10px;display:inline-flex;align-items:center;justify-content:center;box-shadow: 0 4px 15px rgba(99,102,241,0.2);">
                    <i class="fa-solid fa-boxes-stacked text-white text-sm animate-pulse"></i>
                </span>
                <span x-show="activeTab === 'compras'">Compras - Lotes de Minerales</span>
                <span x-show="activeTab === 'ventas'">Ventas de Minerales</span>
                <span x-show="activeTab === 'stock'">Stock e Inventario de Lotes</span>
                <span x-show="activeTab === 'reportes'">Reportes de Comercialización</span>
            </h1>
            <p class="text-sm mt-1 ml-11" style="color:var(--text-muted)">
                <span x-show="activeTab === 'compras'">Registra compras de minerales, genera números de lote únicos (LT-000001) e ingresa análisis de laboratorio.</span>
                <span x-show="activeTab === 'ventas'">Registra despachos y salidas utilizando los lotes disponibles en almacén.</span>
                <span x-show="activeTab === 'stock'">Consulta la disponibilidad, saldo de peso y valor en inventario de cada lote.</span>
                <span x-show="activeTab === 'reportes'">Analiza volumen comercializado, ingresos, egresos y balances financieros.</span>
            </p>
        </div>

        <div class="flex gap-3 ml-11 md:ml-0">
            <button x-show="activeTab === 'compras' || activeTab === 'stock'" @click="initCompra()"
                    class="px-5 py-3 rounded-xl font-black text-xs uppercase tracking-wider bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white btn-3d-amber flex items-center gap-2 transition duration-200">
                <i class="fa-solid fa-file-import text-sm"></i> 📥 Registrar Compra (Lote)
            </button>
            <button x-show="activeTab === 'ventas' || activeTab === 'stock'" @click="initVenta()"
                    class="px-5 py-3 rounded-xl font-black text-xs uppercase tracking-wider bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white btn-3d-emerald flex items-center gap-2 transition duration-200">
                <i class="fa-solid fa-file-export text-sm"></i> 📤 Registrar Venta
            </button>
        </div>
    </div>

    {{-- Error displaying block --}}
    @if($errors->any())
        <div class="p-4 bg-rose-100 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-450 rounded-xl text-xs font-semibold">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ═══════════════ NAVIGATION TABS ═══════════════ --}}
    <div class="erp-tabs-bar">
        <button class="erp-tab-btn" :class="activeTab === 'compras' ? 'active-compra' : ''" @click="activeTab = 'compras'">
            <i class="fa-solid fa-circle-arrow-down"></i>
            <span>Compras (Lotes)</span>
        </button>
        <button class="erp-tab-btn" :class="activeTab === 'ventas' ? 'active-venta' : ''" @click="activeTab = 'ventas'">
            <i class="fa-solid fa-circle-arrow-up"></i>
            <span>Ventas</span>
        </button>
        <button class="erp-tab-btn" :class="activeTab === 'stock' ? 'active-stock' : ''" @click="activeTab = 'stock'">
            <i class="fa-solid fa-boxes-stacked"></i>
            <span>Stock (Inventario)</span>
        </button>
        <button class="erp-tab-btn" :class="activeTab === 'reportes' ? 'active-reportes' : ''" @click="activeTab = 'reportes'">
            <i class="fa-solid fa-chart-line"></i>
            <span>Reportes Avanzados</span>
        </button>
    </div>

    {{-- ═══════════════ TAB 1: COMPRAS (LOTES) ═══════════════ --}}
    <div x-show="activeTab === 'compras'" class="space-y-6">
        <div class="erp-card">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800/80 flex items-center justify-between" style="background:rgba(255,255,255,0.02)">
                <h3 class="text-sm font-bold uppercase tracking-wider" style="color:var(--text-main)"><i class="fa-solid fa-warehouse mr-2 text-amber-500"></i> Lotes Ingresados al Almacén</h3>
                <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase bg-amber-500/10 text-amber-500">Entradas</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs uppercase tracking-wider" style="background:rgba(255,255,255,0.01); border-bottom:1.5px solid var(--erp-border)">
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Lote ID</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Fecha</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Proveedor</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Presentación</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Ley de Minerales</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Peso Original</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Stock Restante</th>
                            <th class="px-6 py-4 font-bold text-right" style="color:var(--erp-text-muted)">Total Pagado</th>
                            <th class="px-6 py-4 text-center" style="color:var(--erp-text-muted)">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @forelse($transacciones->where('tipo', 'compra') as $item)
                        @php
                            $pctStock = $item->peso_neto_seco > 0 ? ($item->peso_disponible / $item->peso_neto_seco) * 100 : 0;
                            $stockColor = $pctStock > 50 ? 'bg-emerald-500' : ($pctStock > 20 ? 'bg-amber-500' : 'bg-rose-500');
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/20 transition duration-150">
                            <td class="px-6 py-4 font-mono font-bold text-xs" style="color:var(--text-main)">
                                <a href="javascript:void(0)" @click="showFicha({{ $item->id }})" class="hover:underline text-indigo-400">LOT-{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</a>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs">{{ $item->fecha->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 font-bold" style="color:var(--text-main)">{{ $item->cliente_proveedor }}</td>
                            <td class="px-6 py-4 text-xs font-semibold">
                                {{ $item->presentacion === 'Otro' ? ($item->presentacion_otro ?: 'Otro') : $item->presentacion }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($item->analisis as $an)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-amber-500/10 dark:bg-amber-500/15 text-amber-500 dark:text-amber-400 border border-amber-500/25 inline-block shadow-sm">
                                            {{ $an->mineral }}: {{ number_format($an->ley, 2) }}%
                                        </span>
                                    @empty
                                        <span class="text-xs italic text-slate-500">Sin análisis</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs">{{ number_format($item->peso_neto_seco, 2) }} Kg</td>
                            <td class="px-6 py-4">
                                <div class="w-32">
                                    <div class="flex justify-between items-center text-[10px] font-mono mb-1">
                                        <span class="font-bold">{{ number_format($item->peso_disponible, 2) }} Kg</span>
                                        <span class="text-slate-400">{{ round($pctStock) }}%</span>
                                    </div>
                                    <div class="stock-bar-bg">
                                        <div class="stock-bar-fill {{ $stockColor }}" style="width: {{ $pctStock }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono font-extrabold text-right text-xs" style="color:var(--text-main)">Bs. {{ number_format($item->monto_total, 2) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center items-center gap-2">
                                    <button @click="showFicha({{ $item->id }})" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-indigo-500 hover:text-white transition" title="Ficha Técnica">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                    <button @click="editCompra({{ $item }})" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-amber-500 hover:text-white transition" title="Editar Lote">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>
                                    <form action="{{ route('transacciones-minerales.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de eliminar este lote?')" >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-rose-500 hover:text-white transition" title="Eliminar Lote">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-16 text-slate-500">
                                <i class="fa-solid fa-scale-balanced text-4xl mb-3 block opacity-30"></i>
                                No se registran lotes comprados en el almacén.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ═══════════════ TAB 2: VENTAS (SALIDAS) ═══════════════ --}}
    <div x-show="activeTab === 'ventas'" class="space-y-6">
        <div class="erp-card">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800/80 flex items-center justify-between" style="background:rgba(255,255,255,0.02)">
                <h3 class="text-sm font-bold uppercase tracking-wider" style="color:var(--text-main)"><i class="fa-solid fa-truck-loading mr-2 text-emerald-500"></i> Despachos y Salidas de Lotes</h3>
                <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase bg-emerald-500/10 text-emerald-500">Ventas</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs uppercase tracking-wider" style="background:rgba(255,255,255,0.01); border-bottom:1.5px solid var(--erp-border)">
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Venta ID</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Fecha</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Cliente</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Lote Origen</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Destino</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Cantidad Despachada</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Peso Despachado</th>
                            <th class="px-6 py-4 font-bold text-right" style="color:var(--erp-text-muted)">Total Venta</th>
                            <th class="px-6 py-4 text-center" style="color:var(--erp-text-muted)">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @forelse($transacciones->where('tipo', 'venta') as $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/20 transition duration-150">
                            <td class="px-6 py-4 font-mono font-bold text-xs" style="color:var(--text-main)">
                                SLD-{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-4 font-mono text-xs">{{ $item->fecha->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 font-bold" style="color:var(--text-main)">{{ $item->cliente_proveedor }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-indigo-400">
                                @if($item->lote)
                                    <a href="javascript:void(0)" @click="showFicha({{ $item->lote_id }})" class="hover:underline">LOT-{{ str_pad($item->lote_id, 5, '0', STR_PAD_LEFT) }}</a>
                                @else
                                    <span class="text-slate-500">Lote Eliminado</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold">{{ $item->destino ?: 'No registrado' }}</td>
                            <td class="px-6 py-4 font-mono text-xs">{{ number_format($item->cantidad, 2) }}</td>
                            <td class="px-6 py-4 font-mono text-xs">{{ number_format($item->peso_neto_seco, 2) }} Kg</td>
                            <td class="px-6 py-4 font-mono font-extrabold text-right text-xs" style="color:var(--text-main)">Bs. {{ number_format($item->monto_total, 2) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center items-center gap-2">
                                    <button @click="editVenta({{ $item }})" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-amber-500 hover:text-white transition" title="Editar Venta">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>
                                    <form action="{{ route('transacciones-minerales.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de cancelar esta venta? Se restituirá el stock del lote original.')" >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-rose-500 hover:text-white transition" title="Eliminar Venta">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-16 text-slate-500">
                                <i class="fa-solid fa-truck-loading text-4xl mb-3 block opacity-30"></i>
                                No se registran ventas/despachos en el almacén.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ═══════════════ TAB 3: STOCK (INVENTARIO) ═══════════════ --}}
    <div x-show="activeTab === 'stock'" class="space-y-6">


        <!-- Stock Table Card -->
        <div class="erp-card">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800/80 flex items-center justify-between" style="background:rgba(255,255,255,0.02)">
                <h3 class="text-sm font-bold uppercase tracking-wider" style="color:var(--text-main)"><i class="fa-solid fa-warehouse mr-2 text-cyan-500"></i> Inventario de Lotes en Almacén</h3>
                <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase bg-cyan-500/10 text-cyan-400">Stock</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs uppercase tracking-wider" style="background:rgba(255,255,255,0.01); border-bottom:1.5px solid var(--erp-border)">
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Lote ID</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Fecha</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Proveedor</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Presentación</th>
                            <th class="px-6 py-4 font-bold text-right" style="color:var(--erp-text-muted)">Peso Disponible</th>
                            <th class="px-6 py-4 font-bold text-right" style="color:var(--erp-text-muted)">Valor Estimado</th>
                            <th class="px-6 py-4 font-bold text-center" style="color:var(--erp-text-muted)">Estado</th>
                            <th class="px-6 py-4 font-bold text-center" style="color:var(--erp-text-muted)">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todosLosLotes as $lote)
                        @php
                            $soldWeight = $lote->ventas->sum('peso_neto_seco');
                            $hasSales = $lote->ventas->count() > 0;
                            
                            if ($lote->peso_disponible <= 0) {
                                $estado = 'Vendido';
                                $badgeClass = 'bg-slate-500/10 text-slate-400 border border-slate-500/20';
                            } elseif ($hasSales && $lote->peso_disponible < $lote->peso_neto_seco) {
                                $estado = 'Parcialmente Vendido';
                                $badgeClass = 'bg-amber-500/10 text-amber-500 border border-amber-500/20';
                            } else {
                                $estado = 'Disponible';
                                $badgeClass = 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20';
                            }
                            
                            $valorEstimado = $lote->peso_disponible * $lote->precio_unidad;
                        @endphp
                        <tr class="border-b border-slate-200 dark:border-slate-800/80 hover:bg-slate-500/[0.02] transition-colors duration-150">
                            <td class="px-6 py-4 font-mono font-bold text-sm">
                                <a href="javascript:void(0)" @click="showFicha({{ $lote->id }})" class="hover:underline text-cyan-400">LOT-{{ str_pad($lote->id, 5, '0', STR_PAD_LEFT) }}</a>
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold" style="color:var(--text-main)">
                                {{ $lote->fecha->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-350 dark:text-slate-200">
                                {{ $lote->cliente_proveedor }}
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold">
                                <span class="px-2 py-1 bg-slate-900 border border-slate-800 text-slate-350 rounded-md font-mono">
                                    {{ $lote->presentacion }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-mono font-bold text-xs">
                                {{ number_format($lote->peso_disponible, 2, ',', '.') }} Kg
                                <span class="block text-[10px] text-slate-500 font-semibold font-sans">Original: {{ number_format($lote->peso_neto_seco, 2, ',', '.') }} Kg</span>
                            </td>
                            <td class="px-6 py-4 text-right font-mono font-bold text-xs text-emerald-400">
                                Bs. {{ number_format($valorEstimado, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider inline-block {{ $badgeClass }}">
                                    {{ $estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button @click="showFicha({{ $lote->id }})" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-cyan-500 hover:text-white transition" title="Ver Detalle">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-16 text-slate-500">
                                <i class="fa-solid fa-boxes-stacked text-4xl mb-3 block opacity-30"></i>
                                No se registran lotes en el inventario.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ═══════════════ TAB 4: REPORTES DE MINERALES ═══════════════ --}}
    <div x-show="activeTab === 'reportes'" class="space-y-6">
        <!-- Executive Filter Box -->
        <div class="erp-card p-6">
            <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-200 dark:border-slate-800/80">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-sliders text-indigo-500 text-base"></i>
                    <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-800 dark:text-slate-100">Filtros de Búsqueda de Comercialización</h3>
                </div>
                <button type="button" 
                        onclick="const f = this.closest('div').nextElementSibling; f.reset(); Array.from(f.elements).forEach(e => e.value = ''); submitFilterRealTime(f);" 
                        class="px-3 py-1.5 rounded-xl bg-slate-800/40 hover:bg-slate-800 border border-slate-700/60 text-slate-300 hover:text-white text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-rotate-left text-amber-500 text-xs"></i> Limpiar Filtros
                </button>
            </div>

            <form action="{{ route('transacciones-minerales.index') }}" method="GET"
                  onchange="submitFilterRealTime(this)"
                  class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <input type="hidden" name="tab" value="reportes">

                <div>
                    <label class="erp-label">Tipo Movimiento</label>
                    <select name="tipo" class="premium-filter-input w-full">
                        <option value="">Todos los movimientos</option>
                        <option value="compra" {{ request('tipo') === 'compra' ? 'selected' : '' }}>Compras (Entradas)</option>
                        <option value="venta" {{ request('tipo') === 'venta' ? 'selected' : '' }}>Ventas (Salidas)</option>
                    </select>
                </div>

                <div>
                    <label class="erp-label">Proveedor / Cliente</label>
                    <input type="text" name="cliente_proveedor" value="{{ request('cliente_proveedor') }}"
                           placeholder="Búsqueda por nombre..." class="premium-filter-input w-full">
                </div>

                <div>
                    <label class="erp-label">Filtrar por Mineral</label>
                    <select name="mineral" class="premium-filter-input w-full">
                        <option value="">Cualquier Mineral</option>
                        <option value="Zinc" {{ request('mineral') === 'Zinc' ? 'selected' : '' }}>Zinc (Zn)</option>
                        <option value="Plomo" {{ request('mineral') === 'Plomo' ? 'selected' : '' }}>Plomo (Pb)</option>
                        <option value="Plata" {{ request('mineral') === 'Plata' ? 'selected' : '' }}>Plata (Ag)</option>
                        <option value="Cobre" {{ request('mineral') === 'Cobre' ? 'selected' : '' }}>Cobre (Cu)</option>
                        <option value="Estaño" {{ request('mineral') === 'Estaño' ? 'selected' : '' }}>Estaño (Sn)</option>
                    </select>
                </div>

                <div>
                    <label class="erp-label">Ley Mínima (%)</label>
                    <input type="number" step="0.01" name="ley" value="{{ request('ley') }}"
                           placeholder="Ej. 10.00" class="premium-filter-input w-full">
                </div>

                <div>
                    <label class="erp-label">Bocamina</label>
                    <select name="bocamina_id" class="premium-filter-input w-full">
                        <option value="">Todas las Bocaminas</option>
                        @foreach($bocaminas as $boc)
                            <option value="{{ $boc->id }}" {{ request('bocamina_id') == $boc->id ? 'selected' : '' }}>{{ $boc->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="erp-label">Tipo de Presentación</label>
                    <select name="presentacion" class="premium-filter-input w-full">
                        <option value="">Todas las Presentaciones</option>
                        <option value="Sacos" {{ request('presentacion') === 'Sacos' ? 'selected' : '' }}>Sacos</option>
                        <option value="Volqueta" {{ request('presentacion') === 'Volqueta' ? 'selected' : '' }}>Volqueta</option>
                        <option value="Concentrado" {{ request('presentacion') === 'Concentrado' ? 'selected' : '' }}>Concentrado</option>
                        <option value="Otro" {{ request('presentacion') === 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>

                <div x-data="{ rango: '{{ request('rango_fecha', (request('fecha_desde') || request('fecha_hasta')) ? 'personalizado' : '') }}' }" class="sm:col-span-2 lg:col-span-2">
                    <label class="erp-label">Periodo de Fecha</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-start">
                        <select name="rango_fecha" x-model="rango" class="premium-filter-input w-full">
                            <option value="">Todas las Fechas</option>
                            <option value="hoy">Hoy</option>
                            <option value="semanal">Esta Semana (Semanal)</option>
                            <option value="mensual">Este Mes (Mensual)</option>
                            <option value="personalizado">Personalizado...</option>
                        </select>

                        <div x-show="rango === 'personalizado'" x-cloak class="flex gap-2">
                            <div class="flex-1">
                                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" placeholder="Desde" class="premium-filter-input w-full !text-xs">
                            </div>
                            <div class="flex-1">
                                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" placeholder="Hasta" class="premium-filter-input w-full !text-xs">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Executive Summary Cards for Filtered Results -->
        @php
            $rptCompras = $transacciones->where('tipo', 'compra')->sum('monto_total');
            $rptVentas = $transacciones->where('tipo', 'venta')->sum('monto_total');
            $rptBalance = $rptVentas - $rptCompras;
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="erp-card p-4 rounded-xl border border-amber-500/20 bg-amber-500/5">
                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-amber-500">Total Compras Filtradas</span>
                <p class="text-xl font-black font-mono text-amber-400 mt-1">Bs. {{ number_format($rptCompras, 2) }}</p>
            </div>
            <div class="erp-card p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/5">
                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-emerald-500">Total Ventas Filtradas</span>
                <p class="text-xl font-black font-mono text-emerald-400 mt-1">Bs. {{ number_format($rptVentas, 2) }}</p>
            </div>
            <div class="erp-card p-4 rounded-xl border {{ $rptBalance >= 0 ? 'border-indigo-500/20 bg-indigo-500/5' : 'border-rose-500/20 bg-rose-500/5' }}">
                <span class="block text-[10px] font-extrabold uppercase tracking-wider {{ $rptBalance >= 0 ? 'text-indigo-400' : 'text-rose-400' }}">Balance Neto Filtrado</span>
                <p class="text-xl font-black font-mono mt-1 {{ $rptBalance >= 0 ? 'text-indigo-300' : 'text-rose-400' }}">Bs. {{ number_format($rptBalance, 2) }}</p>
            </div>
        </div>

        <div id="report-output" class="erp-card">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800/80 flex items-center justify-between" style="background:rgba(255,255,255,0.02)">
                <h3 class="text-sm font-bold uppercase tracking-wider" style="color:var(--text-main)"><i class="fa-solid fa-list-check mr-2 text-indigo-500"></i> Reporte Filtrado de Operaciones</h3>
                <div class="flex gap-2">
                    <button class="px-3 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-bold font-mono transition duration-150 flex items-center gap-1.5 shadow-[0_0_10px_rgba(16,185,129,0.1)] active:scale-95 cursor-pointer" onclick="window.doExportExcel()">
                        <i class="fa-solid fa-file-excel text-emerald-400"></i> Excel
                    </button>
                    <button class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 text-xs font-bold font-mono transition duration-150 flex items-center gap-1.5 shadow-[0_0_10px_rgba(244,63,94,0.1)] active:scale-95 cursor-pointer" onclick="window.doExportPDF()">
                        <i class="fa-solid fa-file-pdf text-rose-400"></i> PDF
                    </button>
                </div>
            </div>
                        <i class="fa-solid fa-file-pdf text-rose-400"></i> PDF
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="mineral-reports-table">
                    <thead>
                        <tr class="text-xs uppercase tracking-wider" style="background:rgba(255,255,255,0.01); border-bottom:1.5px solid var(--erp-border)">
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Fecha</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Tipo</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Cliente/Proveedor</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Bocamina</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Presentación</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Cantidad</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Peso</th>
                            <th class="px-6 py-4 font-bold" style="color:var(--erp-text-muted)">Leyes Encontradas</th>
                            <th class="px-6 py-4 text-right font-bold" style="color:var(--erp-text-muted)">Monto Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @forelse($transacciones as $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/20 transition duration-150">
                            <td class="px-6 py-4 font-mono text-xs">{{ $item->fecha->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold font-mono {{ $item->tipo === 'compra' ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' }}">
                                    <i class="fa-solid {{ $item->tipo === 'compra' ? 'fa-arrow-down-left mr-1.5' : 'fa-arrow-up-right mr-1.5' }}"></i>
                                    {{ $item->tipo === 'compra' ? 'Compra' : 'Venta' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold" style="color:var(--text-main)">{{ $item->cliente_proveedor }}</td>
                            <td class="px-6 py-4 text-xs font-semibold">{{ $item->bocamina->nombre ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-xs">{{ $item->presentacion === 'Otro' ? ($item->presentacion_otro ?: 'Otro') : $item->presentacion }}</td>
                            <td class="px-6 py-4 font-mono text-xs">{{ number_format($item->cantidad, 2) }}</td>
                            <td class="px-6 py-4 font-mono text-xs">{{ number_format($item->peso_neto_seco, 2) }} Kg</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @php
                                        $analisisList = $item->tipo === 'compra' ? $item->analisis : ($item->lote ? $item->lote->analisis : []);
                                    @endphp
                                    @forelse($analisisList as $an)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-amber-500/10 dark:bg-amber-500/15 text-amber-500 dark:text-amber-400 border border-amber-500/25 inline-block shadow-sm">
                                            {{ $an->mineral }}: {{ number_format($an->ley, 2) }}%
                                        </span>
                                    @empty
                                        <span class="text-xs italic text-slate-500">—</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono font-extrabold text-right text-xs text-amber-500 dark:text-amber-400">Bs. {{ number_format($item->monto_total, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-16 text-slate-500">
                                <i class="fa-solid fa-file-invoice text-4xl mb-3 block opacity-30"></i>
                                No se encontraron registros con los filtros seleccionados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ═══════════════ MODAL: COMPRA (LOTE) ═══════════════ --}}
    <div x-show="openCompraModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div @click.away="openCompraModal = false" class="erp-card w-full max-w-2xl overflow-hidden shadow-2xl relative" style="background:var(--erp-bg-card);">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800/80 flex items-center justify-between" style="background:rgba(255,255,255,0.02)">
                <h3 class="text-lg font-black" style="color:var(--text-main)">
                    <span x-text="isEditMode ? '📥 Editar Lote de Mineral' : '📥 Ingreso de Lote al Almacén'"></span>
                </h3>
                <button @click="openCompraModal = false" class="text-slate-400 hover:text-slate-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form :action="actionUrl" method="POST">
                @csrf
                <template x-if="isEditMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <input type="hidden" name="tipo" value="compra">

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[75vh] overflow-y-auto">
                    <div>
                        <label class="erp-label">Fecha de Compra</label>
                        <input type="date" name="fecha" required x-model="compraFecha" class="erp-input font-mono">
                    </div>

                    <div>
                        <label class="erp-label">Proveedor (¿De quién compra?)</label>
                        <input type="text" name="cliente_proveedor" required x-model="compraProveedor" placeholder="Nombre del Proveedor o Cooperativa" class="erp-input">
                    </div>

                    <div>
                        <label class="erp-label">Bocamina de Origen (Opcional)</label>
                        <select name="bocamina_id" x-model="compraBocaminaId" class="erp-input erp-input-select">
                            <option value="">— Seleccionar Bocamina —</option>
                            @foreach($bocaminas as $boc)
                                <option value="{{ $boc->id }}">{{ $boc->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="erp-label">Presentación / Empaque</label>
                        <select name="presentacion" x-model="compraPresentacion" class="erp-input erp-input-select">
                            <option value="Sacos">Sacos</option>
                            <option value="Volqueta">Volqueta</option>
                            <option value="Concentrado">Concentrado</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    <div x-show="compraPresentacion === 'Otro'" class="col-span-1 md:col-span-2">
                        <label class="erp-label">Especificar Presentación</label>
                        <input type="text" name="presentacion_otro" x-model="compraPresentacionOtro" placeholder="Escribe el nombre de la presentación" class="erp-input">
                    </div>

                    <div>
                        <label class="erp-label">Cantidad (Sacos, volquetadas, etc.)</label>
                        <input type="number" step="0.01" name="cantidad" required x-model="compraCantidad" placeholder="Ej. 100" class="erp-input font-mono">
                    </div>

                    <div>
                        <label class="erp-label">Peso Total (Kg)</label>
                        <input type="number" step="0.01" name="peso_neto_seco" required x-model="compraPeso" @input="calcCompraTotal()" placeholder="Ej. 5000" class="erp-input font-mono">
                    </div>

                    <div>
                        <label class="erp-label">Precio Unitario (por Kg)</label>
                        <input type="number" step="0.01" name="precio_unidad" required x-model="compraPrecio" @input="calcCompraTotal()" placeholder="Ej. 1.50" class="erp-input font-mono">
                    </div>

                    <div>
                        <label class="erp-label" style="color:#f59e0b">Monto Total Liquidado (Bs.)</label>
                        <input type="number" step="0.01" name="monto_total" required x-model="compraTotal" class="erp-input font-mono border-amber-500/30" readonly style="background:rgba(255,255,255,0.02)">
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="erp-label">Observaciones</label>
                        <textarea name="observacion" x-model="compraObservacion" rows="2" class="erp-input" placeholder="Detalles o notas sobre el lote de compra..."></textarea>
                    </div>

                    {{-- Dynamic Lab Analysis Table --}}
                    <div class="col-span-1 md:col-span-2 border-t border-slate-200 dark:border-slate-800/80 pt-4 mt-2">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs font-bold uppercase tracking-widest" style="color:var(--text-main)"><i class="fa-solid fa-flask text-amber-500 mr-1.5"></i> Análisis Químico de Laboratorio</h4>
                            <button type="button" @click="addMineral()" class="px-2.5 py-1 text-[10px] font-black uppercase rounded bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 hover:bg-indigo-500 hover:text-white transition">
                                <i class="fa-solid fa-plus mr-1"></i> Agregar Mineral
                            </button>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(an, index) in compraAnalisis" :key="index">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1">
                                        <select :name="'analisis['+index+'][mineral]'" x-model="an.mineral" class="erp-input erp-input-select" required>
                                            <option value="">— Seleccionar Mineral —</option>
                                            <option value="Zinc">Zinc (Zn)</option>
                                            <option value="Plomo">Plomo (Pb)</option>
                                            <option value="Plata">Plata (Ag)</option>
                                            <option value="Cobre">Cobre (Cu)</option>
                                            <option value="Estaño">Estaño (Sn)</option>
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <div class="relative">
                                            <input type="number" step="0.01" min="0" max="100" :name="'analisis['+index+'][ley]'" x-model="an.ley" placeholder="Ley en % (ej. 48.50)" class="erp-input font-mono pr-8" required>
                                            <span class="absolute right-3 top-2 text-xs text-slate-500">%</span>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeMineral(index)" class="p-2 rounded bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white transition">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800/80 bg-slate-50 dark:bg-slate-900/40 flex justify-end gap-2">
                    <button type="button" @click="openCompraModal = false" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-350">Cancelar</button>
                    <button type="submit" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white">Guardar Lote</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════════ MODAL: VENTA (DESPACHO) ═══════════════ --}}
    <div x-show="openVentaModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div @click.away="openVentaModal = false" class="erp-card w-full max-w-2xl overflow-hidden shadow-2xl relative" style="background:var(--erp-bg-card);">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800/80 flex items-center justify-between" style="background:rgba(255,255,255,0.02)">
                <h3 class="text-lg font-black" style="color:var(--text-main)">
                    <span x-text="isEditMode ? '📤 Editar Registro de Venta' : '📤 Despacho de Venta (Salida)'"></span>
                </h3>
                <button @click="openVentaModal = false" class="text-slate-400 hover:text-slate-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form :action="actionUrl" method="POST">
                @csrf
                <template x-if="isEditMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <input type="hidden" name="tipo" value="venta">

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[75vh] overflow-y-auto">
                    <div>
                        <label class="erp-label">Fecha de Venta</label>
                        <input type="date" name="fecha" required x-model="ventaFecha" class="erp-input font-mono">
                    </div>

                    <div>
                        <label class="erp-label">Cliente (¿A quién vende?)</label>
                        <input type="text" name="cliente_proveedor" required x-model="ventaCliente" placeholder="Nombre del Comprador / Empresa" class="erp-input">
                    </div>

                    <div>
                        <label class="erp-label">Destino de Despacho (Opcional)</label>
                        <input type="text" name="destino" x-model="ventaDestino" placeholder="Ej. Fundición Vinto" class="erp-input">
                    </div>

                    {{-- ═══════════════ MULTI-LOTE CREATION MODE ═══════════════ --}}
                    <template x-if="!isEditMode">
                        <div class="col-span-1 md:col-span-2 space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-2">
                                <h4 class="text-xs font-black uppercase tracking-widest text-emerald-500"><i class="fa-solid fa-list-check mr-1.5"></i> Lotes a Despachar (Salidas)</h4>
                                <button type="button" @click="addVentaLote()" class="px-2.5 py-1 text-[10px] font-black uppercase rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500 hover:text-white transition">
                                    <i class="fa-solid fa-plus mr-1"></i> Agregar Lote
                                </button>
                            </div>

                            <div class="space-y-4">
                                <template x-for="(item, index) in ventaLotes" :key="index">
                                    <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/20 space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-400" x-text="'Lote #' + (index + 1)"></span>
                                            <button type="button" @click="removeVentaLote(index)" class="text-rose-400 hover:text-rose-600 transition" x-show="ventaLotes.length > 1">
                                                <i class="fa-solid fa-trash-can text-xs"></i> Quitar
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div>
                                                <label class="erp-label">Seleccionar Lote *</label>
                                                <select :name="'lotes['+index+'][lote_id]'" x-model="item.lote_id" @change="onVentaLoteSelected(index)" class="erp-input erp-input-select" required>
                                                    <option value="">— Seleccionar Lote Disponible —</option>
                                                    @foreach($lotesDisponibles as $lot)
                                                        <option value="{{ $lot->id }}">
                                                            LOT-{{ str_pad($lot->id, 5, '0', STR_PAD_LEFT) }} (Stock: {{ number_format($lot->peso_disponible, 2) }} Kg)
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div x-show="item.info" class="p-2.5 rounded-lg bg-slate-900/60 text-[11px] space-y-1 font-semibold border border-slate-800/80">
                                                <div>Presentación: <span class="text-white" x-text="item.info?.presentacion === 'Otro' ? item.info?.presentacion_otro : item.info?.presentacion"></span></div>
                                                <div>Disponibilidad: <span class="text-emerald-400" x-text="item.info?.peso_disponible + ' Kg / ' + item.info?.cantidad_disponible + ' Sacos'"></span></div>
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    <template x-for="an in (item.info ? item.info.analisis : [])">
                                                        <span class="px-1 py-0.5 rounded text-[9px] font-mono bg-slate-800 text-slate-300">
                                                            <span x-text="an.mineral"></span>: <span x-text="parseFloat(an.ley).toFixed(2)"></span>%
                                                        </span>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 pt-2" x-show="item.lote_id">
                                            <div>
                                                <label class="erp-label">Cantidad</label>
                                                <input type="number" step="0.01" :name="'lotes['+index+'][cantidad]'" x-model="item.cantidad" class="erp-input font-mono" required placeholder="Ej. 20">
                                                <span class="text-[9px] text-rose-500 block mt-1" x-show="item.info && parseFloat(item.cantidad) > parseFloat(item.info.cantidad_disponible)">Supera stock!</span>
                                            </div>

                                            <div>
                                                <label class="erp-label">Peso (Kg)</label>
                                                <input type="number" step="0.01" :name="'lotes['+index+'][peso_neto_seco]'" x-model="item.peso_neto_seco" @input="calcVentaItemTotal(index)" class="erp-input font-mono" required placeholder="Ej. 1000">
                                                <span class="text-[9px] text-rose-500 block mt-1" x-show="item.info && parseFloat(item.peso_neto_seco) > parseFloat(item.info.peso_disponible)">Supera stock!</span>
                                            </div>

                                            <div>
                                                <label class="erp-label">Precio Unit. (Kg)</label>
                                                <input type="number" step="0.01" :name="'lotes['+index+'][precio_unitario]'" x-model="item.precio_unidad" @input="calcVentaItemTotal(index)" class="erp-input font-mono" required placeholder="Ej. 2.10">
                                            </div>

                                            <div>
                                                <label class="erp-label">Monto Item (Bs.)</label>
                                                <input type="number" step="0.01" :name="'lotes['+index+'][monto_total]'" x-model="item.monto_total" class="erp-input font-mono bg-slate-950/20" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- ═══════════════ SINGLE-LOTE EDIT MODE FALLBACK ═══════════════ --}}
                    <template x-if="isEditMode">
                        <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="erp-label">Lote Origen</label>
                                <select name="lote_id" x-model="ventaLoteId" class="erp-input erp-input-select" required disabled>
                                    @foreach($lotesDisponibles as $lot)
                                        <option value="{{ $lot->id }}">LOT-{{ str_pad($lot->id, 5, '0', STR_PAD_LEFT) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div x-show="loteInfo" class="p-3 rounded-lg bg-indigo-500/5 text-xs space-y-1 font-semibold border border-indigo-500/20">
                                <div>Presentación: <span class="text-white" x-text="loteInfo?.presentacion"></span></div>
                                <div>Peso Max: <span class="text-indigo-400" x-text="loteInfo?.peso_disponible + ' Kg'"></span></div>
                            </div>

                            <div>
                                <label class="erp-label">Cantidad a Vender</label>
                                <input type="number" step="0.01" name="cantidad" required x-model="ventaCantidad" class="erp-input font-mono">
                            </div>

                            <div>
                                <label class="erp-label">Peso Neto Seco (Kg)</label>
                                <input type="number" step="0.01" name="peso_neto_seco" required x-model="ventaPeso" @input="calcVentaTotal()" class="erp-input font-mono">
                            </div>

                            <div>
                                <label class="erp-label">Precio Unitario Venta</label>
                                <input type="number" step="0.01" name="precio_unidad" required x-model="ventaPrecio" @input="calcVentaTotal()" class="erp-input font-mono">
                            </div>

                            <div>
                                <label class="erp-label" style="color:#10b981">Monto Total Cobrado (Bs.)</label>
                                <input type="number" step="0.01" name="monto_total" required x-model="ventaTotal" class="erp-input font-mono bg-slate-950/20" readonly>
                            </div>
                        </div>
                    </template>

                    <div class="col-span-1 md:col-span-2">
                        <label class="erp-label">Observaciones de la Venta</label>
                        <textarea name="observacion" x-model="ventaObservacion" rows="2" class="erp-input" placeholder="Detalles o notas sobre el despacho..."></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800/80 bg-slate-50 dark:bg-slate-900/40 flex justify-end gap-2">
                    <button type="button" @click="openVentaModal = false" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-350">Cancelar</button>
                    <button type="submit" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white">Registrar Venta</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════════ MODAL: DETALLE FICHA LOTE ═══════════════ --}}
    <div x-show="openDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div @click.away="openDetailModal = false" class="erp-card w-full max-w-2xl overflow-hidden shadow-2xl relative" style="background:var(--erp-bg-card);">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800/80 flex items-center justify-between" style="background:rgba(255,255,255,0.02)">
                <h3 class="text-sm font-black uppercase tracking-wider" style="color:var(--text-main)"><i class="fa-solid fa-cube text-amber-500 mr-1.5"></i> Ficha del Lote de Compra</h3>
                <button @click="openDetailModal = false" class="text-slate-400 hover:text-slate-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="p-6 space-y-6 max-h-[80vh] overflow-y-auto" x-show="fichaLote">
                <div class="grid grid-cols-2 gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wide">Código de Lote</span>
                        <span class="text-lg font-black text-white font-mono" x-text="fichaLote ? 'LOT-' + String(fichaLote.id).padStart(5, '0') : ''"></span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wide">Fecha Registro</span>
                        <span class="text-sm font-semibold" style="color:var(--text-main)" x-text="fichaLote ? new Date(fichaLote.fecha).toLocaleDateString('es-BO') : ''"></span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wide">Proveedor</span>
                        <span class="text-sm font-bold text-amber-500" x-text="fichaLote ? fichaLote.cliente_proveedor : ''"></span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wide">Bocamina de Origen</span>
                        <span class="text-sm font-semibold" style="color:var(--text-main)" x-text="fichaLote?.bocamina ? fichaLote.bocamina.nombre : 'No asignada'"></span>
                    </div>
                </div>

                {{-- Stock actual --}}
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/30">
                    <span class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3"><i class="fa-solid fa-warehouse mr-1 text-amber-500"></i> Niveles y Stock en Almacén</span>
                    <div class="grid grid-cols-2 gap-6 mb-3 font-mono">
                        <div>
                            <span class="block text-[10px] text-slate-500">Peso Stock Restante:</span>
                            <strong class="text-base text-white" x-text="fichaLote ? parseFloat(fichaLote.peso_disponible).toFixed(2) + ' Kg' : ''"></strong>
                            <span class="text-xs text-slate-500 block">Original: <span x-text="fichaLote ? parseFloat(fichaLote.peso_neto_seco).toFixed(2) + ' Kg' : ''"></span></span>
                        </div>
                        <div>
                            <span class="block text-[10px] text-slate-500">Cantidad Stock Restante:</span>
                            <strong class="text-base text-white" x-text="fichaLote ? parseFloat(fichaLote.cantidad_disponible).toFixed(2) : ''"></strong>
                            <span class="text-xs text-slate-500 block">Original: <span x-text="fichaLote ? parseFloat(fichaLote.cantidad).toFixed(2) : ''"></span></span>
                        </div>
                    </div>
                </div>

                {{-- Leyes químicas --}}
                <div>
                    <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-2"><i class="fa-solid fa-flask text-amber-500 mr-1"></i> Leyes del Laboratorio</span>
                    <div class="grid grid-cols-3 gap-2">
                        <template x-for="an in (fichaLote ? fichaLote.analisis : [])">
                            <div class="p-3 rounded-lg border border-slate-200 dark:border-slate-800 text-center bg-white dark:bg-slate-950/20">
                                <span class="block text-[10px] text-slate-400 font-bold uppercase" x-text="an.mineral"></span>
                                <strong class="text-sm text-white font-mono" x-text="parseFloat(an.ley).toFixed(2) + '%'"></strong>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Historial de Despachos --}}
                <div>
                    <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-2"><i class="fa-solid fa-history text-indigo-500 mr-1"></i> Historial de Salidas / Ventas del Lote</span>
                    <div class="overflow-hidden border border-slate-100 dark:border-slate-800 rounded-lg">
                        <table class="w-full text-left text-xs font-mono">
                            <thead>
                                <tr class="bg-slate-100 dark:bg-slate-900">
                                    <th class="p-2.5">Venta ID</th>
                                    <th class="p-2.5">Fecha</th>
                                    <th class="p-2.5">Cliente</th>
                                    <th class="p-2.5 text-right">Peso</th>
                                    <th class="p-2.5 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="vt in (fichaLote ? fichaLote.ventas : [])">
                                    <tr class="border-t border-slate-100 dark:border-slate-800 hover:bg-slate-800/20">
                                        <td class="p-2.5">SLD-<span x-text="String(vt.id).padStart(5, '0')"></span></td>
                                        <td class="p-2.5" x-text="new Date(vt.fecha).toLocaleDateString('es-BO')"></td>
                                        <td class="p-2.5" x-text="vt.cliente_proveedor"></td>
                                        <td class="p-2.5 text-right" x-text="parseFloat(vt.peso_neto_seco).toFixed(2) + ' Kg'"></td>
                                        <td class="p-2.5 text-right font-bold text-emerald-400" x-text="'Bs. ' + parseFloat(vt.monto_total).toFixed(2)"></td>
                                    </tr>
                                </template>
                                <template x-if="!fichaLote || !fichaLote.ventas || !fichaLote.ventas.length">
                                    <tr>
                                        <td colspan="5" class="p-4 text-center text-slate-500 italic">No se han realizado ventas a partir de este lote aún.</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Obs --}}
                <div x-show="fichaLote?.observacion">
                    <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wide">Observaciones</span>
                    <p class="text-xs text-slate-400 mt-1" x-text="fichaLote?.observacion"></p>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800/80 bg-slate-50 dark:bg-slate-900/40 flex justify-end">
                <button type="button" @click="openDetailModal = false" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-350">Cerrar Ficha</button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// ─── Real-time AJAX Filter para Reportes de Almacén ─────────────────────
function submitFilterRealTime(form) {
    const url = new URL(form.action || window.location.href, window.location.origin);
    url.search = '';
    new FormData(form).forEach((v, k) => { if (v && String(v).trim()) url.searchParams.set(k, v); });
    window.history.pushState({}, '', url.toString());

    const container = document.getElementById('report-output');
    if (!container) { form.submit(); return; }

    container.style.opacity = '0.4';
    container.style.pointerEvents = 'none';

    fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text())
        .then(html => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const fresh = doc.getElementById('report-output');
            if (fresh) container.innerHTML = fresh.innerHTML;
            else window.location.reload();
        })
        .catch(() => form.submit())
        .finally(() => { container.style.opacity = '1'; container.style.pointerEvents = ''; });
}

// ─── PDF Export (Elegante, sin fondos negros) ───────────────────────────
window.doExportPDF = function() {
    if (typeof html2pdf === 'undefined') {
        alert('Cargando librería PDF, intenta nuevamente.');
        return;
    }
    const tableContainer = document.getElementById('report-output');
    if (!tableContainer) return;
    const table = tableContainer.querySelector('table');
    if (!table) return;

    const now = new Date();
    const dateStr = now.toLocaleDateString('es-BO', { day:'2-digit', month:'long', year:'numeric' });
    const timeStr = now.toLocaleTimeString('es-BO', { hour:'2-digit', minute:'2-digit' });

    // CSS y HTML personalizado para la impresión del reporte
    const elegantHtml = `
        <div style="font-family:Arial,sans-serif;padding:30px;color:#1e293b;background:#ffffff;">
            <!-- Header -->
            <div style="display:flex;justify-content:space-between;align-items:center;border-bottom:3px solid #1e293b;padding-bottom:15px;margin-bottom:20px;">
                <div>
                    <h1 style="font-size:22px;font-weight:900;color:#0f172a;margin:0;">Reporte de Compra y Venta de Minerales</h1>
                    <p style="font-size:10px;color:#64748b;margin:3px 0 0 0;">Generado automáticamente por el SCPM el ${dateStr} a las ${timeStr}</p>
                </div>
                <div style="text-align:right;">
                    <span style="font-size:9px;text-transform:uppercase;color:#94a3b8;font-weight:700;">Documento Oficial</span>
                    <div style="font-size:12px;font-weight:800;color:#0f172a;margin-top:2px;">ALMACÉN DE MINERALES</div>
                </div>
            </div>
            
            <!-- Table content -->
            <div style="border:1.5px solid #cbd5e1;border-radius:8px;overflow:hidden;">
                <table style="width:100%;border-collapse:collapse;font-size:11px;">
                    <thead>
                        <tr style="background:#475569;color:#ffffff;">
                            ${Array.from(table.querySelectorAll('thead th')).map(th => `<th style="padding:10px;text-align:left;font-weight:700;text-transform:uppercase;border:1px solid #475569;">${th.innerText}</th>`).join('')}
                        </tr>
                    </thead>
                    <tbody>
                        ${Array.from(table.querySelectorAll('tbody tr')).map((tr, ri) => `
                            <tr style="background:${ri % 2 === 0 ? '#ffffff' : '#f8fafc'};">
                                ${Array.from(tr.querySelectorAll('td')).map(td => `<td style="padding:9px 10px;border:1px solid #e2e8f0;color:#334155;vertical-align:middle;">${td.innerHTML}</td>`).join('')}
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    `;

    const wrap = document.createElement('div');
    wrap.style.cssText = 'position:absolute;left:-9999px;top:-9999px;width:1000px;background:#ffffff;';
    wrap.innerHTML = elegantHtml;
    document.body.appendChild(wrap);

    html2pdf().set({
        margin: [0.4, 0.35, 0.4, 0.35],
        filename: 'Reporte_Minerales_' + now.toISOString().slice(0,10) + '.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2.2, useCORS: true, backgroundColor: '#ffffff' },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'landscape' }
    }).from(wrap).save().finally(() => {
        document.body.removeChild(wrap);
    });
};

// ─── Excel Export ───────────────────────────────────────────────
window.doExportExcel = function() {
    if (typeof XLSX === 'undefined') {
        alert('Cargando librería Excel, intenta nuevamente.');
        return;
    }
    const tableContainer = document.getElementById('report-output');
    if (!tableContainer) return;
    const table = tableContainer.querySelector('table');
    if (!table) return;

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.table_to_sheet(table);
    XLSX.utils.book_append_sheet(wb, ws, 'Reporte de Minerales');
    XLSX.writeFile(wb, 'Reporte_Minerales_' + new Date().toLocaleDateString('es-BO').replace(/\//g,'-') + '.xlsx');
};
</script>
@endpush
