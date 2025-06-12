@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                    Catálogo de Productos
                </h2>

                <!-- Filtros -->
                <div class="mb-8">
                    <div class="flex flex-wrap gap-4">
                        @foreach($categories as $category)
                        <a href="{{ route('catalog', ['category' => $category->id]) }}" 
                           class="px-4 py-2 rounded-full text-sm font-medium {{ request('category') == $category->id ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                            {{ $category->name }}
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Lista de Productos -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $product)
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="aspect-w-3 aspect-h-2">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-medium text-gray-900">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $product->category->name }}</p>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="text-lg font-bold text-green-600">S/. {{ number_format($product->price, 2) }}</span>
                                <span class="text-sm text-gray-500">Stock: {{ $product->stock }}</span>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('products.show', $product) }}" 
                                   class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                    Ver Detalles
                                </a>
                            </div>
                        </div>
                        <div class="px-4 py-3 bg-gray-50">
                            <div class="flex items-center space-x-2">
                                <img src="{{ $product->seller->user->profile_photo_url }}" alt="{{ $product->seller->user->name }}" class="h-6 w-6 rounded-full">
                                <span class="text-sm text-gray-500">{{ $product->seller->user->name }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 