@extends('layouts.admin')

@section('page-title', 'Catalogo Tecnico')
@section('page-kicker', 'Productos, variantes y stock directo')

@section('content')
    <div class="page-content">
        @livewire('product-manager')
    </div>
@endsection
