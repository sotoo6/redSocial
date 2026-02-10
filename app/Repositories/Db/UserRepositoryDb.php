<?php

namespace App\Repositories\Db;

use App\Contracts\IUserRepository;
use Illuminate\Support\Facades\DB;

/**
 * Repositorio de usuarios usando MySQL/MariaDB (tabla `users`).
 */
class UserRepositoryDb implements IUserRepository
{
    /**
     * Devuelve todos los usuarios.
     * Se usa para listados o depuración (no para login).
     */
    public function all(): array
    {
        return DB::table('users')
            ->orderBy('idUser')
            ->get()
            ->map(fn($u) => (array)$u)
            ->toArray();
    }

    /**
     * Busca un usuario por email.
     * Se usa en login/registro para comprobar si existe.
     */
    public function findByEmail(string $email): ?array
    {
        $row = DB::table('users')->where('email', $email)->first();
        return $row ? (array)$row : null;
    }

    /**
     * Inserta un nuevo usuario en la base de datos.
     * Espera: name, email, password_hash, role (y opcional theme).
     */
    public function save(array $user): void
    {
        DB::table('users')->insert([
            'name' => $user['name'],
            'email' => $user['email'],
            'password_hash' => $user['password_hash'],
            'role' => $user['role'],
            'theme' => $user['theme'] ?? 'light',
            'createdAt' => now(),
        ]);
    }

    /**
     * Actualiza datos de un usuario existente.
     * En tu app se actualiza sobre todo el tema (y opcionalmente nombre/rol).
     * Se identifica por email (porque es UNIQUE).
     */
    public function update(array $user): void
    {
        DB::table('users')
            ->where('email', $user['email'])
            ->update([
                'name' => $user['name'],
                'role' => $user['role'],
                'theme' => $user['theme'] ?? 'light',
            ]);
    }
}
