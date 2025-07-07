<x-app-layout>
    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen bg-gray-100">
        <!-- Mobile sidebar backdrop -->
        <div 
            x-show="sidebarOpen" 
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-600 bg-opacity-75 z-20 lg:hidden"
            @click="sidebarOpen = false">
        </div>

        <!-- Sidebar -->
        <aside 
            x-cloak
            :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
            class="fixed inset-y-0 left-0 z-30 w-64 transform bg-white shadow-lg lg:relative lg:translate-x-0 transition duration-300 ease-in-out flex flex-col py-8 px-4">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold text-yellow-600">Panel</h2>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-600 hover:text-gray-800">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
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
        <main class="flex-1 mt-3 lg:ml-64">
            <!-- Mobile header -->
            <div class="lg:hidden bg-white shadow-sm py-2 px-4 mb-4">
                <button @click="sidebarOpen = true" class="text-gray-600 hover:text-gray-800">
                    {{-- <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg> --}}
                    Ver Panel
                </button>
            </div>
            
            <div class="p-4 lg:p-8">
                @yield('content')
            </div>
        </main>
    </div>
</x-app-layout>

