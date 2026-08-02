@extends('layouts.app')

@section('title', 'Perfil de Usuario')

@section('content')
<style>
    /* ============================================================
       DISEÑO ERP PREMIUM - EDICIÓN DE PERFIL
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

    /* Inputs y Selects */
    .erp-input {
        background: rgba(15, 23, 42, 0.45) !important;
        border: 1.5px solid var(--erp-border) !important;
        color: var(--erp-text-main) !important;
        border-radius: 10px !important;
        transition: all 0.2s ease;
        width: 100% !important;
    }
    .light-theme .erp-input {
        background: #ffffff !important;
    }
    .erp-input:focus {
        border-color: #f59e0b !important;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1) !important;
        outline: none;
    }
</style>

<div class="erp-container max-w-4xl mx-auto space-y-6 pb-12" x-data="{ 
    avatarPreview: '{{ $user->avatar ? asset($user->avatar) : '' }}',
    handleFileChange(event) {
        const file = event.target.files[0];
        if (file) {
            this.avatarPreview = URL.createObjectURL(file);
        }
    },
    password: '',
    get lengthValid() { return this.password.length >= 8 },
    get caseValid() { return /[a-z]/.test(this.password) && /[A-Z]/.test(this.password) },
    get numberValid() { return /[0-9]/.test(this.password) },
    get symbolValid() { return /[^A-Za-z0-9]/.test(this.password) }
}">

    <!-- Header -->
    <div class="flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center">
            <i class="fa-solid fa-user-shield text-amber-500 text-lg"></i>
        </div>
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight" style="color:var(--erp-text-main)">Perfil de Usuario</h1>
            <p class="text-sm mt-1" style="color:var(--erp-text-muted)">Administra tu información personal, foto de perfil y credenciales de acceso.</p>
        </div>
    </div>

    <!-- Main Settings Form -->
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Card 1: Personal Info -->
        <div class="erp-card p-6 space-y-6">
            <div class="flex items-center space-x-2 pb-3 border-b" style="border-color:var(--erp-border)">
                <i class="fa-regular fa-id-card text-amber-500 text-sm"></i>
                <h3 class="text-xs font-bold uppercase tracking-wider font-sans" style="color:var(--erp-text-main)">Información Personal</h3>
            </div>

            <!-- Avatar selection row (Centered / clean alignment) -->
            <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6 p-4 rounded-xl bg-slate-900/10 border" style="border-color:var(--erp-border)">
                <div class="relative group">
                    <template x-if="avatarPreview">
                        <img :src="avatarPreview" class="w-24 h-24 rounded-2xl object-cover border-2 border-amber-500/20 shadow-md">
                    </template>
                    <template x-if="!avatarPreview">
                        <div class="w-24 h-24 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-500">
                            <i class="fa-solid fa-user text-3xl text-slate-600"></i>
                        </div>
                    </template>
                </div>
                
                <div class="flex-1 text-center sm:text-left space-y-2">
                    <h4 class="text-sm font-bold" style="color:var(--erp-text-main)">Foto de Perfil</h4>
                    <p class="text-xs" style="color:var(--erp-text-muted)">Formatos recomendados: JPG o PNG. Tamaño máximo: 2 MB.</p>
                    <div class="flex flex-wrap gap-2 justify-center sm:justify-start pt-1">
                        <label class="inline-flex items-center justify-center px-3.5 py-1.5 rounded-lg bg-slate-900 border border-slate-800 hover:bg-slate-800 text-slate-200 text-xs font-bold cursor-pointer transition">
                            <i class="fa-solid fa-upload mr-1.5 text-amber-500"></i> Seleccionar Imagen
                            <input type="file" name="avatar" accept="image/*" class="hidden" @change="handleFileChange($event)">
                        </label>
                    </div>
                </div>
            </div>

            <!-- Fields Form layout: Spacious columns grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 font-sans">Nombre del Usuario / Sistema</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="erp-input px-3.5 py-2.5 text-sm" required>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 font-sans">Correo Electrónico</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="erp-input px-3.5 py-2.5 text-sm" required>
                </div>
            </div>
        </div>

        <!-- Card 2: Password Change -->
        <div class="erp-card p-6 space-y-6">
            <div class="flex items-center space-x-2 pb-3 border-b" style="border-color:var(--erp-border)">
                <i class="fa-solid fa-shield-halved text-amber-500 text-sm"></i>
                <h3 class="text-xs font-bold uppercase tracking-wider font-sans" style="color:var(--erp-text-main)">Cambiar Contraseña</h3>
            </div>

            <p class="text-xs -mt-2 leading-relaxed" style="color:var(--erp-text-muted)">
                Deje estos campos en blanco si no desea cambiar su contraseña actual.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- New Password input -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 font-sans">Nueva Contraseña</label>
                    <input type="password" name="password" x-model="password" class="erp-input px-3.5 py-2.5 text-sm" placeholder="Introduzca nueva contraseña">
                    
                    <!-- Password strength & requirement tags (Alpine.js feedback) -->
                    <div x-show="password.length > 0" class="pt-2.5 space-y-2 border-t border-slate-800/40 mt-3" x-transition>
                        <span class="block text-[10px] font-black text-slate-550 uppercase tracking-widest font-sans mb-1.5">Requisitos de Seguridad obligatorios:</span>
                        
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid animate-pulse" :class="lengthValid ? 'fa-circle-check text-emerald-500 animate-none' : 'fa-circle-xmark text-rose-500'"></i>
                                <span :class="lengthValid ? 'text-emerald-500 font-semibold' : 'text-rose-550 dark:text-rose-400 font-medium'">Min. 8 caracteres</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid animate-pulse" :class="caseValid ? 'fa-circle-check text-emerald-500 animate-none' : 'fa-circle-xmark text-rose-500'"></i>
                                <span :class="caseValid ? 'text-emerald-500 font-semibold' : 'text-rose-550 dark:text-rose-400 font-medium'">Mayús. y Minús.</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid animate-pulse" :class="numberValid ? 'fa-circle-check text-emerald-500 animate-none' : 'fa-circle-xmark text-rose-500'"></i>
                                <span :class="numberValid ? 'text-emerald-500 font-semibold' : 'text-rose-550 dark:text-rose-400 font-medium'">Al menos un número</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid animate-pulse" :class="symbolValid ? 'fa-circle-check text-emerald-500 animate-none' : 'fa-circle-xmark text-rose-500'"></i>
                                <span :class="symbolValid ? 'text-emerald-500 font-semibold' : 'text-rose-550 dark:text-rose-400 font-medium'">Símbolo especial</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Confirm password input -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 font-sans">Confirmar Nueva Contraseña</label>
                    <input type="password" name="password_confirmation" class="erp-input px-3.5 py-2.5 text-sm" placeholder="Confirme nueva contraseña">
                </div>
            </div>
        </div>

        <!-- Submit Button Row -->
        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-slate-950 text-sm font-black uppercase tracking-wider transition duration-200 shadow-lg shadow-orange-500/10">
                <i class="fa-solid fa-circle-check mr-2"></i> Guardar Cambios
            </button>
        </div>

    </form>

</div>
@endsection
