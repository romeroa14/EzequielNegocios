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
                @include('livewire.partials.filter-content', [
                    'categories' => $categories,
                    'subcategories' => $subcategories,
                    'productLines' => $productLines,
                    'brands' => $brands,
                    'presentations' => $presentations
                ])

                <!-- Apply Filters Button -->
                <div class="mt-6 space-y-2">
                    <button 
                        wire:click="applyFilters"
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200"
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