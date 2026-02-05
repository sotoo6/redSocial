@extends('layout')

@section('title', 'Mensajes no válidos')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/homeStyle.css') }}">
@endsection

@section('content')
<div class="home-container">
    <h1>· Mensajes no válidos ·</h1>

    <a href="{{ url('/moderation') }}" class="volver-btn">
        Volver a moderación
    </a>

    <div id="mensajes-publicados">
        @if (empty($rejectedMessages))
            <p style="margin-top:20px; font-size:1.1em; opacity:0.8;">
                No hay mensajes no válidos registrados.
            </p>
        @else
            <ul>
                @foreach ($rejectedMessages as $m)
                    <li class="mensaje">
                        <p class="autor">{{ $m['author'] }}</p>
                        <p class="asignatura">{{ $m['subject'] }}</p>
                        <p class="texto">{!! nl2br(e($m['content'])) !!}</p>
                        <em style="color:grey; font-size:12px;">
                            {{ $m['createdAt'] ?? '' }}
                        </em>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection