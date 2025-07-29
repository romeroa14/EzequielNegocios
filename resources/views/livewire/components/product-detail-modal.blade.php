<div>
    <div
        x-data="{ 
            selectedImageIndex: 0,
            show: false,
            images: [],
            init() {
                this.$watch('show', value => {
                    if (value && $wire.listing) {
                        this.images = $wire.listing.images;
                        this.selectedImageIndex = 0;
                        console.log('Modal opened with images:', {
                            images: this.images,
                            selectedIndex: this.selectedImageIndex,
                            wireData: $wire.listing
                        });
                    }
                });

                Livewire.on('modal-ready', () => {
                    this.show = true;
                    this.images = $wire.listing.images;
                    console.log('Modal ready:', {
                        images: this.images,
                        selectedIndex: this.selectedImageIndex,
                        wireData: $wire.listing
                    });
                });

                Livewire.on('modal-closed', () => {
                    this.show = false;
                    this.selectedImageIndex = 0;
                    this.images = [];
                });
            },
            changeImage(index) {
                console.log('Changing image:', {
                    fromIndex: this.selectedImageIndex,
                    toIndex: index,
                    currentImage: this.images[this.selectedImageIndex],
                    newImage: this.images[index],
                    allImages: this.images
                });
                this.selectedImageIndex = index;
            }
        }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <!-- Backdrop -->
        <div 
            class="fixed inset-0 transition-opacity" 
            @click="show = false; $wire.closeModal()"
        >
            <div class="absolute inset-0 bg-black bg-opacity-30 backdrop-blur-sm"></div>
        </div>

        <!-- Modal -->
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-white w-full max-w-6xl rounded-lg shadow-xl overflow-hidden">
                <!-- Close button -->
                <button 
                    @click="show = false; $wire.closeModal()"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 z-10 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors duration-200 shadow-lg"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Content -->
                @if($listing)
                <div class="flex flex-col md:flex-row">
                    <!-- Left side - Images -->
                    <div class="w-full md:w-2/3 p-6 bg-white">
                        <!-- Main image -->
                        <div class="relative aspect-w-4 aspect-h-3 bg-gray-50 rounded-lg mb-4">
                            @if($listing && !empty($listing['images']))
                                <img 
                                    src="{{ $listing['images'][$selectedImageIndex ?? 0] }}"
                                    class="w-full h-full object-contain"
                                    alt="{{ $listing['title'] }}"
                                >
                            @endif
                        </div>

                        <!-- Thumbnails -->
                        <div class="grid grid-cols-6 gap-2 mt-4">
                            @if($listing && !empty($listing['images']))
                                @foreach($listing['images'] as $index => $image)
                                    <button 
                                        type="button"
                                        wire:click="$set('selectedImageIndex', {{ $index }})"
                                        class="relative aspect-square rounded-lg overflow-hidden transition-all duration-200 ease-in-out
                                               {{ $selectedImageIndex === $index ? 'ring-2 ring-blue-500' : 'hover:ring-2 hover:ring-blue-300' }}"
                                    >
                                        <img 
                                            src="{{ $image }}"
                                            class="w-full h-full object-cover"
                                            alt="Imagen {{ $index + 1 }}"
                                        >
                                        <div 
                                            class="absolute inset-0 transition-opacity duration-200
                                                   {{ $selectedImageIndex === $index ? 'bg-black bg-opacity-0' : 'bg-black bg-opacity-10' }}"
                                        ></div>
                                    </button>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Right side - Info -->
                    <div class="w-full md:w-1/3 p-6 bg-gray-50 border-l">
                        <!-- Status and category -->
                        <div class="mb-4">
                            <span 
                                class="px-2 py-1 rounded-full text-xs font-medium {{ $listing['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}"
                            >
                                {{ $listing['formatted_status'] }}
                            </span>
                            <span class="mx-2 text-gray-300">·</span>
                            <span class="text-sm text-gray-500">{{ $listing['product']['name'] }}</span>
                        </div>

                        <!-- Title -->
                        <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $listing['title'] }}</h1>

                        <!-- Price -->
                        <div class="mb-6">
                            <div class="flex items-center">
                                <span class="text-3xl font-bold text-gray-900">$</span>
                                <span class="text-3xl font-bold text-gray-900">{{ $listing['formatted_price'] }}</span>
                            </div>
                            <span class="text-sm text-gray-500">{{ $listing['quantity_available'] }} disponibles</span>
                        </div>

                        <!-- Product Details -->
                        <div class="mb-4 space-y-2 text-sm text-gray-600">
                            @if($listing['product']['category_name'] !== 'N/A')
                                <div>
                                    <span class="font-medium">Categoría:</span>
                                    <span>{{ $listing['product']['category_name'] }}</span>
                                </div>
                            @endif
                            @if($listing['product']['subcategory_name'] !== 'N/A')
                                <div>
                                    <span class="font-medium">Subcategoría:</span>
                                    <span>{{ $listing['product']['subcategory_name'] }}</span>
                                </div>
                            @endif
                            @if($listing['product']['brand_name'] !== 'N/A')
                                <div>
                                    <span class="font-medium">Marca:</span>
                                    <span>{{ $listing['product']['brand_name'] }}</span>
                                </div>
                            @endif
                            @if($listing['formatted_date'])
                                <div>
                                    <span class="font-medium">Fecha de cosecha:</span>
                                    <span>{{ $listing['formatted_date'] }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Location -->
                        <div class="flex items-center mb-4">
                            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-sm text-gray-600">{{ $listing['formatted_location'] }}</span>
                        </div>

                        <!-- Seller -->
                        <div class="flex items-center mb-6">
                            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-sm text-gray-600">{{ $listing['seller']['name'] }}</span>
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Descripción</h3>
                            <p class="text-sm text-gray-600">{{ $listing['description'] }}</p>
                        </div>

                        <!-- Contact Button -->
                        <a 
                            href="{{ route('productor.show', ['listing' => $listing['id']]) }}"
                            class="block w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg text-center transition duration-150 ease-in-out"
                        >
                            Contactar Vendedor
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div> 