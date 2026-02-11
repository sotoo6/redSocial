<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\IUserRepository;
use App\Exceptions\DatabaseUnavailableException;

class ThemeController extends Controller
{
    private IUserRepository $users;

    /**
     * Inyecta el repositorio de usuarios (DB/JSON según implementación activa).
     *
     * @param IUserRepository $users
     */
    public function __construct(IUserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * Alterna el tema (light/dark) del usuario logueado:
     * - Actualiza la sesión
     * - Actualiza el campo theme en la BD (si existe el usuario)
     * - Escribe cookie 'theme' (30 días)
     * - Redirige a ?return=... o a /
     *
     * @param  Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

        try {
            // Guardar también en el repositorio (DB/JSON)
            $existing = $this->users->findByEmail($user['email']);
            if ($existing) {
                $existing['theme'] = $new;
                $this->users->update($existing);
            }
        } catch (DatabaseUnavailableException $e) {
            // Si la BD cae, no rompemos la app: mantenemos el tema en sesión/cookie
            // y mostramos un aviso.
            if ($request->has('return')) {
                return redirect($request->query('return'))
                    ->with('dbError', 'No se pudo guardar el tema en la base de datos. Se aplicó solo para esta sesión.');
            }

            return redirect('/')
                ->with('dbError', 'No se pudo guardar el tema en la base de datos. Se aplicó solo para esta sesión.');
        } catch (\Throwable $e) {
            // Error inesperado: mismo comportamiento para no romper la navegación
            if ($request->has('return')) {
                return redirect($request->query('return'))
                    ->with('error', 'Error inesperado al guardar el tema.');
            }

            return redirect('/')
                ->with('error', 'Error inesperado al guardar el tema.');
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
