<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sistema Compra-Venta Agrícola')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <a href="/" class="flex items-center">
                            <span class="text-2xl font-bold text-green-600">🌱 AgroMarket</span>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:ml-10 md:flex md:space-x-8">
                        <a href="/" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out">
                            Inicio
                        </a>
                        <a href="/catalogo" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out">
                            Catálogo
                        </a>
                        <a href="/productores" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out">
                            Productores
                        </a>
                    </div>
                </div>

                <!-- Right side of navbar -->
                <div class="flex items-center space-x-4">
                    @auth
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-700">Hola, {{ Auth::user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="/login" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                            Iniciar Sesión
                        </a>
                        <a href="/register" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                            Registrarse
                        </a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button @click="open = !open" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <div x-data="{ open: false }" x-show="open" class="md:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="/" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Inicio</a>
                    <a href="/catalogo" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Catálogo</a>
                    <a href="/productores" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Productores</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center">
                        <span class="text-2xl font-bold text-green-600">🌱 AgroMarket</span>
                    </div>
                    <p class="mt-2 text-gray-600">
                        Conectando productores agrícolas con distribuidores y consumidores finales.
                        Encuentra los mejores productos frescos directamente del campo.
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Enlaces</h3>
                    <ul class="mt-4 space-y-4">
                        <li><a href="/" class="text-base text-gray-500 hover:text-gray-900">Inicio</a></li>
                        <li><a href="/catalogo" class="text-base text-gray-500 hover:text-gray-900">Catálogo</a></li>
                        <li><a href="/productores" class="text-base text-gray-500 hover:text-gray-900">Productores</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Soporte</h3>
                    <ul class="mt-4 space-y-4">
                        <li><a href="/contacto" class="text-base text-gray-500 hover:text-gray-900">Contacto</a></li>
                        <li><a href="/ayuda" class="text-base text-gray-500 hover:text-gray-900">Ayuda</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 border-t border-gray-200 pt-8">
                <p class="text-center text-gray-400">
                    &copy; {{ date('Y') }} AgroMarket. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html> 