<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Diagnóstico de Autenticación -->
        {{-- <div class="bg-yellow-100 p-4 text-sm">
            @auth
                <p>✅ Usuario autenticado: {{ auth()->user()->email }}</p>
                @if(auth()->user()->person)
                    <p>✅ Persona asociada: {{ auth()->user()->person->first_name }} {{ auth()->user()->person->last_name }}</p>
                    <p>✅ Rol actual: {{ auth()->user()->person->role }}</p>
                @else
                    <p>❌ No hay persona asociada al usuario</p>
                @endif
            @else
                <p>❌ Usuario no autenticado</p>
            @endauth
        </div> --}}
        
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('catalog') }}" class="text-2xl font-bold text-yellow-600">
                        🌱 AgroMarket
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    @auth
                        @if(auth()->user()->role === 'seller')
                            <x-nav-link :href="route('seller.dashboard')" :active="request()->routeIs('seller.dashboard')">
                                {{ __('Dashboard de Vendedor') }}
                            </x-nav-link>
                        @endif
                        
                        @if(auth()->user()->role === 'buyer')
                            <x-nav-link :href="route('buyer.dashboard')" :active="request()->routeIs('buyer.dashboard')">
                                {{ __('Dashboard de Comprador') }}
                            </x-nav-link>
                        @endif
                    @endauth
                    
                    <x-nav-link :href="route('catalog')" :active="request()->routeIs('catalog')">
                        {{ __('Catálogo') }}
                    </x-nav-link>
                    <x-nav-link :href="route('producers')" :active="request()->routeIs('producers')">
                        {{ __('Productores') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>
                                    @if(auth()->user()->person)
                                        {{ auth()->user()->person->first_name }} {{ auth()->user()->person->last_name }}
                                    @else
                                        {{ auth()->user()->first_name }} {{ auth()->user()->last_name }} 
                                    @endif

                                    @if (!auth()->user()->email_verified_at)
                                        <span title="Cuenta no verificada" class="ml-2 text-xs text-red-500 font-semibold">
                                            <svg class="inline h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z" />
                                            </svg>
                                            No verificado
                                        </span>
                                    @endif
                                </div>

                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @if(auth()->user()->person && auth()->user()->person->role === 'seller')
                                <x-dropdown-link :href="route('seller.dashboard')">
                                    {{ __('Dashboard') }}
                                </x-dropdown-link>
                            @endif

                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Perfil') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Cerrar Sesión') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                        {{ __('Iniciar Sesión') }}
                    </a>
                    <a href="{{ route('register') }}" class="ml-4 bg-yellow-600 hover:bg-yellow-700 text-gray-500 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        {{ __('Registrarse') }}
                    </a>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                @if(auth()->user()->role === 'seller')
                    <x-responsive-nav-link :href="route('seller.dashboard')" :active="request()->routeIs('seller.dashboard')">
                        {{ __('Dashboard de Vendedor') }}
                    </x-responsive-nav-link>
                @endif
                
                @if(auth()->user()->role === 'buyer')
                    <x-responsive-nav-link :href="route('buyer.dashboard')" :active="request()->routeIs('buyer.dashboard')">
                        {{ __('Dashboard de Comprador') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
            
            <x-responsive-nav-link :href="route('catalog')" :active="request()->routeIs('catalog')">
                {{ __('Catálogo') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('producers')" :active="request()->routeIs('producers')">
                {{ __('Productores') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">
                        @if(auth()->user()->person)
                            {{ auth()->user()->person->first_name }} {{ auth()->user()->person->last_name }}
                        @else
                            {{ auth()->user()->email }}
                        @endif
                    </div>
                    <div class="font-medium text-sm text-gray-500">
                        {{ auth()->user()->email }}
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    @if(auth()->user()->person && auth()->user()->person->role === 'seller')
                        <x-responsive-nav-link :href="route('seller.dashboard')">
                            {{ __('Dashboard') }}
                        </x-responsive-nav-link>
                    @endif

                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Perfil') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Cerrar Sesión') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>