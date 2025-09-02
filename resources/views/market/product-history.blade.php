@extends('layouts.app')

@section('title', 'Historial de Precios - ' . $product->name . ' - EzequielNegocios')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('market.index') }}" class="text-indigo-600 hover:text-indigo-800">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                            </a>
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">
                                    📊 Historial de Precios
                                </h1>
                                <p class="text-gray-600 mt-2">
                                    Evolución de precios para: <strong>{{ $product->name }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Precio Actual</div>
                        <div class="font-semibold text-2xl text-green-600">
                            {{ $currentPrice ? $currentPrice->formatted_price : 'No disponible' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Producto -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-16 w-16">
                            @if($product->image)
                                <img class="h-16 w-16 rounded-lg object-cover" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                            @else
                                <div class="h-16 w-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            <div class="text-lg font-medium text-gray-900">{{ $product->name }}</div>
                            <div class="text-sm text-gray-500">{{ $product->productCategory->name ?? 'Sin categoría' }}</div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-500">Total de Cambios</div>
                        <div class="text-2xl font-semibold text-blue-600">{{ $history->count() }}</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-500">Último Cambio</div>
                        <div class="text-lg font-semibold text-gray-900">
                            {{ $history->first() ? $history->first()->change_date->format('d/m/Y') : 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial de Precios -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                @if($history->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fecha del Cambio
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Precio Anterior
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Precio Nuevo
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Diferencia
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        % Cambio
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cambiado por
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Notas
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($history as $change)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $change->change_date->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $change->formatted_old_price }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $change->formatted_new_price }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold {{ $change->change_type === 'increase' ? 'text-green-600' : ($change->change_type === 'decrease' ? 'text-red-600' : 'text-gray-600') }}">
                                                {{ $change->formatted_price_difference }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold {{ $change->change_type === 'increase' ? 'text-green-600' : ($change->change_type === 'decrease' ? 'text-red-600' : 'text-gray-600') }}">
                                                {{ $change->price_change_percentage > 0 ? '+' : '' }}{{ number_format($change->price_change_percentage, 1) }}%
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $change->changedBy->full_name ?? 'Sistema' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            @if($change->notes)
                                                <div class="max-w-xs truncate" title="{{ $change->notes }}">
                                                    {{ $change->notes }}
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
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay historial disponible</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Este producto no ha tenido cambios de precio registrados.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
