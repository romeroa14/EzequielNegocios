@extends('layouts.app')

@section('content')
<div class="py-8 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Información del Productor -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="h-16 w-16 bg-gray-200 rounded-full flex items-center justify-center">
                        <span class="text-2xl text-gray-600">{{ substr($producer->first_name, 0, 1) }}</span>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $producer->first_name }} {{ $producer->last_name }}
                    </h1>
                    <p class="text-gray-500">
                        <span class="inline-flex items-center">
                            <svg class="h-5 w-5 text-gray-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            @if($producer->state && $producer->municipality)
                                {{ $producer->state->name }}, {{ $producer->municipality->name }}
                            @elseif($producer->state)
                                {{ $producer->state->name }}
                            @else
                                Ubicación no especificada
                            @endif
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Productos del Productor -->
        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-900">Productos disponibles</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($listings as $listing)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <!-- Product Image -->
                        <div class="aspect-w-4 aspect-h-3 bg-gray-200">
                            @if(!empty($listing->images))
                                <img 
                                    src="{{ $listing->main_image_url }}"
                                    alt="{{ $listing->title }}"
                                    class="w-full h-48 object-cover"
                                    onerror="this.src='{{ asset('images/placeholder.png') }}'"
                                />
                            @else
                                <img 
                                    src="{{ asset('images/placeholder.png') }}"
                                    alt="Placeholder"
                                    class="w-full h-48 object-cover"
                                />
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                {{ $listing->title }}
                            </h3>
                            <p class="text-sm text-gray-600 mb-2">{{ $listing->product->name }}</p>
                            <p class="text-sm text-gray-500 mb-3">{{ Str::limit($listing->description, 80) }}</p>

                            <!-- Price and Quantity -->
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <span class="text-lg font-bold text-green-600">${{ number_format($listing->unit_price, 2) }}</span>
                                    <span class="text-sm text-gray-500">/ unidad</span>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $listing->quantity_available }} disponibles
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="text-xs text-gray-500 mb-4">
                                📍 @if($listing->state && $listing->municipality)
                                    {{ $listing->state->name }}, {{ $listing->municipality->name }}
                                @elseif($listing->state)
                                    {{ $listing->state->name }}
                                @else
                                    Ubicación no especificada
                                @endif
                            </div>

                            <!-- Contact Button -->
                            <a 
                                href="https://wa.me/+58{{ $producer->phone }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="block w-full bg-green-600 hover:bg-green-700 text-white text-center font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out"
                            >
                                Contactar por WhatsApp
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 bg-white rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8l-8 5-8-5" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay productos disponibles</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Este productor no tiene productos listados actualmente.
                        </p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($listings->hasPages())
                <div class="mt-6">
                    {{ $listings->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 