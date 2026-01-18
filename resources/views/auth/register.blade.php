<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="{{ asset('css/registerStyle.css') }}">
</head>
<body>

<div id="contenedor">

    <form id="register-form" method="POST" action="{{ url('/register') }}">
        @csrf

        <div class="columna izquierda">
            <h1>Registro de usuario</h1>

            <label>¿Deseas registrarte como usuario o como profesor?</label>
            <select id="selector" name="role">
                <option value="profesor" {{ old('role') === 'profesor' ? 'selected' : '' }}>Profesor</option>
                <option value="alumno" {{ old('role') === 'alumno' ? 'selected' : '' }}>Alumno</option>
            </select>

            @if ($errors->any())
                <p class="error-msg" style="color:#8B0000;">
                    {{ $errors->first() }}
                </p>
            @endif
        </div>

        <div class="columna derecha">

            <label for="name">Nombre completo:</label>
            <input type="text" id="name" name="name" required value="{{ old('name') }}">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required value="{{ old('email') }}">

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Registrar</button>
        </div>
    </form>
</div>

<script src="/js/hash.js"></script>
</html>