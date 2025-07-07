<x-app-layout>
    <div class="flex min-h-screen bg-gray-100">
        <!-- Sidebar - Using Tailwind responsive classes instead of Alpine.js -->
        <aside class="transform -translate-x-full md:translate-x-0 fixed md:relative left-0 top-0 h-screen w-64 bg-white shadow-lg flex flex-col py-8 px-4 transition-transform duration-300 ease-in-out z-20">
            <div class="mb-8 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-yellow-600">Panel</h2>
                <!-- Botón cerrar solo visible en móvil -->
                <button id="closeSidebar" class="md:hidden text-gray-600 hover:text-gray-800">
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
        <div class="flex-1">
            <!-- Mobile header - Using vanilla JS instead of Alpine -->
            <div class="md:hidden bg-white shadow-sm py-2 px-4 mb-4">
                <button id="openSidebar" class="text-gray-600 hover:text-gray-800">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
            
            <main class="p-4 md:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Overlay for mobile -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-10 hidden md:hidden"></div>

    <script>
        // Vanilla JavaScript para el manejo del sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('aside');
            const overlay = document.getElementById('sidebarOverlay');
            const openButton = document.getElementById('openSidebar');
            const closeButton = document.getElementById('closeSidebar');

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }

            openButton.addEventListener('click', openSidebar);
            closeButton.addEventListener('click', closeSidebar);
            overlay.addEventListener('click', closeSidebar);

            // Cerrar sidebar al cambiar el tamaño de la ventana a desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) { // 768px es el breakpoint 'md' de Tailwind
                    closeSidebar();
                }
            });
        });
    </script>
</x-app-layout>

