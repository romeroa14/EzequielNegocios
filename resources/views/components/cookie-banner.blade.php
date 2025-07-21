@if($show)
<div id="cookie-banner" class="fixed bottom-0 left-0 right-0 bg-green-500 text-white p-4 z-50 shadow-lg border-t-4 border-green-600">
    <div class="max-w-7xl mx-auto bg-green-500">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <!-- Información de cookies -->
            <div class="flex-1">
                <h3 class="text-lg font-semibold mb-2 text-white">🍪 Política de Cookies</h3>
                <p class="text-sm text-green-100 mb-3">
                    Utilizamos cookies para mejorar tu experiencia, analizar el tráfico y personalizar contenido. 
                    Al continuar navegando, aceptas nuestro uso de cookies.
                </p>
                
                <!-- Opciones de cookies -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                    <!-- Cookies Esenciales -->
                    <div class="flex items-center">
                        <input type="checkbox" id="essential-cookies" checked disabled 
                               class="mr-2 rounded border-green-300 text-green-600 focus:ring-green-500">
                        <label for="essential-cookies" class="text-sm">
                            <span class="font-medium text-white">Esenciales</span>
                            <span class="text-green-200 block">Siempre activas</span>
                        </label>
                    </div>
                    
                    <!-- Cookies de Análisis -->
                    <div class="flex items-center">
                        <input type="checkbox" id="analytics-cookies" 
                               class="mr-2 rounded border-green-300 text-green-600 focus:ring-green-500">
                        <label for="analytics-cookies" class="text-sm">
                            <span class="font-medium text-white">Análisis</span>
                            <span class="text-green-200 block">Google Analytics</span>
                        </label>
                    </div>
                    
                    <!-- Cookies de Publicidad -->
                    <div class="flex items-center">
                        <input type="checkbox" id="advertising-cookies" 
                               class="mr-2 rounded border-green-300 text-green-600 focus:ring-green-500">
                        <label for="advertising-cookies" class="text-sm">
                            <span class="font-medium text-white">Publicidad</span>
                            <span class="text-green-200 block">Google AdSense</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('privacy-policy') }}" 
                   class="px-4 py-2 text-sm text-green-200 hover:text-white transition-colors font-medium">
                    Política de Privacidad
                </a>
                <a href="{{ route('cookie-policy') }}" 
                   class="px-4 py-2 text-sm text-green-200 hover:text-white transition-colors font-medium">
                    Política de Cookies
                </a>
                <button onclick="acceptAllCookies()" 
                        class="px-6 py-2 bg-white hover:bg-gray-100 text-green-800 rounded-lg transition-colors font-medium">
                    Aceptar Todo
                </button>
                <button onclick="acceptSelectedCookies()" 
                        class="px-6 py-2 bg-green-900 hover:bg-green-950 text-white rounded-lg transition-colors">
                    Aceptar Seleccionadas
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function acceptAllCookies() {
    // Aceptar todas las cookies
    setCookiePreferences({
        essential: true,
        analytics: true,
        advertising: true
    });
    hideCookieBanner();
}

function acceptSelectedCookies() {
    // Aceptar solo las cookies seleccionadas
    const analytics = document.getElementById('analytics-cookies').checked;
    const advertising = document.getElementById('advertising-cookies').checked;
    
    setCookiePreferences({
        essential: true,
        analytics: analytics,
        advertising: advertising
    });
    hideCookieBanner();
}

function setCookiePreferences(preferences) {
    // Guardar preferencias en el servidor
    fetch('{{ route("cookie.preferences") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(preferences)
    });
    
    // Guardar en localStorage también
    localStorage.setItem('cookie_preferences', JSON.stringify(preferences));
}

function hideCookieBanner() {
    const banner = document.getElementById('cookie-banner');
    banner.style.animation = 'slideDown 0.5s ease-out forwards';
    setTimeout(() => {
        banner.remove();
    }, 500);
}

// Cargar preferencias guardadas
document.addEventListener('DOMContentLoaded', function() {
    const savedPreferences = localStorage.getItem('cookie_preferences');
    if (savedPreferences) {
        const preferences = JSON.parse(savedPreferences);
        document.getElementById('analytics-cookies').checked = preferences.analytics;
        document.getElementById('advertising-cookies').checked = preferences.advertising;
    }
});
</script>

<style>
@keyframes slideDown {
    from {
        transform: translateY(0);
        opacity: 1;
    }
    to {
        transform: translateY(100%);
        opacity: 0;
    }
}
</style>
@endif