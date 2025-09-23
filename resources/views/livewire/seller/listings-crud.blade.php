<div >
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
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <!-- Imagen Principal -->
                    <div class="relative aspect-w-16 aspect-h-9 bg-gray-100">
                        @if($listing->hasImages() && !empty($listing->images))
                            <img 
                                src="{{ $listing->main_image_url }}"
                                alt="{{ $listing->title }}"
                                class="w-full h-48 object-cover cursor-pointer"
                                wire:click="$dispatch('openListingDetail', { listingId: {{ $listing->id }} })"
                                onerror="this.src='{{ asset('images/placeholder.png') }}'"
                            >
                            @if(count($listing->images) > 1)
                                <div class="absolute bottom-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                                    +{{ count($listing->images) - 1 }} fotos
                                </div>
                            @endif
                        @else
                            <div class="flex items-center justify-center h-48 bg-gray-50">
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Información -->
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $listing->title }}</h3>
                            <div class="flex items-center gap-2">
                                @if($listing->is_harvesting)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800 flex items-center gap-1">
                                        <span>🌾</span>
                                        <span>En Cosecha</span>
                                    </span>
                                @endif
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $listing->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($listing->status) }}
                                </span>
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-2">{{ $listing->product->name }}</p>
                        
                        <div class="flex justify-between items-center mb-2">
                <div>
                    <!-- Precio en moneda original -->
                    <span class="text-lg font-bold text-green-600">{{ $listing->formatted_price }}</span>
                    
                    <!-- Precio convertido -->
                    @if($listing->current_rate)
                        @if($listing->currency_type === 'USD')
                            <div class="text-sm text-gray-500">≈ {{ $listing->formatted_bs_price }}</div>
                        @else
                            <div class="text-sm text-gray-500">≈ {{ $listing->formatted_usd_price }}</div>
                        @endif
                        <div class="text-xs text-gray-400">Tasa BCV: {{ $listing->current_rate }}</div>
                    @endif
                </div>
                            <span class="text-sm text-gray-500">{{ $listing->formatted_presentation }}</span>
                        </div>

                        <div class="text-sm text-gray-500 mb-4 space-y-1">
                            <p class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $listing->location }}
                            </p>
                            @if($listing->is_harvesting && $listing->harvest_date)
                                <p class="flex items-center gap-1 text-orange-600">
                                    <span>🌾</span>
                                    <span>Cosecha: {{ \Carbon\Carbon::parse($listing->harvest_date)->format('d/m/Y') }}</span>
                                </p>
                            @endif
                        </div>

                        <div class="flex justify-between items-center">
                            <button 
                                wire:click="openModal({{ $listing->id }})"
                                class="text-blue-600 hover:text-blue-800 font-medium text-sm"
                            >
                                Editar
                            </button>
                            <button 
                                wire:click="confirmDelete({{ $listing->id }})"
                                class="text-red-600 hover:text-red-800 font-medium text-sm"
                            >
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12 bg-white rounded-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No tienes publicaciones</h3>
                    <p class="mt-1 text-sm text-gray-500">Comienza creando una nueva publicación.</p>
                </div>
            @endforelse
        </div>

        <!-- Modal de Detalle de Publicación -->
        <div
            x-data="{ 
                show: false,
                listingId: null,
                selectedImageIndex: 0,
                getImages() {
                    const listing = $wire.listings.find(l => l.id === this.listingId);
                    return listing?.images || [];
                },
                getImageUrl(path) {
                    if (!path) return '{{ asset('images/placeholder.png') }}';
                    
                    @if(app()->environment('production'))
                        // En producción, usar la URL de R2
                        const r2Url = '{{ config('filesystems.disks.r2.url') }}';
                        return r2Url + '/' + path;
                    @else
                        // En desarrollo, usar storage local
                        return '{{ asset('storage') }}' + '/' + path;
                    @endif
                }
            }"
            @openListingDetail.window="
                show = true;
                listingId = $event.detail.listingId;
                selectedImageIndex = 0;
            "
            x-show="show"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div 
                    class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                    @click="show = false"
                ></div>

                <div class="inline-block w-full max-w-4xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-lg shadow-xl">
                    <!-- Contenido del Modal -->
                    <div class="grid grid-cols-3 gap-4">
                        <!-- Imagen Principal (2/3 del ancho) -->
                        <div class="col-span-2 relative">
                            <template x-if="listingId && getImages().length > 0">
                                <img 
                                    :src="getImageUrl(getImages()[selectedImageIndex])"
                                    class="w-full h-[600px] object-contain bg-gray-100"
                                    alt="Imagen principal"
                                >
                            </template>
                        </div>

                        <!-- Lista de Miniaturas y Detalles (1/3 del ancho) -->
                        <div class="p-4 space-y-4">
                            <!-- Miniaturas -->
                            <div class="grid grid-cols-2 gap-2 mb-4">
                                <template x-if="listingId">
                                    <template x-for="(image, index) in getImages()" :key="index">
                                        <div 
                                            @click="selectedImageIndex = index"
                                            :class="{'ring-2 ring-blue-500': selectedImageIndex === index}"
                                            class="cursor-pointer rounded-lg overflow-hidden"
                                        >
                                            <img 
                                                :src="getImageUrl(image)"
                                                class="w-full h-20 object-cover"
                                                :alt="'Miniatura ' + (index + 1)"
                                            >
                                        </div>
                                    </template>
                                </template>
                            </div>

                            <!-- Detalles de la Publicación -->
                            <template x-if="listingId">
                                <div class="space-y-3">
                                    <h3 x-text="$wire.listings.find(l => l.id === listingId)?.title" class="text-xl font-bold"></h3>
                                    <p class="text-gray-600" x-text="$wire.listings.find(l => l.id === listingId)?.description"></p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-2xl font-bold text-green-600">
                                            $<span x-text="$wire.listings.find(l => l.id === listingId)?.unit_price"></span>
                                        </span>
                                        <span class="text-gray-600" x-text="$wire.listings.find(l => l.id === listingId)?.formatted_presentation"></span>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <p class="mb-1">Calidad: <span x-text="$wire.listings.find(l => l.id === listingId)?.quality_grade" class="font-medium"></span></p>
                                        <p class="mb-1">Cosecha: <span x-text="$wire.listings.find(l => l.id === listingId)?.harvest_date" class="font-medium"></span></p>
                                        <p>Ubicación: <span x-text="$wire.listings.find(l => l.id === listingId)?.location" class="font-medium"></span></p>
                                    </div>
                                </div>
                            </template>

                            <!-- Botón de Cerrar -->
                            <button 
                                @click="show = false"
                                class="mt-4 w-full bg-gray-100 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-200"
                            >
                                Cerrar
                            </button>
                        </div>
                </div>
                </div>
            </div>
    </div>

    <!-- Modal para crear/editar publicación -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
            
            <div class="fixed inset-0 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-2-">
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
                                        <div class="col-span-2">
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

                                        <!-- Grid de presentación, cantidad y precio en una línea -->
                                        <div class="col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                            <!-- Presentación -->
                                            <div>
                                                <label class="block text-sm font-medium mb-1">Presentación</label>
                                                <select 
                                                    wire:model.live="form.product_presentation_id" 
                                                    class="w-full border rounded px-3 py-2 text-sm bg-white"
                                                >
                                                    <option value="">Selecciona una presentación</option>
                                                    @foreach($presentations as $presentation)
                                                        <option value="{{ $presentation->id }}">{{ $presentation->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('form.product_presentation_id')
                                                    <span class="text-red-600 text-xs">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Cantidad por presentación -->
                                    <div>
                                                <label class="block text-sm font-medium mb-1">
                                                    Cantidad en {{ $selectedPresentation?->unit_type ?? 'unidades' }}
                                                </label>
                                        <input 
                                            type="number" 
                                            step="0.01" 
                                                    wire:model.live="form.presentation_quantity" 
                                            class="w-full border rounded px-3 py-2 text-sm" 
                                                    min="0.01"
                                        />
                                                @error('form.presentation_quantity')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                            <!-- Precio por presentación -->
                                <div>
                                    <label class="block text-sm font-medium mb-1">
                                        Precio por {{ $selectedPresentation?->name ?? 'presentación' }}
                                    </label>
                                    <div class="flex">
                                        <!-- Selector de moneda -->
                                        <select wire:model.live="form.currency_type" class="border rounded-l px-3 py-2 text-sm bg-white border-r-0 min-w-20">
                                            <option value="USD">$</option>
                                            <option value="VES">Bs.D</option>
                                        </select>
                                        <!-- Input de precio -->
                                        <input 
                                            type="number" 
                                            step="0.01" 
                                            wire:model="form.unit_price" 
                                            class="w-full border rounded-r px-3 py-2 text-sm border-l-0" 
                                            min="0.01"
                                            placeholder="Ingrese el precio"
                                        />
                                    </div>
                                    @error('form.unit_price')
                                        <span class="text-red-600 text-xs">{{ $message }}</span>
                                    @enderror
                                    @error('form.currency_type')
                                        <span class="text-red-600 text-xs">{{ $message }}</span>
                                    @enderror
                                    
                                    <!-- Información de conversión -->
                                    @if($form['unit_price'] && $form['currency_type'])
                                        <div class="text-xs text-gray-500 mt-1">
                                            @if($form['currency_type'] === 'USD')
                                                ≈ Bs.D {{ number_format($form['unit_price'] * ($this->currentUsdRate ?? 0), 2) }}
                                            @else
                                                ≈ ${{ number_format($form['unit_price'] / ($this->currentUsdRate ?? 1), 2) }}
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                    </div>

                                        <!-- Calidad en línea separada -->
                                        <div class="col-span-2">
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
                                    </div>
                                    <!-- Toggle de Cosecha y Fecha en una fila -->
                                    <div class="w-full mt-4">
                                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 overflow-hidden">
                                            <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4">
                                                <!-- Checkbox de cosecha -->
                                                <div class="flex-shrink-0 w-full lg:w-1/2">
                                                    <label class="flex items-start cursor-pointer">
                                                        <input 
                                                            type="checkbox" 
                                                            wire:model.live="form.is_harvesting"
                                                            class="w-3 h-3 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-1 mt-1"
                                                        />
                                                        <span class="ml-2 text-xs font-medium text-gray-700">
                                                            <span class="mr-1">🌾</span>
                                                            <span class="text-blue-600 font-semibold">¡Anuncia tu cosecha!</span>
                                                            <br>
                                                            <span class="text-gray-600">Marca si tu producto está en cosecha y los compradores sabrán cuándo estará listo para la venta.</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                
                                                <!-- Campo de fecha (condicional) -->
                                                @if($form['is_harvesting'])
                                                    <div class="w-full lg:w-1/2 transition-all duration-300 ease-in-out">
                                                        <label class="block text-xs font-medium text-gray-700 mb-1">
                                                            <span class="text-green-600 font-semibold">📅 ¿Cuándo estará listo?</span>
                                                        </label>
                                                        <input 
                                                            type="date" 
                                                            wire:model="form.harvest_date" 
                                                            class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                                                            placeholder="dd/mm/aaaa"
                                                        />
                                                        @error('form.harvest_date')
                                                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                                                        @enderror
                                                        <p class="text-xs text-green-600 mt-1 font-medium">
                                                            Los compradores verán esta fecha y podrán planificar su compra
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            @error('form.is_harvesting')
                                                <span class="text-red-600 text-xs mt-2 block text-center">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Ubicación en una sola línea -->
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
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
                                    </div>

                                    <!-- Estatus -->
                                    <div class="w-full mt-4">
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
                                        @error('selectedImages')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
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
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Notificación de publicación agregada
        window.addEventListener('listing-added', event => {
            Swal.fire({
                icon: 'success',
                title: '¡Publicación creada!',
                text: 'La publicación se ha creado correctamente.',
                confirmButtonColor: '#3b82f6'
            });
        });

        // Notificación de publicación actualizada
        window.addEventListener('listing-updated', event => {
            Swal.fire({
                icon: 'success',
                title: '¡Publicación actualizada!',
                text: 'La publicación se ha actualizado correctamente.',
                confirmButtonColor: '#3b82f6'
            });
        });

        // Notificación de publicación eliminada
        window.addEventListener('listing-deleted', event => {
            Swal.fire({
                icon: 'success',
                title: '¡Publicación eliminada!',
                text: 'La publicación se ha eliminado correctamente.',
                confirmButtonColor: '#3b82f6'
            });
        });

        // Confirmación de eliminación
        window.addEventListener('show-delete-confirmation', event => {
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¡No podrás revertir esta acción!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('deleteListing');
                }
            });
        });

        // Notificación de error
        window.addEventListener('error', event => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: event.detail || 'Ha ocurrido un error inesperado.',
                confirmButtonColor: '#3b82f6'
            });
        });
    });
</script>

