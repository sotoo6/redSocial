@extends('layout')

@section('title', 'Iniciar sesión')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/loginStyle.css') }}">
@endsection

@section('content')
<div class="login-container">
    <h1>Inicia sesión</h1>

    <form method="POST" action="{{ url('/login') }}">
        @csrf

        <label>E-mail</label>
        <input type="email" name="email" placeholder="Introduce tu e-mail" required>

        <label>Contraseña</label>
        <input type="password" name="password" placeholder="Introduce tu contraseña" required>

        <button type="submit">ENTRAR</button>
    </form>

    @if ($errors->any())
        <p class="error-msg">{{ $errors->first() }}</p>
    @endif

    <p class="register-link">
        ¿No tienes cuenta?
        <a href="{{ url('/register') }}">Regístrate aquí</a>
    </p>
</div>
@endsection