@extends('layout')

@section('content')

<h1>· Mensajes publicados ·</h1>

@if (session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<div class="mensajes-publicados">

    @forelse ($messages as $m)
        <div class="mensaje">
            <h2>{{ $m['author'] }}</h2>
            <p class="asignatura"> {{ $m['subject'] }} </p>

            <p>{{ $m['text'] }}</p>

            <em style="color:grey; font-size:12px;">
                {{ $m['createdAt'] ?? '' }}
            </em>
        </div>
    @empty
        <p style="text-align: center;">No hay mensajes publicados todavía.</p>
    @endforelse

</div>

@endsection