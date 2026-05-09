@extends('storefront::layouts.master')

@section('content')
<div class="mx-auto max-w-2xl py-12">
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-black text-gray-900">Crear cuenta de cliente</h1>
        <p class="mt-2 text-sm text-gray-500">La cuenta permite reutilizar tus datos en futuros pedidos de la tienda virtual.</p>

        @if($errors->any())
            <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm font-semibold text-red-700">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('storefront.cliente.register.submit') }}" class="mt-6 grid gap-4 md:grid-cols-2">
            @csrf
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-bold text-gray-700">Nombre completo</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required class="w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-3 focus:border-brand focus:ring-brand">
            </div>
            <div>
                <label class="mb-1 block text-sm font-bold text-gray-700">Correo</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-3 focus:border-brand focus:ring-brand">
            </div>
            <div>
                <label class="mb-1 block text-sm font-bold text-gray-700">WhatsApp</label>
                <input type="tel" name="whatsapp" value="{{ old('whatsapp') }}" required class="w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-3 focus:border-brand focus:ring-brand">
            </div>
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-bold text-gray-700">Direccion</label>
                <input type="text" name="direccion" value="{{ old('direccion') }}" class="w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-3 focus:border-brand focus:ring-brand">
            </div>
            <div>
                <label class="mb-1 block text-sm font-bold text-gray-700">Contrasena</label>
                <input type="password" name="password" required class="w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-3 focus:border-brand focus:ring-brand">
            </div>
            <div>
                <label class="mb-1 block text-sm font-bold text-gray-700">Confirmar contrasena</label>
                <input type="password" name="password_confirmation" required class="w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-3 focus:border-brand focus:ring-brand">
            </div>
            <div class="md:col-span-2">
                <button class="w-full rounded-lg bg-ink px-4 py-3 font-bold text-white transition hover:bg-brand">Crear cuenta y continuar</button>
            </div>
        </form>

        <p class="mt-5 text-center text-sm text-gray-500">
            Ya tienes cuenta?
            <a href="{{ route('storefront.cliente.login') }}" class="font-bold text-brand hover:text-brand-dark">Ingresa aqui</a>
        </p>
    </div>
</div>
@endsection
