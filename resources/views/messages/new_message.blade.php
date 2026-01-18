@extends('layout')

@section('content')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/newMessageStyle.css') }}">
@endsection

<div class="nuevo-mensaje-container">
<h1>Nuevo mensaje</h1>

<!-- Mostrar errores de validación -->
@if ($errors->any())
    <div style="color: darkred;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="/messages">
    @csrf

    <label for="subject">Asignatura:</label>
    <select name="subject" id="subject">
        <option value="Desarrollo Web Entorno Servidor">Desarrollo Web Entorno Servidor</option>
        <option value="Desarrollo Web Entorno  Cliente">Desarrollo Web Entorno Cliente</option>
        <option value="Diseño Interfaces">Diseño Interfaces</option>
        <option value="Despliegue">Despliegue</option>
        <option value="Digitalización">Digitalización</option>
        <option value="Itinerario Personal">Itinerario Personal</option>
        <option value="Inglés">Inglés</option>
        <option value="Afonamiento">Afondamiento</option>
    </select>

    <br><br>

    <label id="limite" for="text">Mensaje (1-280 caracteres):</label>
    <textarea id="text" name="text" maxlength="280" required></textarea>
    
    <br><br>
    
    <button type="submit">Publicar</button>

    <br><br>

    @if (isset($messagePending))
        <p style="color: {{ isset($rejected) ? 'red' : 'green' }}; margin-top: 10px;">
            {{ isset($rejected)
                ? 'El mensaje contenía contenido no permitido y ha sido enviado al historial de mensajes rechazados.'
                : 'Tu mensaje está pendiente de moderación.'
            }}
        </p>
    @endif
</form>
</div>
@endsection