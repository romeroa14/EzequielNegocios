<div class="bg-white rounded-xl shadow-lg p-4 sm:p-8 mb-8">
    <!-- Header y botón de nuevo producto -->
    <div class="flex flex-wrap gap-4 items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Mis Productos</h2>
        <div class="flex gap-2">
            <button wire:click="openModal"
                class="inline-flex items-center bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded shadow">
                <span class="mr-1">+</span> Nuevo Producto
            </button>
            
        </div>
    </div>

    <!-- Listado de productos -->
    <div class="space-y-10">
        <!-- Productos Universales de Tierra -->
        @if($products['universal']->isNotEmpty())
            <div x-data="{ open: false }">
                <div class="flex items-center gap-2 cursor-pointer group mb-4" @click="open = !open">
                    <h3 class="text-xl font-semibold text-green-600 flex items-center gap-2">
                        <span class="text-2xl">🌎</span>
                        <span class="group-hover:text-green-700">Productos Universales</span>
                        @if($products['universal']->count() > 0)
                            <span class="text-sm text-gray-500">({{ $products['universal']->count() }})</span>
                        @endif
                    </h3>
                    <svg x-show="!open" class="w-6 h-6 text-green-600 group-hover:text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    <svg x-show="open" class="w-6 h-6 text-green-600 group-hover:text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                </div>

                <div x-show="open" x-collapse>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products['universal'] as $product)
                            <div class="bg-white rounded-lg shadow p-4 flex flex-col border-2 border-green-200">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-green-600 text-2xl">🌎</span>
                                        <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
                                    </div>
                                    <span class="text-sm text-gray-500">Por: {{ $product->creator->name }}</span>
                                </div>

                                <p class="text-sm text-gray-500 mb-1">{{ $product->productCategory->name ?? '-' }} >
                                    {{ $product->productSubcategory->name ?? '-' }} >
                                    {{ $product->productLine->name ?? '-' }} >
                                    {{ $product->brand->name ?? '-' }} >
                                    {{ $product->productPresentation->name ?? '-' }}</p>

                                @if ($product->image)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover rounded mb-2">
                                @else
                                    <img src="{{ asset('images/placeholder.png') }}" alt="{{ $product->name }}"
                                        class="w-full h-48 object-cover rounded mb-2">
                                @endif

                                <div class="bg-gray-50 p-3 rounded mt-2">
                                    <h4 class="font-medium text-gray-700 mb-2">Descripción</h4>
                                    <p class="text-gray-600">{{ $product->description }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Productos del Vendedor -->
        <div>
            <h3 class="text-xl font-semibold mb-4">📦 Mis Productos</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($products['seller'] as $product)
            <div id="product-{{ $product->id }}" class="bg-white rounded-lg shadow p-4 flex flex-col">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-yellow-600 text-2xl">📦</span>
                <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
                        </div>

                        <p class="text-sm text-gray-500 mb-1">{{ $product->productCategory->name ?? '-' }} >
                            {{ $product->productSubcategory->name ?? '-' }} >
                            {{ $product->productLine->name ?? '-' }} >
                            {{ $product->brand->name ?? '-' }} >
                            {{ $product->productPresentation->name ?? '-' }}</p>

                @if ($product->image)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover rounded mb-2">
                @else
                    <img src="{{ asset('images/placeholder.png') }}" alt="{{ $product->name }}"
                                class="w-full h-48 object-cover rounded mb-2">
                @endif

                        <div class="bg-gray-50 p-3 rounded mt-2">
                            <h4 class="font-medium text-gray-700 mb-2">Descripción</h4>
                            <p class="text-gray-600">{{ $product->description }}</p>
                        </div>

                        <div class="flex justify-between mt-auto pt-4">
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
        </div>
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
        <div class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
            
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-2">
                    <div class="relative w-full max-w-3xl bg-white shadow-xl rounded-lg">
                        <!-- Header fijo -->
                        <div class="sticky top-0 bg-white px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                            <h2 class="text-lg font-semibold truncate">{{ $editingProduct ? 'Editar Producto' : 'Nuevo Producto' }}</h2>
                            <button wire:click="closeModal" class="text-gray-400 hover:text-gray-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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

                            <form wire:submit.prevent="saveProduct" class="space-y-4">
                                <!-- Categoría y Subcategoría en una fila -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                        <label class="block text-sm font-medium mb-1">Categoría</label>
                        <select 
                            wire:model="form.product_category_id" 
                            wire:change="categoryChanged($event.target.value)"
                                            class="w-full border rounded px-3 py-2 text-sm"
                        >
                            <option value="">Selecciona una categoría</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('form.product_category_id')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                                    <div>
                        <label class="block text-sm font-medium mb-1">Subcategoría</label>
                        <select 
                                            wire:model.live="form.product_subcategory_id" 
                                            class="w-full border rounded px-3 py-2 text-sm"
                        >
                            <option value="">Selecciona una subcategoría</option>
                            @foreach ($subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                            @endforeach
                        </select>
                        @error('form.product_subcategory_id')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                                </div>

                                <!-- Línea, Marca y Estado en una fila -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                        <label class="block text-sm font-medium mb-1">Línea de producto</label>
                        <select 
                            wire:model="form.product_line_id" 
                                            class="w-full border rounded px-3 py-2 text-sm"
                                            @if(empty($form['product_subcategory_id'])) disabled @endif
                        >
                                            <option value="">Selecciona una línea</option>
                                            @if($lines && $lines->count() > 0)
                            @foreach ($lines as $line)
                                <option value="{{ $line->id }}">{{ $line->name }}</option>
                            @endforeach
                                            @elseif(!empty($form['product_subcategory_id']))
                                                <option value="" disabled>No hay líneas disponibles para esta subcategoría</option>
                                            @else
                                                <option value="" disabled>Seleccione una subcategoría primero</option>
                                            @endif
                        </select>
                        @error('form.product_line_id')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                                        
                                        
                    </div>


                                    <div>
                        <label class="block text-sm font-medium mb-1">Marca</label>
                                        <select wire:model="form.brand_id" class="w-full border rounded px-3 py-2 text-sm">
                            <option value="">Selecciona una marca</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                        @error('form.brand_id')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    
                                </div>

                                <!-- Presentación y Cantidad en una fila -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-medium mb-1">Presentación</label>
                                        <select 
                                            wire:model="form.product_presentation_id" 
                                            wire:change="presentationChanged"
                                            class="w-full border rounded px-3 py-2 text-sm"
                                        >
                                            <option value="">Selecciona presentación</option>
                                            @foreach ($presentations as $presentation)
                                                <option value="{{ $presentation->id }}">{{ $presentation->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('form.product_presentation_id')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-1">
                                            Cantidad
                                            @if($selectedPresentation)
                                                en {{ $selectedPresentation->unit_type }}
                                            @endif
                                        </label>
                        <input 
                                            type="number" 
                                            wire:model="form.custom_quantity" 
                                            class="w-full border rounded px-3 py-2 text-sm"
                                            step="0.01"
                                            min="0.01"
                                        />
                                        @error('form.custom_quantity')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Nombre y SKU en una fila -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Nombre</label>
                                        <input type="text" wire:model="form.name" class="w-full border rounded px-3 py-2 text-sm" />
                        @error('form.name')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-1">SKU Base</label>
                                        <input type="text" wire:model="form.sku_base" class="w-full border rounded px-3 py-2 text-sm" />
                                        @error('form.sku_base')
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

                                <!-- Imagen -->
                                <div>
                                    <label class="block text-sm font-medium mb-1">Imagen</label>
                        @if ($editingProduct && $editingProduct->image && !$changeImage)
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $editingProduct->image_url }}" alt="Imagen actual" class="h-16 rounded shadow">
                                <button 
                                    type="button" 
                                    wire:click="enableImageChange"
                                                class="text-blue-500 hover:text-blue-600 text-sm"
                                >
                                    Cambiar imagen
                                </button>
                            </div>
                        @else
                            <input 
                                type="file" 
                                wire:model="form.image" 
                                accept=".png,.jpg,.jpeg,.webp"
                                class="w-full border rounded px-3 py-2 text-sm" 
                            />
                            @error('form.image')
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                            @enderror
                        @endif
                    </div>

                                <!-- Info Estacional -->
                                <div>
                                    <label class="block text-sm font-medium mb-1">Información Estacional</label>
                        <input 
                            type="text" 
                            wire:model="form.seasonal_info" 
                                        class="w-full border rounded px-3 py-2 text-sm"
                            placeholder="Ej: Primavera, Verano..." 
                        />
                        @error('form.seasonal_info')
                                        <span class="text-red-600 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                                <div class="flex items-end">
                                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            wire:model="form.is_active" 
                                            class="rounded border-gray-300 text-yellow-500 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50" 
                        />
                                        <span class="ml-2 text-sm text-gray-700">Activo</span>
                                    </label>
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
                                        class="px-4 py-2 text-sm font-medium bg-yellow-500 hover:bg-yellow-600 text-white rounded"
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
    // Variable global para almacenar el ID del producto que necesita scroll
    window.pendingScrollProductId = null;

    

    // Función para hacer scroll y destacar producto (scope global)
    window.scrollToProduct = function(productId) {
        console.log('🔍 Buscando producto con ID:', productId);
        
        // Intentar múltiples veces para encontrar el elemento
        let attempts = 0;
        const maxAttempts = 10;
        
        const findAndScroll = () => {
            attempts++;
            const productElement = document.getElementById(`product-${productId}`);
            
            if (productElement) {
                console.log('✅ Producto encontrado, haciendo scroll...');
                
                // Scroll suave al producto
                productElement.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center',
                    inline: 'center'
                });
                
                // Destacar el producto con una animación
                productElement.style.transition = 'all 0.3s ease';
                productElement.style.transform = 'scale(1.05)';
                productElement.style.boxShadow = '0 15px 35px rgba(245, 158, 66, 0.3)';
                productElement.style.border = '2px solid #f59e42';
                
                // Remover el destaque después de 3 segundos
                setTimeout(() => {
                    productElement.style.transform = 'scale(1)';
                    productElement.style.boxShadow = '';
                    productElement.style.border = '';
                }, 3000);
                
                return true;
            } else if (attempts < maxAttempts) {
                console.log(`⏳ Intento ${attempts}/${maxAttempts} - Elemento no encontrado, reintentando...`);
                setTimeout(findAndScroll, 200);
                return false;
            } else {
                console.log('❌ No se pudo encontrar el producto después de', maxAttempts, 'intentos');
                return false;
            }
        };
        
        findAndScroll();
    }

    // Listener adicional para Livewire (fuera de DOMContentLoaded)
    document.addEventListener('livewire:init', () => {
        console.log('⚡ Livewire inicializado, configurando listeners adicionales...');
        
        Livewire.on('product-added', (event) => {
            console.log('🎉 Livewire event product-added:', event);
            const productId = event[0]?.productId;
            if (productId) {
                console.log('💾 Almacenando ID del producto para scroll:', productId);
                window.pendingScrollProductId = productId;
            }
        });
        
        Livewire.on('product-updated', (event) => {
            console.log('🔄 Livewire event product-updated:', event);
            const productId = event[0]?.productId;
            if (productId) {
                console.log('💾 Almacenando ID del producto actualizado para scroll:', productId);
                window.pendingScrollProductId = productId;
            }
        });
        
        // Listener específico para scroll
        Livewire.on('scroll-to-product', (event) => {
            console.log('🎯 Evento scroll-to-product recibido:', event);
            const productId = event[0]?.productId;
            if (productId) {
                setTimeout(() => {
                    window.scrollToProduct(productId);
                }, 2000); // Delay más largo para asegurar que el DOM se actualice
            }
        });
        
        // Listener para cuando se cierra el modal
        Livewire.on('modal-closed', () => {
            console.log('🚪 Modal cerrado, verificando si hay productos nuevos...');
            // Este listener se ejecutará después de que se cierre el modal
        });
        
        // Listener para cuando se cargan los productos
        Livewire.on('products-loaded', () => {
            console.log('📦 Productos cargados, verificando si hay scroll pendiente...');
            if (window.pendingScrollProductId) {
                console.log('🎯 Haciendo scroll al producto pendiente:', window.pendingScrollProductId);
                setTimeout(() => {
                    window.scrollToProduct(window.pendingScrollProductId);
                    window.pendingScrollProductId = null; // Limpiar después de usar
                }, 1000);
            }
        });
    });

    // Listener global para scroll (funciona en cualquier momento)
    window.addEventListener('scroll-to-product', event => {
        console.log('🎯 Evento global scroll-to-product:', event.detail);
        const productId = event.detail?.productId;
        if (productId) {
            setTimeout(() => {
                window.scrollToProduct(productId);
            }, 2000);
        }
    });

    // Notificaciones de productos
    document.addEventListener('DOMContentLoaded', function() {
        console.log('📱 DOM cargado, configurando listeners...');
        
        window.addEventListener('product-added', event => {
            console.log('🎉 Evento product-added recibido:', event.detail);
            
            Swal.fire({
                icon: 'success',
                title: '¡Producto agregado!',
                text: 'El producto se ha creado correctamente.',
                confirmButtonColor: '#f59e42'
            }).then(() => {
                const productId = event.detail?.productId;
                console.log('🆔 ID del producto:', productId);
                
                if (productId) {
                    // Delay más largo para móviles
                    setTimeout(() => {
                        window.scrollToProduct(productId);
                    }, 1000);
                }
            });
        });

        window.addEventListener('product-updated', event => {
            console.log('🔄 Evento product-updated recibido:', event.detail);
            
            Swal.fire({
                icon: 'success',
                title: '¡Producto actualizado!',
                text: 'El producto se ha actualizado correctamente.',
                confirmButtonColor: '#f59e42'
            }).then(() => {
                const productId = event.detail?.productId;
                console.log('🆔 ID del producto actualizado:', productId);
                
                if (productId) {
                    setTimeout(() => {
                        window.scrollToProduct(productId);
                    }, 1000);
                }
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

    // Listener adicional para Livewire después de actualizar DOM
    document.addEventListener('livewire:navigated', () => {
        console.log('🔄 Livewire navegación completada');
    });

    // Listener para cuando Livewire actualiza el DOM
    document.addEventListener('livewire:updated', () => {
        console.log('🔄 Livewire DOM actualizado');
    });
</script>
