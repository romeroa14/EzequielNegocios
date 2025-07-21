@extends('layouts.app')

@section('title', 'Política de Privacidad - EzequielNegocios')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-green-100">
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-green-200">
                <div class="p-8">
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-4">Política de Privacidad</h1>
                        <div class="w-24 h-1 bg-green-500 mx-auto rounded-full"></div>
                    </div>
                    
                    <div class="prose prose-lg max-w-none">
                        <p class="text-gray-600 mb-6 text-center">
                            <strong>Última actualización:</strong> {{ date('d/m/Y') }}
                        </p>

                        <section class="mb-8 p-6 bg-green-50 rounded-lg">
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center">
                                <span class="mr-3">🔒</span>
                                1. Información que Recopilamos
                            </h2>
                            <p class="text-gray-700 mb-4">
                                Recopilamos información que nos proporcionas directamente, como cuando:
                            </p>
                            <ul class="list-disc list-inside text-gray-700 mb-4 ml-4">
                                <li>Te registras en nuestra plataforma</li>
                                <li>Publicas productos o servicios</li>
                                <li>Realizas transacciones</li>
                                <li>Nos contactas para soporte</li>
                            </ul>
                        </section>

                        <section class="mb-8 p-6 bg-blue-50 rounded-lg">
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center">
                                <span class="mr-3">📊</span>
                                2. Información Automática
                            </h2>
                            <p class="text-gray-700 mb-4">
                                También recopilamos información automáticamente cuando utilizas nuestro sitio:
                            </p>
                            <ul class="list-disc list-inside text-gray-700 mb-4 ml-4">
                                <li>Información del dispositivo y navegador</li>
                                <li>Dirección IP y ubicación aproximada</li>
                                <li>Páginas visitadas y tiempo de navegación</li>
                                <li>Cookies y tecnologías similares</li>
                            </ul>
                        </section>

                        <section class="mb-8 p-6 bg-yellow-50 rounded-lg">
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center">
                                <span class="mr-3">🎯</span>
                                3. Uso de la Información
                            </h2>
                            <p class="text-gray-700 mb-4">
                                Utilizamos tu información para:
                            </p>
                            <ul class="list-disc list-inside text-gray-700 mb-4 ml-4">
                                <li>Proporcionar y mejorar nuestros servicios</li>
                                <li>Facilitar transacciones entre usuarios</li>
                                <li>Enviar comunicaciones importantes</li>
                                <li>Personalizar tu experiencia</li>
                                <li>Cumplir con obligaciones legales</li>
                            </ul>
                        </section>

                        <section class="mb-8 p-6 bg-purple-50 rounded-lg">
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center">
                                <span class="mr-3">🤝</span>
                                4. Compartir Información
                            </h2>
                            <p class="text-gray-700 mb-4">
                                No vendemos, alquilamos ni compartimos tu información personal con terceros, excepto:
                            </p>
                            <ul class="list-disc list-inside text-gray-700 mb-4 ml-4">
                                <li>Con tu consentimiento explícito</li>
                                <li>Para cumplir con obligaciones legales</li>
                                <li>Con proveedores de servicios que nos ayudan a operar</li>
                                <li>Para proteger nuestros derechos y seguridad</li>
                            </ul>
                        </section>

                        <section class="mb-8 p-6 bg-red-50 rounded-lg">
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center">
                                <span class="mr-3">🛡️</span>
                                5. Seguridad
                            </h2>
                            <p class="text-gray-700 mb-4">
                                Implementamos medidas de seguridad técnicas y organizativas para proteger tu información personal contra acceso no autorizado, alteración, divulgación o destrucción.
                            </p>
                        </section>

                        <section class="mb-8 p-6 bg-indigo-50 rounded-lg">
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center">
                                <span class="mr-3">⚖️</span>
                                6. Tus Derechos
                            </h2>
                            <p class="text-gray-700 mb-4">
                                Tienes derecho a:
                            </p>
                            <ul class="list-disc list-inside text-gray-700 mb-4 ml-4">
                                <li>Acceder a tu información personal</li>
                                <li>Corregir información inexacta</li>
                                <li>Solicitar la eliminación de tus datos</li>
                                <li>Oponerte al procesamiento de tus datos</li>
                                <li>Portabilidad de tus datos</li>
                            </ul>
                        </section>

                        <section class="mb-8 p-6 bg-pink-50 rounded-lg">
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center">
                                <span class="mr-3">🍪</span>
                                7. Cookies y Tecnologías Similares
                            </h2>
                            <p class="text-gray-700 mb-4">
                                Utilizamos cookies y tecnologías similares para mejorar tu experiencia. Puedes gestionar tus preferencias de cookies en cualquier momento.
                            </p>
                        </section>

                        <section class="mb-8 p-6 bg-orange-50 rounded-lg">
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center">
                                <span class="mr-3">📝</span>
                                8. Cambios a esta Política
                            </h2>
                            <p class="text-gray-700 mb-4">
                                Podemos actualizar esta política ocasionalmente. Te notificaremos sobre cambios significativos por email o mediante un aviso en nuestro sitio.
                            </p>
                        </section>

                        <section class="mb-8 p-6 bg-teal-50 rounded-lg">
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center">
                                <span class="mr-3">📞</span>
                                9. Contacto
                            </h2>
                            <p class="text-gray-700 mb-4">
                                Si tienes preguntas sobre esta política de privacidad, contáctanos en:
                            </p>
                            <div class="bg-white p-4 rounded-lg border border-teal-200">
                                <p class="text-gray-700">
                                    <strong>Email:</strong> alfredoromerox15@gmail.com<br>
                                    <strong>Dirección:</strong> Venezuela<br>
                                    <strong>Teléfono:</strong> +58 424-253-6795
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