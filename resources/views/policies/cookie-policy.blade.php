@extends('layouts.app')

@section('title', 'Política de Cookies - EzequielNegocios')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50">
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-blue-200">
                <div class="p-8">
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-4">Política de Cookies</h1>
                        <div class="w-24 h-1 bg-blue-500 mx-auto rounded-full"></div>
                    </div>
                    
                    <div class="prose prose-lg max-w-none">
                        <p class="text-gray-600 mb-6 text-center">
                            <strong>Última actualización:</strong> {{ date('d/m/Y') }}
                        </p>

                        <section class="mb-8 p-6 bg-blue-50 rounded-lg">
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center">
                                <span class="mr-3">🍪</span>
                                ¿Qué son las Cookies?
                            </h2>
                            <p class="text-gray-700 mb-4">
                                Las cookies son pequeños archivos de texto que se almacenan en tu dispositivo cuando visitas nuestro sitio web. 
                                Nos ayudan a mejorar tu experiencia y a entender cómo utilizas nuestro sitio.
                            </p>
                        </section>

                        <section class="mb-8 p-6 bg-green-50 rounded-lg">
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center">
                                <span class="mr-3">📋</span>
                                Tipos de Cookies que Utilizamos
                            </h2>
                            
                            <div class="space-y-6">
                                <div class="border-l-4 border-green-500 pl-4 bg-white p-4 rounded-lg">
                                    <h3 class="text-xl font-semibold text-gray-800 mb-2">🍪 Cookies Esenciales</h3>
                                    <p class="text-gray-700 mb-2">
                                        Estas cookies son necesarias para el funcionamiento básico del sitio web. No se pueden desactivar.
                                    </p>
                                    <ul class="list-disc list-inside text-gray-600 text-sm ml-4">
                                        <li>Autenticación de usuarios</li>
                                        <li>Preferencias de idioma</li>
                                        <li>Seguridad del sitio</li>
                                    </ul>
                                </div>

                                <div class="border-l-4 border-blue-500 pl-4 bg-white p-4 rounded-lg">
                                    <h3 class="text-xl font-semibold text-gray-800 mb-2">📊 Cookies de Análisis</h3>
                                    <p class="text-gray-700 mb-2">
                                        Nos ayudan a entender cómo utilizas nuestro sitio para mejorarlo.
                                    </p>
                                    <ul class="list-disc list-inside text-gray-600 text-sm ml-4">
                                        <li>Google Analytics - Análisis de tráfico</li>
                                        <li>Páginas más visitadas</li>
                                        <li>Tiempo de navegación</li>
                                    </ul>
                                </div>

                                <div class="border-l-4 border-yellow-500 pl-4 bg-white p-4 rounded-lg">
                                    <h3 class="text-xl font-semibold text-gray-800 mb-2">💰 Cookies de Publicidad</h3>
                                    <p class="text-gray-700 mb-2">
                                        Utilizadas para mostrar anuncios relevantes y medir su efectividad.
                                    </p>
                                    <ul class="list-disc list-inside text-gray-600 text-sm ml-4">
                                        <li>Google AdSense - Anuncios personalizados</li>
                                        <li>Medición de efectividad publicitaria</li>
                                        <li>Preferencias de anuncios</li>
                                    </ul>
                                </div>
                            </div>
                        </section>

                        <section class="mb-8 p-6 bg-purple-50 rounded-lg">
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center">
                                <span class="mr-3">🔗</span>
                                Cookies de Terceros
                            </h2>
                            <p class="text-gray-700 mb-4">
                                Utilizamos servicios de terceros que pueden establecer cookies:
                            </p>
                            
                            <div class="bg-white p-4 rounded-lg mb-4 border border-purple-200">
                                <h4 class="font-semibold text-gray-800 mb-2">Google Analytics</h4>
                                <p class="text-gray-700 text-sm mb-2">
                                    Utilizamos Google Analytics para analizar el uso del sitio. Google puede usar los datos recopilados 
                                    para contextualizar y personalizar los anuncios de su propia red publicitaria.
                                </p>
                                <p class="text-gray-600 text-xs">
                                    <strong>Política de privacidad:</strong> 
                                    <a href="https://policies.google.com/privacy" class="text-blue-600 hover:underline" target="_blank">
                                        https://policies.google.com/privacy
                                    </a>
                                </p>
                            </div>

                            <div class="bg-white p-4 rounded-lg border border-purple-200">
                                <h4 class="font-semibold text-gray-800 mb-2">Google AdSense</h4>
                                <p class="text-gray-700 text-sm mb-2">
                                    Utilizamos Google AdSense para mostrar anuncios. Google puede usar cookies para mostrar anuncios 
                                    basados en tus visitas anteriores a este y otros sitios web.
                                </p>
                                <p class="text-gray-600 text-xs">
                                    <strong>Política de privacidad:</strong> 
                                    <a href="https://policies.google.com/privacy" class="text-blue-600 hover:underline" target="_blank">
                                        https://policies.google.com/privacy
                                    </a>
                                </p>
                            </div>
                        </section>

                        <section class="mb-8 p-6 bg-yellow-50 rounded-lg">
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center">
                                <span class="mr-3">⚙️</span>
                                Gestionar tus Preferencias de Cookies
                            </h2>
                            <p class="text-gray-700 mb-4">
                                Puedes gestionar tus preferencias de cookies de varias maneras:
                            </p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                    <h4 class="font-semibold text-green-800 mb-2">En nuestro sitio</h4>
                                    <p class="text-green-700 text-sm">
                                        Utiliza nuestro banner de cookies para gestionar tus preferencias en cualquier momento.
                                    </p>
                                </div>
                                
                                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                    <h4 class="font-semibold text-blue-800 mb-2">Configuración del navegador</h4>
                                    <p class="text-blue-700 text-sm">
                                        Puedes configurar tu navegador para rechazar cookies o recibir notificaciones cuando se envíen.
                                    </p>
                                </div>
                            </div>
                        </section>

                        <section class="mb-8 p-6 bg-red-50 rounded-lg">
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center">
                                <span class="mr-3">⚠️</span>
                                ¿Qué pasa si desactivo las Cookies?
                            </h2>
                            <p class="text-gray-700 mb-4">
                                Si desactivas las cookies, algunas funciones del sitio pueden no funcionar correctamente:
                            </p>
                            <ul class="list-disc list-inside text-gray-700 mb-4 ml-4">
                                <li>No podrás iniciar sesión</li>
                                <li>Algunas preferencias no se guardarán</li>
                                <li>Los anuncios pueden ser menos relevantes</li>
                                <li>No podremos analizar el uso del sitio para mejorarlo</li>
                            </ul>
                        </section>

                        <section class="mb-8 p-6 bg-indigo-50 rounded-lg">
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center">
                                <span class="mr-3">📝</span>
                                Actualizaciones de esta Política
                            </h2>
                            <p class="text-gray-700 mb-4">
                                Podemos actualizar esta política de cookies ocasionalmente. Te notificaremos sobre cambios significativos 
                                mediante un aviso en nuestro sitio o por email.
                            </p>
                        </section>

                        <section class="mb-8 p-6 bg-teal-50 rounded-lg">
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center">
                                <span class="mr-3">📞</span>
                                Contacto
                            </h2>
                            <p class="text-gray-700 mb-4">
                                Si tienes preguntas sobre nuestra política de cookies, contáctanos:
                            </p>
                            <div class="bg-white p-4 rounded-lg border border-teal-200">
                                <p class="text-gray-700">
                                    <strong>Email:</strong> cookies@ezequielnegocios.com<br>
                                    <strong>Asunto:</strong> Consulta sobre Política de Cookies
                                </p>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 