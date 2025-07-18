@extends('layouts.app')

@section('title', 'Catálogo de Productos - EzequielNegocios')

@section('content')
    <!-- Banner superior -->
    <x-ad-sense-banner type="banner" />
    
    @livewire('product-catalog')
    
    <!-- Banner inferior -->
    <x-ad-sense-banner type="banner" />
@endsection 