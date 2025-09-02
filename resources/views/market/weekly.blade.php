@extends('layouts.app')

@section('title', 'Precios Semanales - EzequielNegocios')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            📅 Precios Semanales
                        </h1>
                        <p class="text-gray-600 mt-2">
                            Vista semanal de precios de mercado
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Semana del</div>
                        <div class="font-semibold">
                            {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('market.weekly') }}" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-64">
                        <label for="week" class="block text-sm font-medium text-gray-700 mb-2">
                            Seleccionar Semana
                        </label>
                        <select name="week" id="week" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            @foreach($availableWeeks as $week)
                                <option value="{{ $week['value'] }}" {{ $startDate->format('Y-m-d') == $week['value'] ? 'selected' : '' }}>
                                    {{ $week['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="flex gap-2">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Filtrar
                        </button>
                        <a href="{{ route('market.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Vista Diaria
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Contenido Semanal -->
        @if($marketPrices->count() > 0)
            @foreach($marketPrices as $date => $prices)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">
                            {{ \Carbon\Carbon::parse($date)->format('l, d/m/Y') }}
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $prices->count() }} productos actualizados
                        </p>
                    </div>
                    
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Producto
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Precio
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Moneda
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actualizado por
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Notas
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($prices as $price)
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
                                                                     class="h-6 w-6">
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
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $price->currency === 'VES' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ $price->currency }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $price->updated_by_name }}
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
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay precios disponibles</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        No se encontraron precios para la semana seleccionada.
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
