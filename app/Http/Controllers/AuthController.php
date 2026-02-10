<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\IUserRepository;
use App\Models\User;

class AuthController extends Controller
{
    private IUserRepository $users;

    /**
     * Inyectamos el repositorio de usuarios.
     */
    public function __construct(IUserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * Muestra el formulario de registro (GET).
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Procesa el registro (POST).
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'], // llega SHA-256 desde JS
            'role'     => ['required', 'in:alumno,profesor'],
        ]);

        // Comprobamos si el email está registrado
        $existing = $this->users->findByEmail($data['email']);
        if ($existing) {
            return back()
                ->withErrors(['email' => 'El email ya está registrado'])
                ->withInput();
        }

        // password llega en SHA-256, pero aplicamos password_hash (bcrypt)
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        // Creamos el modelo POO
        $user = new User(
            $data['name'],
            $data['email'],
            $passwordHash,
            $data['role']
        );

        // Guardamos (DB o JSON según el repositorio activo)
        $payload = $user->toArray();

        // Asegura clave correcta para repositorio DB: password_hash
        // (por si User::toArray() devuelve 'password' en vez de 'password_hash')
        $payload['password_hash'] = $passwordHash;
        unset($payload['password']);

        $this->users->save($payload);

        // Rotar ID de sesión
        $request->session()->regenerate();

        return redirect('/login')->with('status', 'Usuario registrado correctamente');
    }

    /**
     * Muestra el formulario de login (GET).
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Procesa login
     */
    public function login(Request $request)
    {
        // Validación
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'], // llega SHA-256 desde JS
        ]);

        // Buscar usuario por email
        $user = $this->users->findByEmail($data['email']);

        // La columna en BD/JSON debe ser password_hash
        $storedHash = $user['password_hash'] ?? null;

        // Si no existe o la contraseña no coincide
        if (!$user || !$storedHash || !password_verify($data['password'], $storedHash)) {
            return back()
                ->withErrors(['email' => 'Credenciales incorrectas'])
                ->withInput();
        }

        // Rotar ID
        $request->session()->regenerate();

        // Guardamos datos mínimos en sesión
        $request->session()->put('user', [
            'idUser' => $user['idUser'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
            'theme' => $user['theme'] ?? 'light',
        ]);

        // Guardar cookie del tema
        cookie()->queue(
            'theme',
            $user['theme'] ?? 'light',
            60 * 24 * 30, // 30 días
            null,
            null,
            false,
            true
        );

        return redirect('/');
    }

    /**
     * Cierra sesión
     */
    public function logout(Request $request)
    {
        // Elimina datos de sesión
        $request->session()->forget('user');

        // Invalida sesión y token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
