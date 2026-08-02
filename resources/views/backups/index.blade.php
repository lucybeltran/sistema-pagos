@extends('layouts.app')

@section('title', 'Copias de Seguridad')

@section('content')
<style>
    /* ============================================================
       DISEÑO ERP PREMIUM - CONFIGURACIÓN Y RESPALDOS
       ============================================================ */
    .erp-container {
        --erp-border: rgba(255, 255, 255, 0.06);
        --erp-bg-card: rgba(15, 23, 42, 0.45);
        --erp-text-main: #f8fafc;
        --erp-text-muted: #94a3b8;
        --erp-depth-3d: 0 10px 25px -5px rgba(0,0,0,0.3), 0 8px 10px -6px rgba(0,0,0,0.3);
    }

    .light-theme .erp-container {
        --erp-border: rgba(15, 23, 42, 0.08);
        --erp-bg-card: #ffffff;
        --erp-text-main: #0f172a;
        --erp-text-muted: #64748b;
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
        overflow: hidden;
    }
    
    .erp-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px -10px rgba(0,0,0,0.25);
    }
    
    .light-theme .erp-card:hover {
        box-shadow: 0 15px 25px -5px rgba(0,0,0,0.05);
    }

    /* Premium Alert Warning */
    .security-warning-card {
        background: rgba(245, 158, 11, 0.06) !important;
        border: 1.5px solid rgba(245, 158, 11, 0.15) !important;
        border-left: 5px solid #f59e0b !important;
        border-radius: 14px;
    }
    .light-theme .security-warning-card {
        background: rgba(245, 158, 11, 0.03) !important;
    }

    /* Inputs y Selects */
    .erp-input, .erp-select {
        background: rgba(15, 23, 42, 0.45) !important;
        border: 1.5px solid var(--erp-border) !important;
        color: var(--erp-text-main) !important;
        border-radius: 10px !important;
        transition: all 0.2s ease;
    }
    .light-theme .erp-input, .light-theme .erp-select {
        background: #ffffff !important;
    }
    .erp-input:focus, .erp-select:focus {
        border-color: #f59e0b !important;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1) !important;
        outline: none;
    }
</style>

<div class="erp-container max-w-5xl mx-auto space-y-6 pb-12" x-data="{ 
    openUploadModal: false, 
    processing: false 
}">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-database text-amber-500 text-lg"></i>
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight" style="color:var(--erp-text-main)">Copias de Seguridad (Backups)</h1>
            </div>
            <p class="text-sm mt-1.5 ml-13" style="color:var(--erp-text-muted)">Gestión integral de respaldos ZIP, imágenes de storage y restauración inteligente sin duplicados.</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="openUploadModal = true" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-800 hover:bg-slate-800 text-slate-200 text-sm font-bold transition duration-200 shadow-md">
                <i class="fa-solid fa-cloud-arrow-up mr-2 text-amber-500"></i> Subir & Restaurar ZIP
            </button>
            <form action="{{ route('backups.store') }}" method="POST" @submit="processing = true" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-slate-950 text-sm font-black transition duration-200 shadow-lg shadow-orange-500/10">
                    <i class="fa-solid fa-cloud-arrow-down mr-2"></i> Generar Respaldo Ahora
                </button>
            </form>
        </div>
    </div>

    <!-- Security Alert Warning Card -->
    <div class="security-warning-card p-5">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-amber-500/20 flex items-center justify-center mt-0.5">
                <i class="fa-solid fa-circle-exclamation text-amber-500"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-amber-500 font-mono">💡 Recomendación importante de Seguridad:</h4>
                <p class="text-sm mt-1.5 leading-relaxed text-slate-350 dark:text-slate-305">
                    Para evitar la pérdida total de datos por fallas de hardware en este equipo, te sugerimos 
                    <strong class="text-amber-550 dark:text-amber-400 font-bold">descargar el archivo ZIP de tus respaldos al menos una vez al mes</strong> 
                    y guardarlo en un disco externo, pendrive o en la nube. Así tendrás tu información y fotos a salvo.
                </p>
            </div>
        </div>
    </div>

    <!-- Automatic Backups & Schedule Settings -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Schedule Settings Form -->
        <div class="lg:col-span-2 erp-card p-6 flex flex-col justify-between">
            <form action="{{ route('backups.configurar') }}" method="POST" @submit="processing = true" class="space-y-6">
                @csrf
                
                <div class="flex items-center space-x-2 pb-3 border-b" style="border-color:var(--erp-border)">
                    <i class="fa-regular fa-clock text-amber-500 text-sm"></i>
                    <h3 class="text-xs font-bold uppercase tracking-wider font-mono" style="color:var(--erp-text-main)">Programación de Respaldos Automáticos</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Status Toggle -->
                    <div class="space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider font-mono text-slate-400">Estado del Servicio</label>
                        <div class="flex items-center space-x-3 pt-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="activo" value="1" class="sr-only peer" {{ $settings['activo'] ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-slate-400 after:border-slate-300 after:border after:rounded-full after:height-5 after:width-5 after:transition-all peer-checked:bg-amber-500 peer-checked:after:bg-slate-950"></div>
                            </label>
                            <span class="text-sm font-semibold" style="color:var(--erp-text-main)">Activar respaldos automáticos</span>
                        </div>
                        <p class="text-xs leading-relaxed mt-2" style="color:var(--erp-text-muted)">
                            Cuando está activo, el servidor ejecutará y guardará copias de seguridad de la base de datos e imágenes en segundo plano.
                        </p>
                    </div>

                    <!-- Freq & Hour Settings -->
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider font-mono text-slate-400">Frecuencia de Ejecución</label>
                            <select name="frecuencia" class="w-full erp-select px-3 py-2.5 text-sm">
                                <option value="diario" {{ $settings['frecuencia'] === 'diario' ? 'selected' : '' }}>Diario (Cada noche)</option>
                                <option value="semanal" {{ $settings['frecuencia'] === 'semanal' ? 'selected' : '' }}>Semanal (Todos los domingos)</option>
                                <option value="mensual" {{ $settings['frecuencia'] === 'mensual' ? 'selected' : '' }}>Mensual (Último día del mes)</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider font-mono text-slate-400">Hora del Respaldo</label>
                            <div class="relative">
                                <input type="time" name="hora" value="{{ $settings['hora'] }}" class="w-full erp-input px-3 py-2 text-sm">
                                <i class="fa-regular fa-clock absolute right-3 top-3 text-slate-500 text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t" style="border-color:var(--erp-border)">
                    <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-black uppercase tracking-wider transition duration-200">
                        <i class="fa-solid fa-save mr-2"></i> Guardar Programación
                    </button>
                </div>
            </form>
        </div>

        <!-- Right 1 Col: Next Execution Status Panel -->
        <div class="erp-card p-6 flex flex-col justify-between">
            <div class="flex items-center space-x-2 pb-3 border-b" style="border-color:var(--erp-border)">
                <i class="fa-solid fa-hourglass-half text-amber-500 text-sm"></i>
                <h3 class="text-sm font-bold uppercase tracking-wider font-mono" style="color:var(--erp-text-main)">Próxima Ejecución</h3>
            </div>
            
            <div class="flex-1 flex flex-col items-center justify-center py-6">
                <!-- Status indicator light -->
                <div class="flex items-center space-x-2 mb-3">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest font-mono">PROGRAMADO</span>
                </div>
                
                <h2 class="text-sm font-bold uppercase tracking-wider" style="color:var(--erp-text-muted)">
                    @if($settings['frecuencia'] === 'diario')
                        Esta Noche
                    @elseif($settings['frecuencia'] === 'semanal')
                        Fin de Semana
                    @else
                        Fin de Mes
                    @endif
                </h2>
                
                <p class="text-2xl font-black tracking-tight mt-2 font-mono" style="color:var(--erp-text-main)">
                    {{ date('d/m/Y', strtotime($settings['frecuencia'] === 'diario' ? 'tomorrow' : ($settings['frecuencia'] === 'semanal' ? 'next sunday' : 'last day of this month'))) }}
                </p>
                <p class="text-xs mt-1 font-mono text-slate-400">
                    a las {{ $settings['hora'] }} hs
                </p>
            </div>
            
            <div class="text-[10px] text-center font-mono pt-4 border-t leading-relaxed" style="border-color:var(--erp-border); color:var(--erp-text-muted)">
                El servidor ejecutará una copia de seguridad en la fecha y hora indicadas.
            </div>
        </div>
    </div>

    <!-- Backups List Table -->
    <div class="erp-card overflow-hidden">
        <div class="px-6 py-4.5 border-b flex items-center justify-between" style="border-color:var(--erp-border); background:rgba(255,255,255,0.01)">
            <h3 class="text-sm font-bold uppercase tracking-wider font-mono" style="color:var(--erp-text-main)">Historial de Respaldos Guardados</h3>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-500 border border-amber-500/20 font-mono">
                {{ count($backups) }} Archivos
            </span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs uppercase tracking-wider" style="background:rgba(255,255,255,0.01); border-bottom:1.5px solid var(--erp-border)">
                        <th class="py-4.5 px-6 font-bold" style="color:var(--erp-text-muted)">Archivo</th>
                        <th class="py-4.5 px-6 font-bold" style="color:var(--erp-text-muted)">Fecha de Creación</th>
                        <th class="py-4.5 px-6 font-bold" style="color:var(--erp-text-muted)">Tamaño</th>
                        <th class="py-4.5 px-6 font-bold text-right" style="color:var(--erp-text-muted)">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm" style="border-color:var(--erp-border)">
                    @forelse($backups as $backup)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/10 transition duration-150">
                            <td class="py-4 px-6 font-medium" style="color:var(--erp-text-main)">
                                <div class="flex items-center space-x-2.5">
                                    <i class="fa-solid fa-file-zipper text-amber-500 text-lg"></i>
                                    <span>{{ $backup['filename'] }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 font-mono text-slate-400">
                                {{ date('d/m/Y H:i:s', strtotime($backup['created_at'])) }}
                            </td>
                            <td class="py-4 px-6 font-mono text-slate-400">
                                {{ $backup['size'] }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="inline-flex items-center justify-end space-x-2">
                                    <a href="{{ route('backups.download', $backup['filename']) }}" class="p-1.5 rounded-lg bg-slate-900 border border-slate-800 text-amber-500 hover:text-amber-400 hover:bg-slate-800 transition" title="Descargar Respaldo">
                                        <i class="fa-solid fa-download text-xs"></i>
                                    </a>
                                    
                                    <!-- Restore Button triggering individual confirmation -->
                                    <form action="{{ route('backups.restore') }}" method="POST" onsubmit="return confirm('¿Está seguro de que desea restaurar este respaldo? Se sobrescribirá la base de datos actual.')" class="inline">
                                        @csrf
                                        <input type="hidden" name="backup_file_name" value="{{ $backup['filename'] }}">
                                        <button type="submit" class="p-1.5 rounded-lg bg-slate-900 border border-slate-800 text-emerald-500 hover:text-emerald-400 hover:bg-slate-800 transition" title="Restaurar este Respaldo">
                                            <i class="fa-solid fa-rotate-left text-xs"></i>
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('backups.destroy', $backup['filename']) }}" method="POST" onsubmit="return confirm('¿Está seguro de que desea eliminar este respaldo permanentemente?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-slate-900 border border-slate-800 text-rose-500 hover:text-rose-400 hover:bg-slate-800 transition" title="Eliminar Respaldo">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-500 font-mono">
                                <i class="fa-solid fa-box-open block text-2xl mb-2 text-slate-650"></i>
                                No se encontraron archivos de respaldo guardados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Upload & Restore Modal -->
    <div x-show="openUploadModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md" x-cloak>
        <div @click.away="openUploadModal = false" class="w-full max-w-md bg-slate-900 border border-slate-850 rounded-2xl p-6 shadow-2xl relative">
            <button @click="openUploadModal = false" class="absolute top-4 right-4 text-slate-500 hover:text-slate-300">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
            
            <div class="flex items-center space-x-2.5 mb-4">
                <i class="fa-solid fa-cloud-arrow-up text-amber-500 text-lg"></i>
                <h3 class="text-md font-bold text-slate-200 uppercase tracking-wider font-mono">Restaurar desde archivo ZIP</h3>
            </div>
            
            <form action="{{ route('backups.restore') }}" method="POST" enctype="multipart/form-data" @submit="processing = true; openUploadModal = false" class="space-y-4">
                @csrf
                <div class="border-2 border-dashed border-slate-800 hover:border-amber-500/50 rounded-xl p-8 flex flex-col items-center justify-center transition bg-slate-950/30">
                    <i class="fa-solid fa-file-zipper text-3xl text-slate-500 mb-3"></i>
                    <span class="text-xs text-slate-400 text-center leading-relaxed font-sans">Selecciona el archivo ZIP de respaldo generado anteriormente</span>
                    <input type="file" name="backup_file" accept=".zip" class="mt-4 text-xs text-slate-400 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-black file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-400 cursor-pointer font-sans" required>
                </div>
                
                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" @click="openUploadModal = false" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold uppercase tracking-wider font-sans">Cancelar</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black uppercase tracking-wider font-sans">Subir & Restaurar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Processing Overlay -->
    <div x-show="processing" class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-slate-950/80 backdrop-blur-md" x-cloak>
        <div class="w-14 h-14 rounded-full border-2 border-amber-500/20 border-t-amber-500 animate-spin flex items-center justify-center">
            <i class="fa-solid fa-gem text-amber-500 absolute text-base animate-pulse"></i>
        </div>
        <p class="text-xs font-black text-slate-300 uppercase tracking-widest mt-4 font-mono">Procesando copia de seguridad...</p>
    </div>

</div>
@endsection
