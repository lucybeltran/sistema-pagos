@extends('layouts.app')

@section('title', 'Procesar Pago')

@section('content')
<div x-data="paymentWizard()" class="space-y-6">

    <!-- Header -->
    <div>
        <a href="{{ route('pagos.index') }}" class="text-xs text-slate-400 hover:text-amber-500 flex items-center font-medium transition duration-150">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Volver al Historial
        </a>
        <h1 class="text-3xl font-bold tracking-tight text-slate-100 mt-1">Procesar Liquidación de Pago</h1>
        <p class="text-sm text-slate-400 mt-1">Genera la planilla de pago neto liquidando trabajos y deduciendo anticipos automáticamente.</p>
    </div>

    <!-- Wizard Grid -->
    <form action="{{ route('pagos.store') }}" method="POST" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        @csrf
        <!-- Col 1: Inputs & Details Selection -->
        <div class="glass-card rounded-xl p-6 lg:col-span-1 space-y-5 h-fit">
            <h3 class="text-md font-bold text-slate-200 border-b border-slate-800 pb-3 flex items-center">
                <i class="fa-solid fa-calculator mr-2 text-amber-500"></i> Datos de Liquidación
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label for="bocamina_filtro_id" class="block text-sm font-medium text-slate-300">Filtrar por Bocamina</label>
                    <select id="bocamina_filtro_id" x-model="bocaminaFiltroId" @change="trabajadorId = ''; clear()"
                            class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-bold text-amber-500">
                        <option value="">-- Todas las Bocaminas --</option>
                        @foreach($bocaminas as $bocamina)
                            <option value="{{ $bocamina->id }}">{{ $bocamina->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="trabajador_id" class="block text-sm font-medium text-slate-300">Seleccionar Trabajador / Contratista</label>
                    <select id="trabajador_id" name="trabajador_id" required x-model="trabajadorId" @change="onTrabajadorChange()"
                            class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-bold">
                        <option value="">-- Seleccionar Trabajador / Contratista --</option>
                        <template x-for="t in filteredTrabajadores" :key="t.id">
                            <option :value="t.id" x-text="t.nombre + ' (CI: ' + t.ci + ') - ' + (t.bocamina ? t.bocamina.nombre : '')"></option>
                        </template>
                    </select>
                </div>

                <!-- Tipo de Pago / Liquidación -->
                <div x-show="trabajadorId" class="space-y-1 transition-all duration-200" x-cloak>
                    <label class="block text-sm font-medium text-slate-350">Tipo de Pago / Liquidación</label>
                    <div class="grid grid-cols-2 gap-2 mt-1">
                        <button type="button" @click="tipoPagoPlanilla = 'completo'; userEditedMontoPagado = false; recalculate()"
                                :class="tipoPagoPlanilla === 'completo' ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-slate-950 font-bold' : 'bg-slate-900 text-slate-400 border border-slate-800 hover:text-slate-200'"
                                class="px-3 py-2 rounded-lg text-xs font-semibold text-center transition duration-150 shadow-md">
                            <i class="fa-solid fa-circle-check mr-1"></i> Pago Completo
                        </button>
                        <button type="button" @click="tipoPagoPlanilla = 'adelanto'; userEditedMontoPagado = true; montoPagado = (neto * 0.5).toFixed(2); recalculate()"
                                :class="tipoPagoPlanilla === 'adelanto' ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-slate-950 font-bold' : 'bg-slate-900 text-slate-400 border border-slate-800 hover:text-slate-200'"
                                class="px-3 py-2 rounded-lg text-xs font-semibold text-center transition duration-150 shadow-md">
                            <i class="fa-solid fa-hourglass-half mr-1"></i> Dar Adelanto / Parcial
                        </button>
                    </div>
                </div>

                <div>
                    <label for="fecha" class="block text-sm font-medium text-slate-300">Fecha de Liquidación</label>
                    <input id="fecha" name="fecha" type="date" required x-model="fecha"
                           class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono">
                </div>

                <div>
                    <label for="subtotal" class="block text-sm font-medium text-slate-300">Monto por Trabajo Realizado (Bs.)</label>
                    <input id="subtotal" name="subtotal" type="number" step="0.01" min="0" required x-model="subtotal" @input="recalculate()"
                           class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono font-bold text-amber-500"
                           placeholder="0.00">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="bonos" class="block text-sm font-medium text-slate-300">Bonos Adicionales</label>
                        <input id="bonos" name="bonos" type="number" step="0.01" min="0" required x-model="bonos" @input="recalculate()"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono"
                               placeholder="0.00">
                    </div>
                    <div>
                        <label for="descuentos" class="block text-sm font-medium text-slate-300">Descuentos Extra</label>
                        <input id="descuentos" name="descuentos" type="number" step="0.01" min="0" required x-model="descuentos" @input="recalculate()"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono"
                               placeholder="0.00">
                    </div>
                </div>

                <div class="hidden">
                    <label for="tipo_cambio" class="block text-sm font-medium text-slate-300">Tipo de Cambio (T/C)</label>
                    <input id="tipo_cambio" name="tipo_cambio" type="number" step="0.01" min="0.01" required x-model="tipoCambio" @input="recalculate()"
                           class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono"
                           placeholder="6.96">
                </div>

                <div>
                    <label for="observacion" class="block text-sm font-medium text-slate-300 flex justify-between">
                        <span>Observación</span>
                        <span x-show="parseFloat(descuentos) > 0" class="text-red-400 text-xs font-bold" x-cloak>* Requerido por descuento</span>
                    </label>
                    <textarea id="observacion" name="observacion" rows="2" x-model="observacion"
                              :required="parseFloat(descuentos) > 0"
                              :class="parseFloat(descuentos) > 0 && !observacion.trim() ? 'border-red-500/50 focus:ring-red-500' : 'border-slate-700 focus:ring-amber-500'"
                              class="mt-1 block w-full px-3 py-2 bg-slate-900 rounded-lg text-slate-100 focus:outline-none text-sm transition-colors duration-150"
                              :placeholder="parseFloat(descuentos) > 0 ? 'Explica detalladamente por qué se realiza el descuento...' : 'Ej. Liquidación semanal'"></textarea>
                    <div x-show="parseFloat(descuentos) > 0 && !observacion.trim()" class="text-red-400 text-[10px] mt-1 font-semibold flex items-center" x-cloak>
                        <i class="fa-solid fa-circle-xmark mr-1"></i> Debes especificar el motivo del descuento.
                    </div>
                </div>

                <!-- Forma de Pago -->
                <div>
                    <label for="metodo_pago" class="block text-sm font-medium text-slate-300">Forma de Pago</label>
                    <select id="metodo_pago" name="metodo_pago" required
                            class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-bold text-amber-500">
                        <option value="efectivo">Efectivo</option>
                        <option value="cheque">Cheque</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>


                <!-- Monto Real Pagado (Efectivo Entregado) -->
                <div x-show="trabajadorId" class="space-y-1 transition-all duration-200" x-cloak>
                    <label for="monto_pagado" class="block text-sm font-medium text-slate-300">
                        <span x-show="tipoPagoPlanilla === 'completo'">Monto a Pagar (Total Neto)</span>
                        <span x-show="tipoPagoPlanilla === 'adelanto'">Monto Real a Pagar (Efectivo Entregado)</span>
                    </label>
                    <input id="monto_pagado" name="monto_pagado" type="number" step="0.01" min="0" required 
                           x-model="montoPagado" 
                           :disabled="tipoPagoPlanilla === 'completo'"
                           @input="userEditedMontoPagado = true; recalculate()"
                           :class="tipoPagoPlanilla === 'completo' ? 'bg-slate-800/80 text-slate-400 border-slate-800 cursor-not-allowed' : 'bg-slate-900 border-slate-700 text-amber-500'"
                           class="mt-1 block w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm font-mono font-bold">
                    
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
                        <div x-show="tipoPagoPlanilla === 'completo' && totalSaldosPendientes > 0" class="mt-2 flex items-start p-2.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-450 border border-amber-500/30 animate-pulse">
                            <i class="fa-solid fa-circle-exclamation mr-2 mt-0.5 text-xs text-amber-400"></i>
                            <div>
                                <span>RECUERDO: Se está incluyendo un saldo pendiente de la semana anterior de <strong>Bs. <span x-text="totalSaldosPendientes.toFixed(2)"></span></strong>. El pago total neto de Bs. <span x-text="neto.toFixed(2)"></span> liquidará esta deuda por completo.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Entregado por -->
                <div>
                    <label for="entregado_por" class="block text-sm font-medium text-slate-300">Entregado por (Persona que paga)</label>
                    <input id="entregado_por" name="entregado_por" type="text" required
                           value="{{ Auth::user()->name ?? 'Administración TORMAN' }}"
                           class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" :disabled="!trabajadorId || (parseFloat(descuentos) > 0 && !observacion.trim())"
                            :class="(!trabajadorId || (parseFloat(descuentos) > 0 && !observacion.trim())) ? 'opacity-50 cursor-not-allowed' : ''"
                            class="w-full flex justify-center py-2.5 px-4 rounded-lg text-sm font-bold text-slate-950 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 transition duration-150">
                        Procesar y Confirmar Pago <i class="fa-solid fa-circle-check ml-2 self-center"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Col 2: Details Breakdown Unpaid works and Advances -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Loader State -->
            <div x-show="loading" class="glass-card rounded-xl p-8 flex flex-col items-center justify-center space-y-4">
                <i class="fa-solid fa-circle-notch fa-spin text-3xl text-amber-500"></i>
                <p class="text-sm text-slate-400">Buscando planilla y anticipos pendientes...</p>
            </div>

            <!-- Empty State -->
            <div x-show="!trabajadorId && !loading" class="glass-card rounded-xl p-8 text-center space-y-3">
                <div class="w-16 h-16 rounded-full bg-slate-900 flex items-center justify-center mx-auto text-slate-600 border border-slate-800">
                    <i class="fa-solid fa-user-plus text-xl"></i>
                </div>
                <div>
                    <h4 class="text-md font-bold text-slate-200">Ningún contratista seleccionado</h4>
                    <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">Selecciona un trabajador o filtra por bocamina para cargar los trabajos acumulados de la semana y los anticipos a cuenta.</p>
                </div>
            </div>

            <!-- Wizard details view -->
            <div x-show="trabajadorId && !loading" class="space-y-6" x-cloak>
                
                <!-- Worker Mini Profile -->
                <div class="glass-card rounded-xl p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <span class="text-[10px] text-amber-500 font-mono tracking-wider uppercase">Contratista Seleccionado</span>
                        <h2 class="text-xl font-bold text-slate-100 mt-0.5" x-text="trabajador ? trabajador.nombre : ''"></h2>
                        <div class="flex items-center space-x-4 text-xs text-slate-400 mt-1">
                            <span><i class="fa-solid fa-id-card mr-1"></i> C.I: <strong class="text-slate-300" x-text="trabajador ? trabajador.ci : ''"></strong></span>
                            <span class="w-1 h-1 bg-slate-750 rounded-full"></span>
                            <span><i class="fa-solid fa-mountain mr-1"></i> Bocamina: <strong class="text-slate-300" x-text="trabajador && trabajador.bocamina ? trabajador.bocamina.nombre : 'Ninguna'"></strong></span>
                        </div>
                    </div>
                </div>

                <!-- Unpaid Trabajos Section -->
                <div class="glass-card rounded-xl p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-850 pb-3">
                        <h3 class="text-sm font-bold text-slate-200 flex items-center">
                            <i class="fa-solid fa-list-check mr-2 text-amber-500"></i> Avance de Trabajos de la Semana (No Liquidados)
                        </h3>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono bg-slate-900 text-slate-400 border border-slate-800"
                              x-text="trabajos.length + ' pendientes'"></span>
                    </div>

                    <!-- Work items list -->
                    <template x-if="trabajos.length === 0">
                        <div class="text-center py-6 text-slate-500 text-xs">
                            <i class="fa-solid fa-circle-info mr-1"></i> No se registran trabajos individuales pendientes en la semana para este contratista. Puedes ingresar un monto subtotal a pagar directamente a la izquierda.
                        </div>
                    </template>

                    <template x-if="trabajos.length > 0">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-slate-300">
                                <thead>
                                    <tr class="text-[10px] text-slate-500 uppercase tracking-wider border-b border-slate-800 font-bold">
                                        <th class="py-2.5">Fecha</th>
                                        <th class="py-2.5">Métrica / Detalle</th>
                                        <th class="py-2.5 text-right">Cantidad</th>
                                        <th class="py-2.5 text-right">Precio Unit. (Bs.)</th>
                                        <th class="py-2.5 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/40">
                                    <template x-for="job in trabajos" :key="job.id">
                                        <tr>
                                            <td class="py-2.5 font-mono text-slate-400" x-text="job.fecha"></td>
                                            <td class="py-2.5 font-medium text-slate-200" x-text="job.observacion || 'Avance semanal'"></td>
                                            <td class="py-2.5 text-right font-mono" x-text="parseFloat(job.cantidad).toFixed(2)"></td>
                                            <td class="py-2.5 text-right font-mono" x-text="'Bs. ' + parseFloat(job.precio_unitario).toFixed(2)"></td>
                                            <td class="py-2.5 text-right font-mono font-bold text-slate-100" x-text="'Bs. ' + parseFloat(job.subtotal).toFixed(2)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>

                <!-- Pending Balances from Previous Weeks (Credit to worker) -->
                <div x-show="saldosPendientes.length > 0" class="glass-card rounded-xl p-6 space-y-4 border border-amber-500/15" x-cloak>
                    <div class="border-b border-slate-850 pb-3">
                        <h3 class="text-sm font-bold text-slate-200 flex items-center">
                            <i class="fa-solid fa-clock-rotate-left mr-2 text-amber-500"></i> Saldos Pendientes de Planillas Anteriores (A completar)
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-1">Saldos adeudados al contratista de semanas previas que se cancelarán en este pago.</p>
                    </div>

                    <div class="space-y-3 font-mono text-xs">
                        <template x-for="sal in saldosPendientes" :key="sal.id">
                            <div class="flex justify-between items-center p-3 rounded-lg bg-amber-500/5 border border-amber-500/10">
                                <div>
                                    <span class="text-slate-350 font-bold block" x-text="'Planilla del ' + new Date(sal.fecha.replace(/-/g, '\/')).toLocaleDateString('es-ES', {year: 'numeric', month: '2-digit', day: '2-digit'})"></span>
                                    <span class="text-[10px] text-slate-450 font-sans block mt-0.5" x-text="sal.observacion || 'Liquidación parcial'"></span>
                                    <span class="text-[9px] text-slate-500 block mt-1" x-text="'Neto Original: Bs. ' + parseFloat(sal.neto).toFixed(2) + ' | Pagado anterior semana: Bs. ' + parseFloat(sal.monto_pagado).toFixed(2)"></span>
                                </div>
                                <div class="text-right">
                                    <span class="text-slate-500 text-[9px] block uppercase font-sans">Por Pagar:</span>
                                    <span class="text-amber-500 font-bold text-sm" x-text="'Bs. ' + parseFloat(sal.saldo_pendiente).toFixed(2)"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
 
                <!-- Outstanding Advances Section -->
                <div class="glass-card rounded-xl p-6 space-y-4">
                    <div class="border-b border-slate-850 pb-3">
                        <h3 class="text-sm font-bold text-slate-200 flex items-center">
                            <i class="fa-solid fa-money-bill-trend-up mr-2 text-amber-500"></i> Descontar Anticipos Vigentes (A Cuenta)
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-1">Selecciona cuáles anticipos deseas descontar de esta planilla semanal.</p>
                    </div>

                    <!-- Advances checklist -->
                    <template x-if="anticipos.length === 0">
                        <div class="text-center py-6 text-slate-500 text-xs">
                            <i class="fa-solid fa-circle-check mr-1"></i> El contratista no tiene anticipos pendientes a cuenta.
                        </div>
                    </template>

                    <template x-if="anticipos.length > 0">
                        <div class="space-y-3">
                            <template x-for="ant in anticipos" :key="ant.id">
                                <div class="flex items-center justify-between p-3 rounded-lg border transition duration-150"
                                     :class="ant.aplicado ? 'bg-red-500/5 border-red-500/20' : 'bg-slate-900/40 border-slate-800'">
                                    
                                    <div class="flex items-center space-x-3">
                                        <input type="checkbox" :id="'ant-' + ant.id" x-model="ant.aplicado" @change="recalculate()"
                                               class="h-4.5 w-4.5 rounded border-slate-700 text-amber-500 focus:ring-amber-500 bg-slate-950">
                                        <label :for="'ant-' + ant.id" class="cursor-pointer">
                                            <div class="text-xs font-bold text-slate-200" x-text="'Anticipo del ' + ant.fecha"></div>
                                            <div class="text-[10px] text-slate-400 font-mono mt-0.5" x-text="'Saldo pendiente: Bs. ' + parseFloat(ant.saldo).toFixed(2)"></div>
                                        </label>
                                    </div>

                                    <!-- Amount to deduct (Only input if checked) -->
                                    <div class="flex items-center space-x-2" x-show="ant.aplicado">
                                        <span class="text-[10px] text-slate-450 uppercase font-mono">Descontar:</span>
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

                <!-- Live Payroll Breakdown Card -->
                <div class="glass-card rounded-xl p-6 gold-glow border border-amber-500/25">
                    <h3 class="text-sm font-bold text-slate-200 border-b border-slate-850 pb-3 flex items-center">
                        <i class="fa-solid fa-receipt mr-2 text-amber-500"></i> Resumen de Liquidación de Planilla
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-3 text-xs">
                        <div class="space-y-2 font-mono">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Trabajo Realizado (Semana):</span>
                                <span class="text-slate-200" x-text="'Bs. ' + subtotal.toFixed(2)"></span>
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
                            <div class="hidden">
                                <span>Equivalente en Dólares ($us):</span>
                                <span class="font-bold font-mono text-slate-200" x-text="'$us. ' + (isNaN(neto / tipoCambio) || tipoCambio <= 0 ? '0.00' : (neto / tipoCambio).toFixed(2))"></span>
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
            bonos: 0,
            descuentos: 0,
            tipoCambio: 6.96,
            observacion: '',
            loading: false,
            
            // Data loaded dynamically
            trabajador: null,
            trabajos: [],
            anticipos: [],
            saldosPendientes: [],
            totalSaldosPendientes: 0,
            montoPagado: 0,
            userEditedMontoPagado: false,
            tipoPagoPlanilla: 'completo',
            
            // Live Totals
            subtotal: 0,
            anticiposDescontados: 0,
            neto: 0,

            get filteredTrabajadores() {
                if (!this.bocaminaFiltroId) {
                    return this.trabajadoresList;
                }
                return this.trabajadoresList.filter(t => t.bocamina_id == this.bocaminaFiltroId);
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
                    this.trabajos = data.trabajos;
                    this.saldosPendientes = data.saldos_pendientes || [];
                    this.totalSaldosPendientes = parseFloat(data.total_saldos_pendientes) || 0;
                    this.userEditedMontoPagado = false;
                    this.tipoPagoPlanilla = 'completo';
                    // Map advances to support live preview and checkboxes
                    this.anticipos = data.anticipos.map(a => ({
                        ...a,
                        aplicado: false,
                        liveDeduccion: 0
                    }));
                    this.subtotal = parseFloat(data.subtotal) || 0;
                    this.recalculate();
                } catch (e) {
                    console.error('Error cargando los datos del trabajador', e);
                } finally {
                    this.loading = false;
                }
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
                    
                    // If checked but liveDeduccion is 0 or unassigned, suggest maximum possible
                    if (a.liveDeduccion === undefined || a.liveDeduccion === null || a.liveDeduccion === 0) {
                        a.liveDeduccion = Math.min(parseFloat(a.saldo), capacidad);
                    } else {
                        a.liveDeduccion = Math.min(parseFloat(a.saldo), parseFloat(a.liveDeduccion) || 0);
                    }
                    
                    // Cap at remaining capacity
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
                this.trabajos = [];
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
