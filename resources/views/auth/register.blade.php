@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900">
                Crear Cuenta
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                ¿Ya tienes una cuenta? 
                <a href="{{ route('login') }}" class="font-medium text-green-600 hover:text-green-500">
                    Inicia sesión aquí
                </a>
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Nombres -->
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700">
                    Nombres
                </label>
                <div class="mt-1">
                    <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus
                           class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>
                @error('first_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Apellidos -->
            <div class="mt-4">
                <label for="last_name" class="block text-sm font-medium text-gray-700">
                    Apellidos
                </label>
                <div class="mt-1">
                    <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required
                           class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>
                @error('last_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mt-4">
                <label for="email" class="block text-sm font-medium text-gray-700">
                    Correo Electrónico
                </label>
                <div class="mt-1">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                           class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Identificación -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">
                    Identificación
                </label>
                <div class="mt-1 flex gap-2">
                    <select name="identification_type" required
                            class="appearance-none w-24 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <option value="V" {{ old('identification_type') == 'V' ? 'selected' : '' }}>V</option>
                        <option value="E" {{ old('identification_type') == 'E' ? 'selected' : '' }}>E</option>
                        <option value="J" {{ old('identification_type') == 'J' ? 'selected' : '' }}>J</option>
                        <option value="G" {{ old('identification_type') == 'G' ? 'selected' : '' }}>G</option>
                    </select>
                    <input type="text" name="identification_number" value="{{ old('identification_number') }}" required
                           class="appearance-none flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                           placeholder="Número de identificación">
                </div>
                @error('identification_type')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('identification_number')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Teléfono -->
            <div class="mt-4">
                <label for="phone" class="block text-sm font-medium text-gray-700">
                    Teléfono
                </label>
                <div class="mt-1">
                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required
                           class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>
                @error('phone')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Dirección -->
            <div class="mt-4">
                <label for="address" class="block text-sm font-medium text-gray-700">
                    Dirección
                </label>
                <div class="mt-1">
                    <input id="address" type="text" name="address" value="{{ old('address') }}" required
                           class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>
                @error('address')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sector -->
            <div class="mt-4">
                <label for="sector" class="block text-sm font-medium text-gray-700">
                    Sector/Urbanización
                </label>
                <div class="mt-1">
                    <input id="sector" type="text" name="sector" value="{{ old('sector') }}"
                           class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>
                @error('sector')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tipo de Cuenta -->
            <div class="mt-4">
                <label for="role" class="block text-sm font-medium text-gray-700">
                    Tipo de Cuenta
                </label>
                <div class="mt-1">
                    <select id="role" name="role" required
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <option value="">Seleccione un tipo</option>
                        <option value="buyer" {{ old('role') == 'buyer' ? 'selected' : '' }}>Comprador</option>
                        <option value="seller" {{ old('role') == 'seller' ? 'selected' : '' }}>Vendedor</option>
                    </select>
                </div>
                @error('role')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Campos adicionales para vendedores -->
            <div id="seller-fields" class="mt-4 hidden">
                <div class="space-y-4">
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700">
                            Nombre de la Empresa
                        </label>
                        <div class="mt-1">
                            <input id="company_name" type="text" name="company_name" value="{{ old('company_name') }}"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                        @error('company_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="company_rif" class="block text-sm font-medium text-gray-700">
                            RIF de la Empresa
                        </label>
                        <div class="mt-1">
                            <input id="company_rif" type="text" name="company_rif" value="{{ old('company_rif') }}"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                        @error('company_rif')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contraseña -->
            <div class="mt-4">
                <label for="password" class="block text-sm font-medium text-gray-700">
                    Contraseña
                </label>
                <div class="mt-1">
                    <input id="password" type="password" name="password" required
                           class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirmar Contraseña -->
            <div class="mt-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                    Confirmar Contraseña
                </label>
                <div class="mt-1">
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>
            </div>

            <div class="mt-6">
                <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Registrarse
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const sellerFields = document.getElementById('seller-fields');

        function toggleSellerFields() {
            if (roleSelect.value === 'seller') {
                sellerFields.classList.remove('hidden');
            } else {
                sellerFields.classList.add('hidden');
            }
        }

        roleSelect.addEventListener('change', toggleSellerFields);
        toggleSellerFields(); // Para manejar el estado inicial
    });
</script>
@endpush

@endsection 