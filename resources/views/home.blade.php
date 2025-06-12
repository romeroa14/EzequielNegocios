@extends('layouts.app')

@section('title', 'Inicio - AgroMarket')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                        Bienvenido {{ Auth::user()->name }}
                    </h2>

                    @if(Auth::user()->person && Auth::user()->person->role === 'buyer')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Mis Compras -->
                            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">Mis Compras</h3>
                                <p class="text-gray-600 mb-4">Revisa el historial de tus compras y el estado de tus pedidos.</p>
                                <a href="#" class="text-green-600 hover:text-green-700 font-medium">
                                    Ver mis compras →
                                </a>
                            </div>

                            <!-- Catálogo -->
                            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">Catálogo de Productos</h3>
                                <p class="text-gray-600 mb-4">Explora nuestra amplia variedad de productos agrícolas.</p>
                                <a href="{{ route('catalog') }}" class="text-green-600 hover:text-green-700 font-medium">
                                    Explorar catálogo →
                                </a>
                            </div>

                            <!-- Productores -->
                            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">Productores</h3>
                                <p class="text-gray-600 mb-4">Conoce a los productores y sus productos disponibles.</p>
                                <a href="{{ route('producers') }}" class="text-green-600 hover:text-green-700 font-medium">
                                    Ver productores →
                                </a>
                            </div>
                        </div>

                        <!-- Últimas Compras -->
                        <div class="mt-8">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Últimas Compras</h3>
                            <div class="bg-white rounded-lg shadow overflow-hidden">
                                <div class="p-6 text-center text-gray-500">
                                    No hay compras recientes para mostrar.
                                </div>
                            </div>
                        </div>

                        <!-- Productos Recomendados -->
                        <div class="mt-8">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Productos Recomendados</h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div class="bg-white p-4 rounded-lg shadow-md border border-gray-200">
                                    <div class="text-center text-gray-500">
                                        No hay recomendaciones disponibles.
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-500">
                                Bienvenido al sistema. Por favor, contacta al administrador si no puedes acceder a tus funciones.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 