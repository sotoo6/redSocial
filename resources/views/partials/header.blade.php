{{-- resources/views/partials/header.blade.php --}}

@php
    $theme = request()->cookie('theme') ?? (session('user.theme') ?? 'light');

    $user = session('user'); // ['name'=>..., 'role'=>..., 'theme'=>...]
    $returnUrl = url()->full(); // equivalente a $_SERVER['REQUEST_URI'] pero completo
@endphp

<style>
    body {
        background-color: {{ $theme === 'dark' ? '#111' : '#fff' }};
        color: {{ $theme === 'dark' ? '#fff' : '#111' }};
    }

    header {
        background: {{ $theme === 'dark' ? '#222' : '#fff' }};
        border-bottom: 1px solid {{ $theme === 'dark' ? '#444' : '#ffffff91' }};
        position: fixed !important;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 999;
    }
</style>

<header class="main-header {{ $theme === 'dark' ? 'dark-mode' : '' }}">
    <div class="header-left">
        {{-- Saludo --}}
        <strong>¡Hola {{ e($user['name'] ?? '') }}!</strong>

        {{-- Navegación --}}
        <a href="{{ route('home') }}">Inicio</a>
        <a href="{{ url('/messages/new') }}">Nuevo mensaje</a>

        {{-- Moderación solo profes --}}
        @if (($user['role'] ?? '') === 'profe' || ($user['role'] ?? '') === 'profesor')
            <a href="{{ url('/moderation') }}">Moderación</a>
        @endif
    </div>

    <div class="header-right">
        {{-- Cambiar tema (necesitas tener esta ruta hecha) --}}
        <a href="{{ url('/theme/toggle') . '?return=' . urlencode($returnUrl) }}">
            Cambiar tema
        </a>

        {{-- Logout por POST --}}
        <form method="POST" action="{{ url('/logout') }}"
              onsubmit="return confirm('¿Seguro que deseas cerrar sesión?');">
            @csrf
            <button type="submit" class="logout-btn">Cerrar sesión</button>
        </form>
    </div>
</header>