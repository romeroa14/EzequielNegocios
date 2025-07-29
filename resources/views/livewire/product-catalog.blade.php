<div>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-900">Catálogo de Productos</h3>
                <!-- Mobile Filter Button -->
                <button 
                    class="lg:hidden bg-white p-2 rounded-lg shadow-sm border border-gray-200 text-gray-600 hover:bg-gray-50"
                    @click="$dispatch('set-show-filters', true)"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Filters Sidebar - Desktop -->
                <div class="hidden lg:block">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-4">
                        <h3 class="text-xl font-medium text-gray-900 mb-4">Filtros</h3>
                        @include('livewire.partials.filter-content')
                    </div>
                </div>

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
                                            <span class="text-lg font-bold text-green-600">${{ number_format($product->unit_price, 2) }}</span>
                                            <span class="text-sm text-gray-500">/ unidad</span>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $product->quantity_available }} disponibles
                                        </div>
                                    </div>

                                    <!-- Location and Producer -->
                                    <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                        <span>📍 {{ $product->short_location }}</span>
                                        <span>👤 {{ $product->person->first_name . ' ' . $product->person->last_name ?? 'Productor' }}</span>
                                    </div>

                                    <!-- Action Button -->
                                    <button 
                                        wire:click="$dispatch('contactProducer', { productId: {{ $product->id }} })"
                                        class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out"
                                    >
                                        Contactar Productor
                                    </button>
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

    <!-- Mobile Filters Drawer -->
    <div 
        x-data="{ show: false }"
        x-show="show"
        x-on:set-show-filters.window="show = $event.detail"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 z-50 lg:hidden"
        x-cloak
    >
        <div 
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed inset-y-0 right-0 max-w-xs w-full bg-white shadow-xl p-6 overflow-y-auto"
            @click.away="show = false"
        >
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-medium text-gray-900">Filtros</h3>
                <button 
                    class="text-gray-400 hover:text-gray-500"
                    @click="show = false"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            @include('livewire.partials.filter-content')
        </div>
    </div>

    <!-- Product Detail Modal -->
    <livewire:components.product-detail-modal />
</div>