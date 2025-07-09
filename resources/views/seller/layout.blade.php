<x-app-layout>
    <div class="flex min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="hidden md:flex md:w-150 md:flex-col md:fixed md:inset-y-0 bg-white shadow-lg">
            <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto">
                <div class="px-4 mb-8">
                    <h2 class="text-2xl font-bold text-yellow-600">Panel</h2>
                </div>
                
                <nav class="flex-1 px-4 space-y-2">
                    <a href="{{ route('seller.products.index') }}"
                       class="px-4 py-2 rounded hover:bg-yellow-100 font-semibold text-gray-700 {{ request()->routeIs('seller.products.*') ? 'bg-yellow-200' : '' }} block">
                        Productos
                    </a>
                    <a href="{{ route('seller.listings.index') }}"
                       class="px-4 py-2 rounded hover:bg-blue-100 font-semibold text-gray-700 {{ request()->routeIs('seller.listings.*') ? 'bg-blue-200' : '' }} block">
                        Publicaciones
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Mobile sidebar -->
        <div class="md:hidden">
            <div class="fixed inset-0 z-40 flex">
                <!-- Sidebar overlay -->
                <div id="sidebarOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden"></div>

                <!-- Sidebar -->
                <div id="mobileSidebar" class="relative flex-1 flex flex-col max-w-xs w-full bg-white transform -translate-x-full transition-transform duration-300 ease-in-out">
                    <div class="absolute top-0 right-0 -mr-12 pt-2">
                        <button id="closeSidebar" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                            <span class="sr-only">Cerrar sidebar</span>
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                        <div class="px-4 mb-8">
                            <h2 class="text-2xl font-bold text-yellow-600">Panel</h2>
                        </div>
                        <nav class="px-4 space-y-2">
                            <a href="{{ route('seller.products.index') }}"
                               class="px-4 py-2 rounded hover:bg-yellow-100 font-semibold text-gray-700 {{ request()->routeIs('seller.products.*') ? 'bg-yellow-200' : '' }} block">
                                Productos
                            </a>
                            <a href="{{ route('seller.listings.index') }}"
                               class="px-4 py-2 rounded hover:bg-blue-100 font-semibold text-gray-700 {{ request()->routeIs('seller.listings.*') ? 'bg-blue-200' : '' }} block">
                                Publicaciones
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="md:pl-64 flex flex-col flex-1">
            <!-- Mobile header -->
            <div class="md:hidden bg-white shadow-sm py-2 px-4">
                <button id="openSidebar" class="text-gray-500 hover:text-gray-600">
                    <span class="sr-only">Abrir sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
            
            <main class="flex-1 p-4 md:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileSidebar = document.getElementById('mobileSidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const openButton = document.getElementById('openSidebar');
            const closeButton = document.getElementById('closeSidebar');

            function openSidebar() {
                mobileSidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.remove('hidden');
            }

            function closeSidebar() {
                mobileSidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
            }

            openButton?.addEventListener('click', openSidebar);
            closeButton?.addEventListener('click', closeSidebar);
            sidebarOverlay?.addEventListener('click', closeSidebar);

            // Cerrar sidebar al cambiar el tamaño de la ventana a desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    closeSidebar();
                }
            });
        });
    </script>
</x-app-layout>

