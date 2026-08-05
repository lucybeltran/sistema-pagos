@extends('layouts.app')

@section('title', 'Procesar Pago')

@section('content')
<!-- Custom Premium Styles for Process Payment View -->
<style>
    /* 3D recessed input styling matching trabajadores page */
    input[type="text"], 
    input[type="number"], 
    input[type="date"], 
    select, 
    textarea {
        border-radius: 12px !important;
        padding: 0.75rem 1.1rem !important;
        font-size: 13.5px !important;
        font-family: 'Outfit', sans-serif !important;
        width: 100% !important;
        transition: all 0.2s ease !important;
    }

    .light-theme input[type="text"], 
    .light-theme input[type="number"], 
    .light-theme input[type="date"], 
    .light-theme select, 
    .light-theme textarea {
        color: #0f172a !important; /* Solid dark text */
        background-color: #ffffff !important;
        border: 1.5px solid rgba(79, 70, 229, 0.22) !important;
        box-shadow: 
            inset 0 1.5px 3px rgba(15, 23, 42, 0.06), 
            0 1px 0 rgba(255, 255, 255, 0.8) !important;
    }

    html:not(.light-theme) input[type="text"], 
    html:not(.light-theme) input[type="number"], 
    html:not(.light-theme) input[type="date"], 
    html:not(.light-theme) select, 
    html:not(.light-theme) textarea {
        color: #f8fafc !important;
        background-color: #060a13 !important;
        border: 1.5px solid rgba(99, 102, 241, 0.25) !important;
        box-shadow: 
            inset 0 2px 4px rgba(0, 0, 0, 0.06), 
            0 1px 0 rgba(255, 255, 255, 0.04) !important;
    }

    /* Premium Input Group with Left Prefix Badge */
    .premium-input-group {
        display: flex !important;
        align-items: stretch !important;
        border-radius: 12px !important;
        overflow: hidden !important;
        transition: all 0.2s ease !important;
        width: 100% !important;
    }
    
    .light-theme .premium-input-group {
        background-color: #ffffff !important;
        border: 1.5px solid rgba(79, 70, 229, 0.22) !important;
        box-shadow: 
            inset 0 1.5px 3px rgba(15, 23, 42, 0.06), 
            0 1px 0 rgba(255, 255, 255, 0.8) !important;
    }

    html:not(.light-theme) .premium-input-group {
        background-color: #060a13 !important;
        border: 1.5px solid rgba(99, 102, 241, 0.25) !important;
        box-shadow: 
            inset 0 2px 4px rgba(0, 0, 0, 0.06), 
            0 1px 0 rgba(255, 255, 255, 0.04) !important;
    }

    .premium-input-group .prefix-badge {
        display: inline-flex !important;
        align-items: center !important;
        padding: 0 1rem !important;
        font-size: 13px !important;
        font-weight: 800 !important;
        user-select: none !important;
    }

    .light-theme .premium-input-group .prefix-badge {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        border-right: 1.5px solid rgba(79, 70, 229, 0.18) !important;
    }

    html:not(.light-theme) .premium-input-group .prefix-badge {
        background-color: #0b0f19 !important;
        color: #94a3b8 !important;
        border-right: 1.5px solid rgba(99, 102, 241, 0.18) !important;
    }

    .premium-input-group input {
        flex: 1 !important;
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        padding: 0.75rem 1.1rem !important;
        outline: none !important;
        margin: 0 !important;
    }

    .light-theme .premium-input-group input {
        color: #0f172a !important;
    }

    html:not(.light-theme) .premium-input-group input {
        color: #f8fafc !important;
    }

    .premium-input-group:focus-within {
        border-color: #0d9488 !important;
        box-shadow: 
            0 0 0 4px rgba(13, 148, 136, 0.18), 
            0 6px 16px -2px rgba(13, 148, 136, 0.1) !important;
    }

    /* Ultra-Premium Labor Details Card */
    .premium-details-card {
        border-radius: 16px !important;
        padding: 1.25rem !important;
        transition: all 0.3s ease !important;
        position: relative !important;
        overflow: hidden !important;
    }

    .light-theme .premium-details-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
        border: 1.5px solid rgba(99, 102, 241, 0.18) !important;
        box-shadow: 
            0 10px 25px -5px rgba(99, 102, 241, 0.08), 
            inset 0 1px 0 rgba(255, 255, 255, 0.9) !important;
    }

    html:not(.light-theme) .premium-details-card {
        background: linear-gradient(135deg, #0d1224 0%, #070a14 100%) !important;
        border: 1.5px solid rgba(99, 102, 241, 0.28) !important;
        box-shadow: 
            0 10px 25px -5px rgba(0, 0, 0, 0.25), 
            inset 0 1px 0 rgba(255, 255, 255, 0.05) !important;
    }

    .premium-details-card .details-header {
        display: flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.08em !important;
        margin-bottom: 1rem !important;
        padding-bottom: 0.6rem !important;
        border-bottom: 1.5px dashed rgba(99, 102, 241, 0.15) !important;
    }

    .light-theme .premium-details-card .details-header {
        color: #4f46e5 !important;
    }

    html:not(.light-theme) .premium-details-card .details-header {
        color: #a5b4fc !important;
    }

    .premium-details-card .details-grid {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 0.85rem !important;
    }

    .premium-details-card .details-item {
        padding: 0.75rem 0.9rem !important;
        border-radius: 12px !important;
        border: 1px solid transparent !important;
        transition: all 0.2s ease !important;
    }

    /* Colors and details for Sky Blue Item (Bocamina) */
    .light-theme .details-item.item-sky {
        background-color: rgba(14, 165, 233, 0.05) !important;
        border-color: rgba(14, 165, 233, 0.18) !important;
    }
    html:not(.light-theme) .details-item.item-sky {
        background-color: rgba(14, 165, 233, 0.04) !important;
        border-color: rgba(14, 165, 233, 0.18) !important;
    }
    .details-item.item-sky .item-label { color: #0284c7 !important; }
    html:not(.light-theme) .details-item.item-sky .item-label { color: #38bdf8 !important; }

    /* Violet Item (Cargo) */
    .light-theme .details-item.item-violet {
        background-color: rgba(139, 92, 246, 0.05) !important;
        border-color: rgba(139, 92, 246, 0.18) !important;
    }
    html:not(.light-theme) .details-item.item-violet {
        background-color: rgba(139, 92, 246, 0.04) !important;
        border-color: rgba(139, 92, 246, 0.18) !important;
    }
    .details-item.item-violet .item-label { color: #7c3aed !important; }
    html:not(.light-theme) .details-item.item-violet .item-label { color: #a78bfa !important; }

    /* Emerald Item (Tipo Contrato) */
    .light-theme .details-item.item-emerald {
        background-color: rgba(16, 185, 129, 0.05) !important;
        border-color: rgba(16, 185, 129, 0.18) !important;
    }
    html:not(.light-theme) .details-item.item-emerald {
        background-color: rgba(16, 185, 129, 0.04) !important;
        border-color: rgba(16, 185, 129, 0.18) !important;
    }
    .details-item.item-emerald .item-label { color: #059669 !important; }
    html:not(.light-theme) .details-item.item-emerald .item-label { color: #34d399 !important; }

    /* Amber/Gold Item (Tarifa Contrato) */
    .light-theme .details-item.item-amber {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.06) 0%, rgba(245, 158, 11, 0.12) 100%) !important;
        border-color: rgba(245, 158, 11, 0.3) !important;
        box-shadow: inset 0 1px 2px rgba(245, 158, 11, 0.05) !important;
    }
    html:not(.light-theme) .details-item.item-amber {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.05) 0%, rgba(245, 158, 11, 0.08) 100%) !important;
        border-color: rgba(245, 158, 11, 0.25) !important;
    }
    .details-item.item-amber .item-label { color: #d97706 !important; }
    html:not(.light-theme) .details-item.item-amber .item-label { color: #fbbf24 !important; }

    .details-item .item-label {
        font-size: 9.5px !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        display: flex !important;
        align-items: center !important;
        gap: 0.35rem !important;
        margin-bottom: 0.35rem !important;
    }

    .details-item .item-value {
        font-size: 13px !important;
        font-weight: 800 !important;
    }

    .light-theme .details-item .item-value { color: #0f172a !important; }
    html:not(.light-theme) .details-item .item-value { color: #f8fafc !important; }

    .details-item .highlight-value {
        font-size: 15px !important;
        font-weight: 900 !important;
    }

    .light-theme .details-item.item-amber .highlight-value { color: #b45309 !important; }
    html:not(.light-theme) .details-item.item-amber .highlight-value { color: #f59e0b !important; }
    
    .details-item .highlight-value .currency {
        font-size: 11px !important;
        opacity: 0.85;
        margin-right: 0.1rem !important;
    }

    /* Subtotal highlight box */
    .premium-subtotal-box {
        padding: 0.85rem 1.1rem !important;
        border-radius: 12px !important;
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        font-size: 13px !important;
        font-weight: 800 !important;
    }

    .light-theme .premium-subtotal-box {
        background-color: rgba(6, 182, 212, 0.08) !important;
        border: 1.5px solid rgba(6, 182, 212, 0.22) !important;
        color: #0891b2 !important;
    }

    html:not(.light-theme) .premium-subtotal-box {
        background-color: rgba(6, 182, 212, 0.05) !important;
        border: 1.5px solid rgba(6, 182, 212, 0.20) !important;
        color: #22d3ee !important;
    }

    /* 3D button active presses */
    .btn-3d-teal {
        background: linear-gradient(135deg, #0d9488 0%, #059669 100%) !important;
        color: #ffffff !important;
        border: 1px solid #0f766e !important;
        border-bottom: 4px solid #115e59 !important;
        box-shadow: 0 4px 10px rgba(13, 148, 136, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
        font-weight: 800 !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
        transition: all 0.15s ease !important;
        cursor: pointer !important;
    }
    
    .btn-3d-teal:hover:not(:disabled) {
        background: linear-gradient(135deg, #14b8a6 0%, #10b981 100%) !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(13, 148, 136, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.25) !important;
    }

    .btn-3d-teal:active:not(:disabled) {
        transform: translateY(2px) !important;
        border-bottom-width: 1px !important;
    }

    .btn-3d-teal:disabled {
        background: #e2e8f0 !important;
        color: #94a3b8 !important;
        border: 1px solid #cbd5e1 !important;
        border-bottom: 1px solid #cbd5e1 !important;
        box-shadow: none !important;
        transform: none !important;
        cursor: not-allowed !important;
        opacity: 0.85 !important;
    }

    /* Adapt teal text in light theme */
    .light-theme .text-teal-500, .light-theme .text-teal-400 {
        color: #0d9488 !important;
    }
</style>

<div x-data="paymentWizard()" class="space-y-6">

    <!-- Header -->
    <div>
        <a href="{{ route('pagos.index') }}" class="text-xs text-slate-400 hover:text-teal-400 flex items-center font-medium transition duration-150">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Volver al Historial
        </a>
        <h1 class="text-3xl font-bold tracking-tight text-slate-100 mt-1">Procesar Liquidación de Pago</h1>
        <p class="text-sm text-slate-400 mt-1">Genera la planilla de pago neto ingresando la cantidad trabajada según el contrato del personal.</p>
    </div>

    <!-- Wizard Grid -->
    <form action="{{ route('pagos.store') }}" method="POST" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        @csrf
        
        <!-- Hidden Inputs for auto loaded read only data & calculation values to satisfy DB/Backend validation rules -->
        <input type="hidden" name="tipo_contrato_nombre" x-model="tipoContratoNombre">
        <input type="hidden" name="cantidad_trabajada" x-model="cantidadTrabajada">
        <input type="hidden" name="tarifa_pago" x-model="tarifaPago">

        <!-- Col 1: Inputs & Details Selection -->
        <div class="glass-card rounded-xl p-6 lg:col-span-1 space-y-5 h-fit">
            <h3 class="text-md font-bold text-slate-200 border-b border-slate-800 pb-3 flex items-center">
                <i class="fa-solid fa-calculator mr-2 text-teal-500"></i> Datos de Liquidación
            </h3>
            
            <!-- Available Cash Balance Indicator -->
            <div class="p-3.5 rounded-lg border {{ $saldo_caja >= 0 ? 'bg-emerald-500/5 border-emerald-500/20 text-emerald-400' : 'bg-rose-500/5 border-rose-500/20 text-rose-450' }} flex items-center justify-between text-xs font-semibold">
                <span class="flex items-center">
                    <i class="fa-solid fa-vault mr-2 {{ $saldo_caja >= 0 ? 'text-emerald-500' : 'text-rose-500' }}"></i> Saldo en Caja:
                </span>
                <span class="font-mono font-bold text-sm">Bs. {{ number_format($saldo_caja, 2) }}</span>
            </div>
            
            <div class="space-y-4">
                <!-- Filter by Bocamina -->
                <div>
                    <label for="bocamina_filtro_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Filtrar por Bocamina</label>
                    <select id="bocamina_filtro_id" x-model="bocaminaFiltroId" @change="trabajadorId = ''; clear()"
                            class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm">
                        <option value="">-- Todas las Bocaminas --</option>
                        @foreach($bocaminas as $bocamina)
                            <option value="{{ $bocamina->id }}">{{ $bocamina->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Select Trabajador -->
                <div>
                    <label for="trabajador_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Seleccionar Trabajador</label>
                    <select id="trabajador_id" name="trabajador_id" required x-model="trabajadorId" @change="onTrabajadorChange()"
                            class="mt-1 block w-full px-3 py-2.5 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm font-bold">
                        <option value="">-- Seleccionar Trabajador --</option>
                        <template x-for="t in filteredTrabajadores" :key="t.id">
                            <option :value="t.id" x-text="t.nombre + ' (' + (t.rol ? t.rol.toUpperCase() : 'AYUDANTE') + ')'"></option>
                        </template>
                    </select>
                </div>

                <!-- Ultra-Premium Colorful Labor Details Card (Shown when selected) -->
                <div x-show="trabajadorId" class="premium-details-card" x-cloak>
                    <div class="details-header">
                        <i class="fa-solid fa-circle-info text-indigo-400 dark:text-indigo-400"></i>
                        <span>Información de Contrato del Personal</span>
                    </div>
                    
                    <div class="details-grid">
                        <div class="details-item item-sky">
                            <div class="item-label">
                                <i class="fa-solid fa-mountain"></i> Bocamina Asignada
                            </div>
                            <div class="item-value text-xs truncate" x-text="bocaminaNombre"></div>
                        </div>

                        <div class="details-item item-violet">
                            <div class="item-label">
                                <i class="fa-solid fa-user-gear"></i> Cargo / Función
                            </div>
                            <div class="item-value capitalize text-xs truncate" x-text="cargo"></div>
                        </div>

                        <div class="details-item item-emerald">
                            <div class="item-label">
                                <i class="fa-solid fa-file-signature"></i> Tipo de Contrato
                            </div>
                            <div class="item-value uppercase text-xs truncate" x-text="tipoContratoNombre"></div>
                        </div>

                        <div class="details-item item-amber">
                            <div class="item-label">
                                <i class="fa-solid fa-coins"></i> Tarifa del Contrato
                            </div>
                            <div class="item-value highlight-value font-mono">
                                <span class="currency">Bs.</span>
                                <span x-text="tarifaAcordada ? parseFloat(tarifaAcordada).toLocaleString('es-BO', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Single Payment input for the week (Bs.) (Replaced the double input grid) -->
                <div x-show="trabajadorId" class="space-y-1.5" x-cloak>
                    <label for="subtotal_input" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Monto de Pago de la Semana (Bs.)</label>
                    <div class="premium-input-group">
                        <span class="prefix-badge">
                            Bs.
                        </span>
                        <input id="subtotal_input" name="subtotal" type="number" step="0.01" min="0.01" required x-model="subtotal" @input="onSubtotalInput()"
                               class="text-teal-500 font-bold"
                               placeholder="0.00">
                    </div>
                    <span class="help-text">Ingrese la cantidad de dinero a pagar para la liquidación de esta semana.</span>
                </div>

                <!-- Tipo de Pago / Liquidación -->
                <div x-show="trabajadorId" class="space-y-1 transition-all duration-200" x-cloak>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Modalidad de Pago</label>
                    <div class="grid grid-cols-2 gap-2 mt-1">
                        <button type="button" @click="tipoPagoPlanilla = 'completo'; userEditedMontoPagado = false; recalculate()"
                                :class="tipoPagoPlanilla === 'completo' ? 'bg-gradient-to-r from-teal-500 to-emerald-650 text-slate-950 font-bold' : 'bg-slate-900 text-slate-400 border border-slate-800 hover:text-slate-200'"
                                class="px-3 py-2 rounded-lg text-xs font-semibold text-center transition duration-150 shadow-md">
                            <i class="fa-solid fa-circle-check mr-1"></i> Pago Completo
                        </button>
                        <button type="button" @click="tipoPagoPlanilla = 'adelanto'; userEditedMontoPagado = true; montoPagado = (neto * 0.5).toFixed(2); recalculate()"
                                :class="tipoPagoPlanilla === 'adelanto' ? 'bg-gradient-to-r from-teal-500 to-emerald-650 text-slate-950 font-bold' : 'bg-slate-900 text-slate-400 border border-slate-800 hover:text-slate-200'"
                                class="px-3 py-2 rounded-lg text-xs font-semibold text-center transition duration-150 shadow-md">
                            <i class="fa-solid fa-hourglass-half mr-1"></i> Dar Adelanto / Parcial
                        </button>
                    </div>
                </div>

                <div>
                    <label for="fecha" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Fecha de Liquidación</label>
                    <input id="fecha" name="fecha" type="date" required x-model="fecha"
                           class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm font-mono">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="bonos" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Bonos Extra</label>
                        <input id="bonos" name="bonos" type="number" step="0.01" min="0" required x-model="bonos" @input="recalculate()"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm font-mono"
                               placeholder="0.00">
                    </div>
                    <div>
                        <label for="descuentos" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Descuentos Extra</label>
                        <input id="descuentos" name="descuentos" type="number" step="0.01" min="0" required x-model="descuentos" @input="recalculate()"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm font-mono"
                               placeholder="0.00">
                    </div>
                </div>

                <div class="hidden">
                    <label for="tipo_cambio" class="block text-sm font-medium text-slate-300">Tipo de Cambio (T/C)</label>
                    <input id="tipo_cambio" name="tipo_cambio" type="number" step="0.01" min="0.01" required x-model="tipoCambio" @input="recalculate()"
                           class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono">
                </div>

                <div>
                    <label for="observacion" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 flex justify-between">
                        <span>Observación</span>
                        <span x-show="parseFloat(descuentos) > 0" class="text-red-400 text-[10px] font-bold" x-cloak>* Requerido por descuento</span>
                    </label>
                    <textarea id="observacion" name="observacion" rows="2" x-model="observacion"
                              :required="parseFloat(descuentos) > 0"
                              :class="parseFloat(descuentos) > 0 && !observacion.trim() ? 'border-red-500/50 focus:ring-red-500' : 'border-slate-700 focus:ring-teal-500'"
                              class="mt-1 block w-full px-3 py-2 bg-slate-900 rounded-lg text-slate-100 focus:outline-none text-sm transition-colors duration-150"
                              :placeholder="parseFloat(descuentos) > 0 ? 'Explica el motivo del descuento...' : 'Ej. Liquidación semanal'"></textarea>
                    <div x-show="parseFloat(descuentos) > 0 && !observacion.trim()" class="text-red-400 text-[10px] mt-1 font-semibold flex items-center" x-cloak>
                        <i class="fa-solid fa-circle-xmark mr-1"></i> Debes especificar el motivo del descuento.
                    </div>
                </div>

                <!-- Forma de Pago -->
                <div>
                    <label for="metodo_pago" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Forma de Pago</label>
                    <select id="metodo_pago" name="metodo_pago" required
                            class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm font-bold text-teal-500">
                        <option value="efectivo">Efectivo</option>
                        <option value="cheque">Cheque</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>

                <!-- Monto Real Pagado (Efectivo Entregado) -->
                <div x-show="trabajadorId" class="space-y-1 transition-all duration-200" x-cloak>
                    <label for="monto_pagado" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                        <span x-show="tipoPagoPlanilla === 'completo'">Monto a Pagar (Total Neto)</span>
                        <span x-show="tipoPagoPlanilla === 'adelanto'">Monto Entregado / Parcial</span>
                    </label>
                    <input id="monto_pagado" name="monto_pagado" type="number" step="0.01" min="0" required 
                           x-model="montoPagado" 
                           :disabled="tipoPagoPlanilla === 'completo'"
                           @input="userEditedMontoPagado = true; recalculate()"
                           :class="tipoPagoPlanilla === 'completo' ? 'bg-slate-800/80 text-slate-450 border-slate-800 cursor-not-allowed' : 'bg-slate-900 border-slate-700 text-teal-500'"
                           class="mt-1 block w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm font-mono font-bold">
                    
                    <div class="pt-1.5">
                        <div x-show="tipoPagoPlanilla === 'adelanto' && parseFloat(montoPagado) < parseFloat(neto)" class="inline-flex items-center px-2.5 py-1 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/25">
                            <i class="fa-solid fa-triangle-exclamation mr-1.5"></i> Quedará un saldo pendiente de Bs. <span x-text="(neto - parseFloat(montoPagado)).toFixed(2)"></span> a favor del trabajador.
                        </div>
                        <div x-show="tipoPagoPlanilla === 'adelanto' && parseFloat(montoPagado) > parseFloat(neto)" class="inline-flex items-center px-2.5 py-1 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/25">
                            <i class="fa-solid fa-piggy-bank mr-1.5 text-emerald-500"></i> Se creará un anticipo de Bs. <span x-text="(parseFloat(montoPagado) - neto).toFixed(2)"></span> a cuenta del trabajador.
                        </div>
                        <div x-show="tipoPagoPlanilla === 'completo' || parseFloat(montoPagado) == parseFloat(neto)" class="inline-flex items-center px-2.5 py-1 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-450 border border-emerald-500/25">
                            <i class="fa-solid fa-circle-check mr-1.5"></i> Planilla completamente saldada (100% Pago Neto).
                        </div>
                        <div x-show="tipoPagoPlanilla === 'completo' && totalSaldosPendientes > 0" class="mt-2 flex items-start p-2.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-455 border border-amber-500/30">
                            <i class="fa-solid fa-circle-exclamation mr-2 mt-0.5 text-xs text-amber-400"></i>
                            <div>
                                <span>Se liquidará un saldo pendiente acumulado de <strong>Bs. <span x-text="totalSaldosPendientes.toFixed(2)"></span></strong>.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Entregado por -->
                <div>
                    <label for="entregado_por" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Entregado por</label>
                    <input id="entregado_por" name="entregado_por" type="text" required
                           value="{{ Auth::user()->name ?? 'Administración General' }}"
                           class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm">
                </div>

                <!-- Submit Button with Tactile 3D Green Style -->
                <div class="pt-2">
                    <button type="submit" :disabled="!trabajadorId || (parseFloat(descuentos) > 0 && !observacion.trim())"
                            class="btn-3d-teal w-full flex justify-center py-3 px-4 rounded-xl text-xs font-extrabold uppercase tracking-wider text-white transition duration-150">
                        Procesar y Confirmar Pago <i class="fa-solid fa-circle-check ml-2 self-center"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Col 2: Details Breakdown and Advances -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Loader State -->
            <div x-show="loading" class="glass-card rounded-xl p-8 flex flex-col items-center justify-center space-y-4">
                <i class="fa-solid fa-circle-notch fa-spin text-3xl text-teal-500"></i>
                <p class="text-sm text-slate-400">Cargando perfil laboral y anticipos pendientes...</p>
            </div>

            <!-- Empty State -->
            <div x-show="!trabajadorId && !loading" class="glass-card rounded-xl p-8 text-center space-y-3">
                <div class="w-16 h-16 rounded-full bg-slate-900 flex items-center justify-center mx-auto text-slate-600 border border-slate-800">
                    <i class="fa-solid fa-user-plus text-xl"></i>
                </div>
                <div>
                    <h4 class="text-md font-bold text-slate-200">Ningún personal seleccionado</h4>
                    <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">Selecciona un trabajador del listado para cargar automáticamente su ficha de contrato laboral, tarifas acordadas y anticipos acumulados.</p>
                </div>
            </div>

            <!-- Wizard details view -->
            <div x-show="trabajadorId && !loading" class="space-y-6" x-cloak>
                
                <!-- Worker Mini Profile -->
                <div class="glass-card rounded-xl p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <span class="text-[10px] text-teal-500 font-mono tracking-wider uppercase">Ficha Laboral Asignada</span>
                        <h2 class="text-xl font-bold text-slate-100 mt-0.5" x-text="trabajador ? trabajador.nombre : ''"></h2>
                        <div class="flex items-center space-x-4 text-xs text-slate-400 mt-1">
                            <span><i class="fa-solid fa-id-card mr-1"></i> C.I: <strong class="text-slate-350" x-text="trabajador ? (trabajador.ci || 'Sin registrar') : ''"></strong></span>
                            <span class="w-1 h-1 bg-slate-700 rounded-full"></span>
                            <span><i class="fa-solid fa-mountain mr-1"></i> Bocamina: <strong class="text-slate-350" x-text="bocaminaNombre"></strong></span>
                        </div>
                    </div>
                </div>

                <!-- Pending Balances from Previous Weeks (Credit to worker) -->
                <div x-show="saldosPendientes.length > 0" class="glass-card rounded-xl p-6 space-y-4 border border-teal-500/15" x-cloak>
                    <div class="border-b border-slate-850 pb-3">
                        <h3 class="text-sm font-bold text-slate-200 flex items-center">
                            <i class="fa-solid fa-clock-rotate-left mr-2 text-teal-500"></i> Saldos Pendientes de Planillas Anteriores (A completar)
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-1">Saldos adeudados al trabajador de semanas previas que se cancelarán en este pago.</p>
                    </div>

                    <div class="space-y-3 font-mono text-xs">
                        <template x-for="sal in saldosPendientes" :key="sal.id">
                            <div class="flex justify-between items-center p-3 rounded-lg bg-teal-500/5 border border-teal-500/10">
                                <div>
                                    <span class="text-slate-350 font-bold block" x-text="'Planilla del ' + new Date(sal.fecha.replace(/-/g, '\/')).toLocaleDateString('es-ES', {year: 'numeric', month: '2-digit', day: '2-digit'})"></span>
                                    <span class="text-[10px] text-slate-450 font-sans block mt-0.5" x-text="sal.observacion || 'Liquidación parcial'"></span>
                                    <span class="text-[9px] text-slate-500 block mt-1" x-text="'Neto Original: Bs. ' + parseFloat(sal.neto).toFixed(2) + ' | Pagado anterior semana: Bs. ' + parseFloat(sal.monto_pagado).toFixed(2)"></span>
                                </div>
                                <div class="text-right">
                                    <span class="text-slate-500 text-[9px] block uppercase font-sans">Por Pagar:</span>
                                    <span class="text-teal-500 font-bold text-sm" x-text="'Bs. ' + parseFloat(sal.saldo_pendiente).toFixed(2)"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
 
                <!-- Outstanding Advances Section -->
                <div class="glass-card rounded-xl p-6 space-y-4">
                    <div class="border-b border-slate-850 pb-3">
                        <h3 class="text-sm font-bold text-slate-200 flex items-center">
                            <i class="fa-solid fa-money-bill-trend-up mr-2 text-teal-500"></i> Descontar Anticipos Vigentes (A Cuenta)
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-1">Selecciona cuáles anticipos deseas descontar de esta liquidación.</p>
                    </div>

                    <!-- Advances checklist -->
                    <template x-if="anticipos.length === 0">
                        <div class="text-center py-6 text-slate-500 text-xs">
                            <i class="fa-solid fa-circle-check mr-1"></i> El trabajador no tiene anticipos pendientes a cuenta.
                        </div>
                    </template>

                    <template x-if="anticipos.length > 0">
                        <div class="space-y-3">
                            <template x-for="ant in anticipos" :key="ant.id">
                                <div class="flex items-center justify-between p-3 rounded-lg border transition duration-150"
                                     :class="ant.aplicado ? 'bg-red-500/5 border-red-500/20' : 'bg-slate-900/40 border-slate-800'">
                                    
                                    <div class="flex items-center space-x-3">
                                        <input type="checkbox" :id="'ant-' + ant.id" x-model="ant.aplicado" @change="recalculate()"
                                               class="h-4.5 w-4.5 rounded border-slate-700 text-teal-500 focus:ring-teal-500 bg-slate-950">
                                        <label :for="'ant-' + ant.id" class="cursor-pointer">
                                            <div class="text-xs font-bold text-slate-200" x-text="'Anticipo del ' + new Date(ant.fecha.replace(/-/g, '\/')).toLocaleDateString('es-ES', {year: 'numeric', month: '2-digit', day: '2-digit'})"></div>
                                            <div class="text-[10px] text-slate-400 font-mono mt-0.5" x-text="'Saldo pendiente: Bs. ' + parseFloat(ant.saldo).toFixed(2)"></div>
                                        </label>
                                    </div>

                                    <!-- Amount to deduct (Only input if checked) -->
                                    <div class="flex items-center space-x-2" x-show="ant.aplicado">
                                        <span class="text-[10px] text-slate-455 uppercase font-mono">Descontar:</span>
                                        <div class="relative rounded-md shadow-sm w-28">
                                            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                                <span class="text-slate-500 text-[10px]">Bs</span>
                                            </div>
                                            <input type="number" step="0.01" min="0" :max="ant.saldo"
                                                   :name="'deducciones_anticipos['+ant.id+']'"
                                                   x-model="ant.liveDeduccion"
                                                   @input="recalculate()"
                                                   class="block w-full pl-7 pr-2 py-1 bg-slate-950 border border-red-500/30 rounded text-right text-xs font-mono font-bold text-red-400 focus:outline-none focus:border-red-500">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Live Payroll Breakdown Card (With corrected subtotal box contrast class) -->
                <div class="glass-card rounded-xl p-6 teal-glow border border-teal-500/25">
                    <h3 class="text-sm font-bold text-slate-200 border-b border-slate-850 pb-3 flex items-center">
                        <i class="fa-solid fa-receipt mr-2 text-teal-500"></i> Resumen de Liquidación de Planilla
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-3 text-xs">
                        <div class="space-y-2 font-mono">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Trabajo Realizado (Semana):</span>
                                <span class="text-slate-200" x-text="'Bs. ' + parseFloat(subtotal).toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Bonos Adicionales (+):</span>
                                <span class="text-emerald-400" x-text="'+Bs. ' + (parseFloat(bonos) || 0).toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Descuentos Extra (-):</span>
                                <span class="text-red-400" x-text="'-Bs. ' + (parseFloat(descuentos) || 0).toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between" x-show="totalSaldosPendientes > 0" x-cloak>
                                <span class="text-slate-400">Saldos Semanas Anteriores (+):</span>
                                <span class="text-emerald-400 font-bold" x-text="'+Bs. ' + totalSaldosPendientes.toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between border-b border-slate-800 pb-2.5">
                                <span class="text-slate-400">Anticipos Descontados (-):</span>
                                <span class="text-red-400 font-bold" x-text="'-Bs. ' + anticiposDescontados.toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between items-center pt-2.5">
                                <span class="text-slate-200 font-bold uppercase tracking-wide text-sm">Pago Neto Recibir:</span>
                                <span class="text-emerald-400 font-bold text-2xl" x-text="'Bs. ' + neto.toFixed(2)"></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    function paymentWizard() {
        return {
            trabajadorId: '',
            bocaminaFiltroId: '',
            trabajadoresList: @json($trabajadores),
            fecha: '{{ now()->toDateString() }}',
            cantidadTrabajada: 1, // Set to 1 by default
            tarifaPago: 0,
            bonos: 0,
            descuentos: 0,
            tipoCambio: 6.96,
            observacion: '',
            loading: false,
            
            // Loaded dynamically
            trabajador: null,
            bocaminaNombre: '',
            cargo: '',
            tipoContratoNombre: '',
            tarifaAcordada: 0,
            anticipos: [],
            saldosPendientes: [],
            totalSaldosPendientes: 0,
            montoPagado: 0,
            userEditedMontoPagado: false,
            tipoPagoPlanilla: 'completo',
            
            // Live calculated
            subtotal: 0,
            anticiposDescontados: 0,
            neto: 0,

            get filteredTrabajadores() {
                if (!this.bocaminaFiltroId) {
                    return this.trabajadoresList;
                }
                return this.trabajadoresList.filter(t => {
                    return t.bocamina_id == this.bocaminaFiltroId;
                });
            },
            
            async onTrabajadorChange() {
                if (!this.trabajadorId) {
                    this.clear();
                    return;
                }
                this.loading = true;
                try {
                    const res = await fetch('/pagos/trabajador-data/' + this.trabajadorId);
                    const data = await res.json();
                    
                    this.trabajador = data.trabajador;
                    this.bocaminaNombre = data.bocamina_nombre;
                    this.cargo = data.cargo;
                    this.tipoContratoNombre = data.tipo_contrato_nombre;
                    this.tarifaAcordada = parseFloat(data.tarifa_acordada) || 0.00;
                    
                    // Pre-populate hidden calculations values
                    this.cantidadTrabajada = 1;
                    this.subtotal = this.tarifaAcordada;
                    this.tarifaPago = this.subtotal;

                    this.saldosPendientes = data.saldos_pendientes || [];
                    this.totalSaldosPendientes = parseFloat(data.total_saldos_pendientes) || 0;
                    
                    this.userEditedMontoPagado = false;
                    this.tipoPagoPlanilla = 'completo';
                    
                    // Map advances
                    this.anticipos = data.anticipos.map(a => ({
                        ...a,
                        aplicado: false,
                        liveDeduccion: 0
                    }));
                    this.recalculate();
                } catch (e) {
                    console.error('Error loading worker details', e);
                } finally {
                    this.loading = false;
                }
            },
            
            onSubtotalInput() {
                this.subtotal = parseFloat(this.subtotal) || 0;
                this.tarifaPago = this.subtotal; // Set tarifa to match the subtotal so subtotal = tarifa * 1
                this.cantidadTrabajada = 1;      // Ensure quantity is 1
                this.recalculate();
            },
            
            recalculate() {
                const sub = parseFloat(this.subtotal) || 0;
                const b = parseFloat(this.bonos) || 0;
                const d = parseFloat(this.descuentos) || 0;
                const prevSaldos = parseFloat(this.totalSaldosPendientes) || 0;
                
                let capacidad = sub + b - d + prevSaldos;
                if (capacidad < 0) capacidad = 0;
                
                let totalDeducido = 0;
                
                this.anticipos.forEach(a => {
                    if (!a.aplicado) {
                        a.liveDeduccion = 0;
                        return;
                    }
                    
                    if (a.liveDeduccion === undefined || a.liveDeduccion === null || a.liveDeduccion === 0) {
                        a.liveDeduccion = Math.min(parseFloat(a.saldo), capacidad);
                    } else {
                        a.liveDeduccion = Math.min(parseFloat(a.saldo), parseFloat(a.liveDeduccion) || 0);
                    }
                    
                    a.liveDeduccion = Math.min(a.liveDeduccion, capacidad);
                    
                    totalDeducido += a.liveDeduccion;
                    capacidad -= a.liveDeduccion;
                });
                
                this.anticiposDescontados = totalDeducido;
                this.neto = sub + b - d - totalDeducido + prevSaldos;
                if (this.tipoPagoPlanilla === 'completo') {
                    this.montoPagado = this.neto;
                } else if (!this.userEditedMontoPagado) {
                    this.montoPagado = this.neto;
                }
            },
            
            clear() {
                this.trabajador = null;
                this.bocaminaNombre = '';
                this.cargo = '';
                this.tipoContratoNombre = '';
                this.tarifaAcordada = 0;
                this.cantidadTrabajada = 1;
                this.tarifaPago = 0;
                this.anticipos = [];
                this.saldosPendientes = [];
                this.totalSaldosPendientes = 0;
                this.montoPagado = 0;
                this.userEditedMontoPagado = false;
                this.tipoPagoPlanilla = 'completo';
                this.subtotal = 0;
                this.anticiposDescontados = 0;
                this.neto = 0;
                this.tipoCambio = 6.96;
            }
        };
    }
</script>
@endpush
