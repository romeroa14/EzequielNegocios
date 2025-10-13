@extends('layouts.app')

@section('title', 'Producto - EzequielNegocios')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="flex items-center justify-between" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <a href="{{ route('catalogo') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-2">
                            <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            <span>Inicio</span>
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('catalogo') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Catálogo</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-4 text-sm font-medium text-gray-500">{{ $listing->title }}</span>
                        </div>
                    </li>
                </ol>
                
                <!-- Botón de regreso rápido -->
                <a 
                    href="{{ route('catalogo') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition duration-150 ease-in-out"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver al Catálogo
                </a>
            </nav>
        </div>

        <!-- Product Detail -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="flex flex-col lg:flex-row">
                <!-- Left side - Images -->
                <div class="w-full lg:w-3/5 p-4 md:p-6">
                    <!-- Main image -->
                    <div class="relative bg-gray-50 rounded-lg mb-4 overflow-hidden" style="height: 500px;">
                        @php
                            // Manejar tanto arrays como JSON strings
                            $images = $listing->images;
                            if (is_string($images)) {
                                $images = json_decode($images, true);
                            }
                            $hasImages = $images && is_array($images) && count($images) > 0;
                        @endphp
                        
                        @if($hasImages)
                            <img 
                                src="{{ Storage::url($images[0]) }}"
                                class="w-full h-full object-cover"
                                alt="{{ $listing->title }}"
                                id="main-image"
                                onerror="this.src='{{ asset('images/placeholder.png') }}'"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                <div class="text-center">
                                    <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-gray-500">Sin imagen disponible</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Thumbnails -->
                    @if($hasImages && count($images) > 1)
                        <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                            @foreach($images as $index => $image)
                                <button 
                                    type="button"
                                    onclick="changeMainImage('{{ Storage::url($image) }}')"
                                    class="relative rounded-lg overflow-hidden transition-all duration-200 ease-in-out hover:ring-2 hover:ring-blue-300"
                                    style="height: 60px;"
                                >
                                    <img 
                                        src="{{ Storage::url($image) }}"
                                        class="w-full h-full object-cover"
                                        alt="Imagen {{ $index + 1 }}"
                                    >
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Right side - Info -->
                <div class="w-full lg:w-2/5 p-6 bg-gray-50 border-l">
                    <!-- Status and category -->
                    <div class="mb-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $listing->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($listing->status) }}
                        </span>
                        <span class="mx-2 text-gray-300">·</span>
                        <span class="text-sm text-gray-500">{{ $listing->product->name }}</span>
                    </div>

                    <!-- Title -->
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $listing->title }}</h1>

                    <!-- Price -->
                    <div class="mb-6">
                        <div class="flex items-center gap-3">
                            <div>
                                <span class="text-4xl font-bold text-gray-900">{{ $listing->formatted_price }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-500 mt-2">
                            <span>por {{ $listing->productPresentation->name ?? 'unidad' }}</span>
                            <span class="text-gray-300">·</span>
                            <span>{{ $listing->presentation_quantity }} {{ $listing->productPresentation->unit ?? 'unidades' }}</span>
                        </div>
                    </div>

                    <!-- Product Details -->
                    <div class="mb-4 space-y-2 text-sm text-gray-600">
                        @if($listing->product->productCategory)
                            <div>
                                <span class="font-medium">Categoría:</span>
                                <span>{{ $listing->product->productCategory->name }}</span>
                            </div>
                        @endif
                        @if($listing->product->productSubcategory)
                            <div>
                                <span class="font-medium">Subcategoría:</span>
                                <span>{{ $listing->product->productSubcategory->name }}</span>
                            </div>
                        @endif
                        @if($listing->product->brand)
                            <div>
                                <span class="font-medium">Marca:</span>
                                <span>{{ $listing->product->brand->name }}</span>
                            </div>
                        @endif
                        @if($listing->harvest_date)
                            <div>
                                <span class="font-medium">Fecha de cosecha:</span>
                                <span>{{ $listing->harvest_date->format('d/m/Y') }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Location -->
                    <div class="flex items-center mb-4">
                        <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-sm text-gray-600">{{ $listing->formatted_location }}</span>
                    </div>

                    <!-- Seller -->
                    <div class="flex items-center mb-6">
                        <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-sm text-gray-600">{{ $listing->person->first_name }} {{ $listing->person->last_name }}</span>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Descripción</h3>
                        <p class="text-sm text-gray-600">{{ $listing->description }}</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <!-- Botón de regreso al marketplace -->
                        <a 
                            href="{{ route('catalogo') }}"
                            class="block w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-4 rounded-lg text-center transition duration-150 ease-in-out flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Regresar al Marketplace
                        </a>
                        
                        <a 
                            href="{{ route('productores.show', ['producer' => $listing->person->id]) }}"
                            class="block w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg text-center transition duration-150 ease-in-out"
                        >
                            Ver Productor
                        </a>
                        
                        <button 
                            onclick="generateShareLink()"
                            class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg text-center transition duration-150 ease-in-out text-sm shadow-sm hover:shadow-md"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                            Comparte esta publicación
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function changeMainImage(imageSrc) {
    document.getElementById('main-image').src = imageSrc;
}

function generateShareLink() {
    const shareUrl = window.location.href;
    
    // Crear modal simple
    const modal = document.createElement('div');
    modal.innerHTML = `
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;">
            <div style="background: white; padding: 20px; border-radius: 10px; max-width: 500px; width: 90%;">
                <h3 style="margin-bottom: 20px;">Compartir Producto</h3>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 14px; color: #374151; margin-bottom: 8px; font-weight: 500;">Link del producto:</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" id="shareLink" value="${shareUrl}" readonly style="flex: 1; padding: 8px; border: 1px solid #d1d5db; border-radius: 5px; font-size: 14px; background: #f9fafb;" onclick="this.select()">
                        <button onclick="copyToClipboard()" style="padding: 8px 16px; background: #10b981; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">Copiar</button>
                    </div>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 5px; cursor: pointer;">Cerrar</button>
            </div>
        </div>
    `;
    
    // Función para copiar al portapapeles
    window.copyToClipboard = function() {
        const input = document.getElementById('shareLink');
        input.select();
        input.setSelectionRange(0, 99999);
        document.execCommand('copy');
        
        // Mostrar mensaje de confirmación
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = '¡Copiado!';
        button.style.background = '#059669';
        
        setTimeout(() => {
            button.textContent = originalText;
            button.style.background = '#10b981';
        }, 2000);
    };
    
    document.body.appendChild(modal);
}
</script>
@endsection
