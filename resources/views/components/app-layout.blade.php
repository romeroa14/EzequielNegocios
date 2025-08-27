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

        <!-- Page Heading -->
        @if(isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
    
    <!-- Form Tracking -->
    <x-form-tracking />
    
    <!-- Cookie Banner -->
    <x-cookie-banner />
</body>
</html> 