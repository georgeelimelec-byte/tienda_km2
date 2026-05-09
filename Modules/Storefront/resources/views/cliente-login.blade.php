@extends('storefront::layouts.master')

@section('content')
<div class="mx-auto max-w-md py-12">
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-black text-gray-900">Ingresar para pedir</h1>
        <p class="mt-2 text-sm text-gray-500">Usa tu cuenta para precargar tus datos y completar el pedido por WhatsApp.</p>

        @if(session('error'))
            <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm font-semibold text-red-700">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm font-semibold text-red-700">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('storefront.cliente.login.submit') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-bold text-gray-700">Correo</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-3 focus:border-brand focus:ring-brand">
            </div>
            <div>
                <label class="mb-1 block text-sm font-bold text-gray-700">Contrasena</label>
                <input type="password" name="password" required class="w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-3 focus:border-brand focus:ring-brand">
            </div>
            <button class="w-full rounded-lg bg-ink px-4 py-3 font-bold text-white transition hover:bg-brand">Ingresar</button>
        </form>

        <p class="mt-5 text-center text-sm text-gray-500">
            No tienes cuenta?
            <a href="{{ route('storefront.cliente.register') }}" class="font-bold text-brand hover:text-brand-dark">Registrate</a>
        </p>
    </div>
</div>
@endsection
