<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Red Social')</title>

    <link rel="stylesheet" href="{{ asset('css/headerStyle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeStyle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modalStyle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">

    @yield('styles')
</head>

@php
    $theme = session('user.theme') ?? cookie('theme', 'light');
@endphp

<body class="{{ $theme }}">
    @if(session()->has('user'))
        @include('partials.header')
    @endif

    <div class="container">

        {{-- Mensajes globales (errores BD / avisos generales) --}}
        @if(session('dbError'))
            <div class="alert alert-danger" style="margin: 12px 0;">
                {{ session('dbError') }}
            </div>
        @endif

        @if(session('status'))
            <div class="alert alert-success msg-estado" style="margin: 12px 0;">
                {{ session('status') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" style="margin: 12px 0;">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>

    <script src="{{ asset('js/hash.js') }}"></script>
</body>
</html>