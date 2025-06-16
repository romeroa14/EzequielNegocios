<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mis Productos') }}
            </h2>
            <a href="{{ route('seller.products.create') }}" 
               class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                {{ __('Nuevo Producto') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($listings->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500 text-lg">No tienes productos publicados aún.</p>
                            <a href="{{ route('seller.products.create') }}" 
                               class="mt-4 inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                                {{ __('Publicar mi primer producto') }}
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($listings as $listing)
                                <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                    <div class="aspect-w-16 aspect-h-9">
                                        <img src="{{ $listing->product->image_url ?? asset('images/placeholder.png') }}" 
                                             alt="{{ $listing->product->name }}"
                                             class="object-cover w-full h-48">
                                    </div>
                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $listing->product->name }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ $listing->product->category->name }} > 
                                            {{ $listing->product->subcategory->name }}
                                        </p>
                                        <div class="mt-2 flex justify-between items-center">
                                            <span class="text-lg font-bold text-gray-900">
                                                ${{ number_format($listing->price, 2) }}
                                            </span>
                                            <span class="text-sm text-gray-600">
                                                {{ $listing->available_quantity }} disponibles
                                            </span>
                                        </div>
                                        <div class="mt-4 flex justify-between items-center">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('seller.products.edit', $listing) }}" 
                                                   class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-bold py-2 px-3 rounded">
                                                    {{ __('Editar') }}
                                                </a>
                                                <form action="{{ route('seller.products.destroy', $listing) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="bg-red-500 hover:bg-red-600 text-white text-sm font-bold py-2 px-3 rounded">
                                                        {{ __('Eliminar') }}
                                                    </button>
                                                </form>
                                            </div>
                                            <form action="{{ route('seller.products.toggle-status', $listing) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="{{ $listing->is_active ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-500 hover:bg-gray-600' }} text-white text-sm font-bold py-2 px-3 rounded">
                                                    {{ $listing->is_active ? 'Activo' : 'Inactivo' }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $listings->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 