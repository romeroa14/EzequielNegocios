<div>
    <!-- Header y botón de nuevo producto -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Mis Productos</h2>
        <button wire:click="openModal" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded shadow">
            + Nuevo Producto
        </button>
    </div>

    <!-- Listado de productos -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($products as $product)
            <div class="bg-white rounded-lg shadow p-4 flex flex-col">
                <img src="{{ $product->image_url ?? asset('images/placeholder.png') }}" alt="{{ $product->name }}" class="w-full h-40 object-cover rounded mb-2">
                <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
                <p class="text-sm text-gray-500 mb-1">{{ $product->category->name ?? '-' }} > {{ $product->subcategory->name ?? '-' }}</p>
                <p class="text-gray-700 text-sm flex-1">{{ $product->description }}</p>
                <div class="flex justify-between mt-4">
                    <button wire:click="openModal({{ $product->id }})" class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold py-1 px-3 rounded">Editar</button>
                    <!-- Botón de eliminar (lógica a implementar) -->
                    <button class="bg-red-500 hover:bg-red-600 text-white text-xs font-bold py-1 px-3 rounded" disabled>Eliminar</button>
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
    @if($showModal)
        <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
                <button wire:click="closeModal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <h2 class="text-xl font-bold mb-4">{{ $editingProduct ? 'Editar Producto' : 'Nuevo Producto' }}</h2>
                <form wire:submit.prevent="saveProduct">
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Categoría</label>
                        <select wire:model="form.category_id" wire:change="categoryChanged($event.target.value)" class="w-full border rounded px-3 py-2">
                            <option value="">Selecciona una categoría</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Subcategoría</label>
                        <select wire:model="form.subcategory_id" class="w-full border rounded px-3 py-2">
                            <option value="">Selecciona una subcategoría</option>
                            @forelse($subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                            @empty
                                <option value="">No hay subcategorías disponibles</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Nombre</label>
                        <input type="text" wire:model="form.name" class="w-full border rounded px-3 py-2" />
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Descripción</label>
                        <textarea wire:model="form.description" class="w-full border rounded px-3 py-2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">SKU Base</label>
                        <input type="text" wire:model="form.sku_base" class="w-full border rounded px-3 py-2" />
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Tipo de Unidad</label>
                        <select wire:model="form.unit_type" class="w-full border rounded px-3 py-2">
                            <option value="">Selecciona un tipo</option>
                            <option value="kg">Kilogramo</option>
                            <option value="ton">Tonelada</option>
                            <option value="saco">Saco</option>
                            <option value="caja">Caja</option>
                            <option value="unidad">Unidad</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Imagen</label>
                        <input type="file" wire:model="form.image" class="w-full border rounded px-3 py-2" />
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Información estacional</label>
                        <input type="text" wire:model="form.seasonal_info" class="w-full border rounded px-3 py-2" placeholder="Ej: Primavera, Verano..." />
                    </div>
                    <div class="mb-3 flex items-center">
                        <input type="checkbox" wire:model="form.is_active" class="mr-2" />
                        <span class="text-sm">Activo</span>
                    </div>
                    <div class="flex justify-end mt-4">
                        <button type="button" @click="closeModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">Cancelar</button>
                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
