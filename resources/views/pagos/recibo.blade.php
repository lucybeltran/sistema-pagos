@extends('layouts.app')

@section('title', 'Comprobante de Egreso #' . str_pad($pago->id, 5, '0', STR_PAD_LEFT))

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar (no-print) -->
    <div class="flex items-center justify-between no-print">
        <div>
            <a href="{{ route('pagos.index') }}" class="text-xs text-slate-400 hover:text-amber-500 flex items-center font-medium transition duration-150">
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Volver a Historial
            </a>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100 mt-1">Comprobante de Pago</h1>
        </div>
        <div class="flex space-x-3">
            <button onclick="window.print()" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-sm font-bold text-slate-950 transition duration-150 shadow-lg shadow-orange-500/10">
                <i class="fa-solid fa-print mr-2"></i> Imprimir Recibo
            </button>
        </div>
    </div>

    <!-- Printable Area (White Paper Style Container) -->
    <div class="mx-auto max-w-4xl bg-white text-slate-900 border border-slate-300 shadow-xl rounded-xl p-8 md:p-12 print-container font-sans text-sm relative">
        
        <!-- Outer Emerald/Green border resembling Excel / voucher border -->
        <div class="border-2 border-emerald-800 p-6 md:p-8 rounded-lg bg-white">
            
            <!-- Header Grid: Logo, Title/Date, Amounts -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center border-b-2 border-emerald-850 pb-6 mb-6">
                <!-- Column 1: Logo & Company Hexagonal SVG -->
                <div class="md:col-span-4 flex items-center space-x-3">
                    <svg class="w-24 h-16 flex-shrink-0" viewBox="0 0 120 80" xmlns="http://www.w3.org/2000/svg">
                        <!-- Grey Hexagon (LOGO) -->
                        <polygon points="30,5 60,5 75,30 60,55 30,55 15,30" fill="none" stroke="#64748b" stroke-width="2" />
                        <text x="45" y="34" font-family="sans-serif" font-size="10" font-weight="bold" fill="#64748b" text-anchor="middle">LOGO</text>
                        
                        <!-- Blue Hexagon (TORMAN) -->
                        <polygon points="65,25 95,25 110,50 95,75 65,75 50,50" fill="#2563eb" stroke="#1d4ed8" stroke-width="2" />
                        <text x="80" y="54" font-family="sans-serif" font-size="9" font-weight="bold" fill="#ffffff" text-anchor="middle">TORMAN</text>
                    </svg>
                    <div>
                        <h2 class="text-xl font-black uppercase tracking-widest text-slate-850 leading-none">TORMAN</h2>
                        <span class="text-[9px] text-slate-500 font-mono tracking-wider uppercase block mt-1">Control de Operaciones</span>
                    </div>
                </div>

                <!-- Column 2: Centered Large Title in Emerald & Date/Time -->
                <div class="md:col-span-5 text-center">
                    <h1 class="text-3xl font-extrabold tracking-wider text-emerald-800 uppercase">Recibo de Pago</h1>
                    <div class="mt-2 text-slate-600 font-mono text-xs flex flex-col items-center justify-center space-y-1">
                        <div>
                            <span class="font-bold">Nº Correlativo:</span>
                            <span class="text-sm font-bold text-red-650 ml-1 underline decoration-red-500 decoration-2">{{ str_pad($pago->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="text-[10px] text-slate-500 mt-1 space-x-1">
                            <span>Fecha: <strong class="text-slate-800">{{ $pago->fecha->format('d/m/Y') }}</strong></span>
                            <span>|</span>
                            <span>Hora: <strong class="text-slate-800">{{ $pago->created_at->format('H:i:s') }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Column 3: Amount Table (Bs. / USD / TC) as secondary reference block -->
                <div class="md:col-span-3 flex justify-center md:justify-end">
                    <table class="text-xs border-2 border-emerald-800 rounded overflow-hidden w-full max-w-[180px] shadow-sm">
                        <tr class="border-b border-emerald-800 bg-white">
                            <td class="bg-emerald-50 font-bold border-r border-emerald-800 px-3 py-1.5 text-emerald-900">Bs.</td>
                            <td class="px-3 py-1.5 font-mono font-bold text-slate-900 text-right">{{ number_format($pago->monto_pagado, 2, ',', '.') }}</td>
                        </tr>
                        <tr class="border-b border-emerald-800 bg-white">
                            <td class="bg-slate-50 font-bold border-r border-emerald-800 px-3 py-1.5 text-slate-750">$us</td>
                            <td class="px-3 py-1.5 font-mono text-slate-700 text-right">{{ number_format($pago->monto_pagado / $pago->tipo_cambio, 2, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-white">
                            <td class="bg-slate-50 font-bold border-r border-emerald-800 px-3 py-1.5 text-slate-750">T/C</td>
                            <td class="px-3 py-1.5 font-mono text-slate-700 text-right">{{ number_format($pago->tipo_cambio, 2, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Metadata Banner (Bocamina & Contractor details) -->
            <div class="flex flex-col sm:flex-row justify-between items-center bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 mb-6 text-xs text-slate-650 font-mono">
                <div>
                    <span class="font-bold text-slate-500">BOCAMINA:</span>
                    <span class="font-extrabold text-slate-800 uppercase ml-1">{{ $pago->trabajador->bocamina->nombre ?? 'N/A' }}</span>
                </div>
                <div class="mt-1 sm:mt-0">
                    <span class="font-bold text-slate-500">CONTRATISTA:</span>
                    <span class="font-extrabold text-slate-850 uppercase ml-1">{{ $pago->trabajador->nombre }} (C.I. {{ $pago->trabajador->ci }})</span>
                </div>
            </div>

            <!-- Spacious Form Rows with Bottom Borders -->
            <div class="space-y-6 mb-8">
                <!-- Recibí de -->
                <div class="flex flex-col sm:flex-row sm:items-end space-y-1 sm:space-y-0 sm:space-x-3">
                    <span class="text-sm font-bold text-emerald-800 uppercase tracking-wider w-28 flex-shrink-0 pb-1">Recibí de:</span>
                    <div class="flex-grow border-b-2 border-slate-200 pb-1 text-slate-850 font-bold text-sm uppercase px-2 font-mono">
                        TORMAN - ADMINISTRACIÓN (por: {{ $pago->entregado_por ?? 'Administrador' }})
                    </div>
                </div>

                <!-- La suma de -->
                <div class="flex flex-col sm:flex-row sm:items-end space-y-1 sm:space-y-0 sm:space-x-3">
                    <span class="text-sm font-bold text-emerald-800 uppercase tracking-wider w-28 flex-shrink-0 pb-1">La suma de:</span>
                    <div class="flex-grow border-b-2 border-slate-200 pb-1 text-slate-800 font-extrabold text-xs uppercase px-2 font-mono leading-relaxed">
                        {{ $pago->monto_letras }} BOLIVIANOS
                    </div>
                </div>

                <!-- Por concepto de -->
                <div class="flex flex-col sm:flex-row sm:items-end space-y-1 sm:space-y-0 sm:space-x-3">
                    <span class="text-sm font-bold text-emerald-800 uppercase tracking-wider w-28 flex-shrink-0 pb-1">Por concepto de:</span>
                    <div class="flex-grow border-b-2 border-slate-200 pb-1 text-slate-850 text-xs font-semibold uppercase px-2 leading-relaxed">
                        @if($pago->trabajador->contratos()->where('estado', 'activo')->exists())
                            PAGO POR AVANCE EN CONTRATO: {{ $pago->trabajador->contratos()->where('estado', 'activo')->first()->descripcion }} ({{ $pago->trabajador->contratos()->where('estado', 'activo')->first()->codigo }})
                        @else
                            LIQUIDACIÓN DE TRABAJOS DE PRODUCCIÓN EN {{ $pago->trabajador->bocamina->nombre }}
                        @endif
                        @if($pago->observacion)
                            <span class="text-slate-500 font-normal normal-case font-mono"> - {{ $pago->observacion }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Form of Payment Checkboxes (Estilo Excel/Vale de Pago) -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-t border-b border-slate-200 py-4 mb-8 space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-6">
                    <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Forma de Pago:</span>
                    <div class="flex items-center space-x-2 {{ $pago->metodo_pago !== 'efectivo' ? 'opacity-45' : '' }}">
                        <span class="w-5 h-5 inline-flex items-center justify-center border border-slate-400 rounded bg-white font-mono text-xs font-bold">{{ $pago->metodo_pago === 'efectivo' ? '✓' : '' }}</span>
                        <span class="text-xs text-slate-850 font-bold">Efectivo</span>
                    </div>
                    <div class="flex items-center space-x-2 {{ $pago->metodo_pago !== 'cheque' ? 'opacity-45' : '' }}">
                        <span class="w-5 h-5 inline-flex items-center justify-center border border-slate-400 rounded bg-white font-mono text-xs font-bold">{{ $pago->metodo_pago === 'cheque' ? '✓' : '' }}</span>
                        <span class="text-xs text-slate-850 font-bold">Cheque</span>
                    </div>
                    <div class="flex items-center space-x-2 {{ $pago->metodo_pago !== 'transferencia' ? 'opacity-45' : '' }}">
                        <span class="w-5 h-5 inline-flex items-center justify-center border border-slate-400 rounded bg-white font-mono text-xs font-bold">{{ $pago->metodo_pago === 'transferencia' ? '✓' : '' }}</span>
                        <span class="text-xs text-slate-850 font-bold">Transferencia</span>
                    </div>
                </div>
                <div class="text-[10px] text-slate-500 font-mono">
                    <span>Moneda de Pago: Bolivianos (Bs.)</span>
                </div>
            </div>

            <!-- Detailed Pay Breakdown Table (Single source of layout amounts to avoid duplication) -->
            <div class="border border-emerald-800/40 rounded-lg overflow-hidden mb-8">
                <div class="bg-emerald-800 text-white px-4 py-2 font-bold uppercase tracking-wider text-xs">
                    Detalle de Liquidación de Planilla
                </div>
                <table class="w-full text-xs text-left">
                    <thead>
                        <tr class="bg-slate-50 font-bold text-slate-700 border-b border-slate-200">
                            <th class="px-4 py-2.5">Detalle / Concepto de Pago</th>
                            <th class="px-4 py-2.5 text-right">Monto (Bs.)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-150 font-mono text-slate-700">
                        <tr class="bg-white">
                            <td class="px-4 py-2.5 font-sans">Avance / Trabajo de la Semana</td>
                            <td class="px-4 py-2.5 text-right font-bold text-slate-900">Bs. {{ number_format($pago->subtotal, 2, ',', '.') }}</td>
                        </tr>
                        @if($pago->bonos > 0)
                        <tr class="bg-white">
                            <td class="px-4 py-2.5 font-sans text-emerald-850 font-semibold">(+) Bonos y Adicionales</td>
                            <td class="px-4 py-2.5 text-right font-bold text-emerald-700">Bs. {{ number_format($pago->bonos, 2, ',', '.') }}</td>
                        </tr>
                        @endif
                        @if($pago->descuentos > 0)
                        <tr class="bg-white">
                            <td class="px-4 py-2.5 font-sans text-red-800 font-semibold">(-) Descuentos Extra</td>
                            <td class="px-4 py-2.5 text-right font-bold text-red-650">Bs. {{ number_format($pago->descuentos, 2, ',', '.') }}</td>
                        </tr>
                        @endif
                        
                        @if($pago->anticipos_descontados > 0)
                        <tr class="bg-red-50/10 font-bold">
                            <td class="px-4 py-2.5 font-sans text-red-700">(-) A Cuenta (Anticipo Descontado)</td>
                            <td class="px-4 py-2.5 text-right text-red-650">Bs. {{ number_format($pago->anticipos_descontados, 2, ',', '.') }}</td>
                        </tr>
                        @endif
                        
                        <tr class="bg-emerald-50/30 font-black text-sm border-t-2 border-emerald-800 text-emerald-950">
                            <td class="px-4 py-3 font-sans uppercase">Total Neto Debido</td>
                            <td class="px-4 py-3 text-right text-emerald-800 text-base">Bs. {{ number_format($pago->neto, 2, ',', '.') }}</td>
                        </tr>
                        
                        <tr class="bg-slate-50 font-bold border-t border-slate-200 text-slate-800">
                            <td class="px-4 py-2.5 font-sans uppercase">Efectivo Pagado / Entregado</td>
                            <td class="px-4 py-2.5 text-right text-slate-900 font-bold">Bs. {{ number_format($pago->monto_pagado, 2, ',', '.') }}</td>
                        </tr>
                        
                        @if($pago->saldo_pendiente > 0)
                        <tr class="bg-amber-50/40 font-bold border-t border-amber-200 text-amber-900">
                            <td class="px-4 py-2.5 font-sans uppercase">(-) Saldo Restante Adeudado</td>
                            <td class="px-4 py-2.5 text-right text-amber-700 font-bold">Bs. {{ number_format($pago->saldo_pendiente, 2, ',', '.') }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Signature block (Recibido por (Contratista) vs Entregado por (Administración)) -->
            <div class="grid grid-cols-2 gap-12 mt-12 pt-8 border-t border-dashed border-emerald-800/30 text-center text-xs">
                <div class="flex flex-col items-center">
                    <div class="w-48 border-b border-slate-350 mb-1.5"></div>
                    <span class="font-bold text-slate-850 uppercase text-[10px]">{{ $pago->trabajador->nombre }}</span>
                    <span class="text-[9px] text-slate-500 uppercase tracking-widest mt-0.5 font-mono">Recibí Conforme (Contratista)</span>
                    <span class="text-[9px] text-slate-600 font-mono mt-1">C.I.: {{ $pago->trabajador->ci }}</span>
                    @if($pago->trabajador->telefono)
                        <span class="text-[8px] text-slate-400 font-mono mt-0.5">Telf: {{ $pago->trabajador->telefono }}</span>
                    @endif
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-48 border-b border-slate-350 mb-1.5"></div>
                    <span class="font-bold text-slate-850 uppercase text-[10px]">{{ $pago->entregado_por ?? 'Administración TORMAN' }}</span>
                    <span class="text-[9px] text-slate-500 uppercase tracking-widest mt-0.5 font-mono">Entregué Conforme</span>
                </div>
            </div>

            <!-- Branding Footer contacts (bottom-left) -->
            <div class="mt-8 flex justify-between items-center text-[9px] text-slate-400 border-t border-slate-100 pt-3 font-mono">
                <div class="flex space-x-6">
                    <span class="flex items-center"><i class="fa-brands fa-facebook mr-1.5 text-emerald-700"></i> TORMAN</span>
                    <span class="flex items-center"><i class="fa-solid fa-phone mr-1.5 text-emerald-700"></i> 74.225.855</span>
                </div>
                <div>
                    <span class="text-[8px] text-slate-400">SCPM - Sistema de Control de Pagos Mineros</span>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
