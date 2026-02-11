<?php

/**
 * Repositorio de usuarios basado en Base de Datos.
 *
 * Implementación de {@see \App\Contracts\IUserRepository} que utiliza el Query Builder
 * de Laravel para interactuar con la tabla `users` en MySQL/MariaDB.
 *
 * Consideraciones de seguridad:
 * - Se usa Query Builder con parámetros (evita concatenación de SQL con datos del usuario).
 *
 * Manejo de errores:
 * - Se captura {@see \Illuminate\Database\QueryException}, se registra el error en logs
 *   y se lanza {@see \App\Exceptions\DatabaseUnavailableException} con un mensaje genérico.
 *
 * @package App\Repositories\Db
 */

namespace App\Repositories\Db;

use App\Contracts\IUserRepository;
use App\Exceptions\DatabaseUnavailableException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Repositorio de usuarios usando MySQL/MariaDB (tabla `users`).
 *
 * Proporciona métodos de consulta e inserción/actualización para la gestión de usuarios.
 * Este repositorio se usa, entre otros, para login/registro y para actualizar preferencias
 * (por ejemplo, el tema).
 */
class UserRepositoryDb implements IUserRepository
{
    /**
     * Obtiene todos los usuarios.
     *
     * Se usa para listados o depuración (no para autenticación).
     *
     * @return array<int, array<string, mixed>> Lista de usuarios como arrays asociativos.
     * @throws DatabaseUnavailableException Si ocurre un error de base de datos.
     */
    public function all(): array
    {
        try {
            return DB::table('users')
                ->orderBy('idUser')
                ->get()
                ->map(fn ($u) => (array) $u)
                ->toArray();
        } catch (QueryException $e) {
            Log::error('DB error en UserRepositoryDb::all', ['error' => $e->getMessage()]);
            throw new DatabaseUnavailableException('No se ha podido acceder a la base de datos. Inténtalo más tarde.');
        }
    }

    /**
     * Busca un usuario por email.
     *
     * Se usa en login/registro para comprobar si existe un usuario con el email dado.
     *
     * @param string $email Email del usuario (se pasa como parámetro en la consulta).
     * @return array<string, mixed>|null Usuario encontrado o null si no existe.
     * @throws DatabaseUnavailableException Si ocurre un error de base de datos.
     */
    public function findByEmail(string $email): ?array
    {
        try {
            $row = DB::table('users')
                ->where('email', $email)
                ->first();

            return $row ? (array)$row : null;
        } catch (QueryException $e) {
            Log::error('DB error en UserRepositoryDb::findByEmail', ['error' => $e->getMessage()]);
            throw new DatabaseUnavailableException('No se ha podido acceder a la base de datos. Inténtalo más tarde.');
        }
    }

    /**
     * Inserta un nuevo usuario en la base de datos.
     *
     * Campos esperados en $user:
     * - name (string)
     * - email (string)
     * - password_hash (string)
     * - role (string)
     * - theme (string) [opcional, por defecto 'light']
     *
     * @param array<string, mixed> $user Datos del usuario a insertar.
     * @return void
     * @throws DatabaseUnavailableException Si ocurre un error de base de datos.
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
     * Actualiza los datos de un usuario existente.
     *
     * Se identifica por email (clave única en el sistema). En la aplicación se actualiza
     * principalmente el tema, y también puede actualizarse nombre/rol.
     *
     * Campos usados por este método:
     * - email (string) [obligatorio]
     * - name (string) [obligatorio en esta implementación]
     * - role (string) [obligatorio en esta implementación]
     * - theme (string) [opcional, por defecto 'light']
     *
     * @param array<string, mixed> $user Datos del usuario a actualizar.
     * @return void
     * @throws DatabaseUnavailableException Si ocurre un error de base de datos.
     */
    public function update(array $user): void
    {
        try {
            DB::table('users')
                ->where('email', $user['email'])
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
