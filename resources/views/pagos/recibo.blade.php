@extends('layouts.app')

@section('title', 'Comprobante de Egreso #' . str_pad($pago->id, 5, '0', STR_PAD_LEFT))

@section('content')
<!-- Custom Styles for Premium Receipt and Print layout -->
<style>
    /* Premium Gym-Catharsis High-Contrast styling for printable receipt container */
    .receipt-card-wrapper {
        background: #ffffff !important;
        color: #0f172a !important;
        border-radius: 20px !important;
        border: 2px solid #0f172a !important;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08) !important;
        position: relative !important;
        overflow: hidden !important;
    }

    .receipt-card-wrapper::before {
        content: '' !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        height: 6px !important;
        background: linear-gradient(90deg, #10b981, #0ea5e9, #6366f1) !important;
        z-index: 10 !important;
    }

    /* Print styles to guarantee exact copy on standard A4 paper */
    @media print {
        body {
            background: #ffffff !important;
            color: #000000 !important;
        }
        .no-print {
            display: none !important;
        }
        .receipt-card-wrapper {
            border: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            background: #ffffff !important;
        }
        .receipt-card-wrapper::before {
            display: none !important;
        }
        .print-container {
            border: 2px solid #000000 !important;
            border-radius: 8px !important;
            padding: 1.5rem !important;
        }
    }

    /* 3D button styling */
    .btn-3d-receipt {
        border-radius: 12px !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        transition: all 0.15s ease !important;
        cursor: pointer !important;
    }

    .btn-3d-receipt-pdf {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: #ffffff !important;
        border: 1px solid #b91c1c !important;
        border-bottom: 4.5px solid #991b1b !important;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.25) !important;
    }

    .btn-3d-receipt-pdf:hover {
        background: linear-gradient(135deg, #f87171 0%, #ef4444 100%) !important;
        transform: translateY(-1px);
    }

    .btn-3d-receipt-pdf:active {
        transform: translateY(2px) !important;
        border-bottom-width: 1px !important;
    }

    .btn-3d-receipt-excel {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: #ffffff !important;
        border: 1px solid #047857 !important;
        border-bottom: 4.5px solid #065f46 !important;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25) !important;
    }

    .btn-3d-receipt-excel:hover {
        background: linear-gradient(135deg, #34d399 0%, #10b981 100%) !important;
        transform: translateY(-1px);
    }

    .btn-3d-receipt-excel:active {
        transform: translateY(2px) !important;
        border-bottom-width: 1px !important;
    }

    .btn-3d-receipt-print {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        color: #0f172a !important;
        border: 1px solid #b45309 !important;
        border-bottom: 4.5px solid #78350f !important;
        box-shadow: 0 4px 10px rgba(245, 158, 11, 0.25) !important;
    }

    .btn-3d-receipt-print:hover {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%) !important;
        transform: translateY(-1px);
    }

    .btn-3d-receipt-print:active {
        transform: translateY(2px) !important;
        border-bottom-width: 1px !important;
    }
</style>

<div class="space-y-6">
    <!-- Top Action Bar (no-print) -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 no-print">
        <div>
            <a href="{{ route('pagos.index') }}" class="text-xs text-slate-400 hover:text-indigo-400 flex items-center font-medium transition duration-150">
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Volver a Historial
            </a>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100 mt-1">Comprobante de Pago</h1>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="downloadPDF()" class="btn-3d-receipt btn-3d-receipt-pdf inline-flex items-center justify-center px-4 py-2.5 text-xs">
                <i class="fa-solid fa-file-pdf mr-2 text-sm"></i> Descargar PDF
            </button>
            <button onclick="downloadExcel()" class="btn-3d-receipt btn-3d-receipt-excel inline-flex items-center justify-center px-4 py-2.5 text-xs">
                <i class="fa-solid fa-file-excel mr-2 text-sm"></i> Exportar Excel
            </button>
            <button onclick="window.print()" class="btn-3d-receipt btn-3d-receipt-print inline-flex items-center justify-center px-5 py-2.5 text-xs">
                <i class="fa-solid fa-print mr-2 text-sm"></i> Imprimir Recibo
            </button>
        </div>
    </div>

    <!-- Printable Area (White Paper Style Container) -->
    <div id="receipt-card" class="mx-auto max-w-4xl receipt-card-wrapper p-8 md:p-10 font-sans text-sm relative">
        
        <!-- Inner Border for Professional Aesthetic -->
        <div class="print-container border-2 border-slate-900 p-6 md:p-8 rounded-xl bg-white">
            
            <!-- Header Grid: Logo, Title/Date, Amounts -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center border-b-2 border-slate-900 pb-6 mb-6">
                <!-- Column 1: Logo & Company Hexagonal SVG -->
                <div class="md:col-span-4 flex items-center space-x-3">
                    <svg class="w-20 h-14 flex-shrink-0" viewBox="0 0 120 80" xmlns="http://www.w3.org/2000/svg">
                        <!-- Dark background hexagon -->
                        <polygon points="30,5 60,5 75,30 60,55 30,55 15,30" fill="#0f172a" stroke="#0f172a" stroke-width="2" />
                        <text x="45" y="34" font-family="'Outfit', sans-serif" font-size="11" font-weight="900" fill="#ffffff" text-anchor="middle">MINA</text>
                        
                        <!-- Accent color hexagon (TORMAN) -->
                        <polygon points="65,25 95,25 110,50 95,75 65,75 50,50" fill="#10b981" stroke="#047857" stroke-width="2" />
                        <text x="80" y="54" font-family="'Outfit', sans-serif" font-size="10" font-weight="900" fill="#ffffff" text-anchor="middle">TORMAN</text>
                    </svg>
                    <div>
                        <h2 class="text-lg font-black uppercase tracking-widest text-slate-900 leading-none">TORMAN</h2>
                        <span class="text-[9px] text-slate-500 font-mono tracking-wider uppercase block mt-1">Control de Operaciones</span>
                    </div>
                </div>

                <!-- Column 2: Centered Large Title in Emerald & Date/Time -->
                <div class="md:col-span-5 text-center">
                    <h1 class="text-2xl font-black tracking-widest text-slate-900 uppercase">Recibo de Pago</h1>
                    <div class="mt-2 text-slate-700 font-mono text-xs flex flex-col items-center justify-center space-y-1">
                        <div>
                            <span class="font-bold">Nº Correlativo:</span>
                            <span class="text-sm font-extrabold text-red-600 ml-1 underline decoration-red-500 decoration-2">{{ str_pad($pago->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="text-[9.5px] text-slate-500 mt-0.5 space-x-1">
                            <span>Fecha: <strong class="text-slate-800">{{ $pago->fecha->format('d/m/Y') }}</strong></span>
                            <span>|</span>
                            <span>Hora: <strong class="text-slate-800">{{ $pago->created_at->format('H:i:s') }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Column 3: Amount Table (Bs. / USD / TC) as secondary reference block -->
                <div class="md:col-span-3 flex justify-center md:justify-end">
                    <table class="text-xs border-2 border-slate-900 rounded overflow-hidden w-full max-w-[170px] shadow-sm">
                        <tr class="border-b border-slate-900 bg-white">
                            <td class="bg-slate-100 font-extrabold border-r border-slate-900 px-3 py-1.5 text-slate-900">Bs.</td>
                            <td class="px-3 py-1.5 font-mono font-black text-slate-900 text-right">{{ number_format($pago->monto_pagado, 2, ',', '.') }}</td>
                        </tr>
                        <tr class="border-b border-slate-900 bg-white">
                            <td class="bg-slate-55 font-bold border-r border-slate-900 px-3 py-1.5 text-slate-700">$us</td>
                            <td class="px-3 py-1.5 font-mono text-slate-700 text-right">{{ number_format($pago->monto_pagado / $pago->tipo_cambio, 2, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-white">
                            <td class="bg-slate-55 font-bold border-r border-slate-900 px-3 py-1.5 text-slate-700">T/C</td>
                            <td class="px-3 py-1.5 font-mono text-slate-700 text-right">{{ number_format($pago->tipo_cambio, 2, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Metadata Banner (Bocamina & Contractor details) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 border border-slate-900 rounded-xl px-5 py-3 mb-6 text-xs text-slate-800 font-mono">
                <div>
                    <span class="font-bold text-slate-500"><i class="fa-solid fa-mountain mr-1 text-slate-600"></i> BOCAMINA:</span>
                    <span class="font-extrabold text-slate-900 uppercase ml-1">{{ $pago->trabajador->bocamina->nombre ?? 'N/A' }}</span>
                </div>
                <div class="sm:text-right">
                    <span class="font-bold text-slate-500"><i class="fa-solid fa-user-check mr-1 text-slate-600"></i> CONTRATISTA:</span>
                    <span class="font-extrabold text-slate-900 uppercase ml-1">{{ $pago->trabajador->nombre }} (C.I. {{ $pago->trabajador->ci }})</span>
                </div>
            </div>

            <!-- Spacious Form Rows with Bottom Borders -->
            <div class="space-y-5 mb-8">
                <!-- Recibí de -->
                <div class="flex flex-col sm:flex-row sm:items-end space-y-1 sm:space-y-0 sm:space-x-3">
                    <span class="text-xs font-black text-slate-800 uppercase tracking-widest w-28 flex-shrink-0 pb-1">Recibí de:</span>
                    <div class="flex-grow border-b-2 border-slate-200 pb-1 text-slate-900 font-extrabold text-sm uppercase px-2 font-mono">
                        TORMAN - ADMINISTRACIÓN (por: {{ $pago->entregado_por ?? 'Administrador' }})
                    </div>
                </div>

                <!-- La suma de -->
                <div class="flex flex-col sm:flex-row sm:items-end space-y-1 sm:space-y-0 sm:space-x-3">
                    <span class="text-xs font-black text-slate-800 uppercase tracking-widest w-28 flex-shrink-0 pb-1">La suma de:</span>
                    <div class="flex-grow border-b-2 border-slate-200 pb-1 text-slate-900 font-extrabold text-xs uppercase px-2 font-mono leading-relaxed">
                        {{ $pago->monto_letras }} BOLIVIANOS
                    </div>
                </div>

                <!-- Por concepto de -->
                <div class="flex flex-col sm:flex-row sm:items-end space-y-1 sm:space-y-0 sm:space-x-3">
                    <span class="text-xs font-black text-slate-800 uppercase tracking-widest w-28 flex-shrink-0 pb-1">Concepto:</span>
                    <div class="flex-grow border-b-2 border-slate-200 pb-1 text-slate-800 text-xs font-semibold uppercase px-2 leading-relaxed">
                        PLANILLA DE PAGO: {{ number_format($pago->cantidad_trabajada, 2) }} ({{ $pago->tipo_contrato_nombre }}) A TARIFA DE Bs. {{ number_format($pago->tarifa_pago, 2) }}
                        @if($pago->observacion)
                            <span class="text-slate-500 font-normal normal-case font-mono"> - {{ $pago->observacion }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Form of Payment Checkboxes (Estilo Excel/Vale de Pago) -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-t border-b border-slate-900 py-4 mb-8 space-y-4 sm:space-y-0">
                <div class="flex flex-wrap items-center gap-6">
                    <span class="text-xs font-black text-slate-800 uppercase tracking-widest">Forma de Pago:</span>
                    <div class="flex items-center space-x-2 {{ $pago->metodo_pago !== 'efectivo' ? 'opacity-45' : '' }}">
                        <span class="w-5 h-5 inline-flex items-center justify-center border border-slate-900 rounded bg-white font-mono text-xs font-bold">{{ $pago->metodo_pago === 'efectivo' ? '✓' : '' }}</span>
                        <span class="text-xs text-slate-900 font-bold">Efectivo</span>
                    </div>
                    <div class="flex items-center space-x-2 {{ $pago->metodo_pago !== 'cheque' ? 'opacity-45' : '' }}">
                        <span class="w-5 h-5 inline-flex items-center justify-center border border-slate-900 rounded bg-white font-mono text-xs font-bold">{{ $pago->metodo_pago === 'cheque' ? '✓' : '' }}</span>
                        <span class="text-xs text-slate-900 font-bold">Cheque</span>
                    </div>
                    <div class="flex items-center space-x-2 {{ $pago->metodo_pago !== 'transferencia' ? 'opacity-45' : '' }}">
                        <span class="w-5 h-5 inline-flex items-center justify-center border border-slate-900 rounded bg-white font-mono text-xs font-bold">{{ $pago->metodo_pago === 'transferencia' ? '✓' : '' }}</span>
                        <span class="text-xs text-slate-900 font-bold">Transferencia</span>
                    </div>
                </div>
                <div class="text-[10px] text-slate-500 font-mono">
                    <span>Moneda de Pago: Bolivianos (Bs.)</span>
                </div>
            </div>

            <!-- Detailed Pay Breakdown Table -->
            <div class="border border-slate-900 rounded-xl overflow-hidden mb-8">
                <div class="bg-slate-900 text-white px-4 py-2.5 font-bold uppercase tracking-wider text-xs flex justify-between items-center">
                    <span>Detalle de Liquidación de Planilla</span>
                    <i class="fa-solid fa-receipt text-xs text-slate-400"></i>
                </div>
                <table class="w-full text-xs text-left">
                    <thead>
                        <tr class="bg-slate-100 font-extrabold text-slate-800 border-b border-slate-900">
                            <th class="px-4 py-2.5">Detalle / Concepto de Pago</th>
                            <th class="px-4 py-2.5 text-right w-44">Monto (Bs.)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-mono text-slate-800">
                        <tr class="bg-white">
                            <td class="px-4 py-2.5 font-sans">
                                <strong>{{ $pago->tipo_contrato_nombre }}</strong> ({{ number_format($pago->cantidad_trabajada, 2, ',', '.') }} unidades a Bs. {{ number_format($pago->tarifa_pago, 2, ',', '.') }} c/u)
                            </td>
                            <td class="px-4 py-2.5 text-right font-extrabold text-slate-900">Bs. {{ number_format($pago->subtotal, 2, ',', '.') }}</td>
                        </tr>
                        @if($pago->bonos > 0)
                        <tr class="bg-white">
                            <td class="px-4 py-2.5 font-sans text-emerald-800 font-semibold">(+) Bonos y Adicionales</td>
                            <td class="px-4 py-2.5 text-right font-extrabold text-emerald-700">Bs. {{ number_format($pago->bonos, 2, ',', '.') }}</td>
                        </tr>
                        @endif
                        @if($pago->descuentos > 0)
                        <tr class="bg-white">
                            <td class="px-4 py-2.5 font-sans text-red-800 font-semibold">(-) Descuentos Extra</td>
                            <td class="px-4 py-2.5 text-right font-extrabold text-red-650">Bs. {{ number_format($pago->descuentos, 2, ',', '.') }}</td>
                        </tr>
                        @endif
                        
                        @if($pago->anticipos_descontados > 0)
                        <tr class="bg-red-50/20 font-bold">
                            <td class="px-4 py-2.5 font-sans text-red-800">(-) A Cuenta (Anticipo Descontado)</td>
                            <td class="px-4 py-2.5 text-right text-red-650 font-extrabold">Bs. {{ number_format($pago->anticipos_descontados, 2, ',', '.') }}</td>
                        </tr>
                        @endif
                        
                        <tr class="bg-slate-50 font-black border-t-2 border-slate-900 text-slate-900">
                            <td class="px-4 py-2.5 font-sans uppercase">Total Neto Debido</td>
                            <td class="px-4 py-2.5 text-right text-slate-900 text-sm">Bs. {{ number_format($pago->neto, 2, ',', '.') }}</td>
                        </tr>
                        
                        <tr class="bg-emerald-50/50 font-black border-t border-slate-900 text-emerald-950 text-sm">
                            <td class="px-4 py-3 font-sans uppercase">Efectivo Pagado / Entregado</td>
                            <td class="px-4 py-3 text-right text-emerald-800 text-base">Bs. {{ number_format($pago->monto_pagado, 2, ',', '.') }}</td>
                        </tr>
                        
                        @if($pago->saldo_pendiente > 0)
                        <tr class="bg-amber-50/40 font-bold border-t border-amber-200 text-amber-900">
                            <td class="px-4 py-2.5 font-sans uppercase">(-) Saldo Restante Adeudado</td>
                            <td class="px-4 py-2.5 text-right text-amber-700 font-bold text-xs">Bs. {{ number_format($pago->saldo_pendiente, 2, ',', '.') }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Signature block -->
            <div class="grid grid-cols-2 gap-12 mt-12 pt-8 border-t border-dashed border-slate-350 text-center text-xs">
                <div class="flex flex-col items-center">
                    <div class="w-48 border-b border-slate-400 mb-1.5"></div>
                    <span class="font-bold text-slate-900 uppercase text-[10px]">{{ $pago->trabajador->nombre }}</span>
                    <span class="text-[9px] text-slate-500 uppercase tracking-widest mt-0.5 font-mono font-bold">Recibí Conforme (Contratista)</span>
                    <span class="text-[9px] text-slate-600 font-mono mt-0.5">C.I.: {{ $pago->trabajador->ci }}</span>
                    @if($pago->trabajador->telefono)
                        <span class="text-[8px] text-slate-400 font-mono mt-0.5">Telf: {{ $pago->trabajador->telefono }}</span>
                    @endif
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-48 border-b border-slate-400 mb-1.5"></div>
                    <span class="font-bold text-slate-900 uppercase text-[10px]">{{ $pago->entregado_por ?? 'Administración TORMAN' }}</span>
                    <span class="text-[9px] text-slate-500 uppercase tracking-widest mt-0.5 font-mono font-bold">Entregué Conforme</span>
                </div>
            </div>

            <!-- Branding Footer contacts -->
            <div class="mt-8 flex justify-between items-center text-[9px] text-slate-400 border-t border-slate-200 pt-3 font-mono">
                <div class="flex space-x-6">
                    <span class="flex items-center"><i class="fa-brands fa-facebook mr-1.5 text-slate-650"></i> TORMAN</span>
                    <span class="flex items-center"><i class="fa-solid fa-phone mr-1.5 text-slate-650"></i> 74.225.855</span>
                </div>
                <div>
                    <span class="text-[8px] text-slate-450">SCPM - Sistema de Control de Pagos Mineros</span>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<!-- Load html2pdf and SheetJS (XLSX) from secure CDNs -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
    function downloadPDF() {
        const element = document.getElementById('receipt-card');
        const opt = {
            margin:       0.3,
            filename:     'Recibo_Pago_Nro_' + '{{ str_pad($pago->id, 5, "0", STR_PAD_LEFT) }}' + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2.2, useCORS: true, letterRendering: true },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        
        html2pdf().set(opt).from(element).save();
    }

    function downloadExcel() {
        const wb = XLSX.utils.book_new();
        
        const data = [
            ["COMPROBANTE DE PAGO - TORMAN"],
            ["Nro. Correlativo", '{{ str_pad($pago->id, 5, "0", STR_PAD_LEFT) }}'],
            ["Fecha", '{{ $pago->fecha->format("d/m/Y") }}'],
            ["Hora", '{{ $pago->created_at->format("H:i:s") }}'],
            [],
            ["DATOS DE CONTRA PARTE"],
            ["Bocamina", '{{ $pago->trabajador->bocamina->nombre ?? "N/A" }}'],
            ["Trabajador (Contratista)", '{{ $pago->trabajador->nombre }}'],
            ["Cédula de Identidad", '{{ $pago->trabajador->ci }}'],
            [],
            ["DETALLE DE LIQUIDACIÓN"],
            ["Concepto / Contrato", '{{ $pago->tipo_contrato_nombre }}'],
            ["Cantidad Trabajada", '{{ $pago->cantidad_trabajada }}'],
            ["Tarifa Acordada (Bs.)", '{{ $pago->tarifa_pago }}'],
            ["Subtotal Trabajo (Bs.)", '{{ $pago->subtotal }}'],
            ["Bonos Extra (Bs.)", '{{ $pago->bonos }}'],
            ["Descuentos Extra (Bs.)", '{{ $pago->descuentos }}'],
            ["Anticipos Descontados (Bs.)", '{{ $pago->anticipos_descontados }}'],
            ["Total Neto Debido (Bs.)", '{{ $pago->neto }}'],
            ["Monto Pagado / Entregado (Bs.)", '{{ $pago->monto_pagado }}'],
            ["Saldo Pendiente Restante (Bs.)", '{{ $pago->saldo_pendiente }}'],
            [],
            ["Detalles de Transacción"],
            ["Forma de Pago", '{{ ucfirst($pago->metodo_pago) }}'],
            ["Entregado Por", '{{ $pago->entregado_por ?? "Administración" }}']
        ];

        const ws = XLSX.utils.aoa_to_sheet(data);
        
        ws['!cols'] = [
            { wch: 32 },
            { wch: 45 }
        ];

        XLSX.utils.book_append_sheet(wb, ws, "Comprobante de Pago");

        XLSX.writeFile(wb, 'Recibo_Pago_Nro_' + '{{ str_pad($pago->id, 5, "0", STR_PAD_LEFT) }}' + '.xlsx');
    }
</script>
@endpush
