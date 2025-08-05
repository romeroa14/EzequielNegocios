<div>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-900">Catálogo de Productos</h3>
                
                <!-- Desktop Tasas BCV -->
                <div class="hidden lg:block bg-white rounded-lg shadow-sm border border-gray-200 p-3">
                    <div class="text-sm font-medium text-gray-600 mb-2">Tasas BCV</div>
                    <div class="space-y-1">
                        @if($exchangeRates['usd']['rate'])
                            <div class="flex items-center gap-2">
                                <span class="font-semibold">USD:</span>
                                <span class="text-green-600">Bs.D {{ $exchangeRates['usd']['rate'] }}</span>
                            </div>
                        @endif
                        @if($exchangeRates['eur']['rate'])
                            <div class="flex items-center gap-2">
                                <span class="font-semibold">EUR:</span>
                                <span class="text-green-600">Bs.D {{ $exchangeRates['eur']['rate'] }}</span>
                            </div>
                        @endif
                        <div class="text-xs text-gray-500">
                            Actualizado: {{ $exchangeRates['usd']['fetched_at'] ?? 'No disponible' }}
                        </div>
                    </div>
                </div>

                <!-- Mobile Filter Toggle Button -->
                <button 
                    wire:click="$toggle('showFilters')"
                    class="lg:hidden bg-white p-2 rounded-lg shadow-sm border border-gray-200 text-gray-600 hover:bg-gray-50"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </button>
            </div>

            <div class="lg:grid lg:grid-cols-4 lg:gap-6">
                <!-- Filters Panel - Always visible on desktop, toggleable on mobile -->
                <div 
                    class="transform lg:transform-none lg:opacity-100 lg:relative lg:block
                           fixed inset-y-0 left-0 z-40 w-full max-w-xs lg:max-w-none bg-white lg:bg-transparent
                           transition duration-300 ease-in-out
                           {{ $showFilters ? 'translate-x-0' : '-translate-x-full lg:translate-x-0' }}"
                >
                    <div class="h-full lg:h-auto overflow-y-auto lg:overflow-visible p-6 lg:p-0 lg:sticky lg:top-4">
                        <!-- Mobile Header -->
                        <div class="flex items-center justify-between mb-6 lg:hidden">
                            <h3 class="text-xl font-medium text-gray-900">Filtros</h3>
                            <button 
                                wire:click="$set('showFilters', false)"
                                class="text-gray-400 hover:text-gray-500"
                            >
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Filter Content -->
                        <div class="bg-white lg:rounded-lg lg:shadow-sm lg:border lg:border-gray-200 lg:p-6">
                            <h3 class="hidden lg:block text-xl font-medium text-gray-900 mb-4">Filtros</h3>
                            @include('livewire.partials.filter-content', [
                                'categories' => $categories,
                                'subcategories' => $subcategories,
                                'productLines' => $productLines,
                                'brands' => $brands,
                                'presentations' => $presentations
                            ])
                        </div>
                    </div>
                </div>

                <!-- Backdrop - Mobile only -->
                @if($showFilters)
                    <div 
                        class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden"
                        wire:click="$set('showFilters', false)"
                    ></div>
                @endif

                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <!-- Products Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        @forelse($products as $product)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                                <!-- Product Image -->
                                <div 
                                    wire:click="showProductDetail({{ $product->id }})"
                                    class="aspect-w-4 aspect-h-3 bg-gray-200 relative group cursor-pointer"
                                >
                                    <img 
                                        src="{{ $this->getFirstImageUrl($product) }}"
                                        alt="{{ $product->title }}"
                                        class="w-full h-48 object-cover"
                                        onerror="this.src='{{ asset('images/placeholder.png') }}'"
                                    />
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-opacity duration-200"></div>
                                </div>

                                <!-- Product Info -->
                                <div class="p-4">
                                    <h3 
                                        wire:click="showProductDetail({{ $product->id }})"
                                        class="text-lg font-semibold text-gray-900 line-clamp-2 cursor-pointer hover:text-blue-600"
                                    >
                                        {{ $product->title }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-2">{{ $product->product->name }}</p>
                                    <p class="text-sm text-gray-500 mb-3">{{ Str::limit($product->description, 80) }}</p>

                                    <!-- Price and Quantity -->
                                    <div class="flex items-center justify-between mb-3">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <!-- Precio en moneda original -->
                                                <span class="text-lg font-bold text-green-600">{{ $product->formatted_price }}</span>
                                                
                                                <!-- Precio convertido -->
                                                @if($product->current_rate)
                                                    @if($product->currency_type === 'USD')
                                                        <span class="text-sm text-gray-500">≈ {{ $product->formatted_bs_price }}</span>
                                                    @else
                                                        <span class="text-sm text-gray-500">≈ {{ $product->formatted_usd_price }}</span>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                                <span>por {{ $product->productPresentation->name ?? 'unidad' }}</span>
                                                <span class="text-gray-300">·</span>
                                                <span>{{ $product->presentation_quantity }} {{ $product->productPresentation->unit_type ?? 'unidades' }}</span>
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            disponibles
                                        </div>
                                    </div>

                                    <!-- Location and Producer -->
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span>📍 {{ $product->short_location }}</span>
                                        <span>👤 {{ $product->person->first_name . ' ' . $product->person->last_name }}</span>
                                    </div>

                                    <!-- Action Button -->
                                    <a 
                                        href="{{ route('productores.show', ['producer' => $product->person_id]) }}"
                                        class="block w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md text-center transition duration-150 ease-in-out"
                                    >
                                        Ver Productor
                                    </a>
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