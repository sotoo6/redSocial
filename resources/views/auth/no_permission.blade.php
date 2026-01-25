@extends('layout')

@section('title', 'Acceso restringido')

@section('content')
<div style="max-width: 700px; margin: 0 auto; padding: 40px; text-align:center;">
    <h1>No tienes permisos para moderar mensajes</h1>
    <p>Solo los usuarios con rol <span style="font-weight: bold;">PROFESOR</span> pueden acceder a la moderación.</p>

    <a href="{{ url('/') }}" style="display:inline-block; margin-top: 15px;">
        Volver al inicio
    </a>
</div>
@endsection