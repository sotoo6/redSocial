<?php

namespace App\Repositories\Db;

use App\Contracts\IUserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use App\Exceptions\DatabaseUnavailableException;

/**
 * Repositorio de usuarios usando MySQL/MariaDB (tabla `users`).
 *
 * Protección contra SQL Injection:
 * - Se usa Query Builder (DB::table()) con WHERE/INSERT/UPDATE parametrizados.
 * - No se construyen consultas concatenando strings con inputs del usuario.
 *
 * Manejo de errores de conexión/consulta:
 * - Captura QueryException y lanza DatabaseUnavailableException con mensaje genérico.
 * - Registra el detalle técnico en logs (storage/logs/laravel.log).
 */
class UserRepositoryDb implements IUserRepository
{
    /**
     * Devuelve todos los usuarios.
     * Se usa para listados o depuración (no para login).
     */
    public function all(): array
    {
        try {
            return DB::table('users')
                ->orderBy('idUser')
                ->get()
                ->map(fn($u) => (array)$u)
                ->toArray();
        } catch (QueryException $e) {
            Log::error('DB error en UserRepositoryDb::all', ['error' => $e->getMessage()]);
            throw new DatabaseUnavailableException('No se ha podido acceder a la base de datos. Inténtalo más tarde.');
        }
    }

    /**
     * Busca un usuario por email.
     * Se usa en login/registro para comprobar si existe.
     */
    public function findByEmail(string $email): ?array
    {
        try {
            $row = DB::table('users')
                ->where('email', $email) // parametrizado (evita injection)
                ->first();

            return $row ? (array)$row : null;
        } catch (QueryException $e) {
            Log::error('DB error en UserRepositoryDb::findByEmail', ['error' => $e->getMessage()]);
            throw new DatabaseUnavailableException('No se ha podido acceder a la base de datos. Inténtalo más tarde.');
        }
    }

    /**
     * Inserta un nuevo usuario en la base de datos.
     * Espera: name, email, password_hash, role (y opcional theme).
     */
    public function save(array $user): void
    {
        try {
            DB::table('users')->insert([
                'name'          => $user['name'],
                'email'         => $user['email'],
                'password_hash' => $user['password_hash'],
                'role'          => $user['role'],
                'theme'         => $user['theme'] ?? 'light',
                'createdAt'     => now(),
            ]);
        } catch (QueryException $e) {
            Log::error('DB error en UserRepositoryDb::save', ['error' => $e->getMessage()]);
            throw new DatabaseUnavailableException('No se ha podido guardar el usuario. Inténtalo más tarde.');
        }
    }

    /**
     * Actualiza datos de un usuario existente.
     * En tu app se actualiza sobre todo el tema (y opcionalmente nombre/rol).
     * Se identifica por email (porque es UNIQUE).
     */
    public function update(array $user): void
    {
        try {
            DB::table('users')
                ->where('email', $user['email']) // parametrizado (evita injection)
                ->update([
                    'name'  => $user['name'],
                    'role'  => $user['role'],
                    'theme' => $user['theme'] ?? 'light',
                ]);
        } catch (QueryException $e) {
            Log::error('DB error en UserRepositoryDb::update', ['error' => $e->getMessage()]);
            throw new DatabaseUnavailableException('No se ha podido actualizar el usuario. Inténtalo más tarde.');
        }
    }
}
