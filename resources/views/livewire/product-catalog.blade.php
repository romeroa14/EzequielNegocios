<div>
    <script>
        document.addEventListener('livewire:init', () => {
            console.log('🔥 Livewire initialized!');
            
            Livewire.on('test-message', (data) => {
                console.log('📨 Livewire event received:', data);
                alert('Livewire event: ' + data.message);
            });
        });
    </script>
    
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header con filtro dropdown -->
            <div class="mb-8">
                <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 gap-4">
                    <div>
                        <h2 class="text-2xl lg:text-3xl font-bold text-gray-900">Catálogo de Productos</h2>
                    </div>
                    
                    <!-- Tasas BCV, Filtros y Ordenamiento -->
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 items-stretch sm:items-center">
                        <!-- Tasas BCV en el header (visible only on large screens and up) -->
                        <div class="hidden lg:flex gap-4 items-center">
                            <div class="flex items-center gap-2 px-3 py-1 bg-green-50 rounded-lg">
                                <span class="text-xs text-gray-600">USD:</span>
                                <span class="text-sm font-medium text-green-700">
                                    {{ number_format($this->bcvRates['usd'], 2, ',', '.') }} Bs
                                </span>
                            </div>
                            <div class="flex items-center gap-2 px-3 py-1 bg-blue-50 rounded-lg">
                                <span class="text-xs text-gray-600">EUR:</span>
                                <span class="text-sm font-medium text-blue-700">
                                    {{ number_format($this->bcvRates['eur'], 2, ',', '.') }} Bs
                                </span>
                            </div>
                        </div>
                        <!-- Sort Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button 
                                @click="open = !open"
                                class="flex items-center justify-center gap-3 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors border border-green-500 w-full sm:w-auto"
                            >
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 7H21" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M6 12H18" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M10 17H14" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                                <span>Ordenar: {{ $sortBy === 'created_at' ? 'Recientes' : ($sortBy === 'title' ? 'Nombre A-Z' : ($sortBy === 'title_desc' ? 'Nombre Z-A' : ($sortBy === 'unit_price' ? 'Precio: Menor a Mayor' : 'Precio: Mayor a Menor'))) }}</span>
                            </button>

                            <!-- Sort Dropdown Menu -->
                            <div 
                                x-show="open" 
                                @click.away="open = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-64 sm:w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                                style="display: none;"
                            >
                                <div class="p-4">
                                    <h6 class="text-sm font-semibold text-gray-900 mb-3">Ordenar por</h6>
                                    <div class="space-y-2">
                                        <label class="flex items-center justify-between p-2 hover:bg-gray-50 rounded cursor-pointer">
                                            <span class="text-sm text-gray-900">Recientes</span>
                                            <input type="radio" wire:model.live="sortBy" value="created_at" name="sortBy" class="text-green-600 focus:ring-green-500">
                                        </label>
                                        <label class="flex items-center justify-between p-2 hover:bg-gray-50 rounded cursor-pointer">
                                            <span class="text-sm text-gray-900">Nombre A-Z</span>
                                            <input type="radio" wire:model.live="sortBy" value="title" name="sortBy" class="text-green-600 focus:ring-green-500">
                                        </label>
                                        <label class="flex items-center justify-between p-2 hover:bg-gray-50 rounded cursor-pointer">
                                            <span class="text-sm text-gray-900">Nombre Z-A</span>
                                            <input type="radio" wire:model.live="sortBy" value="title_desc" name="sortBy" class="text-green-600 focus:ring-green-500">
                                        </label>
                                        <label class="flex items-center justify-between p-2 hover:bg-gray-50 rounded cursor-pointer">
                                            <span class="text-sm text-gray-900">Precio: Menor a Mayor</span>
                                            <input type="radio" wire:model.live="sortBy" value="unit_price" name="sortBy" class="text-green-600 focus:ring-green-500">
                                        </label>
                                        <label class="flex items-center justify-between p-2 hover:bg-gray-50 rounded cursor-pointer">
                                            <span class="text-sm text-gray-900">Precio: Mayor a Menor</span>
                                            <input type="radio" wire:model.live="sortBy" value="unit_price_desc" name="sortBy" class="text-green-600 focus:ring-green-500">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filtro Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button 
                                @click="open = !open"
                                class="flex items-center justify-center gap-3 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors border border-green-500 w-full sm:w-auto"
                            >
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 7H21" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M6 12H18" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M10 17H14" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                                <span>Filtros</span>
                            </button>

                        <!-- Dropdown Menu -->
                        <div 
                            x-show="open" 
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                            style="display: none;"
                        >
                            <div class="p-6">
                              
                                
                                <!-- Búsqueda -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-900 mb-2">Buscar productos</label>
                                    <input 
                                        type="text" 
                                        wire:model.live.debounce.300ms="search"
                                        placeholder="Escribe aquí..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                                    />
                                </div>


                                <!-- Filtros -->
                                <div class="mb-4">
                                    <h6 class="text-sm font-semibold text-gray-900 mb-3">Filtros</h6>
                                    <div class="space-y-3">
                                        <!-- Categoría -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Categoría</label>
                                            <select wire:model.live="selectedCategory" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 bg-white">
                                                <option value="">Todas las Categorías</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                            @if($selectedCategory)
                                                <p class="text-xs text-green-600 mt-1">Filtro activo: {{ $categories->firstWhere('id', $selectedCategory)?->name }}</p>
                                            @endif
                                        </div>

                                        <!-- Subcategoría -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Subcategoría</label>
                                            <select wire:model.live="selectedSubcategory" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 bg-white">
                                                <option value="">Todas las Subcategorías</option>
                                                @foreach($subcategories as $subcategory)
                                                    <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Línea de Producto -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Línea de Producto</label>
                                            <select wire:model.live="selectedLine" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 bg-white">
                                                <option value="">Todas las Líneas</option>
                                                @foreach($productLines as $line)
                                                    <option value="{{ $line->id }}">{{ $line->name }}</option>
                                                @endforeach
                                            </select>
                            </div>

                                        <!-- Marca -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Marca</label>
                                            <select wire:model.live="selectedBrand" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 bg-white">
                                                <option value="">Todas las Marcas</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                @endforeach
                                            </select>
                                            @if($selectedBrand)
                                                <p class="text-xs text-green-600 mt-1">Filtro activo: {{ $brands->firstWhere('id', $selectedBrand)?->name }}</p>
                                            @endif
                                        </div>

                                        <!-- Presentación -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Presentación</label>
                                            <select wire:model.live="selectedPresentation" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 bg-white">
                                                <option value="">Todas las Presentaciones</option>
                                                @foreach($presentations as $presentation)
                                                    <option value="{{ $presentation->id }}">{{ $presentation->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Rango de Precios -->
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Precio Mín.</label>
                                                <input 
                                                    type="number" 
                                                    wire:model.live="minPrice"
                                                    placeholder="0"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                                                />
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Precio Máx.</label>
                                                <input 
                                                    type="number" 
                                                    wire:model.live="maxPrice"
                                                    placeholder="999999"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                                                />
                                            </div>
                        </div>
                    </div>

                    
                </div>

                               
                               

                                <div class="pt-4 border-t border-gray-200">
                                    <button 
                                        wire:click="clearFilters"
                                        class="w-full px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors text-sm font-medium"
                                    >
                                        Limpiar Filtros
                                    </button>
                                </div>
                            </div>
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
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background: linear-gradient(135deg, #10b981, #059669);">
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
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg text-center transition-colors"
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