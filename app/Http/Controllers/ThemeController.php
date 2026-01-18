<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\IUserRepository;

class ThemeController extends Controller
{
    private IUserRepository $users;

    public function __construct(IUserRepository $users)
    {
        $this->users = $users;
    }

    public function toggle(Request $request)
    {
        // Si el usuario no está logueado, redirige al login
        if (!session()->has('user')) {
            return redirect('/login');
        }

        // Obtener usuario actual desde sesión
        $user = session('user');

        // Tema actual (por defecto "light")
        $current = $user['theme'] ?? 'light';
        $new = ($current === 'dark') ? 'light' : 'dark';

        // Actualizar en sesión
        $user['theme'] = $new;
        session(['user' => $user]);

        // Guardar también en el repositorio (users.json)
        $existing = $this->users->findByEmail($user['email']);
        if ($existing) {
            $existing['theme'] = $new;
            $this->users->update($existing);
        }

        // Guardar cookie (30 días)
        cookie()->queue('theme', $new, 60 * 24 * 30, null, null, false, true);

        // Redirigir a la página anterior si existe
        if ($request->has('return')) {
            return redirect($request->query('return'));
        }

        // Si no, al home
        return redirect('/');
    }
}