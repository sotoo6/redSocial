<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Red Social')</title>

    <link rel="stylesheet" href="{{ asset('css/headerStyle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeStyle.css') }}">

    @yield('styles')
</head>
<body>
    @if(session()->has('user'))
        @include('partials.header')
    @endif

    <div class="container">
        @yield('content')
    </div>

    <script src="{{ asset('js/hash.js') }}"></script>
</body>
</html>