@extends('layout')

@section('content')

<h1>· Mensajes publicados ·</h1>

{{-- Filtro por asignatura --}}
<form method="GET" action="{{ url('/') }}" class="filtro-asignatura" style="margin-bottom: 20px;">
    <label for="subject-filter">Filtrar por asignatura:</label>

    <select name="subject" id="subject-filter">
        <option value="todas" {{ request('subject','todas') === 'todas' ? 'selected' : '' }}>Todas</option>

        @foreach ($subjects as $subj)
            <option value="{{ $subj }}" {{ request('subject','todas') === $subj ? 'selected' : '' }}>
                {{ $subj }}
            </option>
        @endforeach
    </select>

    <button type="submit" id="button-filter">Aplicar filtro</button>
</form>

<div class="mensajes-publicados">
    @forelse ($messages as $m)
        <div class="mensaje">
            <h2>{{ $m['author'] }}</h2>
            <p class="asignatura">{{ $m['subject'] }}</p>
            <p>{{ $m['text'] }}</p>
            <em style="color:grey; font-size:12px;">
                {{ $m['createdAt'] ?? '' }}
            </em>

            {{-- Botón borrar (solo si el mensaje es del usuario logueado) --}}
            @if(session('user.name') === ($m['author'] ?? ''))
                <form method="POST"
                      action="{{ url('/messages/' . $m['id'] . '/delete') }}"
                      onsubmit="return confirm('¿Seguro que quieres borrar este mensaje?');"
                      style="margin-top: 10px;">
                    @csrf
                    <button type="submit" class="btn-delete">Borrar</button>
                </form>
            @endif
        </div>
    @empty
        <p style="text-align: center;">No hay mensajes publicados todavía.</p>
    @endforelse
</div>

@endsection