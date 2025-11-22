@extends('layouts.app')

@section('title', 'Precios de Mercado - EzequielNegocios')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            📊 Precios de Mercado
                        </h1>
                        <p class="text-gray-600 mt-2">
                            Actualizaciones semanales de precios desde Coche
                        </p>
                        @if($stats['usd_rate'])
                            <div class="mt-2 text-sm text-blue-600">
                                💱 Tasa de Cambio: $1 = Bs. {{ number_format($stats['usd_rate'], 2, ',', '.') }} 
                                <span class="text-gray-500">({{ $stats['usd_rate_fetched']->format('d/m/Y H:i') }})</span>
                            </div>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Última actualización</div>
                        <div class="font-semibold">
                            {{ $stats['last_update'] ? $stats['last_update']->format('d/m/Y H:i') : 'No disponible' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-500">Fecha</div>
                            <div class="text-2xl font-semibold text-gray-900">
                                {{ now()->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex flex-wrap gap-4 items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Mostrando precios actuales de todos los productos
                    </div>
                    
                    <div class="flex gap-2">
                        <a href="{{ route('market.weekly') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Vista Semanal
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Precios -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                @if($marketPrices->count() > 0)
                    <!-- Vista Desktop (Tabla) -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Producto
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Precio Original
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Conversión
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Moneda
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fecha de actualización
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Notas
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Historial
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($marketPrices as $price)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    @if(\App\Helpers\ImageHelper::imageExists($price->product->image))
                                                        <img class="h-10 w-10 rounded-full object-cover" 
                                                             src="{{ \App\Helpers\ImageHelper::getProductImageUrl($price->product->image, $price->product->name) }}" 
                                                             alt="{{ $price->product->name }}">
                                                    @else
                                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                            <img src="{{ asset('images/default-product.svg') }}" 
                                                                 alt="Imagen por defecto" 
                                                                 class="h-6 w-6 text-gray-400">
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $price->product->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $price->product->productCategory->name ?? 'Sin categoría' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $price->formatted_price }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-blue-600">
                                                @if($price->currency === 'VES' && isset($price->price_usd))
                                                    <div class="font-medium">$ {{ number_format($price->price_usd, 2, ',', '.') }}</div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        Equivalente en USD
                                                    </div>
                                                @elseif($price->currency === 'USD' && isset($price->price_ves_equivalent))
                                                    <div class="font-medium">Bs. {{ number_format($price->price_ves_equivalent, 2, ',', '.') }}</div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        Equivalente en VES
                                                    </div>
                                                @else
                                                    <span class="text-gray-400">Sin conversión</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $price->currency === 'VES' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $price->currency }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ ($price->updated_at ?? $price->price_date)->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            @if($price->notes)
                                                <div class="max-w-xs truncate" title="{{ $price->notes }}">
                                                    {{ $price->notes }}
                                                </div>
                                            @else
                                                <span class="text-gray-400">Sin notas</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <a href="{{ route('market.product.history', $price->product_id) }}" 
                                               class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                Ver Historial
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Vista Móvil (Cards) -->
                    <div class="md:hidden space-y-4">
                        @foreach($marketPrices as $price)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <!-- Primera fila: Producto y Precio -->
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center flex-1">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            @if(\App\Helpers\ImageHelper::imageExists($price->product->image))
                                                <img class="h-12 w-12 rounded-full object-cover" 
                                                     src="{{ \App\Helpers\ImageHelper::getProductImageUrl($price->product->image, $price->product->name) }}" 
                                                     alt="{{ $price->product->name }}">
                                            @else
                                                <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <img src="{{ asset('images/default-product.svg') }}" 
                                                         alt="Imagen por defecto" 
                                                         class="h-8 w-8 text-gray-400">
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $price->product->name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $price->product->productCategory->name ?? 'Sin categoría' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-gray-900">
                                            {{ $price->formatted_price }}
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $price->currency === 'VES' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $price->currency }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Segunda fila: Conversión y Detalles -->
                                <div class="border-t border-gray-200 pt-3">
                                    <div class="grid grid-cols-2 gap-4">
                                        <!-- Conversión -->
                                        <div>
                                            <div class="text-xs font-medium text-gray-500 mb-1">Conversión</div>
                                            <div class="text-sm font-semibold text-blue-600">
                                                @if($price->currency === 'VES' && isset($price->price_usd))
                                                    <div class="font-medium">$ {{ number_format($price->price_usd, 2, ',', '.') }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        Equivalente en USD
                                                    </div>
                                                @elseif($price->currency === 'USD' && isset($price->price_ves_equivalent))
                                                    <div class="font-medium">Bs. {{ number_format($price->price_ves_equivalent, 2, ',', '.') }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        Equivalente en VES
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 text-xs">Sin conversión</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Fecha de actualización -->
                                        <div>
                                            <div class="text-xs font-medium text-gray-500 mb-1">Fecha de actualización</div>
                                            <div class="text-sm text-gray-700">
                                                {{ ($price->updated_at ?? $price->price_date)->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Notas (si existen) -->
                                    @if($price->notes)
                                        <div class="mt-3">
                                            <div class="text-xs font-medium text-gray-500 mb-1">Notas</div>
                                            <div class="text-sm text-gray-700 bg-white p-2 rounded border">
                                                {{ $price->notes }}
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Botón de Historial -->
                                    <div class="mt-3 text-center">
                                        <a href="{{ route('market.product.history', $price->product_id) }}" 
                                           class="inline-flex items-center px-3 py-2 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                            Ver Historial
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay precios disponibles</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            No se encontraron precios para la fecha seleccionada.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
