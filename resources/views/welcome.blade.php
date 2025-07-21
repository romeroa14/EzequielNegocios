@extends('layouts.app')

@section('content')
<div class="min-h-screen">
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-r from-green-600 to-green-700 overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 pb-8 sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                    <div class="sm:text-center lg:text-left">
                        <h1 class="text-4xl tracking-tight font-extrabold text-white sm:text-5xl md:text-6xl">
                            <span class="block xl:inline">Conecta con</span>
                            <span class="block text-green-200 xl:inline">productores locales</span>
                        </h1>
                        <p class="mt-3 text-base text-green-100 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                            Encuentra productos frescos directamente del campo. Conectamos productores agrícolas con distribuidores y consumidores finales en un solo lugar.
                        </p>
                        <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                            <div class="rounded-md shadow">
                                <a href="{{ route('catalog') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-green-700 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10">
                                    Ver Catálogo
                                </a>
                            </div>
                            <div class="mt-3 sm:mt-0 sm:ml-3">
                                <a href="{{ route('producers') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-500 hover:bg-green-400 md:py-4 md:text-lg md:px-10">
                                    Conocer Productores
                                </a>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
            <div class="h-56 w-full bg-green-500 sm:h-72 md:h-96 lg:w-full lg:h-full flex items-center justify-center">
                <div class="text-center text-white">
                    <svg class="mx-auto h-32 w-32 text-green-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="mt-4 text-xl font-semibold">Del campo a tu mesa</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-green-600 font-semibold tracking-wide uppercase">Características</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Una mejor forma de comprar y vender productos agrícolas
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                    Nuestra plataforma facilita la conexión directa entre productores y compradores, eliminando intermediarios y mejorando los márgenes para todos.
                </p>
            </div>

            <div class="mt-10">
                <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10">
                    <div class="relative">
                        <dt>
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-green-500 text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Búsqueda Inteligente</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500">
                            Encuentra exactamente lo que necesitas con nuestros filtros avanzados por categoría, calidad, precio y ubicación.
                        </dd>
                    </div>

                    <div class="relative">
                        <dt>
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-green-500 text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Productores Locales</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500">
                            Conecta con productores de tu región y conoce el origen de tus alimentos.
                        </dd>
                    </div>

                    <div class="relative">
                        <dt>
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-green-500 text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Calidad Garantizada</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500">
                            Todos los productos están clasificados por calidad y cuentan con información detallada de cosecha y frescura.
                        </dd>
                    </div>

                    <div class="relative">
                        <dt>
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-green-500 text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Comunicación Directa</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500">
                            Contacta directamente con los productores para acordar precios, cantidades y condiciones de entrega.
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="relative bg-gray-50">
        <div class="relative h-56 bg-green-600 sm:h-72 md:absolute md:left-0 md:h-full md:w-1/2">
            <div class="h-full flex items-center justify-center">
                <div class="text-center text-white">
                    <svg class="mx-auto h-24 w-24 text-green-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    <p class="mt-4 text-xl font-semibold">Únete a nuestra comunidad</p>
                </div>
            </div>
        </div>
        <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
            <div class="md:ml-auto md:w-1/2 md:pl-10">
                <h2 class="text-base font-semibold uppercase tracking-wider text-gray-300">
                    Mejor precio, mejor calidad
                </h2>
                <p class="mt-2 text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl">
                    ¿Eres productor?
                </p>
                <p class="mt-3 text-lg text-gray-500">
                    Registra tus productos y llega a más clientes. Nuestra plataforma te permite mostrar tus productos, gestionar pedidos y crecer tu negocio.
                </p>
                <div class="mt-8">
                    <div class="inline-flex rounded-md shadow">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            Registrarse como Productor
                            <svg class="-mr-1 ml-3 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="bg-white pt-12 sm:pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Confiado por productores y compradores
                </h2>
                <p class="mt-3 text-xl text-gray-500 sm:mt-4">
                    Nuestra plataforma está creciendo día a día
                </p>
            </div>
        </div>
        <div class="mt-10 pb-12 bg-white sm:pb-16">
            <div class="relative">
                <div class="absolute inset-0 h-1/2 bg-white"></div>
                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="max-w-4xl mx-auto">
                        <dl class="rounded-lg bg-white shadow-lg sm:grid sm:grid-cols-3">
                            <div class="flex flex-col border-b border-gray-100 p-6 text-center sm:border-0 sm:border-r">
                                <dt class="order-2 mt-2 text-lg leading-6 font-medium text-gray-500">
                                    Productores Registrados
                                </dt>
                                <dd class="order-1 text-5xl font-extrabold text-green-600">
                                    {{ App\Models\Person::where('role', 'seller')->count() }}+
                                </dd>
                            </div>
                            <div class="flex flex-col border-t border-b border-gray-100 p-6 text-center sm:border-0 sm:border-l sm:border-r">
                                <dt class="order-2 mt-2 text-lg leading-6 font-medium text-gray-500">
                                    Publicaciones Realizadas
                                </dt>
                                <dd class="order-1 text-5xl font-extrabold text-green-600">
                                    {{ App\Models\ProductListing::where('status', 'active')->count() }}+
                                </dd>
                            </div>
                            <div class="flex flex-col border-t border-gray-100 p-6 text-center sm:border-0 sm:border-l">
                                <dt class="order-2 mt-2 text-lg leading-6 font-medium text-gray-500">
                                    Categorías
                                </dt>
                                <dd class="order-1 text-5xl font-extrabold text-green-600">
                                    {{ App\Models\ProductCategory::where('is_active', true)->count() }}+
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
