<?php

namespace App\Repositories\Db;

use App\Contracts\IUserRepository;
use Illuminate\Support\Facades\DB;

/**
 * Repositorio de usuarios usando MySQL
 */
class UserRepositoryDb implements IUserRepository
{
    public function all(): array
    {
        return DB::table('users')
            ->orderBy('idUser')
            ->get()
            ->map(fn($u) => (array)$u)
            ->toArray();
    }

    public function findByEmail(string $email): ?array
    {
        $row = DB::table('users')->where('email', $email)->first();
        return $row ? (array)$row : null;
    }

    public function save(array $user): void
    {
        DB::table('users')->insert([
            'name' => $user['name'],
            'email' => $user['email'],
            'password_hash' => $user['password_hash'],
            'role' => $user['role'],
            'createdAt' => now(),
        ]);
    }

    public function update(array $user): void
    {
        // buscamos por email porque tu app trabaja mucho con email
        DB::table('users')
            ->where('email', $user['email'])
            ->update([
                'name' => $user['name'],
                'role' => $user['role'],
                'theme' => $user['theme'] ?? 'light',
            ]);
    }
}