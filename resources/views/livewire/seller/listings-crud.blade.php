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
                    <button wire:click="confirmDelete({{ $listing->id }})" class="bg-red-500 hover:bg-red-600 text-white text-base font-bold py-1 px-3 rounded">Eliminar</button>
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
        <div class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
            
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-2">
                    <div class="relative w-full max-w-3xl bg-white shadow-xl rounded-lg">
                        <!-- Header fijo -->
                        <div class="sticky top-0 bg-white px-4 py-3 border-b border-gray-200 flex justify-between items-center z-10">
                            <h2 class="text-lg font-semibold truncate">{{ $editingListing ? 'Editar Publicación' : 'Nueva Publicación' }}</h2>
                            <button wire:click="closeModal" class="text-gray-400 hover:text-gray-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Contenido scrolleable -->
                        <div class="p-4 max-h-[calc(100vh-8rem)] overflow-y-auto">
                            @if(session('error'))
                                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm">
                                    {{ session('error') }}
                                </div>
                            @endif
                            @if(session('success'))
                                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <form wire:submit.prevent="saveListing" class="space-y-4">
                                <!-- Producto y Preview -->
                                <div x-data="{ img: @entangle('form.product_id') }">
                                    <label class="block text-sm font-medium mb-1">Producto</label>
                                    <select 
                                        x-model="img" 
                                        wire:model="form.product_id" 
                                        class="w-full border rounded px-3 py-2 text-sm bg-white"
                                    >
                                        <option value="">Selecciona un producto</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.product_id')
                                        <span class="text-red-600 text-xs">{{ $message }}</span>
                                    @enderror
                                    <template x-if="img">
                                        <img 
                                            :src="img && {{ Js::from($products->pluck('image_url', 'id')) }}[img] ? {{ Js::from($products->pluck('image_url', 'id')) }}[img] : ''" 
                                            class="h-16 sm:h-24 rounded shadow mt-2" 
                                            x-show="img && {{ Js::from($products->pluck('image_url', 'id')) }}[img]"
                                        >
                                    </template>
                                </div>

                                <!-- Grid de campos -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Título</label>
                                        <input 
                                            type="text" 
                                            wire:model="form.title" 
                                            class="w-full border rounded px-3 py-2 text-sm" 
                                        />
                                        @error('form.title')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-1">Precio Unitario</label>
                                        <input 
                                            type="number" 
                                            step="0.01" 
                                            wire:model="form.unit_price" 
                                            class="w-full border rounded px-3 py-2 text-sm" 
                                        />
                                        @error('form.unit_price')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-1">Cantidad Disponible</label>
                                        <input 
                                            type="number" 
                                            wire:model="form.quantity_available" 
                                            class="w-full border rounded px-3 py-2 text-sm" 
                                        />
                                        @error('form.quantity_available')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-1">Calidad</label>
                                        <select 
                                            wire:model="form.quality_grade" 
                                            class="w-full border rounded px-3 py-2 text-sm bg-white"
                                        >
                                            <option value="">Selecciona una calidad</option>
                                            <option value="premium">Premium</option>
                                            <option value="standard">Estándar</option>
                                            <option value="economic">Económico</option>
                                        </select>
                                        @error('form.quality_grade')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-1">Fecha de Cosecha</label>
                                        <input 
                                            type="date" 
                                            wire:model="form.harvest_date" 
                                            class="w-full border rounded px-3 py-2 text-sm" 
                                        />
                                        @error('form.harvest_date')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-1">Ciudad</label>
                                        <input 
                                            type="text" 
                                            wire:model="form.location_city" 
                                            class="w-full border rounded px-3 py-2 text-sm" 
                                        />
                                        @error('form.location_city')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-1">Estado</label>
                                        <input 
                                            type="text" 
                                            wire:model="form.location_state" 
                                            class="w-full border rounded px-3 py-2 text-sm" 
                                        />
                                        @error('form.location_state')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-1">Estatus</label>
                                        <select 
                                            wire:model="form.status" 
                                            class="w-full border rounded px-3 py-2 text-sm bg-white"
                                        >
                                            <option value="pending">Pendiente</option>
                                            <option value="active">Activo</option>
                                            <option value="sold_out">Agotado</option>
                                            <option value="inactive">Inactivo</option>
                                        </select>
                                        @error('form.status')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Descripción -->
                                <div>
                                    <label class="block text-sm font-medium mb-1">Descripción</label>
                                    <textarea 
                                        wire:model="form.description" 
                                        class="w-full border rounded px-3 py-2 text-sm h-20"
                                    ></textarea>
                                    @error('form.description')
                                        <span class="text-red-600 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Botones -->
                                <div class="flex justify-end pt-4 border-t border-gray-200">
                                    <button 
                                        type="button" 
                                        wire:click="closeModal"
                                        class="px-4 py-2 text-sm font-medium bg-gray-300 hover:bg-gray-400 text-gray-800 rounded mr-2"
                                    >
                                        Cancelar
                                    </button>
                                    <button 
                                        type="submit"
                                        class="px-4 py-2 text-sm font-medium bg-blue-500 hover:bg-blue-600 text-white rounded"
                                    >
                                        Guardar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.addEventListener('listing-added', event => {
            Swal.fire({
                icon: 'success',
                title: '¡Publicación agregada!',
                text: 'La publicación se ha creado correctamente.',
                confirmButtonColor: '#3b82f6'
            });
        });

        window.addEventListener('listing-updated', event => {
            Swal.fire({
                icon: 'success',
                title: '¡Publicación actualizada!',
                text: 'La publicación se ha actualizado correctamente.',
                confirmButtonColor: '#3b82f6'
            });
        });

        window.addEventListener('listing-deleted', event => {
            Swal.fire({
                icon: 'success',
                title: '¡Publicación eliminada!',
                text: 'La publicación se ha eliminado correctamente.',
                confirmButtonColor: '#3b82f6'
            });
        });

        window.addEventListener('show-delete-confirmation', event => {
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¡No podrás revertir esto!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3b82f6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.deleteListing(@this.listingIdToDelete);
                }
            });
        });

        window.addEventListener('error', event => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: event.detail,
                confirmButtonColor: '#3b82f6'
            });
        });
    });
</script>

