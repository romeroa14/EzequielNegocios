@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0 bg-gradient-to-br from-green-50 to-green-100">
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
                <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    <option value="buyer">Comprador</option>
                    <option value="seller">Vendedor</option>
                </select>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <!-- Nombre -->
            <div>
                <x-input-label for="first_name" :value="__('Nombres')" />
                <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus autocomplete="first_name" />
                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
            </div>

            <!-- Apellido -->
            <div>
                <x-input-label for="last_name" :value="__('Apellidos')" />
                <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autocomplete="last_name" />
                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
            </div>

            <!-- Email -->
            <div>
                <x-input-label for="email" :value="__('Correo Electrónico')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Teléfono -->
            <div>
                <x-input-label for="phone" :value="__('Teléfono')" />
                <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" required autocomplete="tel" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <!-- Tipo de Identificación -->
            <div>
                <x-input-label for="identification_type" :value="__('Tipo de Identificación')" />
                <select id="identification_type" name="identification_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
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

            <!-- Información de Empresa (solo para vendedores) -->
            <div id="company-info" class="hidden space-y-6">
                <div>
                    <x-input-label for="company_name" :value="__('Nombre de la Empresa')" />
                    <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" />
                    <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="company_rif" :value="__('RIF de la Empresa')" />
                    <x-text-input id="company_rif" class="block mt-1 w-full" type="text" name="company_rif" :value="old('company_rif')" />
                    <x-input-error :messages="$errors->get('company_rif')" class="mt-2" />
                </div>
            </div>

            <!-- Contraseña -->
            <div>
                <x-input-label for="password" :value="__('Contraseña')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirmar Contraseña -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500" href="{{ route('login') }}">
                    {{ __('¿Ya estás registrado?') }}
                </a>

                <x-primary-button class="ml-4">
                    {{ __('Registrarse') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('role').addEventListener('change', function() {
        const companyInfo = document.getElementById('company-info');
        if (this.value === 'seller') {
            companyInfo.classList.remove('hidden');
        } else {
            companyInfo.classList.add('hidden');
        }
    });
</script>
@endpush

@endsection 