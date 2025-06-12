@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                    Productores
                </h2>

                <!-- Lista de Productores -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($producers as $producer)
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center space-x-4">
                                <img src="{{ $producer->user->profile_photo_url }}" alt="{{ $producer->user->name }}" class="h-16 w-16 rounded-full">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">{{ $producer->user->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $producer->location }}</p>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <div class="flex justify-between text-sm text-gray-500 mb-2">
                                    <span>Productos:</span>
                                    <span>{{ $producer->listings_count }}</span>
                                </div>
                                <div class="flex justify-between text-sm text-gray-500">
                                    <span>Calificación:</span>
                                    <div class="flex items-center">
                                        <span class="text-yellow-400">
                                            @for($i = 0; $i < 5; $i++)
                                                @if($i < $producer->rating)
                                                    ★
                                                @else
                                                    ☆
                                                @endif
                                            @endfor
                                        </span>
                                        <span class="ml-1">{{ number_format($producer->rating, 1) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Productos Destacados</h4>
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach($producer->listings->take(3) as $listing)
                                    <div class="aspect-w-1 aspect-h-1">
                                        <img src="{{ $listing->image_url }}" alt="{{ $listing->name }}" class="w-full h-full object-cover rounded">
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-6">
                                <a href="{{ route('producers.show', $producer) }}" 
                                   class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                    Ver Perfil
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                <div class="mt-8">
                    {{ $producers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 