<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Google Analytics -->
    <x-google-analytics />

    <!-- Google Tag Manager -->
    <x-google-tag-manager />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Google AdSense -->
    <x-google-ad-sense />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Styles -->
    <style>
        [x-cloak] { display: none !important; }
        
        .aspect-w-16 {
            position: relative;
            padding-bottom: 56.25%;
        }
        
        .aspect-w-16 > * {
            position: absolute;
            height: 100%;
            width: 100%;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }
        
        /* Fix para botones de navegación */
        nav {
            position: relative;
            z-index: 1000;
            background: white;
        }
        
        .nav-buttons {
            position: relative;
            z-index: 1001;
        }
        
        /* Asegurar que los botones sean clickeables */
        .nav-buttons a {
            position: relative;
            z-index: 1002;
            pointer-events: auto;
            cursor: pointer;
        }
        
        /* Asegurar que no haya elementos superpuestos */
        .nav-buttons a:hover {
            z-index: 1003;
        }

        /* Clases para que AdSense identifique mejor las ubicaciones */
        .ad-banner {
            min-height: 90px;
            margin: 20px 0;
            position: relative;
            z-index: 1;
            background: #f8f9fa;
            border: 1px dashed #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .ad-sidebar {
            min-height: 250px;
            margin: 20px 0;
            background: #f8f9fa;
            border: 1px dashed #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .ad-in-article {
            min-height: 90px;
            margin: 20px 0;
            background: #f8f9fa;
            border: 1px dashed #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <!-- Google Tag Manager (noscript) -->
    <x-google-tag-manager-noscript />

    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Notificaciones Flotantes -->
        @if (session('success'))
        <div id="notification-success" class="fixed top-4 right-4 z-[9999] animate-fade-in-down">
            <div class="max-w-lg bg-green-50 border border-green-200 rounded-lg shadow-lg p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4 w-0 flex-1">
                        <h3 class="text-lg font-semibold text-green-900 mb-1">
                            ¡Éxito!
                        </h3>
                        <p class="text-base text-green-800 leading-relaxed">
                            {{ session('success') }}
                        </p>
                    </div>
                    <div class="ml-4 flex flex-shrink-0">
                        <button onclick="closeNotification('notification-success')" type="button" class="inline-flex text-green-400 hover:text-green-600 transition-colors duration-200">
                            <span class="sr-only">Cerrar</span>
                            <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if (session('error'))
        <div id="notification-error" class="fixed top-4 right-4 z-[9999] animate-fade-in-down">
            <div class="max-w-sm bg-red-50 border border-red-200 rounded-lg shadow-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm font-medium text-red-800">
                            {{ session('error') }}
                        </p>
                    </div>
                    <div class="ml-4 flex flex-shrink-0">
                        <button onclick="closeNotification('notification-error')" type="button" class="inline-flex text-red-400 hover:text-red-500">
                            <span class="sr-only">Cerrar</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if (session('warning'))
        <div id="notification-warning" class="fixed top-4 right-4 z-[9999] animate-fade-in-down">
            <div class="max-w-sm bg-yellow-50 border border-yellow-200 rounded-lg shadow-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm font-medium text-yellow-800">
                            {{ session('warning') }}
                        </p>
                    </div>
                    <div class="ml-4 flex flex-shrink-0">
                        <button onclick="closeNotification('notification-warning')" type="button" class="inline-flex text-yellow-400 hover:text-yellow-500">
                            <span class="sr-only">Cerrar</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if (session('info'))
        <div id="notification-info" class="fixed top-20 right-4 z-[9998] animate-fade-in-down">
            <div class="max-w-lg bg-blue-50 border border-blue-200 rounded-lg shadow-lg p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                    </div>
                    <div class="ml-4 w-0 flex-1">
                        <h3 class="text-lg font-semibold text-blue-900 mb-1">
                            Información
                        </h3>
                        <p class="text-base text-blue-800 leading-relaxed">
                            {{ session('info') }}
                        </p>
                    </div>
                    <div class="ml-4 flex flex-shrink-0">
                        <button onclick="closeNotification('notification-info')" type="button" class="inline-flex text-blue-400 hover:text-blue-600 transition-colors duration-200">
                            <span class="sr-only">Cerrar</span>
                            <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Page Heading -->
        @hasSection('header')
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    @yield('header')
                </div>
            </header>
        @endif

        <!-- Notificación suave para usuarios no verificados -->
        @auth('person')
            @if(!auth('person')->user()->is_verified)
            <div id="profile-completion-banner" class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-400 p-4 mb-4 mx-4 rounded-r-lg shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <span class="font-medium">¡Completa tu perfil!</span> 
                                Agrega más información para acceder a todas las funciones.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('profile.complete') }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            Completar
                        </a>
                        <button onclick="closeProfileBanner()" type="button" class="text-blue-400 hover:text-blue-500">
                            <span class="sr-only">Cerrar</span>
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            @endif
        @endauth

        <!-- Page Content -->
        <main>
            <div class="max-w-full mx-auto px-4">
                @yield('content')
            </div>
        </main>
    </div>

    <style>
        .animate-fade-in-down {
            animation: fadeInDown 0.5s ease-out;
        }

        @keyframes fadeInDown {
            0% {
                opacity: 0;
                transform: translateY(-10px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Estilos para notificaciones más anchas */
        #notification-success,
        #notification-info,
        #notification-error,
        #notification-warning {
            min-width: 400px;
            max-width: 500px;
            z-index: 9999 !important;
            position: fixed !important;
        }

        /* Responsive para móviles */
        @media (max-width: 640px) {
            #notification-success,
            #notification-info,
            #notification-error,
            #notification-warning {
                min-width: 300px;
                max-width: 350px;
                right: 1rem;
                left: 1rem;
            }
        }
    </style>

    <script>
        function closeNotification(notificationId) {
            const notification = document.getElementById(notificationId);
            if (notification) {
                notification.style.animation = 'fadeOutUp 0.5s ease-out forwards';
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }
        }

        function closeProfileBanner() {
            const banner = document.getElementById('profile-completion-banner');
            if (banner) {
                banner.style.animation = 'fadeOutUp 0.5s ease-out forwards';
                setTimeout(() => {
                    banner.remove();
                }, 500);
            }
        }

        // Auto-cerrar todas las notificaciones después de 5 segundos
        setTimeout(() => {
            const notifications = document.querySelectorAll('[id^="notification-"]');
            notifications.forEach(notification => {
                closeNotification(notification.id);
            });
        }, 5000);
    </script>

    @livewireScripts
    @stack('scripts')
    
    <!-- Form Tracking -->
    <x-form-tracking />
    
    <!-- Cookie Banner -->
    <x-cookie-banner />
</body>
</html> 