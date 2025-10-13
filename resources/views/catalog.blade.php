@extends('layouts.app')

@section('title', 'Catálogo de Productos - EzequielNegocios')

@section('content')
    <!-- Banner superior -->
    <x-ad-sense-banner type="banner" />
    
    @livewire('product-catalog', ['productId' => $productId ?? null])
    
    <!-- Banner inferior -->
    <x-ad-sense-banner type="banner" />
@endsection 