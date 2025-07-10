<x-app-layout>
    <div class="flex min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="hidden md:block w-64 bg-white shadow-lg">
            <div class="flex flex-col h-full p-4">
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
            </div>
        </aside>

        <!-- Mobile Sidebar -->
        <div id="mobileSidebar" class="fixed inset-y-0 left-0 z-40 md:hidden transform -translate-x-full transition-all duration-300 ease-in-out">
            <aside class="relative w-[280px] h-full bg-white shadow-xl">
                <div class="flex flex-col h-full p-4">
                    <div class="flex justify-between items-center mb-8">
                        <h2 class="text-2xl font-bold text-yellow-600">Panel</h2>
                        <button id="closeSidebar" class="text-gray-600 hover:text-gray-800 p-2 rounded-lg transition-colors duration-200">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <nav class="flex flex-col space-y-2">
                            <a href="{{ route('seller.products.index') }}"
                            class="px-4 py-2 rounded-lg transition-colors duration-200 hover:bg-yellow-100 font-semibold text-gray-700 {{ request()->routeIs('seller.products.*') ? 'bg-yellow-200' : '' }}">
                                Productos
                            </a>
                            <a href="{{ route('seller.listings.index') }}"
                            class="px-4 py-2 rounded-lg transition-colors duration-200 hover:bg-blue-100 font-semibold text-gray-700 {{ request()->routeIs('seller.listings.*') ? 'bg-blue-200' : '' }}">
                                Publicaciones
                            </a>
                        </nav>
                </div>
            </aside>
        </div>

        <!-- Main content -->
        <div class="flex-1 md:ml-64">
            <!-- Mobile header -->
            <div class="md:hidden bg-white shadow-sm py-2 px-4 sticky top-0 z-20">
                <button id="openSidebar" class="text-gray-600 hover:text-gray-800 p-2 rounded-lg transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
            
            <main class="p-4 md:p-8">
                @yield('content')
            </main>
        </div>

        <!-- Overlay for mobile -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 ease-in-out z-30 md:hidden"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileSidebar = document.getElementById('mobileSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const openButton = document.getElementById('openSidebar');
            const closeButton = document.getElementById('closeSidebar');

            function openSidebar() {
                mobileSidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('opacity-0', 'pointer-events-none');
                document.body.classList.add('overflow-hidden');
            }

            function closeSidebar() {
                mobileSidebar.classList.add('-translate-x-full');
                overlay.classList.add('opacity-0', 'pointer-events-none');
                document.body.classList.remove('overflow-hidden');
            }

            openButton.addEventListener('click', openSidebar);
            closeButton.addEventListener('click', closeSidebar);
            overlay.addEventListener('click', closeSidebar);

            // Cerrar al presionar ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeSidebar();
                }
            });

            // Cerrar al cambiar de ruta (si estás usando turbo o similar)
            document.addEventListener('turbo:visit', closeSidebar);
        });
    </script>
</x-app-layout>

