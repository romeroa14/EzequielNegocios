<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl">
                Catálogo de Productos
            </h1>
            <p class="mt-4 text-lg text-gray-600">
                Descubre productos frescos directamente de los productores
            </p>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <!-- Search Bar -->
            <div class="mb-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-green-500 focus:border-green-500"
                        placeholder="Buscar productos..."
                    >
                </div>
            </div>

            <!-- Filter Toggle Button -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-4 sm:mt-0">
                <div class="flex items-center mb-4 sm:mb-0">
                    <button 
                        wire:click="$toggle('showFilters')"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                        </svg>
                        Filtros
                        @if($showFilters)
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                        @else
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        @endif
                    </button>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-500 whitespace-nowrap">Ordenar por:</label>
                    <select wire:model.live="sortBy" class="w-full sm:w-auto border border-gray-300 rounded-md text-sm py-1 px-2">
                        <option value="created_at">Más recientes</option>
                        <option value="unit_price">Precio</option>
                        <option value="title">Nombre</option>
                        <option value="harvest_date">Fecha de cosecha</option>
                    </select>
                </div>
            </div>

            <!-- Filters Panel -->
            @if($showFilters)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Category Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                            <select wire:model.live="selectedCategory" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                <option value="">Todas las categorías</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subcategory Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subcategoría</label>
                            <select wire:model.live="selectedSubcategory" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                <option value="">Todas las subcategorías</option>
                                @foreach($subcategories as $subcategory)
                                    <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Quality Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Calidad</label>
                            <select wire:model.live="selectedQuality" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500">
                                <option value="">Todas las calidades</option>
                                <option value="premium">Premium</option>
                                <option value="standard">Estándar</option>
                                <option value="economic">Económico</option>
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rango de Precio</label>
                            <div class="flex space-x-2">
                                <input 
                                    type="number" 
                                    wire:model.live.debounce.500ms="minPrice"
                                    placeholder="Min"
                                    class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500"
                                >
                                <input 
                                    type="number" 
                                    wire:model.live.debounce.500ms="maxPrice"
                                    placeholder="Max"
                                    class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500"
                                >
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vendedor</label>
                            <select wire:model.live="producer" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                                <option value="">Todos los productores</option>
                                @foreach($sellers as $seller)
                                    <option value="{{ $seller->id }}">{{ $seller->first_name }} {{ $seller->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Clear Filters Button -->
                    <div class="mt-4 text-right">
                        <button 
                            wire:click="clearFilters"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        >
                            Limpiar Filtros
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            @forelse($products as $product)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                    <!-- Product Image -->
                    <div class="aspect-w-4 aspect-h-3 bg-gray-200">
                        
                        @if($product->product->image)
                            <img 
                                src="{{ $product->product->image_url }}"
                                alt="{{ $product->product->name }}"
                                class="w-full h-80 object-cover rounded"
                            >
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Product Info -->
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">
                                {{ $product->title }}
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($product->quality_grade === 'premium') bg-green-100 text-green-800
                                @elseif($product->quality_grade === 'standard') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif
                            ">
                                @if($product->quality_grade === 'premium') Premium
                                @elseif($product->quality_grade === 'standard') Estándar
                                @else Económico
                                @endif
                            </span>
                        </div>

                        <p class="text-sm text-gray-600 mb-2">{{ $product->product->name }}</p>
                        <p class="text-sm text-gray-500 mb-3 line-clamp-2">{{ Str::limit($product->description, 80) }}</p>

                        <!-- Price and Quantity -->
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <span class="text-lg font-bold text-green-600">${{ number_format($product->unit_price, 2) }}</span>
                                <span class="text-sm text-gray-500">/ unidad</span>
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $product->quantity_available }} disponibles
                            </div>
                        </div>

                        <!-- Location and Producer -->
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                            <span>📍 {{ $product->location_city }}, {{ $product->location_state }}</span>
                            <span>👤 {{ $product->person->first_name . ' ' . $product->person->last_name ?? 'Productor' }}</span>
                        </div>

                        <!-- Action Button -->
                        @if(Auth::check())
                            <button 
                                wire:click="$dispatch('contactProducer', { productId: {{ $product->id }} })"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out"
                            >
                                Contactar Productor
                            </button>
                        @else
                            <a href="{{ route('register') }}" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">Regístrate para contactar</a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8l-8 5-8-5" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay productos disponibles</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        No se encontraron productos que coincidan con tus criterios de búsqueda.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 rounded-lg shadow-sm">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
