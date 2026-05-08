@extends('layouts.admin')

@section('page-title', 'Árbol de Categorías')
@section('page-kicker', 'Gestión Jerárquica')

@section('content')
    <div class="page-content">
        @livewire('category-manager')
    </div>
@endsection
