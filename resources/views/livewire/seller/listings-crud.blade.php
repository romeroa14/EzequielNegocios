<div class="bg-white rounded-xl shadow-lg p-8 mb-8">
    <!-- Header y botón de nueva publicación -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Mis Publicaciones</h2>
        <button wire:click="openModal" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded shadow">
            + Nueva Publicación
        </button>
    </div>

    <!-- Listado de publicaciones -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($listings as $listing)
            <div class="bg-white rounded-lg shadow p-4 flex flex-col min-h-[350px] min-w-[250px] justify-between">
                <div>
                    <h3 class="text-lg font-semibold mb-1">{{ $listing->title }}</h3>
                    <p class="text-base text-gray-500 mb-1">Producto: {{ $listing->product->name ?? '-' }}</p>
                    <p class="text-base text-gray-700 mb-1">Precio: ${{ number_format($listing->unit_price, 2) }}</p>
                    <p class="text-base text-gray-700 mb-1">Cantidad: {{ $listing->quantity_available }}</p>
                    <p class="text-base text-gray-700 mb-1">Calidad: {{ ucfirst($listing->quality_grade) }}</p>
                    <p class="text-base text-gray-700 mb-1">Cosecha: {{ $listing->harvest_date ? $listing->harvest_date->format('Y-m-d') : '-' }}</p>
                    <p class="text-base text-gray-700 mb-1">Ciudad: {{ $listing->location_city }}</p>
                    <p class="text-base text-gray-700 mb-1">Estado: {{ $listing->location_state }}</p>
                    <p class="text-base text-gray-700 mb-1">Estatus: {{ ucfirst($listing->status) }}</p>
                </div>
                @if($listing->images && count($listing->images))
                    <img src="{{ $listing->images_url[0] }}" alt="Imagen" class="w-full h-80 rounded shadow my-2">
                @endif
                <div class="flex justify-between mt-4">
                    <button wire:click="openModal({{ $listing->id }})" class="bg-yellow-500 hover:bg-yellow-600 text-white text-base font-bold py-1 px-3 rounded">Editar</button>
                    <button wire:click="deleteListing({{ $listing->id }})" class="bg-red-500 hover:bg-red-600 text-white text-base font-bold py-1 px-3 rounded">Eliminar</button>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center text-gray-500 py-12">
                No tienes publicaciones aún.
            </div>
        @endforelse
    </div>

    <!-- Modal para crear/editar publicación -->
    @if($showModal)
        <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
                <button wire:click="closeModal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <h2 class="text-xl font-bold mb-4">{{ $editingListing ? 'Editar Publicación' : 'Nueva Publicación' }}</h2>
                @if(session('error'))
                    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif
                <form wire:submit.prevent="saveListing">
                    <div class="mb-3" x-data="{ img: @entangle('form.product_id') }">
                        <label class="block text-sm font-medium mb-1">Producto</label>
                        <select x-model="img" wire:model="form.product_id" class="w-full border rounded px-3 py-2">
                            <option value="">Selecciona un producto</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        @error('form.product_id')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                        <template x-if="img">
                            <img :src="img && {{ Js::from($products->pluck('image_url', 'id')) }}[img] ? {{ Js::from($products->pluck('image_url', 'id')) }}[img] : ''" class="h-24 rounded shadow mt-2" x-show="img && {{ Js::from($products->pluck('image_url', 'id')) }}[img]">
                        </template>
                        <template x-if="img && !{{ Js::from($products->pluck('image_url', 'id')) }}[img]">
                            <p class="text-xs text-gray-500 mt-2">Este producto no tiene imagen</p>
                        </template>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Título</label>
                        <input type="text" wire:model="form.title" class="w-full border rounded px-3 py-2" />
                        @error('form.title')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Descripción</label>
                        <textarea wire:model="form.description" class="w-full border rounded px-3 py-2"></textarea>
                        @error('form.description')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Precio Unitario</label>
                        <input type="number" step="0.01" wire:model="form.unit_price" class="w-full border rounded px-3 py-2" />
                        @error('form.unit_price')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Cantidad Disponible</label>
                        <input type="number" wire:model="form.quantity_available" class="w-full border rounded px-3 py-2" />
                        @error('form.quantity_available')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Calidad</label>
                        <select wire:model="form.quality_grade" class="w-full border rounded px-3 py-2">
                            <option value="">Selecciona una calidad</option>
                            <option value="premium">Premium</option>
                            <option value="standard">Estándar</option>
                            <option value="economic">Económico</option>
                        </select>
                        @error('form.quality_grade')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Fecha de Cosecha</label>
                        <input type="date" wire:model="form.harvest_date" class="w-full border rounded px-3 py-2" />
                        @error('form.harvest_date')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Ciudad</label>
                        <input type="text" wire:model="form.location_city" class="w-full border rounded px-3 py-2" />
                        @error('form.location_city')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Estado</label>
                        <input type="text" wire:model="form.location_state" class="w-full border rounded px-3 py-2" />
                        @error('form.location_state')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Estatus</label>
                        <select wire:model="form.status" class="w-full border rounded px-3 py-2">
                            <option value="pending">Pendiente</option>
                            <option value="active">Activo</option>
                            <option value="sold_out">Agotado</option>
                            <option value="inactive">Inactivo</option>
                        </select>
                        @error('form.status')
                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex justify-end mt-4">
                        <button type="button" wire:click="closeModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">Cancelar</button>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

