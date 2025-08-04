@extends('layouts.app')

@section('content')
<div class="py-8 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Productores</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($producers as $producer)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 bg-gray-200 rounded-full flex items-center justify-center">
                                    <span class="text-xl text-gray-600">{{ substr($producer->first_name, 0, 1) }}</span>
                                </div>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">
                                    {{ $producer->first_name }} {{ $producer->last_name }}
                                </h2>
                                <p class="text-sm text-gray-500">
                                    <span class="inline-flex items-center">
                                        <svg class="h-4 w-4 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ $producer->state->name }}, {{ $producer->municipality->name }}
                                        </span>
                                </p>
                                </div>
                            </div>

                        <div class="mt-4">
                            <a 
                                href="{{ route('productores.show', $producer) }}"
                                class="block w-full bg-green-600 hover:bg-green-700 text-white text-center font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out"
                            >
                                Ver Productos
                                </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-white rounded-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8l-8 5-8-5" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay productores disponibles</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        No se encontraron productores activos en el sistema.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($producers->hasPages())
            <div class="mt-6">
                {{ $producers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection 