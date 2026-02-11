<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\IUserRepository;
use App\Models\User;
use App\Exceptions\DatabaseUnavailableException;

class AuthController extends Controller
{
    private IUserRepository $users;

    /**
     * Inyecta el repositorio de usuarios (DB/JSON según implementación activa).
     *
     * @param IUserRepository $users Repositorio que gestiona las operaciones CRUD de usuarios.
     */
    public function __construct(IUserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * Muestra el formulario de registro (GET /register).
     *
     * @return \Illuminate\View\View
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Procesa el registro de un usuario (POST /register).
     *
     * - Valida datos.
     * - Comprueba email duplicado.
     * - Aplica password_hash() (bcrypt) al hash SHA-256 recibido desde el frontend.
     * - Inserta el usuario mediante el repositorio.
     * - Regenera sesión y redirige a login.
     *
     * @param  Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'], // llega SHA-256 desde JS
            'role'     => ['required', 'in:alumno,profesor'],
        ]);

        try {
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
            $payload['password_hash'] = $passwordHash;
            unset($payload['password']);

            $this->users->save($payload);

        } catch (DatabaseUnavailableException $e) {
            // Mensaje friendly para el usuario + detalle opcional para depurar
            return back()
                ->withInput()
                ->withErrors(['db' => 'No se puede conectar con la base de datos ahora mismo. Inténtalo más tarde.']);
        } catch (\Throwable $e) {
            // Cualquier otro error inesperado
            return back()
                ->withInput()
                ->withErrors(['error' => 'Ha ocurrido un error inesperado. Inténtalo más tarde.']);
        }

        // Rotar ID de sesión
        $request->session()->regenerate();

        return redirect('/login')->with('status', 'Usuario registrado correctamente');
    }

    /**
     * Muestra el formulario de login (GET /login).
     *
     * @return \Illuminate\View\View
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Procesa el inicio de sesión (POST /login).
     *
     * - Valida datos.
     * - Busca usuario por email.
     * - Verifica password_verify() con el hash almacenado (bcrypt) contra SHA-256 recibido.
     * - Regenera sesión.
     * - Guarda datos mínimos en sesión y cookie del tema.
     *
     * @param  Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'], // llega SHA-256 desde JS
        ]);

        try {
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

        } catch (DatabaseUnavailableException $e) {
            return back()
                ->withInput()
                ->withErrors(['db' => 'No se puede conectar con la base de datos ahora mismo. Inténtalo más tarde.']);
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Ha ocurrido un error inesperado. Inténtalo más tarde.']);
        }

        // Rotar ID
        $request->session()->regenerate();

        // Guardamos datos mínimos en sesión
        $request->session()->put('user', [
            'idUser' => $user['idUser'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
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
     * Cierra la sesión (POST /logout).
     *
     * @param  Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $request->session()->forget('user');

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
