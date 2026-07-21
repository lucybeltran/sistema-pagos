@extends('layouts.app')

@section('title', 'Vale de Anticipo #' . str_pad($anticipo->id, 5, '0', STR_PAD_LEFT))

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar (no-print) -->
    <div class="flex items-center justify-between no-print">
        <div>
            <a href="{{ route('anticipos.index') }}" class="text-xs text-slate-400 hover:text-amber-500 flex items-center font-medium transition duration-150">
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Volver a Anticipos
            </a>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100 mt-1">Comprobante de Anticipo</h1>
        </div>
        <div class="flex space-x-3">
            <button onclick="window.print()" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-sm font-bold text-slate-950 transition duration-150 shadow-lg shadow-orange-500/10">
                <i class="fa-solid fa-print mr-2"></i> Imprimir Vale
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
                    <h1 class="text-3xl font-extrabold tracking-wider text-emerald-800 uppercase">Vale de Anticipo</h1>
                    <div class="mt-2 text-slate-600 font-mono text-xs flex flex-col items-center justify-center space-y-1">
                        <div>
                            <span class="font-bold">Nº Correlativo:</span>
                            <span class="text-sm font-bold text-red-650 ml-1 underline decoration-red-500 decoration-2">{{ str_pad($anticipo->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="text-[10px] text-slate-500 mt-1 space-x-1">
                            <span>Fecha: <strong class="text-slate-800">{{ $anticipo->fecha->format('d/m/Y') }}</strong></span>
                            <span>|</span>
                            <span>Hora: <strong class="text-slate-800">{{ $anticipo->created_at->format('H:i:s') }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Column 3: Amount Table (Bs. / USD / TC) as secondary reference block -->
                <div class="md:col-span-3 flex justify-center md:justify-end">
                    <table class="text-xs border-2 border-emerald-800 rounded overflow-hidden w-full max-w-[180px] shadow-sm bg-slate-50">
                        <tr class="bg-white">
                            <td class="bg-emerald-50 font-bold border-r border-emerald-800 px-3 py-2.5 text-emerald-900 text-sm">Bs.</td>
                            <td class="px-3 py-2.5 font-mono font-black text-slate-900 text-right text-base">{{ number_format($anticipo->monto, 2, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Metadata Banner (Bocamina & Contractor details) -->
            <div class="flex flex-col sm:flex-row justify-between items-center bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 mb-6 text-xs text-slate-650 font-mono">
                <div>
                    <span class="font-bold text-slate-500">BOCAMINA:</span>
                    <span class="font-extrabold text-slate-800 uppercase ml-1">{{ $anticipo->trabajador->bocamina->nombre ?? 'N/A' }}</span>
                </div>
                <div class="mt-1 sm:mt-0">
                    <span class="font-bold text-slate-500">CONTRATISTA / BENEFICIARIO:</span>
                    <span class="font-extrabold text-slate-850 uppercase ml-1">{{ $anticipo->trabajador->nombre }} (C.I. {{ $anticipo->trabajador->ci }})</span>
                </div>
            </div>

            <!-- Spacious Form Rows with Bottom Borders -->
            <div class="space-y-6 mb-8">
                <!-- Recibí de -->
                <div class="flex flex-col sm:flex-row sm:items-end space-y-1 sm:space-y-0 sm:space-x-3">
                    <span class="text-sm font-bold text-emerald-800 uppercase tracking-wider w-28 flex-shrink-0 pb-1">Recibí de:</span>
                    <div class="flex-grow border-b-2 border-slate-200 pb-1 text-slate-850 font-bold text-sm uppercase px-2 font-mono">
                        TORMAN - ADMINISTRACIÓN
                    </div>
                </div>

                <!-- La suma de -->
                <div class="flex flex-col sm:flex-row sm:items-end space-y-1 sm:space-y-0 sm:space-x-3">
                    <span class="text-sm font-bold text-emerald-800 uppercase tracking-wider w-28 flex-shrink-0 pb-1">La cantidad de:</span>
                    <div class="flex-grow border-b-2 border-slate-200 pb-1 text-slate-800 font-extrabold text-xs uppercase px-2 font-mono leading-relaxed">
                        {{ $anticipo->monto_letras }} BOLIVIANOS
                    </div>
                </div>

                <!-- Por concepto de -->
                <div class="flex flex-col sm:flex-row sm:items-end space-y-1 sm:space-y-0 sm:space-x-3">
                    <span class="text-sm font-bold text-emerald-800 uppercase tracking-wider w-28 flex-shrink-0 pb-1">Por concepto de:</span>
                    <div class="flex-grow border-b-2 border-slate-200 pb-1 text-slate-850 text-xs font-semibold uppercase px-2 leading-relaxed">
                        ANTICIPO DE DINERO A CUENTA DE PLANILLA SEMANAL DE TRABAJO
                    </div>
                </div>
            </div>

            <!-- Form of Payment Checkboxes (Estilo Excel/Vale de Pago) -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-t border-b border-slate-200 py-4 mb-8 space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-6">
                    <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Forma de Pago:</span>
                    <div class="flex items-center space-x-2">
                        <span class="w-5.5 h-5.5 inline-flex items-center justify-center border border-slate-400 rounded bg-emerald-50 text-emerald-800 font-black text-sm font-mono">✓</span>
                        <span class="text-xs text-slate-850 font-bold">Efectivo</span>
                    </div>
                    <div class="flex items-center space-x-2 opacity-35">
                        <span class="w-5 h-5 inline-flex border border-slate-400 rounded bg-white"></span>
                        <span class="text-xs text-slate-600 font-medium">Cheque</span>
                    </div>
                    <div class="flex items-center space-x-2 opacity-35">
                        <span class="w-5 h-5 inline-flex border border-slate-400 rounded bg-white"></span>
                        <span class="text-xs text-slate-600 font-medium">Transferencia</span>
                    </div>
                </div>
                <div class="text-[10px] text-slate-500 font-mono">
                    <span>Moneda de Anticipo: Bolivianos (Bs.)</span>
                </div>
            </div>

            <!-- Signature block (Recibí Conforme vs Entregado por) -->
            <div class="grid grid-cols-2 gap-12 mt-16 pt-8 border-t border-dashed border-emerald-800/30 text-center text-xs">
                <div class="flex flex-col items-center">
                    <div class="w-48 border-b border-slate-350 mb-1.5"></div>
                    <span class="font-bold text-slate-850 uppercase text-[10px]">{{ $anticipo->trabajador->nombre }}</span>
                    <span class="text-[9px] text-slate-500 uppercase tracking-widest mt-0.5 font-mono">Recibí Conforme (Beneficiario)</span>
                    <span class="text-[9px] text-slate-600 font-mono mt-1">C.I.: {{ $anticipo->trabajador->ci }}</span>
                    @if($anticipo->trabajador->telefono)
                        <span class="text-[8px] text-slate-400 font-mono mt-0.5">Telf: {{ $anticipo->trabajador->telefono }}</span>
                    @endif
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-48 border-b border-slate-350 mb-1.5"></div>
                    <span class="font-bold text-slate-850 uppercase text-[10px]">{{ Auth::user()->name ?? 'Administración TORMAN' }}</span>
                    <span class="text-[9px] text-slate-500 uppercase tracking-widest mt-0.5 font-mono">Entregado por (Administración)</span>
                </div>
            </div>

            <!-- Branding Footer contacts (bottom-left) -->
            <div class="mt-12 flex justify-between items-center text-[9px] text-slate-400 border-t border-slate-100 pt-3 font-mono">
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
