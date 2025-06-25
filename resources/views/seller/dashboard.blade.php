<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('¡Bienvenido, al Panel de Vendedor  ' . Auth::user()->first_name . '!') }}
        </h2>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                {{-- <h3 class="text-xl font-bold mb-4">¡Bienvenido, {{ Auth::user()->first_name }}!</h3> --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Tarjetas resumen (valores de ejemplo, reemplazar por variables dinámicas) -->
                    <div class="bg-blue-100 p-4 rounded shadow text-center">
                        <div class="text-3xl font-bold text-blue-700">{{ $productsCount }}</div>
                        <div class="text-gray-700">Productos publicados</div>
                    </div>
                    <div class="bg-green-100 p-4 rounded shadow text-center">
                        <div class="text-3xl font-bold text-green-700">{{ $listingsCount }}</div>
                        <div class="text-gray-700">Publicaciones</div>
                    </div>
                    <div class="bg-yellow-100 p-4 rounded shadow text-center">
                        <div class="text-3xl font-bold text-yellow-700">10</div>
                        <div class="text-gray-700">Pedidos pendientes</div>
                    </div>
                </div>
                <div class="flex gap-4">
                    <a href="{{ route('seller.products.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded shadow">Gestionar Productos</a>
                    <a href="{{ route('seller.listings.index') }}" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded shadow">Mis Publicaciones</a>
                    <a href="{{ route('seller.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded shadow">Ver Estadísticas</a>
                </div>
            </div>
        </div>
    </div>
    
</x-app-layout> 