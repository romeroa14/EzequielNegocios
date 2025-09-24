<div>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header estilo NFT -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold text-gray-900">Catálogo de Productos</h2>
                    <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">EXPLORAR MÁS</a>
                </div>

                <!-- Filtros estilo NFT -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex flex-wrap items-center gap-4">
                        <!-- Categoría -->
                        <div class="relative">
                            <select wire:model.live="selectedCategory" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-gray-700 transition-colors border-0 focus:ring-2 focus:ring-blue-500">
                                <option value="">Todas las Categorías</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subcategoría -->
                        <div class="relative">
                            <select wire:model.live="selectedSubcategory" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-gray-700 transition-colors border-0 focus:ring-2 focus:ring-blue-500">
                                <option value="">Todas las Subcategorías</option>
                                @foreach($subcategories as $subcategory)
                                    <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Línea de Producto -->
                        <div class="relative">
                            <select wire:model.live="selectedLine" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-gray-700 transition-colors border-0 focus:ring-2 focus:ring-blue-500">
                                <option value="">Todas las Líneas</option>
                                @foreach($productLines as $line)
                                    <option value="{{ $line->id }}">{{ $line->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Marca -->
                        <div class="relative">
                            <select wire:model.live="selectedBrand" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-gray-700 transition-colors border-0 focus:ring-2 focus:ring-blue-500">
                                <option value="">Todas las Marcas</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Presentación -->
                        <div class="relative">
                            <select wire:model.live="selectedPresentation" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-gray-700 transition-colors border-0 focus:ring-2 focus:ring-blue-500">
                                <option value="">Todas las Presentaciones</option>
                                @foreach($presentations as $presentation)
                                    <option value="{{ $presentation->id }}">{{ $presentation->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Ordenar -->
                        <div class="ml-auto">
                            <select wire:model.live="sortBy" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-gray-700 transition-colors border-0 focus:ring-2 focus:ring-blue-500">
                                <option value="created_at">Más Recientes</option>
                                <option value="title">Nombre A-Z</option>
                                <option value="title_desc">Nombre Z-A</option>
                                <option value="unit_price">Precio: Menor a Mayor</option>
                                <option value="unit_price_desc">Precio: Mayor a Menor</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filtros adicionales -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex flex-wrap items-center gap-4">
                            <!-- Búsqueda -->
                            <div class="flex-1 min-w-64">
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.300ms="search"
                                    placeholder="Buscar productos..."
                                    class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors border-0 focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <!-- Precio Mínimo -->
                            <div class="min-w-32">
                                <input 
                                    type="number" 
                                    wire:model.live="minPrice"
                                    placeholder="Precio mín."
                                    class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors border-0 focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <!-- Precio Máximo -->
                            <div class="min-w-32">
                                <input 
                                    type="number" 
                                    wire:model.live="maxPrice"
                                    placeholder="Precio máx."
                                    class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors border-0 focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <!-- Limpiar Filtros -->
                            <button 
                                wire:click="clearFilters"
                                class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors"
                            >
                                Limpiar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                @forelse($products as $product)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 group">
                        <!-- Product Image -->
                        <div class="relative overflow-hidden">
                            <div 
                                wire:click="showProductDetail({{ $product->id }})"
                                class="relative group cursor-pointer"
                            >
                                <img 
                                    src="{{ $this->getFirstImageUrl($product) }}"
                                    alt="{{ $product->title }}"
                                    class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-300"
                                    onerror="this.src='{{ asset('images/placeholder.png') }}'"
                                />
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-opacity duration-300"></div>
                            </div>
                            
                            <!-- Heart Button -->
                            <button class="absolute top-3 right-3 w-8 h-8 bg-white/90 hover:bg-white rounded-full flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Product Info -->
                        <div class="p-4">
                            <!-- Title -->
                            <div class="mb-3">
                                <h3 
                                    wire:click="showProductDetail({{ $product->id }})"
                                    class="text-lg font-semibold text-gray-900 line-clamp-2 cursor-pointer hover:text-blue-600 transition-colors"
                                >
                                    {{ $product->title }}
                                </h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                        {{ $product->product->name }}
                                    </span>
                                </div>
                            </div>

                            <!-- Meta Info -->
                            <div class="space-y-3 mb-4">
                                <!-- Producer -->
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-xs font-bold">
                                            {{ substr($product->person->first_name, 0, 1) }}{{ substr($product->person->last_name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-500">Producido por</span>
                                        <h6 class="text-sm font-medium text-gray-900">
                                            {{ $product->person->first_name }} {{ $product->person->last_name }}
                                        </h6>
                                    </div>
                                </div>

                                <!-- Price -->
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-xs text-gray-500">Precio</span>
                                        <div class="flex items-center gap-2">
                                            <h5 class="text-lg font-bold text-green-600">{{ $product->formatted_price }}</h5>
                                            @if($product->current_rate)
                                                @if($product->currency_type === 'USD')
                                                    <span class="text-sm text-gray-500">≈ {{ $product->formatted_bs_price }}</span>
                                                @else
                                                    <span class="text-sm text-gray-500">≈ {{ $product->formatted_usd_price }}</span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <a 
                                    href="{{ route('productores.show', ['producer' => $product->person_id]) }}"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg text-center transition-colors"
                                >
                                    Ver Productor
                                </a>
                                <button 
                                    wire:click="showProductDetail({{ $product->id }})"
                                    class="px-4 py-2 border border-gray-300 hover:border-gray-400 text-gray-700 hover:text-gray-900 rounded-lg transition-colors"
                                >
                                    Ver Detalles
                                </button>
                            </div>
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

            <!-- Load More Button -->
            @if($products->hasPages())
                <div class="text-center mt-8">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-8 rounded-lg transition-colors">
                        Cargar Más Productos
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Product Detail Modal -->
    <livewire:components.product-detail-modal />

    <!-- Mobile Tasas BCV -->
    <div class="fixed bottom-0 left-0 right-0 z-20 lg:hidden">
        <div class="bg-white shadow-lg border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 py-2">
                <div class="flex items-center justify-between">
                    <div class="text-sm font-medium text-gray-800">Tasas BCV:</div>
                    <div class="flex items-center gap-6">
                        @if($exchangeRates['usd']['rate'])
                            <div class="flex items-center gap-2">
                                <span class="font-medium">USD:</span>
                                <span class="text-green-600">{{ $exchangeRates['usd']['rate'] }}</span>
                            </div>
                        @endif
                        @if($exchangeRates['eur']['rate'])
                            <div class="flex items-center gap-2">
                                <span class="font-medium">EUR:</span>
                                <span class="text-green-600">{{ $exchangeRates['eur']['rate'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>