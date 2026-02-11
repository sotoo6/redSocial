<?php

/**
 * Repositorio de usuarios basado en JSON.
 *
 * Implementa IUserRepository persistiendo datos en un fichero JSON dentro de
 * storage/app/data.
 *
 * @package App\Repositories\Json
 */

namespace App\Repositories\Json;

use App\Contracts\IUserRepository;

/**
 * Repositorio que implementa IUserRepository guardando usuarios en un archivo JSON
 * en lugar de una base de datos.
 */
class UserRepositoryJson implements IUserRepository
{
    // Ruta al archivo JSON donde se almacenan los usuarios
    /** @var string Ruta absoluta al fichero JSON de usuarios. */
    private string $file;

    /**
     * Inicializa la ruta del fichero JSON de usuarios.
     * @return void
     */

    public function __construct()
    {   
        // Ruta dentro de /storage/app/data/users.json (Laravel)
        $this->file = storage_path('app/data/users.json');
    }

    /**
     * Devuelve todos los usuarios del JSON.
     * Si el archivo no existe, devuelve [].
     */
    public function all(): array
    {
        if (!file_exists($this->file)) return [];
        // true => array asociativo; si falla el decode, devuelve [] con el ??
        return json_decode(file_get_contents($this->file), true) ?? [];
    }

    /**
     * Busca un usuario por email.
     * Recorre todos y devuelve el primero que coincida exactamente.
     */
    public function findByEmail(string $email): ?array
    {
        foreach ($this->all() as $u) {
            if ($u['email'] === $email) {
                return $u;
            }
        }
        return null;
    }

    /**
     * Guarda un usuario nuevo: lo añade al final del array y reescribe el JSON completo.
     */
    public function save(array $user): void
    {
        $users = $this->all();
        $users[] = $user;
        file_put_contents($this->file, json_encode($users, JSON_PRETTY_PRINT));
    }

    /**
     * Actualiza un usuario existente, identificándolo por email.
     * Reemplaza todo el registro por $userData y luego reescribe el JSON.
     */
    public function update(array $userData): void
    {
        $users = $this->all();

        foreach ($users as &$u) {
            if (($u['email'] ?? '') === ($userData['email'] ?? '')) {
                $u = $userData;
                break;
            }
        }

        file_put_contents($this->file, json_encode($users, JSON_PRETTY_PRINT));
    }
}