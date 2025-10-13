@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0 bg-gradient-to-br from-green-50 to-green-100">
    <!-- Banner superior -->
    <div class="w-full max-w-7xl">
        <x-ad-sense-banner type="banner" />
    </div>
    
    <div class="w-full sm:max-w-2xl mt-6 px-6 py-8 bg-white shadow-xl rounded-xl">
        <div class="mb-8 text-center">
            <h2 class="text-3xl font-bold text-gray-900">
                Crear Nueva Cuenta
            </h2>
            <p class="mt-3 text-sm text-gray-600">
                ¿Ya tienes una cuenta? 
                <a href="{{ route('login') }}" class="font-medium text-green-600 hover:text-green-500 transition-colors duration-200">
                    Inicia sesión aquí
                </a>
            </p>
        </div>

        <!-- Registrarse con Google -->
        <div class="mb-6">
            
            
            <div class="mt-6">
                <a href="{{ route('google.redirect') }}" class="w-full flex items-center justify-center px-4 py-3 border border-gray-200 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 cursor-pointer">
                    <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Registrarse con Google
                </a>
            </div>
        </div>

        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">O registrarse con email</span>
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-6" id="user-registration-form">
            @csrf

            <!-- Información del Perfil -->
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center mb-3">
                    <svg class="h-6 w-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-green-900">Perfil Completo</h3>
                </div>
                <p class="text-sm text-green-800 mb-3">
                    Con este perfil podrás <strong>vender</strong> tus productos, <strong>comprar</strong> de otros productores y <strong>consultar</strong> precios de mercado.
                </p>
                <div class="flex items-center text-sm text-green-700">
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Acceso completo a todas las funcionalidades</span>
                </div>
                <input type="hidden" name="role" value="seller">
            </div>

            <!-- Información Personal -->
            <div class="border-t border-gray-200 pt-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Información Personal</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nombre -->
                    <div>
                        <x-input-label for="first_name" :value="__('Nombres')" />
                        <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>

                    <!-- Apellido -->
                    <div>
                        <x-input-label for="last_name" :value="__('Apellidos')" />
                        <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>
                    </div>

                    <!-- Email -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Correo Electrónico')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Teléfono -->
                <div class="mt-4">
                    <x-input-label for="phone" :value="__('Teléfono')" />
                    <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" required />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <!-- Tipo de Identificación -->
                    <div>
                        <x-input-label for="identification_type" :value="__('Tipo de Identificación')" />
                        <select id="identification_type" name="identification_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="V">V</option>
                            <option value="E">E</option>
                            <option value="J">J</option>
                            <option value="G">G</option>
                        </select>
                        <x-input-error :messages="$errors->get('identification_type')" class="mt-2" />
                    </div>

                    <!-- Número de Identificación -->
                    <div>
                        <x-input-label for="identification_number" :value="__('Número de Identificación')" />
                        <x-text-input id="identification_number" class="block mt-1 w-full" type="text" name="identification_number" :value="old('identification_number')" required />
                        <x-input-error :messages="$errors->get('identification_number')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Anuncio intermedio -->
            <x-ad-sense-banner type="in-article" />

            <!-- Dirección del Productor -->
            <div class="border-t border-gray-200 pt-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Dirección del Productor</h3>
                
                <!-- Ubicación -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <!-- Estado -->
                    <div>
                        <x-input-label for="state_id" :value="__('Estado')" />
                        <select id="state_id" name="state_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Selecciona un estado</option>
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('state_id')" class="mt-2" />
                    </div>

                    <!-- Municipio -->
                    <div>
                        <x-input-label for="municipality_id" :value="__('Municipio')" />
                        <select id="municipality_id" name="municipality_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Selecciona un municipio</option>
                        </select>
                        <x-input-error :messages="$errors->get('municipality_id')" class="mt-2" />
                    </div>

                    <!-- Parroquia -->
                    <div>
                        <x-input-label for="parish_id" :value="__('Parroquia')" />
                        <select id="parish_id" name="parish_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Selecciona una parroquia</option>
                        </select>
                        <x-input-error :messages="$errors->get('parish_id')" class="mt-2" />
                    </div>

                </div>
                <!-- Dirección -->
            <div class="border-t border-gray-200 pt-4 mt-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Dirección del Productor</h3>
                
                <div>
                    <x-input-label for="address" :value="__('Dirección')" />
                    <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" />
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="sector" :value="__('Sector')" />
                    <x-text-input id="sector" class="block mt-1 w-full" type="text" name="sector" :value="old('sector')" />
                    <x-input-error :messages="$errors->get('sector')" class="mt-2" />
                </div>
            </div>
            </div>

            

            <!-- Información de Empresa (solo para vendedores) -->
            <!-- <div id="company-info" class="hidden border-t border-gray-200 pt-4"> -->
                <!-- <h3 class="text-lg font-medium text-gray-900 mb-4">Información de la Empresa</h3>

                <div>
                    <x-input-label for="company_name" :value="__('Nombre de la Empresa')" />
                    <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" />
                    <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="company_rif" :value="__('RIF de la Empresa')" />
                    <x-text-input id="company_rif" class="block mt-1 w-full" type="text" name="company_rif" :value="old('company_rif')" />
                    <x-input-error :messages="$errors->get('company_rif')" class="mt-2" />
                    </div>
                </div> -->

                <!-- Contraseña -->
            <div class="border-t border-gray-200 pt-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Seguridad</h3>

                    <div>
                    <x-input-label for="password" :value="__('Contraseña')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <x-primary-button class="ml-4">
                    {{ __('Registrarse') }}
                </x-primary-button>
            </div>
        </form>
    </div>
    
    <!-- Banner inferior -->
    <x-ad-sense-banner type="banner" />
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const stateSelect = document.getElementById('state_id');
    const municipalitySelect = document.getElementById('municipality_id');
    const parishSelect = document.getElementById('parish_id');
    // const companyInfo = document.getElementById('company-info');

    // roleSelect.addEventListener('change', function() {
    //     const role = this.value;
    //     if (role === 'seller') {
    //         companyInfo.classList.remove('hidden');
    //     } else {
    //         companyInfo.classList.add('hidden');
    //     }
    // });

    stateSelect.addEventListener('change', function() {
        const stateId = this.value;
        municipalitySelect.innerHTML = '<option value="">Selecciona un municipio</option>';
        parishSelect.innerHTML = '<option value="">Selecciona una parroquia</option>';
        
        if (stateId) {
            fetch(`/get-municipalities/${stateId}`)
                .then(response => response.json())
                .then(municipalities => {
                    municipalities.forEach(municipality => {
                        const option = new Option(municipality.name, municipality.id);
                        municipalitySelect.add(option);
                    });
                });
        }
    });

    municipalitySelect.addEventListener('change', function() {
        const municipalityId = this.value;
        parishSelect.innerHTML = '<option value="">Selecciona una parroquia</option>';
        
        if (municipalityId) {
            fetch(`/get-parishes/${municipalityId}`)
                .then(response => response.json())
                .then(parishes => {
                    parishes.forEach(parish => {
                        const option = new Option(parish.name, parish.id);
                        parishSelect.add(option);
                    });
                });
        }
    });
});
</script>
@endpush

@endsection 