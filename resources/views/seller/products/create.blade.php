<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nuevo Producto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Información Básica -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">Información Básica</h3>
                                
                                <div>
                                    <x-input-label for="name" :value="__('Nombre del Producto')" />
                                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" 
                                                :value="old('name')" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="description" :value="__('Descripción')" />
                                    <textarea id="description" name="description" rows="4"
                                              class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                              required>{{ old('description') }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="category_id" :value="__('Categoría')" />
                                    <select id="category_id" name="category_id" 
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            required>
                                        <option value="">Selecciona una categoría</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="subcategory_id" :value="__('Subcategoría')" />
                                    <select id="subcategory_id" name="subcategory_id" 
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            required disabled>
                                        <option value="">Primero selecciona una categoría</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('subcategory_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="unit_type" :value="__('Unidad de Medida')" />
                                    <select id="unit_type" name="unit_type" 
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            required>
                                        <option value="">Selecciona una unidad</option>
                                        <option value="kg" {{ old('unit_type') == 'kg' ? 'selected' : '' }}>Kilogramos (kg)</option>
                                        <option value="g" {{ old('unit_type') == 'g' ? 'selected' : '' }}>Gramos (g)</option>
                                        <option value="l" {{ old('unit_type') == 'l' ? 'selected' : '' }}>Litros (l)</option>
                                        <option value="ml" {{ old('unit_type') == 'ml' ? 'selected' : '' }}>Mililitros (ml)</option>
                                        <option value="unit" {{ old('unit_type') == 'unit' ? 'selected' : '' }}>Unidad</option>
                                        <option value="dozen" {{ old('unit_type') == 'dozen' ? 'selected' : '' }}>Docena</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('unit_type')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Información de Venta -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">Información de Venta</h3>

                                <div>
                                    <x-input-label for="price" :value="__('Precio por Unidad')" />
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <x-text-input type="number" name="price" id="price"
                                                    class="pl-7 block w-full pr-12"
                                                    placeholder="0.00"
                                                    step="0.01"
                                                    min="0"
                                                    :value="old('price')"
                                                    required />
                                    </div>
                                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="available_quantity" :value="__('Cantidad Disponible')" />
                                    <x-text-input type="number" name="available_quantity" id="available_quantity"
                                                class="mt-1 block w-full"
                                                min="0"
                                                step="0.01"
                                                :value="old('available_quantity')"
                                                required />
                                    <x-input-error :messages="$errors->get('available_quantity')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="minimum_order_quantity" :value="__('Cantidad Mínima por Orden')" />
                                    <x-text-input type="number" name="minimum_order_quantity" id="minimum_order_quantity"
                                                class="mt-1 block w-full"
                                                min="1"
                                                step="0.01"
                                                :value="old('minimum_order_quantity')"
                                                required />
                                    <x-input-error :messages="$errors->get('minimum_order_quantity')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="maximum_order_quantity" :value="__('Cantidad Máxima por Orden')" />
                                    <x-text-input type="number" name="maximum_order_quantity" id="maximum_order_quantity"
                                                class="mt-1 block w-full"
                                                min="1"
                                                step="0.01"
                                                :value="old('maximum_order_quantity')"
                                                required />
                                    <x-input-error :messages="$errors->get('maximum_order_quantity')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="delivery_time" :value="__('Tiempo de Entrega')" />
                                    <x-text-input type="text" name="delivery_time" id="delivery_time"
                                                class="mt-1 block w-full"
                                                placeholder="Ej: 2-3 días hábiles"
                                                :value="old('delivery_time')"
                                                required />
                                    <x-input-error :messages="$errors->get('delivery_time')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="image" :value="__('Imagen del Producto')" />
                                    <input type="file" name="image" id="image"
                                           class="mt-1 block w-full text-sm text-gray-500
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded-md file:border-0
                                                  file:text-sm file:font-semibold
                                                  file:bg-yellow-50 file:text-yellow-700
                                                  hover:file:bg-yellow-100"
                                           accept="image/*" />
                                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-secondary-button type="button" onclick="window.history.back()">
                                {{ __('Cancelar') }}
                            </x-secondary-button>

                            <x-primary-button class="ml-3">
                                {{ __('Crear Producto') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category_id');
            const subcategorySelect = document.getElementById('subcategory_id');

            categorySelect.addEventListener('change', function() {
                const categoryId = this.value;
                subcategorySelect.disabled = true;
                subcategorySelect.innerHTML = '<option value="">Cargando subcategorías...</option>';

                if (categoryId) {
                    fetch(`/seller/categories/${categoryId}/subcategories`)
                        .then(response => response.json())
                        .then(data => {
                            subcategorySelect.innerHTML = '<option value="">Selecciona una subcategoría</option>';
                            data.subcategories.forEach(subcategory => {
                                const option = new Option(subcategory.name, subcategory.id);
                                subcategorySelect.add(option);
                            });
                            subcategorySelect.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            subcategorySelect.innerHTML = '<option value="">Error al cargar subcategorías</option>';
                        });
                } else {
                    subcategorySelect.innerHTML = '<option value="">Primero selecciona una categoría</option>';
                }
            });
        });
    </script>
    @endpush
</x-app-layout> 