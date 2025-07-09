<div class="bg-white rounded-xl shadow-lg p-8 mb-8">
    <!-- Header y botón de nuevo producto -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Mis Productos</h2>
        <button wire:click="openModal"
            class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded shadow">
            + Nuevo Producto
        </button>
    </div>

    <!-- Listado de productos -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($products as $product)
            <div class="bg-white rounded-lg shadow p-4 flex flex-col">
                <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
                <p class="text-sm text-gray-500 mb-1">{{ $product->category->name ?? '-' }} >
                    {{ $product->subcategory->name ?? '-' }}</p>
                <p class="text-gray-700 text-sm flex-1">{{ $product->description }}</p>
                @if ($product->image)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-80 rounded mb-2">
                @else
                    <img src="{{ asset('images/placeholder.png') }}" alt="{{ $product->name }}"
                        class="w-full h-80 rounded mb-2">
                @endif
                <div class="flex justify-between mt-4">
                    <button wire:click="openModal({{ $product->id }})"
                        class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold py-1 px-3 rounded">Editar</button>
                    <button wire:click="confirmDelete({{ $product->id }})"
                        class="bg-red-500 hover:bg-red-600 text-white text-xs font-bold py-1 px-3 rounded">Eliminar</button>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center text-gray-500 py-12">
                No tienes productos publicados aún.
            </div>
        @endforelse
    </div>

    {{-- <div x-data="{ show: @entangle('showModal') }">
        <button @click="show = true">Abrir modal</button>
        <div x-show="show" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow">
                <h2>Modal</h2>
                <button @click="show = false">Cerrar</button>
            </div>
        </div>
    </div> --}}

    <!-- Modal para crear/editar producto -->
    @if ($showModal)
        <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
                <button wire:click="closeModal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <h2 class="text-xl font-bold mb-4">{{ $editingProduct ? 'Editar Producto' : 'Nuevo Producto' }}</h2>
                <form wire:submit.prevent="saveProduct">
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Categoría</label>
                        <select 
                            wire:model="form.product_category_id" 
                            wire:change="categoryChanged($event.target.value)"
                            class="w-full border rounded px-3 py-2 @error('form.product_category_id') border-red-500 @enderror"
                        >
                            <option value="">Selecciona una categoría</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('form.product_category_id')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Subcategoría</label>
                        <select 
                            wire:model="form.product_subcategory_id" 
                            class="w-full border rounded px-3 py-2 @error('form.product_subcategory_id') border-red-500 @enderror"
                        >
                            <option value="">Selecciona una subcategoría</option>
                            @foreach ($subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                            @endforeach
                        </select>
                        @error('form.product_subcategory_id')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Línea de producto</label>
                        <select 
                            wire:model="form.product_line_id" 
                            class="w-full border rounded px-3 py-2 @error('form.product_line_id') border-red-500 @enderror"
                        >
                            <option value="">Selecciona una línea de producto</option>
                            @foreach ($lines as $line)
                                <option value="{{ $line->id }}">{{ $line->name }}</option>
                            @endforeach
                        </select>
                        @error('form.product_line_id')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Marca</label>
                        <select 
                            wire:model="form.brand_id" 
                            class="w-full border rounded px-3 py-2 @error('form.brand_id') border-red-500 @enderror"
                        >
                            <option value="">Selecciona una marca</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                        @error('form.brand_id')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Nombre</label>
                        <input 
                            type="text" 
                            wire:model="form.name" 
                            class="w-full border rounded px-3 py-2 @error('form.name') border-red-500 @enderror" 
                        />
                        @error('form.name')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Descripción</label>
                        <textarea 
                            wire:model="form.description" 
                            class="w-full border rounded px-3 py-2 @error('form.description') border-red-500 @enderror"
                        ></textarea>
                        @error('form.description')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">SKU Base</label>
                        <input 
                            type="text" 
                            wire:model="form.sku_base" 
                            class="w-full border rounded px-3 py-2 @error('form.sku_base') border-red-500 @enderror" 
                        />
                        @error('form.sku_base')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Tipo de Unidad</label>
                        <select 
                            wire:model="form.unit_type" 
                            class="w-full border rounded px-3 py-2 @error('form.unit_type') border-red-500 @enderror"
                        >
                            <option value="">Selecciona un tipo</option>
                            <option value="kg">Kilogramo</option>
                            <option value="ton">Tonelada</option>
                            <option value="saco">Saco</option>
                            <option value="caja">Caja</option>
                            <option value="unidad">Unidad</option>
                        </select>
                        @error('form.unit_type')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        @if ($editingProduct && $editingProduct->image && !$changeImage)
                            <div class="mt-2">
                                <img src="{{ $editingProduct->image_url }}" alt="Imagen actual" class="h-24 rounded shadow">
                                <p class="text-xs text-gray-500">Imagen actual</p>
                                <button 
                                    type="button" 
                                    wire:click="enableImageChange"
                                    class="mt-2 bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold py-1 px-3 rounded"
                                >
                                    Cambiar imagen
                                </button>
                            </div>
                        @else
                            <label class="block text-sm font-medium mb-1">Imagen</label>
                            <input 
                                type="file" 
                                wire:model="form.image" 
                                class="w-full border rounded px-3 py-2 @error('form.image') border-red-500 @enderror" 
                            />
                            @error('form.image')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Información estacional</label>
                        <input 
                            type="text" 
                            wire:model="form.seasonal_info" 
                            class="w-full border rounded px-3 py-2 @error('form.seasonal_info') border-red-500 @enderror"
                            placeholder="Ej: Primavera, Verano..." 
                        />
                        @error('form.seasonal_info')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3 flex items-center">
                        <input 
                            type="checkbox" 
                            wire:model="form.is_active" 
                            class="mr-2 @error('form.is_active') border-red-500 @enderror" 
                        />
                        <span class="text-sm">Activo</span>
                        @error('form.is_active')
                            <span class="text-red-500 text-xs mt-1 ml-2">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-end mt-4">
                        <button 
                            type="button" 
                            @click="closeModal"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded"
                        >
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.addEventListener('product-added', event => {
            Swal.fire({
                icon: 'success',
                title: '¡Producto agregado!',
                text: 'El producto se ha creado correctamente.',
                confirmButtonColor: '#f59e42'
            });
        });

        window.addEventListener('product-updated', event => {
            Swal.fire({
                icon: 'success',
                title: '¡Producto actualizado!',
                text: 'El producto se ha actualizado correctamente.',
                confirmButtonColor: '#f59e42'
            });
        });

        window.addEventListener('product-deleted', event => {
            Swal.fire({
                icon: 'success',
                title: '¡Producto eliminado!',
                text: 'El producto se ha eliminado correctamente.',
                confirmButtonColor: '#f59e42'
            });
        });

        window.addEventListener('show-delete-confirmation', event => {
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¡No podrás revertir esto!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('deleteProduct', [@this.productIdToDelete]);
                }
            });
        });
    });
</script>
