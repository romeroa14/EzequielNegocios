<div>
    <!-- Mobile Filters Drawer -->
    <div 
        x-data="{ show: @entangle('showFilters') }"
        x-show="show"
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
            class="fixed inset-y-0 right-0 max-w-xs w-full bg-white shadow-xl overflow-y-auto"
            @click.away="$wire.hideFilters()"
        >
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-medium text-gray-900">Filtros</h3>
                    <button 
                        class="text-gray-400 hover:text-gray-500"
                        wire:click="hideFilters"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Filter Content -->
                <div class="space-y-4">
                    <!-- Búsqueda -->
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">Buscar productos</label>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search"
                            placeholder="Escribe aquí..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-gray-900"
                        />
                    </div>

                    <!-- Categoría -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                        <select wire:model.live="selectedCategory" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-gray-900 bg-white">
                            <option value="">Todas las Categorías</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Subcategoría -->
                    @if($subcategories->isNotEmpty())
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subcategoría</label>
                        <select wire:model.live="selectedSubcategory" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-gray-900 bg-white">
                            <option value="">Todas las Subcategorías</option>
                            @foreach($subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Línea de Producto -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Línea de Producto</label>
                        <select wire:model.live="selectedLine" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-gray-900 bg-white">
                            <option value="">Todas las Líneas</option>
                            @foreach($productLines as $line)
                                <option value="{{ $line->id }}">{{ $line->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Marca -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                        <select wire:model.live="selectedBrand" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-gray-900 bg-white">
                            <option value="">Todas las Marcas</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Presentación -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Presentación</label>
                        <select wire:model.live="selectedPresentation" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-gray-900 bg-white">
                            <option value="">Todas las Presentaciones</option>
                            @foreach($presentations as $presentation)
                                <option value="{{ $presentation->id }}">{{ $presentation->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Rango de Precios -->
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio Mín.</label>
                            <input 
                                type="number" 
                                wire:model.live="minPrice"
                                placeholder="0"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-gray-900"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio Máx.</label>
                            <input 
                                type="number" 
                                wire:model.live="maxPrice"
                                placeholder="999999"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-gray-900"
                            />
                        </div>
                    </div>

                    <!-- Tasas BCV -->
                    <div class="pt-4 border-t border-gray-200 mb-4">
                        <h6 class="text-sm font-semibold text-gray-900 mb-3">Tasas BCV</h6>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center p-2 bg-green-50 rounded-lg">
                                <span class="text-sm text-gray-700">USD</span>
                                <span class="text-sm font-medium text-green-700">
                                    {{ number_format($this->bcvRates['usd'], 2, ',', '.') }} Bs
                                </span>
                            </div>
                            <div class="flex justify-between items-center p-2 bg-blue-50 rounded-lg">
                                <span class="text-sm text-gray-700">EUR</span>
                                <span class="text-sm font-medium text-blue-700">
                                    {{ number_format($this->bcvRates['eur'], 2, ',', '.') }} Bs
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Apply Filters Button -->
                <div class="mt-6 space-y-2">
                    <button 
                        wire:click="applyFilters"
                        class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors duration-200"
                    >
                        Aplicar Filtros
                    </button>
                    <button 
                        wire:click="clearFilters"
                        class="w-full bg-gray-100 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors duration-200"
                    >
                        Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>
    </div>
</div> 