@extends('layouts.app')

@section('title', 'Preferencias de Cookies - EzequielNegocios')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-pink-50">
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-purple-200">
                <div class="p-8">
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-4">Gestionar Preferencias de Cookies</h1>
                        <div class="w-24 h-1 bg-purple-500 mx-auto rounded-full"></div>
                    </div>
                    
                    <div class="prose prose-lg max-w-none">
                        <p class="text-gray-600 mb-6 text-center">
                            Controla qué tipos de cookies permitimos en tu dispositivo.
                        </p>

                        <form id="cookie-preferences-form" class="space-y-6">
                            @csrf
                            
                            <!-- Cookies Esenciales -->
                            <div class="border border-green-200 rounded-lg p-6 bg-green-50">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                                            <span class="mr-2">🍪</span>
                                            Cookies Esenciales
                                        </h3>
                                        <p class="text-gray-600 mb-4">
                                            Estas cookies son necesarias para el funcionamiento básico del sitio web. 
                                            No se pueden desactivar.
                                        </p>
                                        <ul class="list-disc list-inside text-gray-600 text-sm ml-4">
                                            <li>Autenticación de usuarios</li>
                                            <li>Preferencias de idioma</li>
                                            <li>Seguridad del sitio</li>
                                        </ul>
                                    </div>
                                    <div class="ml-4">
                                        <input type="checkbox" checked disabled 
                                               class="rounded border-green-300 text-green-600 focus:ring-green-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Cookies de Análisis -->
                            <div class="border border-blue-200 rounded-lg p-6 bg-blue-50">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                                            <span class="mr-2">📊</span>
                                            Cookies de Análisis
                                        </h3>
                                        <p class="text-gray-600 mb-4">
                                            Nos ayudan a entender cómo utilizas nuestro sitio para mejorarlo.
                                        </p>
                                        <ul class="list-disc list-inside text-gray-600 text-sm ml-4">
                                            <li>Google Analytics - Análisis de tráfico</li>
                                            <li>Páginas más visitadas</li>
                                            <li>Tiempo de navegación</li>
                                        </ul>
                                    </div>
                                    <div class="ml-4">
                                        <input type="checkbox" name="analytics" id="analytics-cookies" 
                                               {{ $preferences['analytics'] ? 'checked' : '' }}
                                               class="rounded border-blue-300 text-blue-600 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Cookies de Publicidad -->
                            <div class="border border-yellow-200 rounded-lg p-6 bg-yellow-50">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                                            <span class="mr-2">💰</span>
                                            Cookies de Publicidad
                                        </h3>
                                        <p class="text-gray-600 mb-4">
                                            Utilizadas para mostrar anuncios relevantes y medir su efectividad.
                                        </p>
                                        <ul class="list-disc list-inside text-gray-600 text-sm ml-4">
                                            <li>Google AdSense - Anuncios personalizados</li>
                                            <li>Medición de efectividad publicitaria</li>
                                            <li>Preferencias de anuncios</li>
                                        </ul>
                                    </div>
                                    <div class="ml-4">
                                        <input type="checkbox" name="advertising" id="advertising-cookies" 
                                               {{ $preferences['advertising'] ? 'checked' : '' }}
                                               class="rounded border-yellow-300 text-yellow-600 focus:ring-yellow-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="flex flex-col sm:flex-row gap-4 pt-6">
                                <button type="submit" 
                                        class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors font-medium">
                                    Guardar Preferencias
                                </button>
                                <a href="{{ route('cookie-policy') }}" 
                                   class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors text-center">
                                    Ver Política de Cookies
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('cookie-preferences-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const analytics = document.getElementById('analytics-cookies').checked;
    const advertising = document.getElementById('advertising-cookies').checked;
    
    fetch('{{ route("cookie.preferences.update") }}', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            essential: true,
            analytics: analytics,
            advertising: advertising
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensaje de éxito
            alert('Preferencias guardadas correctamente');
            
            // Actualizar localStorage
            localStorage.setItem('cookie_preferences', JSON.stringify({
                essential: true,
                analytics: analytics,
                advertising: advertising
            }));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al guardar las preferencias');
    });
});
</script>
@endpush

@endsection 