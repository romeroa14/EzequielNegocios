@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0 bg-gradient-to-br from-green-50 to-green-100">
    <div class="w-full sm:max-w-2xl mt-6 px-6 py-8 bg-white shadow-xl rounded-xl">
        <div class="mb-8 text-center">
            <h2 class="text-3xl font-bold text-gray-900">
                Completar Perfil
            </h2>
            <p class="mt-3 text-sm text-gray-600">
                Por favor completa tu información personal para verificar tu cuenta
            </p>
        </div>

        @if (session('warning'))
        <div class="mb-4 p-4 rounded-lg bg-yellow-50 border border-yellow-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-yellow-800">
                        {{ session('warning') }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('profile.complete.store') }}" class="space-y-6" id="complete-profile-form">
            @csrf

            <!-- Información Personal -->
            <div class="border-t border-gray-200 pt-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Información Personal</h3>
                
                <!-- Información del Perfil -->
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center mb-3">
                        <svg class="h-6 w-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <h4 class="text-lg font-semibold text-green-900">Perfil Completo</h4>
                    </div>
                    <p class="text-sm text-green-800 mb-3">
                        Con este perfil podrás <strong>vender</strong> tus productos, <strong>comprar</strong> de otros productores y <strong>consultar</strong> precios de mercado.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs text-green-700">
                        <div class="flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Vender productos</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Comprar productos</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Consultar precios</span>
                        </div>
                    </div>
                    <input type="hidden" name="role" value="seller">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nombre -->
                    <div>
                        <x-input-label for="first_name" :value="__('Nombres')" />
                        <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name', $person->first_name)" required autofocus />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>

                    <!-- Apellido -->
                    <div>
                        <x-input-label for="last_name" :value="__('Apellidos')" />
                        <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name', $person->last_name)" required />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>
                </div>

                <!-- Teléfono -->
                <div class="mt-4">
                    <x-input-label for="phone" :value="__('Teléfono')" />
                    <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone', $person->phone)" required />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <!-- Tipo de Identificación -->
                    <div>
                        <x-input-label for="identification_type" :value="__('Tipo de Identificación')" />
                        <select id="identification_type" name="identification_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="V" {{ old('identification_type', $person->identification_type) == 'V' ? 'selected' : '' }}>V</option>
                            <option value="E" {{ old('identification_type', $person->identification_type) == 'E' ? 'selected' : '' }}>E</option>
                            <option value="J" {{ old('identification_type', $person->identification_type) == 'J' ? 'selected' : '' }}>J</option>
                            <option value="G" {{ old('identification_type', $person->identification_type) == 'G' ? 'selected' : '' }}>G</option>
                        </select>
                        <x-input-error :messages="$errors->get('identification_type')" class="mt-2" />
                    </div>

                    <!-- Número de Identificación -->
                    <div>
                        <x-input-label for="identification_number" :value="__('Número de Identificación')" />
                        <x-text-input id="identification_number" class="block mt-1 w-full" type="text" name="identification_number" :value="old('identification_number', $person->identification_number)" required />
                        <x-input-error :messages="$errors->get('identification_number')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Dirección -->
            <div class="border-t border-gray-200 pt-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Dirección</h3>
                
                <!-- Ubicación -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <!-- Estado -->
                    <div>
                        <x-input-label for="state_id" :value="__('Estado')" />
                        <select id="state_id" name="state_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Selecciona un estado</option>
                            @foreach ($states ?? [] as $state)
                                <option value="{{ $state->id }}" {{ old('state_id', $person->state_id) == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('state_id')" class="mt-2" />
                    </div>

                    <!-- Municipio -->
                    <div>
                        <x-input-label for="municipality_id" :value="__('Municipio')" />
                        <select id="municipality_id" name="municipality_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Selecciona un municipio</option>
                            @if($person->municipality_id)
                                @foreach($municipalities ?? [] as $municipality)
                                    <option value="{{ $municipality->id }}" {{ old('municipality_id', $person->municipality_id) == $municipality->id ? 'selected' : '' }}>
                                        {{ $municipality->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <x-input-error :messages="$errors->get('municipality_id')" class="mt-2" />
                    </div>

                    <!-- Parroquia -->
                    <div>
                        <x-input-label for="parish_id" :value="__('Parroquia')" />
                        <select id="parish_id" name="parish_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Selecciona una parroquia</option>
                            @if($person->parish_id)
                                @foreach($parishes ?? [] as $parish)
                                    <option value="{{ $parish->id }}" {{ old('parish_id', $person->parish_id) == $parish->id ? 'selected' : '' }}>
                                        {{ $parish->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <x-input-error :messages="$errors->get('parish_id')" class="mt-2" />
                    </div>
                </div>

                <!-- Dirección -->
                <div class="mt-4">
                    <x-input-label for="address" :value="__('Dirección')" />
                    <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $person->address)" required />
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="sector" :value="__('Sector')" />
                    <x-text-input id="sector" class="block mt-1 w-full" type="text" name="sector" :value="old('sector', $person->sector)" required />
                    <x-input-error :messages="$errors->get('sector')" class="mt-2" />
                </div>
            </div>

            <!-- Contraseña (solo para usuarios de Google OAuth sin contraseña) -->
            @if($person->google_id && !$person->password)
            <div class="border-t border-gray-200 pt-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Configurar Contraseña</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Como te registraste con Google, puedes crear una contraseña para acceder también con email y contraseña.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="password" :value="__('Nueva Contraseña')" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" minlength="8" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        <p class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres</p>
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" minlength="8" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>
            </div>
            @endif

            <div class="flex items-center justify-end mt-6">
                <x-primary-button class="ml-4">
                    {{ __('Completar Perfil') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stateSelect = document.getElementById('state_id');
    const municipalitySelect = document.getElementById('municipality_id');
    const parishSelect = document.getElementById('parish_id');

    // El rol está fijo como 'seller', no necesitamos JavaScript para manejo de roles

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
