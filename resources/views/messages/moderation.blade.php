@extends('layout')

@section('title', 'Moderación')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/moderationStyle.css') }}">
@endsection

@section('content')
<div class="moderation-container">
    <h1>Mensajes pendientes de moderar</h1>

    <a class="history-button" href="{{ url('/moderation/invalid') }}">
        Ver historial de mensajes no válidos
    </a>

    {{-- Si no hay mensajes pendientes --}}
    @if (empty($pendingMessages))
        <p>No hay mensajes pendientes.</p>
    @else
        <ul>
            @foreach ($pendingMessages as $m)
                <li class="pendiente">
                    {{-- Autor --}}
                    <strong>{{ $m['author'] }}</strong>

                    {{-- Asignatura / categoría --}}
                    ({{ $m['subject'] }}):
                    
                    {{-- Contenido del mensaje --}}
                    {{ $m['content'] }}
                    <br>
                    <em style="color:grey; font-size:12px;">
                            {{ $m['createdAt'] ?? '' }}
                        </em>

                    {{-- Botones de acción --}}
                    <div class="botones">
                        {{-- Aprobar --}}
                        <form method="POST" action="{{ url('/moderation/' . $m['id'] . '/approve') }}">
                            @csrf
                            <button type="submit">Aprobar</button>
                        </form>

                        {{-- Rechazar --}}
                        <form method="POST" action="{{ url('/moderation/' . $m['id'] . '/reject') }}">
                            @csrf
                            <button type="submit">Rechazar</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection