@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0 bg-gradient-to-br from-green-50 to-green-100">
    <!-- Banner superior -->
    <x-ad-sense-banner type="banner" />
    
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

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <!-- Tipo de Usuario -->
            <div>
                <x-input-label for="role" :value="__('Tipo de Usuario')" />
                <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="buyer" {{ old('role') == 'buyer' ? 'selected' : '' }}>Comprador</option>
                    <option value="seller" {{ old('role') == 'seller' ? 'selected' : '' }}>Vendedor</option>
                </select>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
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

            <!-- Dirección -->
            <div class="border-t border-gray-200 pt-4">
                
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

                <div class="mt-4">
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
            <div id="company-info" class="hidden border-t border-gray-200 pt-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Información de la Empresa</h3>

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
                </div>

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
    const companyInfo = document.getElementById('company-info');

    roleSelect.addEventListener('change', function() {
        const role = this.value;
        if (role === 'seller') {
            companyInfo.classList.remove('hidden');
        } else {
            companyInfo.classList.add('hidden');
        }
    });

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