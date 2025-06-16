@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4">Gestión de Productos</h1>
        @livewire('seller.products-crud')
    </div>
@endsection 