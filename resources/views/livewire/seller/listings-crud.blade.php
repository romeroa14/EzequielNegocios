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
                    <p class="text-base text-gray-700 mb-1">Ubicación: 
                        {{ $listing->parish->name ?? '' }}, 
                        {{ $listing->municipality->name ?? '' }}, 
                        {{ $listing->state->name ?? '' }}
                    </p>
                    <p class="text-base text-gray-700 mb-1">Estatus: {{ ucfirst($listing->status) }}</p>
                    
                    <!-- Mostrar imágenes específicas de la publicación -->
                    @if($listing->hasImages())
                        <div class="mt-2">
                            <p class="text-xs text-gray-500 mb-1">Imágenes de la publicación ({{ $listing->images_count }}):</p>
                            <div class="grid grid-cols-2 gap-1">
                                @foreach($listing->images_url as $index => $imageUrl)
                                    @if($index < 2) {{-- Mostrar solo las primeras 2 imágenes --}}
                                        <img src="{{ $imageUrl }}" alt="Imagen {{ $index + 1 }}" class="w-full h-20 object-cover rounded">
                                    @endif
                                @endforeach
                            </div>
                            @if($listing->images_count > 2)
                                <p class="text-xs text-gray-400 mt-1">+{{ $listing->images_count - 2 }} más</p>
                            @endif
                        </div>
                    @else
                        <div class="mt-2 p-2 bg-gray-100 rounded text-xs text-gray-500">
                            Sin imágenes específicas
                        </div>
                    @endif
                </div>
                <div class="flex justify-between mt-4">
                    <button wire:click="openModal({{ $listing->id }})" class="bg-yellow-500 hover:bg-yellow-600 text-white text-base font-bold py-1 px-3 rounded">Editar</button>
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
                            <form wire:submit.prevent="saveListing" class="space-y-4">
                                <!-- Producto -->
                                <div>
                                    <label class="block text-sm font-medium mb-1">Producto</label>
                                    <select 
                                        wire:model="form.product_id" 
                                        class="w-full border rounded px-3 py-2 text-sm bg-white"
                                    >
                                        <option value="">Selecciona un producto</option>
                                        <optgroup label="Productos Universales">
                                            @foreach($products->where('is_universal', true) as $product)
                                                <option value="{{ $product->id }}">🌎 {{ $product->name }}</option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="Mis Productos">
                                            @foreach($products->where('person_id', Auth::id()) as $product)
                                                <option value="{{ $product->id }}">📦 {{ $product->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                    @error('form.product_id')
                                        <span class="text-red-600 text-xs">{{ $message }}</span>
                                    @enderror
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

                                    <!-- Estado -->
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Estado</label>
                                        <select 
                                            wire:model.live="form.state_id"
                                            class="w-full border rounded px-3 py-2 text-sm"
                                        >
                                            <option value="">Seleccione un estado</option>
                                            @foreach($states as $state)
                                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('form.state_id')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Municipio -->
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Municipio</label>
                                        <select 
                                            wire:model.live="form.municipality_id"
                                            class="w-full border rounded px-3 py-2 text-sm"
                                            @if(!$form['state_id']) disabled @endif
                                        >
                                            <option value="">Seleccione un municipio</option>
                                            @foreach($municipalities as $municipality)
                                                <option value="{{ $municipality->id }}">{{ $municipality->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('form.municipality_id')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Parroquia -->
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Parroquia</label>
                                        <select 
                                            wire:model="form.parish_id"
                                            class="w-full border rounded px-3 py-2 text-sm"
                                            @if(!$form['municipality_id']) disabled @endif
                                        >
                                            <option value="">Seleccione una parroquia</option>
                                            @foreach($parishes as $parish)
                                                <option value="{{ $parish->id }}">{{ $parish->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('form.parish_id')
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

                                <!-- Imágenes específicas de la publicación -->
                                <div x-data="{ 
                                    handleFileSelect(event) {
                                        const file = event.target.files[0];
                                        if (file) {
                                            const reader = new FileReader();
                                            reader.onload = (e) => {
                                                @this.handleImageSelected({
                                                    name: file.name,
                                                    preview: e.target.result
                                                });
                                            };
                                            reader.readAsDataURL(file);
                                            event.target.value = ''; // Limpiar input para permitir seleccionar el mismo archivo
                                        }
                                    }
                                }">
                                    <label class="block text-sm font-medium mb-1">Imágenes de la Publicación</label>
                                    <p class="text-xs text-gray-500 mb-2">Estas imágenes serán expuestas en el catálogo.</p>

                                    <!-- Input de archivo oculto -->
                                    <input 
                                        type="file" 
                                        id="imageInput" 
                                        class="hidden"
                                        accept="image/*"
                                        x-ref="fileInput"
                                        @change="handleFileSelect($event)"
                                    />

                                    <!-- Área de imágenes seleccionadas -->
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                                        @foreach($selectedImages as $index => $image)
                                            <div class="relative group">
                                                <img 
                                                    src="{{ $image['preview'] }}" 
                                                    alt="Imagen {{ $index + 1 }}"
                                                    class="w-full h-32 object-cover rounded-lg shadow-sm"
                                                >
                                                <button 
                                                    type="button"
                                                    wire:click.prevent="removeImage({{ $index }})"
                                                    class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach

                                        <!-- Botón para agregar imagen -->
                                        <button 
                                            type="button"
                                            @click.prevent="$refs.fileInput.click()"
                                            class="w-full h-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center hover:border-gray-400 transition-colors"
                                        >
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
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

        window.addEventListener('success', event => {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: event.detail,
                confirmButtonColor: '#3b82f6'
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

    // Función para manejar la selección de imágenes en inputs dinámicos
    function handleImageSelect(input) {
        const file = input.files[0];
        if (file) {
            console.log('Archivo seleccionado:', file.name);
            
            // Crear una vista previa del archivo
            const reader = new FileReader();
            reader.onload = function(e) {
                // Llamar a Livewire con la vista previa
                @this.handleImageSelected(e.target.result);
            };
            reader.readAsDataURL(file);
        }
    }
</script>

