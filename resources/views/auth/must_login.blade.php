@extends('layout')

@section('title', 'Acceso restringido')

@section('content')
<div style="max-width: 700px; margin: 0 auto; padding: 40px; text-align:center;">
    <h1>Debes autenticarte para poder acceder a la aplicación.</h1>
    <p>Inicia sesión para acceder a esta sección.</p>

    <a href="{{ url('/login') }}" style="display:inline-block; margin-top: 15px;">
        Ir a iniciar sesión
    </a>
</div>
@endsection