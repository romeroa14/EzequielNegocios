@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                <h2 class="text-lg font-medium text-gray-900">
                    Información del Perfil
                </h2>

                <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-6" id="profile-form">
                    @csrf
                    @method('PATCH')

                    <!-- Información Personal -->
                    <div>
                        <x-input-label for="first_name" :value="__('Nombre')" />
                        <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $person->first_name)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                    </div>

                    <div>
                        <x-input-label for="last_name" :value="__('Apellido')" />
                        <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $person->last_name)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $person->email)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <!-- Identificación -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="identification_type" :value="__('Tipo de Identificación')" />
                            <select id="identification_type" name="identification_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                <option value="V" {{ old('identification_type', $person->identification_type) == 'V' ? 'selected' : '' }}>V</option>
                                <option value="E" {{ old('identification_type', $person->identification_type) == 'E' ? 'selected' : '' }}>E</option>
                                <option value="J" {{ old('identification_type', $person->identification_type) == 'J' ? 'selected' : '' }}>J</option>
                                <option value="G" {{ old('identification_type', $person->identification_type) == 'G' ? 'selected' : '' }}>G</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('identification_type')" />
                        </div>

                        <div>
                            <x-input-label for="identification_number" :value="__('Número de Identificación')" />
                            <x-text-input id="identification_number" name="identification_number" type="text" class="mt-1 block w-full" :value="old('identification_number', $person->identification_number)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('identification_number')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="phone" :value="__('Teléfono')" />
                        <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" :value="old('phone', $person->phone)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                    </div>

                    <!-- Ubicación -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="state_id" :value="__('Estado')" />
                            <select id="state_id" name="state_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                <option value="">Selecciona un estado</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}" {{ old('state_id', $person->state_id) == $state->id ? 'selected' : '' }}>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('state_id')" />
                        </div>

                        <div>
                            <x-input-label for="municipality_id" :value="__('Municipio')" />
                            <select id="municipality_id" name="municipality_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                <option value="">Selecciona un municipio</option>
                                @foreach ($municipalities as $municipality)
                                    <option value="{{ $municipality->id }}" {{ old('municipality_id', $person->municipality_id) == $municipality->id ? 'selected' : '' }}>
                                        {{ $municipality->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('municipality_id')" />
                        </div>

                        <div>
                            <x-input-label for="parish_id" :value="__('Parroquia')" />
                            <select id="parish_id" name="parish_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                <option value="">Selecciona una parroquia</option>
                                @foreach ($parishes as $parish)
                                    <option value="{{ $parish->id }}" {{ old('parish_id', $person->parish_id) == $parish->id ? 'selected' : '' }}>
                                        {{ $parish->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('parish_id')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="address" :value="__('Dirección')" />
                        <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $person->address)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('address')" />
                    </div>

                    <div>
                        <x-input-label for="sector" :value="__('Sector')" />
                        <x-text-input id="sector" name="sector" type="text" class="mt-1 block w-full" :value="old('sector', $person->sector)" />
                        <x-input-error class="mt-2" :messages="$errors->get('sector')" />
                    </div>

                    @if($person->role === 'seller')
                        <!-- Información de Empresa -->
                        <div>
                            <x-input-label for="company_name" :value="__('Nombre de la Empresa')" />
                            <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" :value="old('company_name', $person->company_name)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
                        </div>

                        <div>
                            <x-input-label for="company_rif" :value="__('RIF de la Empresa')" />
                            <x-text-input id="company_rif" name="company_rif" type="text" class="mt-1 block w-full" :value="old('company_rif', $person->company_rif)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('company_rif')" />
                        </div>
                    @endif

                    <div class="flex items-center gap-4">
                        <x-primary-button type="submit">{{ __('Guardar') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stateSelect = document.getElementById('state_id');
    const municipalitySelect = document.getElementById('municipality_id');
    const parishSelect = document.getElementById('parish_id');
    const profileForm = document.getElementById('profile-form');

    // Mostrar mensaje de éxito si existe
    @if(session('success'))
        Swal.fire({
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#10B981'
        });
    @endif

    // Mostrar errores si existen
    @if($errors->any())
        Swal.fire({
            title: '¡Error!',
            html: `@foreach($errors->all() as $error)<p class="text-sm">{{ $error }}</p>@endforeach`,
            icon: 'error',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#EF4444'
        });
    @endif

    // Confirmar antes de enviar el formulario
    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Se actualizará tu información de perfil',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, actualizar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6B7280',
        }).then((result) => {
            if (result.isConfirmed) {
                profileForm.submit();
            }
        });
    });

    // Código existente para los selects en cascada
    stateSelect.addEventListener('change', function() {
        const stateId = this.value;
        municipalitySelect.innerHTML = '<option value="">Selecciona un municipio</option>';
        parishSelect.innerHTML = '<option value="">Selecciona una parroquia</option>';
        
        if (stateId) {
            fetch(`/profile/municipalities/${stateId}`)
                .then(response => response.json())
                .then(municipalities => {
                    municipalities.forEach(municipality => {
                        const option = new Option(municipality.name, municipality.id);
                        municipalitySelect.add(option);
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: '¡Error!',
                        text: 'Error al cargar los municipios',
                        icon: 'error',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#EF4444'
                    });
                });
        }
    });

    municipalitySelect.addEventListener('change', function() {
        const municipalityId = this.value;
        parishSelect.innerHTML = '<option value="">Selecciona una parroquia</option>';
        
        if (municipalityId) {
            fetch(`/profile/parishes/${municipalityId}`)
                .then(response => response.json())
                .then(parishes => {
                    parishes.forEach(parish => {
                        const option = new Option(parish.name, parish.id);
                        parishSelect.add(option);
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: '¡Error!',
                        text: 'Error al cargar las parroquias',
                        icon: 'error',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#EF4444'
                    });
                });
        }
    });
});
</script>
@endpush

@endsection 