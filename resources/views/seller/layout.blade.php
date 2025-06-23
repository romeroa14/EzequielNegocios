<x-app-layout>
    <div class="flex min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg flex flex-col py-8 px-4">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-yellow-600">Panel</h2>
            </div>
            <nav class="flex flex-col space-y-2">
                <a href="{{ route('seller.products.index') }}"
                   class="px-4 py-2 rounded hover:bg-yellow-100 font-semibold text-gray-700 {{ request()->routeIs('seller.products.*') ? 'bg-yellow-200' : '' }}">
                    Productos
                </a>
                <a href="{{ route('seller.listings.index') }}"
                   class="px-4 py-2 rounded hover:bg-blue-100 font-semibold text-gray-700 {{ request()->routeIs('seller.listings.*') ? 'bg-blue-200' : '' }}">
                    Publicaciones
                </a>
            </nav>
        </aside>
        <!-- Main content -->
        <main class="flex-1 p-8">
            @yield('content')
        </main>
    </div>
</x-app-layout>

