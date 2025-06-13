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

            <!-- Información Personal -->
            <div class="bg-white rounded-lg p-6 border border-gray-100 shadow-sm">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Información Personal</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombres -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">
                            Nombres <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required
                                   class="form-input w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors duration-200"
                                   placeholder="Ingrese sus nombres">
                        </div>
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Apellidos -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">
                            Apellidos <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required
                                   class="form-input w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors duration-200"
                                   placeholder="Ingrese sus apellidos">
                        </div>
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Correo Electrónico <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                   class="form-input w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors duration-200"
                                   placeholder="ejemplo@correo.com">
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">
                            Teléfono <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required
                                   class="form-input w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors duration-200"
                                   placeholder="(XXX) XXX-XXXX">
                        </div>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Identificación -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Identificación <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-4">
                        <select name="identification_type" required
                                class="form-select rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors duration-200">
                            <option value="V" {{ old('identification_type') == 'V' ? 'selected' : '' }}>V</option>
                            <option value="E" {{ old('identification_type') == 'E' ? 'selected' : '' }}>E</option>
                            <option value="J" {{ old('identification_type') == 'J' ? 'selected' : '' }}>J</option>
                            <option value="G" {{ old('identification_type') == 'G' ? 'selected' : '' }}>G</option>
                        </select>
                        <input type="text" name="identification_number" value="{{ old('identification_number') }}" required
                               class="form-input flex-1 rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors duration-200"
                               placeholder="Número de identificación">
                    </div>
                    @error('identification_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('identification_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Información de Ubicación -->
            <div class="bg-white rounded-lg p-6 border border-gray-100 shadow-sm">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Información de Ubicación</h3>
                
                <!-- Dirección -->
                <div class="mb-4">
                    <label for="address" class="block text-sm font-medium text-gray-700">
                        Dirección <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input id="address" type="text" name="address" value="{{ old('address') }}" required
                               class="form-input w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors duration-200"
                               placeholder="Ingrese su dirección completa">
                    </div>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sector -->
                <div>
                    <label for="sector" class="block text-sm font-medium text-gray-700">
                        Sector/Urbanización
                    </label>
                    <div class="mt-1">
                        <input id="sector" type="text" name="sector" value="{{ old('sector') }}"
                               class="form-input w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors duration-200"
                               placeholder="Nombre del sector o urbanización">
                    </div>
                    @error('sector')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Información de la Cuenta -->
            <div class="bg-white rounded-lg p-6 border border-gray-100 shadow-sm">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Información de la Cuenta</h3>

                <!-- Tipo de Cuenta -->
                <div class="mb-4">
                    <label for="role" class="block text-sm font-medium text-gray-700">
                        Tipo de Cuenta <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <select id="role" name="role" required
                                class="form-select w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors duration-200">
                            <option value="">Seleccione un tipo</option>
                            <option value="buyer" {{ old('role') == 'buyer' ? 'selected' : '' }}>Comprador</option>
                            <option value="seller" {{ old('role') == 'seller' ? 'selected' : '' }}>Vendedor</option>
                        </select>
                    </div>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Campos adicionales para vendedores -->
                <div id="seller-fields" class="hidden space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700">
                                Nombre de la Empresa <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input id="company_name" type="text" name="company_name" value="{{ old('company_name') }}"
                                       class="form-input w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors duration-200"
                                       placeholder="Nombre de su empresa">
                            </div>
                            @error('company_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="company_rif" class="block text-sm font-medium text-gray-700">
                                RIF de la Empresa <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input id="company_rif" type="text" name="company_rif" value="{{ old('company_rif') }}"
                                       class="form-input w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors duration-200"
                                       placeholder="J-XXXXXXXXX">
                            </div>
                            @error('company_rif')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contraseña -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Contraseña <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input id="password" type="password" name="password" required
                                   class="form-input w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors duration-200"
                                   placeholder="********">
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                            Confirmar Contraseña <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                   class="form-input w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors duration-200"
                                   placeholder="********">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end mt-8">
                <button type="submit"
                        class="w-full md:w-auto px-6 py-3 border border-transparent rounded-lg text-base font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Crear Cuenta
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
                document.querySelectorAll('#seller-fields input').forEach(input => {
                    input.required = true;
                });
            } else {
                sellerFields.classList.add('hidden');
                document.querySelectorAll('#seller-fields input').forEach(input => {
                    input.required = false;
                });
            }
        }

        roleSelect.addEventListener('change', toggleSellerFields);
        toggleSellerFields(); // Para manejar el estado inicial
    });
</script>
@endpush

@endsection 