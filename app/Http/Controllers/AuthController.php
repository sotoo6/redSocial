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

        // password llega en SHA-256, pero aplicamos password_hash
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        // Creamos el modelo POO
        $user = new User(
            $data['name'],
            $data['email'],
            $passwordHash,
            $data['role']
        );

        // Guardamos en JSON
        $this->users->save($user->toArray());

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

        // Si no existe o la contraseña no coincide
        if (!$user || !password_verify($data['password'], $user['password'])) {
            return back()
                ->withErrors(['email' => 'Credenciales incorrectas'])
                ->withInput();
        }

        // Rotar ID
        $request->session()->regenerate();

        // Guardamos datos mínimos en sesión
        $request->session()->put('user', [
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