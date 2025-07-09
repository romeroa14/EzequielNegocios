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
        <div class="fixed inset-y-0 left-0 z-30 md:hidden">
            <aside class="w-64 h-full bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out">
                <div class="flex flex-col h-full p-4">
                    <div class="flex justify-between items-center mb-8">
                        <h2 class="text-2xl font-bold text-yellow-600">Panel</h2>
                        <button id="closeSidebar" class="text-gray-600 hover:text-gray-800">
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
                </div>
            </aside>
        </div>

        <!-- Main content -->
        <div class="flex-1 md:ml-64">
            <!-- Mobile header -->
            <div class="md:hidden bg-white shadow-sm py-2 px-4">
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

        <!-- Overlay for mobile -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden md:hidden"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileSidebar = document.querySelector('.fixed.inset-y-0 aside');
            const overlay = document.getElementById('sidebarOverlay');
            const openButton = document.getElementById('openSidebar');
            const closeButton = document.getElementById('closeSidebar');

            function openSidebar() {
                mobileSidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            }

            function closeSidebar() {
                mobileSidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }

            openButton.addEventListener('click', openSidebar);
            closeButton.addEventListener('click', closeSidebar);
            overlay.addEventListener('click', closeSidebar);
        });
    </script>
</x-app-layout>

