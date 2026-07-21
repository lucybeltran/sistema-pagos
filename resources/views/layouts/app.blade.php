<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Pagos Mineros') - SCPM</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #f59e0b;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #d97706;
        }
        /* Glassmorphism card utilities */
        .glass-card {
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            position: relative;
            overflow: hidden;
        }
        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #f59e0b, #ea580c, transparent);
            opacity: 0.85;
            z-index: 10;
        }
        .gold-glow {
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.15);
        }
        .gold-border-glow:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 10px rgba(245, 158, 11, 0.3);
        }
        
        /* Global button hover spark styles */
        .global-button-spark {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            z-index: 9999;
        }
        
        /* Premium button hover glows */
        button, .btn, [type="submit"], [type="button"] {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
        }
        
        /* Submit button hover glow */
        button[type="submit"], [type="submit"] {
            box-shadow: 0 0 10px rgba(245, 158, 11, 0.1);
        }
        button[type="submit"]:hover, [type="submit"]:hover {
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.35);
            transform: translateY(-1px);
        }
        
        /* Delete/Danger button hover glow */
        .bg-red-600:hover, .bg-red-500:hover, [class*="bg-red-"]:hover {
            box-shadow: 0 0 18px rgba(239, 68, 68, 0.45) !important;
            transform: translateY(-1px);
        }
        
        /* Success/emerald button hover glow */
        .bg-emerald-600:hover, .bg-emerald-500:hover, [class*="bg-emerald-"]:hover {
            box-shadow: 0 0 18px rgba(16, 185, 129, 0.45) !important;
            transform: translateY(-1px);
        }

        /* Vibrant animated buttons with moving gradient backgrounds */
        .btn-vibrant-amber {
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 50%, #f59e0b 100%) !important;
            background-size: 200% auto !important;
            color: #020617 !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25) !important;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
        }
        .btn-vibrant-success {
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 50%, #10b981 100%) !important;
            background-size: 200% auto !important;
            color: #020617 !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25) !important;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
        }
        .btn-vibrant-danger {
            background: linear-gradient(135deg, #ef4444 0%, #f43f5e 50%, #ef4444 100%) !important;
            background-size: 200% auto !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25) !important;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
        }
        .btn-vibrant-indigo {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #4f46e5 100%) !important;
            background-size: 200% auto !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25) !important;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
        }
        .btn-vibrant-warm {
            background: linear-gradient(135deg, #ea580c 0%, #d97706 50%, #ea580c 100%) !important;
            background-size: 200% auto !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.25) !important;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
        }
        
        .btn-vibrant-amber:hover, .btn-vibrant-success:hover, .btn-vibrant-danger:hover, .btn-vibrant-indigo:hover, .btn-vibrant-warm:hover {
            background-position: right center !important;
            transform: translateY(-2px) !important;
        }
        .btn-vibrant-amber:hover {
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.48) !important;
        }
        .btn-vibrant-success:hover {
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.48) !important;
        }
        .btn-vibrant-danger:hover {
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.48) !important;
        }
        .btn-vibrant-indigo:hover {
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.48) !important;
        }
        .btn-vibrant-warm:hover {
            box-shadow: 0 8px 25px rgba(234, 88, 12, 0.48) !important;
        }
        .btn-vibrant-amber:active, .btn-vibrant-success:active, .btn-vibrant-danger:active, .btn-vibrant-indigo:active, .btn-vibrant-warm:active {
            transform: translateY(0) !important;
        }

        /* Float Screen Toast System */
        .toast-item {
            background: rgba(15, 23, 42, 0.82) !important;
            backdrop-filter: blur(15px) !important;
            -webkit-backdrop-filter: blur(15px) !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.55) !important;
            position: relative;
            overflow: hidden;
            border: 1.5px solid transparent;
            animation: toastSlideIn 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transition: all 0.35s ease;
        }
        .toast-success {
            border-color: rgba(16, 185, 129, 0.35) !important;
            box-shadow: 0 0 25px rgba(16, 185, 129, 0.18) !important;
        }
        .toast-danger {
            border-color: rgba(244, 63, 94, 0.35) !important;
            box-shadow: 0 0 25px rgba(244, 63, 94, 0.18) !important;
        }
        @keyframes toastSlideIn {
            from {
                opacity: 0;
                transform: translateX(120%) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }
        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 100%;
            animation: toastTimer linear forwards;
        }
        .toast-success .toast-progress {
            animation-duration: 4.5s;
        }
        .toast-danger .toast-progress {
            animation-duration: 5.5s;
        }
        @keyframes toastTimer {
            from { width: 100%; }
            to { width: 0%; }
        }
        
        /* Print Styles */
        @media print {
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
            .print-container {
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                border: none !important;
                background: white !important;
                box-shadow: none !important;
            }
        }

        /* Custom Confirmation Modal styles */
        #custom-confirm-modal {
            transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        #custom-confirm-modal .glass-card {
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        #custom-confirm-modal.modal-hide {
            opacity: 0 !important;
        }
        #custom-confirm-modal.modal-hide .glass-card {
            transform: scale(0.9) !important;
        }

        /* Premium Global Inputs */
        input[type="text"], 
        input[type="email"], 
        input[type="password"], 
        input[type="number"], 
        input[type="date"], 
        select, 
        textarea {
            background: rgba(15, 23, 42, 0.45) !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 10px !important;
            color: #f8fafc !important;
            font-family: 'Outfit', sans-serif !important;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
        }
        
        input[type="text"]:focus, 
        input[type="email"]:focus, 
        input[type="password"]:focus, 
        input[type="number"]:focus, 
        input[type="date"]:focus, 
        select:focus, 
        textarea:focus {
            outline: none !important;
            border-color: rgba(245, 158, 11, 0.6) !important;
            box-shadow: 0 0 14px rgba(245, 158, 11, 0.25) !important;
            background: rgba(15, 23, 42, 0.65) !important;
            transform: translateY(-0.5px);
        }

        /* Autofill overrides */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus,
        textarea:-webkit-autofill,
        textarea:-webkit-autofill:hover,
        textarea:-webkit-autofill:focus,
        select:-webkit-autofill,
        select:-webkit-autofill:hover,
        select:-webkit-autofill:focus {
            -webkit-text-fill-color: #f8fafc !important;
            -webkit-box-shadow: 0 0 0px 1000px rgba(15, 23, 42, 0.65) inset !important;
            transition: background-color 5000s ease-in-out 0s !important;
        }

        /* Sidebar Navigation Hover Animations */
        .nav-item {
            transition: all 0.35s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
        }
        .nav-item:hover {
            transform: translateX(6px) !important;
            color: #f59e0b !important;
        }
        .nav-item i {
            transition: transform 0.35s cubic-bezier(0.25, 0.8, 0.25, 1), color 0.35s ease !important;
        }
        .nav-item:hover i {
            transform: scale(1.2) rotate(8deg) !important;
            color: #f97316 !important;
            filter: drop-shadow(0 0 5px rgba(249, 115, 22, 0.6)) !important;
        }
        .nav-item.active-nav-item i {
            color: #f59e0b !important;
            filter: drop-shadow(0 0 6px rgba(245, 158, 11, 0.4)) !important;
        }
    </style>
</head>
<body class="h-full flex overflow-hidden bg-slate-950 relative">
    
    <!-- Canvas for Floating Glowing Gold/Fire Sparks -->
    <canvas id="particle-canvas" class="fixed inset-0 pointer-events-none z-0 opacity-40"></canvas>

    <!-- Sidebar (no-print) -->
    <div class="no-print hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 bg-slate-900/80 backdrop-blur-md border-r border-slate-800/60 z-20">
        <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto">
            <!-- Logo area -->
            <div class="flex items-center flex-shrink-0 px-6 space-x-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-br from-amber-500 to-orange-600 gold-glow">
                    <i class="fa-solid fa-gem text-slate-950 text-lg"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold tracking-wider text-amber-500 uppercase">Control Pagos</h1>
                    <span class="text-[10px] text-slate-400 font-mono tracking-widest block -mt-1">MINERÍA</span>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="mt-8 flex-1 px-4 space-y-1 relative" id="main-nav">
                <!-- Sliding Liquid Glass Pill -->
                <div id="nav-indicator-pill" class="absolute left-3 right-3 rounded-lg opacity-0 pointer-events-none transition-all duration-300 z-0"></div>

                <a href="{{ route('dashboard') }}" class="nav-item flex items-center px-3 py-2.5 text-sm font-medium rounded-lg relative z-10 transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'active-nav-item text-amber-500' : 'text-slate-450 hover:text-slate-200' }}">
                    <i class="fa-solid fa-chart-line w-6 text-center mr-3 text-base"></i>
                    Tablero Principal
                </a>
                
                <a href="{{ route('bocaminas.index') }}" class="nav-item flex items-center px-3 py-2.5 text-sm font-medium rounded-lg relative z-10 transition-colors duration-200 {{ request()->routeIs('bocaminas.*') ? 'active-nav-item text-amber-500' : 'text-slate-450 hover:text-slate-200' }}">
                    <i class="fa-solid fa-mountain w-6 text-center mr-3 text-base"></i>
                    Bocaminas
                </a>

                <a href="{{ route('trabajadores.index') }}" class="nav-item flex items-center px-3 py-2.5 text-sm font-medium rounded-lg relative z-10 transition-colors duration-200 {{ request()->routeIs('trabajadores.*') ? 'active-nav-item text-amber-500' : 'text-slate-450 hover:text-slate-200' }}">
                    <i class="fa-solid fa-user-group w-6 text-center mr-3 text-base"></i>
                    Trabajadores / Contratistas
                </a>

                <a href="{{ route('contratos.index') }}" class="nav-item flex items-center px-3 py-2.5 text-sm font-medium rounded-lg relative z-10 transition-colors duration-200 {{ request()->routeIs('contratos.*') ? 'active-nav-item text-amber-500' : 'text-slate-450 hover:text-slate-200' }}">
                    <i class="fa-solid fa-file-contract w-6 text-center mr-3 text-base"></i>
                    Contratos
                </a>

                <a href="{{ route('anticipos.index') }}" class="nav-item flex items-center px-3 py-2.5 text-sm font-medium rounded-lg relative z-10 transition-colors duration-200 {{ request()->routeIs('anticipos.*') ? 'active-nav-item text-amber-500' : 'text-slate-450 hover:text-slate-200' }}">
                    <i class="fa-solid fa-money-bill-transfer w-6 text-center mr-3 text-base"></i>
                    Anticipos
                </a>

                <a href="{{ route('pagos.index') }}" class="nav-item flex items-center px-3 py-2.5 text-sm font-medium rounded-lg relative z-10 transition-colors duration-200 {{ request()->routeIs('pagos.*') ? 'active-nav-item text-amber-500' : 'text-slate-450 hover:text-slate-200' }}">
                    <i class="fa-solid fa-receipt w-6 text-center mr-3 text-base"></i>
                    Pagos / Recibos
                </a>

                <a href="{{ route('reportes.index') }}" class="nav-item flex items-center px-3 py-2.5 text-sm font-medium rounded-lg relative z-10 transition-colors duration-200 {{ request()->routeIs('reportes.*') ? 'active-nav-item text-amber-500' : 'text-slate-450 hover:text-slate-200' }}">
                    <i class="fa-solid fa-chart-pie w-6 text-center mr-3 text-base"></i>
                    Reportes
                </a>
            </nav>
            
            <!-- User Section -->
            <div class="flex-shrink-0 flex border-t border-slate-800/80 p-4">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center border border-slate-700">
                        <i class="fa-solid fa-user-tie text-amber-500 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-200 truncate">
                            {{ Auth::user()->name ?? 'Administrador' }}
                        </p>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-amber-500 hover:text-amber-400 font-medium underline flex items-center mt-0.5">
                                <i class="fa-solid fa-right-from-bracket mr-1.5"></i> Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Header & Navigation (no-print) -->
    <div x-data="{ open: false }" class="no-print md:hidden fixed top-0 w-full bg-slate-900 border-b border-slate-800 z-30">
        <div class="flex items-center justify-between h-16 px-4">
            <div class="flex items-center space-x-3">
                <div class="flex items-center justify-center w-8 h-8 rounded bg-gradient-to-br from-amber-500 to-orange-600 gold-glow">
                    <i class="fa-solid fa-gem text-slate-950 text-sm"></i>
                </div>
                <h1 class="text-md font-bold tracking-wider text-amber-500 uppercase">SCP Minero</h1>
            </div>
            <button @click="open = !open" class="text-slate-400 hover:text-slate-200 focus:outline-none">
                <i class="fa-solid fa-bars text-xl"></i>
            </button>
        </div>
        
        <!-- Mobile menu list -->
        <div x-show="open" @click.away="open = false" class="px-2 pt-2 pb-4 space-y-1 bg-slate-900 border-b border-slate-800">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-amber-500/10 text-amber-500' : 'text-slate-300 hover:bg-slate-800' }}">Tablero Principal</a>
            <a href="{{ route('bocaminas.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('bocaminas.*') ? 'bg-amber-500/10 text-amber-500' : 'text-slate-300 hover:bg-slate-800' }}">Bocaminas</a>
            <a href="{{ route('trabajadores.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('trabajadores.*') ? 'bg-amber-500/10 text-amber-500' : 'text-slate-300 hover:bg-slate-800' }}">Trabajadores / Contratistas</a>
            <a href="{{ route('contratos.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('contratos.*') ? 'bg-amber-500/10 text-amber-500' : 'text-slate-300 hover:bg-slate-800' }}">Contratos</a>

            <a href="{{ route('anticipos.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('anticipos.*') ? 'bg-amber-500/10 text-amber-500' : 'text-slate-300 hover:bg-slate-800' }}">Anticipos</a>
            <a href="{{ route('pagos.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('pagos.*') ? 'bg-amber-500/10 text-amber-500' : 'text-slate-300 hover:bg-slate-800' }}">Pagos / Recibos</a>
            <a href="{{ route('reportes.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('reportes.*') ? 'bg-amber-500/10 text-amber-500' : 'text-slate-300 hover:bg-slate-800' }}">Reportes</a>
            
            <form action="{{ route('logout') }}" method="POST" class="block w-full">
                @csrf
                <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-400 hover:bg-slate-800">Cerrar Sesión</button>
            </form>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex flex-col flex-1 w-full md:pl-64 overflow-hidden">
        <!-- Top bar (only for desktop desktop, no-print) -->
        <header class="no-print hidden md:flex items-center justify-end h-16 bg-slate-900/70 backdrop-blur-md border-b border-slate-800/60 px-8 flex-shrink-0 relative z-20">
            <div class="flex items-center space-x-4">
                <span class="text-xs text-slate-400 flex items-center space-x-1">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-ping mr-1"></span>
                    Servidor Local Conectado
                </span>
                <div class="h-4 w-px bg-slate-800"></div>
                <span id="realtime-clock" class="text-sm text-slate-300 font-mono"></span>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 relative overflow-y-auto focus:outline-none p-4 md:p-8 pt-20 md:pt-8 bg-slate-950/40 z-10">
            
            <!-- Floating Toast Notifications (no-print) -->
            <div id="toast-container" class="no-print fixed top-6 right-6 z-50 flex flex-col space-y-4 max-w-sm w-full">
                @if(session('success'))
                    <div class="toast-item toast-success" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false }, 4500)">
                        <div class="flex items-start p-4">
                            <div class="flex-shrink-0 text-emerald-400">
                                <i class="fa-solid fa-circle-check text-xl animate-bounce"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-450">Operación Exitosa</p>
                                <p class="text-sm text-slate-100 font-semibold mt-1">{{ session('success') }}</p>
                            </div>
                            <button @click="show = false" class="ml-4 text-slate-500 hover:text-slate-300 transition duration-150">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <div class="toast-progress bg-emerald-500"></div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="toast-item toast-danger" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false }, 5500)">
                        <div class="flex items-start p-4">
                            <div class="flex-shrink-0 text-rose-500">
                                <i class="fa-solid fa-circle-exclamation text-xl animate-pulse"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-450">Error en Operación</p>
                                <p class="text-sm text-slate-100 font-semibold mt-1">{{ $errors->first() }}</p>
                            </div>
                            <button @click="show = false" class="ml-4 text-slate-500 hover:text-slate-300 transition duration-150">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <div class="toast-progress bg-rose-500"></div>
                    </div>
                @endif
            </div>

            @yield('content')
            
        </main>
    </div>

    <!-- Real-time Spanish Clock -->
    <script>
        function updateClock() {
            const now = new Date();
            const days = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
            const months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
            
            const dayName = days[now.getDay()];
            const day = now.getDate();
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();
            
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            const formatted = `${dayName.charAt(0).toUpperCase() + dayName.slice(1)}, ${day} de ${monthName} de ${year} | ${hours}:${minutes}:${seconds}`;
            
            const clockEl = document.getElementById('realtime-clock');
            if (clockEl) {
                clockEl.textContent = formatted;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();

        // --- GLOBAL PARTICLE CANVAS FOR MINING SPARKS ---
        const canvas = document.getElementById('particle-canvas');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            let width = canvas.width = window.innerWidth;
            let height = canvas.height = window.innerHeight;
            
            let mouse = { x: -1000, y: -1000, active: false };
            
            window.addEventListener('resize', () => {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            });
            
            window.addEventListener('mousemove', (e) => {
                mouse.x = e.clientX;
                mouse.y = e.clientY;
                mouse.active = true;
            });
            
            window.addEventListener('mouseleave', () => {
                mouse.active = false;
            });
            
            const particles = [];
            const maxParticles = 55; // Balanced for dashboard viewability
            
            class Spark {
                constructor() {
                    this.reset(true);
                }
                
                reset(initial = false) {
                    this.x = Math.random() * width;
                    this.y = initial ? Math.random() * height : height + 15;
                    this.size = Math.random() * 2.2 + 0.8;
                    this.speedY = Math.random() * 1.3 + 0.5;
                    this.speedX = (Math.random() - 0.5) * 0.5;
                    this.life = Math.random() * 0.7 + 0.3;
                    this.decay = Math.random() * 0.0025 + 0.0015;
                    this.opacity = Math.random() * 0.75 + 0.15;
                    this.wiggleFreq = Math.random() * 0.01 + 0.003;
                    this.wiggleAmp = Math.random() * 1.5 + 0.2;
                }
                
                update() {
                    this.y -= this.speedY;
                    this.life -= this.decay;
                    this.x += Math.sin(this.y * this.wiggleFreq) * 0.15 * this.wiggleAmp;
                    
                    if (mouse.active) {
                        const dx = this.x - mouse.x;
                        const dy = this.y - mouse.y;
                        const dist = Math.sqrt(dx * dx + dy * dy);
                        if (dist < 120) {
                            const force = (120 - dist) / 120;
                            this.x += (dx / dist) * force * 3;
                            this.y += (dy / dist) * force * 1;
                        }
                    }
                    
                    if (this.life <= 0 || this.x < 0 || this.x > width || this.y < -15) {
                        this.reset();
                    }
                }
                
                draw() {
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    
                    let r, g, b;
                    if (this.life > 0.7) {
                        r = 254; g = 240; b = 138; // yellow
                    } else if (this.life > 0.4) {
                        r = 249; g = 115; b = 22;  // orange
                    } else if (this.life > 0.18) {
                        r = 239; g = 68; b = 68;   // red
                    } else {
                        r = 100; g = 116; b = 139;  // gray ash
                    }
                    
                    ctx.fillStyle = `rgba(${r}, ${g}, ${b}, ${this.life * this.opacity})`;
                    ctx.shadowBlur = this.life > 0.55 ? this.size * 3.0 : 0;
                    ctx.shadowColor = 'rgb(249, 115, 22)';
                    ctx.fill();
                    ctx.shadowBlur = 0;
                }
            }
            
            for (let i = 0; i < maxParticles; i++) {
                particles.push(new Spark());
            }
            
            function animate() {
                ctx.clearRect(0, 0, width, height);
                for (let i = 0; i < particles.length; i++) {
                    particles[i].update();
                    particles[i].draw();
                }
                requestAnimationFrame(animate);
            }
            animate();
        }

        // --- GLOBAL BUTTON SPARK GENERATOR ON CLICK/HOVER ---
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Dynamic styles injection for card animations
            const animStyle = document.createElement("style");
            animStyle.innerText = `
                @keyframes slideUpFade {
                    from { opacity: 0; transform: translateY(18px); }
                    to { opacity: 1; transform: translateY(0); }
                }
            `;
            document.head.appendChild(animStyle);

            // Stagger page card loads
            const cards = document.querySelectorAll('.glass-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.animation = 'slideUpFade 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards';
                card.style.animationDelay = `${index * 65}ms`;
            });

            // 2. Liquid Glass Navbar indicator pill
            const nav = document.getElementById('main-nav');
            const pill = document.getElementById('nav-indicator-pill');
            const items = document.querySelectorAll('.nav-item');
            if (nav && pill && items.length > 0) {
                let activeItem = nav.querySelector('.active-nav-item');
                
                function positionPill(el) {
                    if (!el) {
                        pill.style.opacity = '0';
                        return;
                    }
                    pill.style.opacity = '1';
                    pill.style.top = `${el.offsetTop}px`;
                    pill.style.height = `${el.offsetHeight}px`;
                    pill.style.background = 'linear-gradient(135deg, rgba(245, 158, 11, 0.22), rgba(249, 115, 22, 0.12))';
                    pill.style.border = '1.5px solid rgba(245, 158, 11, 0.45)';
                    pill.style.boxShadow = '0 0 20px rgba(245, 158, 11, 0.35)';
                    pill.style.transition = 'all 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275)'; // Liquid Elastic Bounce
                }
                
                if (activeItem) {
                    setTimeout(() => positionPill(activeItem), 80);
                }
                
                items.forEach(item => {
                    item.addEventListener('mouseenter', () => positionPill(item));
                });
                nav.addEventListener('mouseleave', () => positionPill(activeItem));
            }

            // 3. Global buttons interaction (including nav items for premium sparks hover)
            const buttons = document.querySelectorAll('button, .btn, [type="submit"], [type="button"], a.btn, a.inline-flex, .nav-item');
            buttons.forEach(btn => {
                if (btn.id && btn.id.includes('eye')) return;
                
                const style = window.getComputedStyle(btn);
                if (style.position === 'static') {
                    btn.style.position = 'relative';
                }
                if (btn.classList.contains('nav-item')) {
                    btn.style.overflow = 'visible';
                } else if (style.overflow !== 'hidden') {
                    btn.style.overflow = 'hidden';
                }
                
                let hoverInterval;
                btn.addEventListener('mouseenter', (e) => {
                    spawnGlobalSparks(btn, 3);
                    hoverInterval = setInterval(() => spawnGlobalSparks(btn, 1), 300);
                });
                btn.addEventListener('mouseleave', () => {
                    clearInterval(hoverInterval);
                });
                
                btn.addEventListener('click', () => {
                    spawnGlobalSparks(btn, 10);
                });
            });
        });

        // 4. Form submit glass processing overlay & Global confirm modal handler
        function showProcessingOverlay(form) {
            if (form.action && form.action.includes('logout')) return;
            
            const card = form.closest('.glass-card') || form.closest('main') || document.body;
            
            // Prevent duplicate overlays
            if (card.querySelector('.processing-overlay')) return;
            
            const overlay = document.createElement('div');
            overlay.className = 'processing-overlay';
            overlay.style.position = 'absolute';
            overlay.style.inset = '0';
            overlay.style.background = 'rgba(10, 12, 18, 0.84)';
            overlay.style.backdropFilter = 'blur(10px)';
            overlay.style.webkitBackdropFilter = 'blur(10px)';
            overlay.style.borderRadius = '16px';
            overlay.style.zIndex = '50';
            overlay.style.display = 'flex';
            overlay.style.flexDirection = 'column';
            overlay.style.alignItems = 'center';
            overlay.style.justifyContent = 'center';
            overlay.style.opacity = '0';
            overlay.style.transition = 'opacity 0.25s ease';
            
            overlay.innerHTML = `
                <div style="position: relative; display: flex; align-items: center; justify-content: center;">
                    <div class="w-14 h-14 rounded-full border-2 border-amber-500/20 border-t-amber-500 animate-spin"></div>
                    <i class="fa-solid fa-gem text-amber-500 absolute text-base animate-pulse"></i>
                </div>
                <span class="text-xs text-amber-500 font-mono tracking-widest uppercase mt-4 animate-pulse">Procesando...</span>
            `;
            
            card.appendChild(overlay);
            overlay.offsetHeight; // force reflow
            overlay.style.opacity = '1';
        }

        // Global Custom Confirmation Modal
        function showCustomConfirmModal(message, reportUrl, onConfirm) {
            const existingModal = document.getElementById('custom-confirm-modal');
            if (existingModal) existingModal.remove();
            
            const modalContainer = document.createElement('div');
            modalContainer.id = 'custom-confirm-modal';
            modalContainer.className = 'fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md';
            modalContainer.style.opacity = '0';
            modalContainer.style.transition = 'opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1)';
            
            let reportButtonHtml = '';
            if (reportUrl) {
                reportButtonHtml = `
                    <a href="${reportUrl}" target="_blank" id="confirm-report-btn" class="px-4 py-2.5 text-xs font-bold uppercase tracking-wider rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 text-slate-950 shadow-lg shadow-amber-500/10 hover:from-amber-600 hover:to-orange-600 transition-all duration-150 inline-flex items-center">
                        <i class="fa-solid fa-file-pdf mr-1.5 text-sm"></i> Generar Reporte
                    </a>
                `;
            }
            
            const modalContentHtml = `
                <div class="glass-card w-full max-w-md rounded-2xl overflow-hidden shadow-2xl border border-red-500/35 relative transform scale-90 transition-all duration-300 ease-out" style="background: rgba(15, 23, 42, 0.92);">
                    <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #ef4444, #f59e0b, #ef4444); opacity: 0.9;"></div>
                    
                    <div class="p-6 space-y-5 text-center font-sans">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-500/10 border border-red-500/30 text-red-500 animate-pulse mt-2">
                            <i class="fa-solid fa-trash-can text-2xl animate-bounce" style="animation-duration: 2s;"></i>
                        </div>
                        
                        <div class="space-y-3">
                            <h3 class="text-lg font-bold tracking-wider text-red-500 uppercase font-mono">Advertencia de Eliminación</h3>
                            <p class="text-sm text-slate-200 font-semibold px-2 leading-relaxed">${message}</p>
                            <p class="text-xs text-slate-400 px-3 leading-relaxed">Se recomienda generar o revisar un reporte de este historial antes de eliminar el registro de forma permanente.</p>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 border-t border-slate-800/80 bg-slate-900/40 flex flex-wrap justify-center gap-3">
                        <button type="button" id="confirm-cancel-btn" class="px-4 py-2.5 text-xs font-bold uppercase tracking-wider rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700/60 hover:border-slate-650 transition-all duration-200">
                            Cancelar
                        </button>
                        
                        ${reportButtonHtml}
                        
                        <button type="button" id="confirm-ok-btn" class="px-4 py-2.5 text-xs font-bold uppercase tracking-wider rounded-lg bg-gradient-to-r from-red-650 to-rose-500 text-white shadow-lg shadow-red-500/20 hover:from-red-550 hover:to-rose-450 transition-all duration-300">
                            Eliminar de todas formas
                        </button>
                    </div>
                </div>
            `;
            
            modalContainer.innerHTML = modalContentHtml;
            document.body.appendChild(modalContainer);
            
            const card = modalContainer.querySelector('.glass-card');
            
            setTimeout(() => {
                modalContainer.style.opacity = '1';
                card.style.transform = 'scale(1)';
            }, 10);
            
            const closeModal = () => {
                modalContainer.classList.add('modal-hide');
                setTimeout(() => {
                    modalContainer.remove();
                }, 250);
            };
            
            const cancelBtn = modalContainer.querySelector('#confirm-cancel-btn');
            const okBtn = modalContainer.querySelector('#confirm-ok-btn');
            
            if (typeof spawnGlobalSparks === 'function') {
                cancelBtn.addEventListener('mouseenter', () => spawnGlobalSparks(cancelBtn, 2));
                cancelBtn.addEventListener('click', () => spawnGlobalSparks(cancelBtn, 6));
                okBtn.addEventListener('mouseenter', () => spawnGlobalSparks(okBtn, 3));
                okBtn.addEventListener('click', () => spawnGlobalSparks(okBtn, 12));
            }
            
            cancelBtn.addEventListener('click', closeModal);
            okBtn.addEventListener('click', () => {
                closeModal();
                if (onConfirm) onConfirm();
            });
            
            modalContainer.addEventListener('click', (e) => {
                if (e.target === modalContainer) {
                    closeModal();
                }
            });
        }

        // Intercept click events in the capturing phase to prevent inline confirm() browser alerts
        document.addEventListener('click', (e) => {
            const button = e.target.closest('button[type="submit"]');
            if (!button) return;
            
            const form = button.closest('form');
            if (!form) return;
            
            const onsubmitAttr = form.getAttribute('onsubmit');
            if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                // Prevent the submission before the browser executes onsubmit!
                e.preventDefault();
                e.stopImmediatePropagation();
                
                // Extract message
                const match = onsubmitAttr.match(/confirm\(\s*['"](.*?)['"]\s*\)/);
                const message = match ? match[1] : '¿Estás seguro de continuar?';
                
                // Extract report URL from form action route dynamically
                let reportUrl = '/reportes';
                if (form.action) {
                    if (form.action.includes('/trabajadores/')) {
                        const idMatch = form.action.match(/\/trabajadores\/(\d+)/);
                        if (idMatch) reportUrl = `/reportes?tab=trabajador&trabajador_id=${idMatch[1]}`;
                    } else if (form.action.includes('/bocaminas/')) {
                        const idMatch = form.action.match(/\/bocaminas\/(\d+)/);
                        if (idMatch) reportUrl = `/reportes?tab=bocamina&bocamina_id=${idMatch[1]}`;
                    } else if (form.action.includes('/contratos/')) {
                        reportUrl = '/reportes?tab=general';
                    } else if (form.action.includes('/anticipos/')) {
                        reportUrl = '/reportes?tab=anticipos';
                    } else if (form.action.includes('/trabajos/')) {
                        reportUrl = '/reportes?tab=general';
                    }
                }
                
                showCustomConfirmModal(message, reportUrl, () => {
                    showProcessingOverlay(form);
                    form.removeAttribute('onsubmit');
                    form.submit();
                });
            }
        }, true);

        // Intercept all submit events in the capturing phase
        document.addEventListener('submit', (e) => {
            const form = e.target;
            
            // Check if this form uses confirm() inline
            const onsubmitAttr = form.getAttribute('onsubmit');
            if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                // Prevent form execution immediately
                e.preventDefault();
                e.stopImmediatePropagation();
                
                // Extract message
                const match = onsubmitAttr.match(/confirm\(\s*['"](.*?)['"]\s*\)/);
                const message = match ? match[1] : '¿Estás seguro de continuar?';
                
                let reportUrl = '/reportes';
                if (form.action) {
                    if (form.action.includes('/trabajadores/')) {
                        const idMatch = form.action.match(/\/trabajadores\/(\d+)/);
                        if (idMatch) reportUrl = `/reportes?tab=trabajador&trabajador_id=${idMatch[1]}`;
                    } else if (form.action.includes('/bocaminas/')) {
                        const idMatch = form.action.match(/\/bocaminas\/(\d+)/);
                        if (idMatch) reportUrl = `/reportes?tab=bocamina&bocamina_id=${idMatch[1]}`;
                    } else if (form.action.includes('/contratos/')) {
                        reportUrl = '/reportes?tab=general';
                    } else if (form.action.includes('/anticipos/')) {
                        reportUrl = '/reportes?tab=anticipos';
                    } else if (form.action.includes('/trabajos/')) {
                        reportUrl = '/reportes?tab=general';
                    }
                }

                showCustomConfirmModal(message, reportUrl, () => {
                    showProcessingOverlay(form);
                    form.removeAttribute('onsubmit');
                    form.submit();
                });
            } else {
                // Standard submission, just show overlay
                showProcessingOverlay(form);
            }
        }, true);

        function spawnGlobalSparks(element, count) {
            const rect = element.getBoundingClientRect();
            
            let colorGrad = 'radial-gradient(circle, #fcd34d 0%, #f97316 60%, #ef4444 100%)';
            let shadow = '#f59e0b';
            
            if (element.classList.contains('bg-red-600') || 
                element.classList.contains('bg-red-500') || 
                element.className.includes('bg-red-') || 
                element.textContent.toLowerCase().includes('eliminar') || 
                element.textContent.toLowerCase().includes('borrar')) {
                colorGrad = 'radial-gradient(circle, #fecaca 0%, #ef4444 60%, #b91c1c 100%)';
                shadow = '#ef4444';
            } 
            else if (element.classList.contains('bg-emerald-600') || 
                     element.classList.contains('bg-emerald-500') || 
                     element.className.includes('bg-emerald-') || 
                     element.textContent.toLowerCase().includes('guardar') || 
                     element.textContent.toLowerCase().includes('crear') || 
                     element.textContent.toLowerCase().includes('procesar') ||
                     element.textContent.toLowerCase().includes('éxito')) {
                colorGrad = 'radial-gradient(circle, #a7f3d0 0%, #10b981 60%, #047857 100%)';
                shadow = '#10b981';
            }
            
            for (let i = 0; i < count; i++) {
                const spark = document.createElement('span');
                spark.className = 'global-button-spark';
                const size = Math.random() * 3.0 + 1.6;
                spark.style.width = `${size}px`;
                spark.style.height = `${size}px`;
                spark.style.background = colorGrad;
                spark.style.boxShadow = `0 0 5px ${shadow}`;
                
                const x = Math.random() * rect.width;
                const y = rect.height - Math.random() * 3;
                spark.style.left = `${x}px`;
                spark.style.top = `${y}px`;
                
                const vx = (Math.random() - 0.5) * 45;
                const vy = -(Math.random() * 45 + 25);
                
                element.appendChild(spark);
                
                spark.animate([
                    { transform: 'translate(0, 0) scale(1)', opacity: 1 },
                    { transform: `translate(${vx}px, ${vy}px) scale(0)', opacity: 0 }
                ], {
                    duration: Math.random() * 500 + 400,
                    easing: 'cubic-bezier(0.1, 0.8, 0.3, 1)'
                }).onfinish = () => spark.remove();
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
