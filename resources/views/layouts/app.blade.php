<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Google Tag Manager -->
    <x-google-tag-manager />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Google AdSense -->
    <x-google-ad-sense />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Estilos para AdSense -->
    <style>
        /* Clases para que AdSense identifique mejor las ubicaciones */
        .ad-banner {
            min-height: 90px;
            margin: 20px 0;
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
        <div id="notification" class="fixed top-4 right-4 z-50 animate-fade-in-down">
            <div class="max-w-sm bg-green-50 border border-green-200 rounded-lg shadow-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm font-medium text-green-800">
                            {{ session('success') }}
                        </p>
                    </div>
                    <div class="ml-4 flex flex-shrink-0">
                        <button onclick="closeNotification()" type="button" class="inline-flex text-green-400 hover:text-green-500">
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

        <!-- Page Heading -->
        @hasSection('header')
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    @yield('header')
                </div>
            </header>
        @endif

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
    </style>

    <script>
        function closeNotification() {
            const notification = document.getElementById('notification');
            notification.style.animation = 'fadeOutUp 0.5s ease-out forwards';
            setTimeout(() => {
                notification.remove();
            }, 500);
        }

        // Auto-cerrar después de 5 segundos
        setTimeout(() => {
            const notification = document.getElementById('notification');
            if (notification) {
                closeNotification();
            }
        }, 5000);
    </script>

    @livewireScripts
    @stack('scripts')
    
    <!-- Cookie Banner -->
    <x-cookie-banner />
</body>
</html> 