<?php

/**
 * Modelo: Usuario.
 *
 * Clase utilizada para estructurar datos y convertirlos a arrays para 
 * persistencia en repositorios.
 *
 * @package App\Models
 */

namespace App\Models;

/**
 * Representa un usuario en la aplicación.
 *
 * @package App\\Models
 */
class User
{
    /** @var string Nombre visible del usuario. */
    public string $name;

    /** @var string Email del usuario. */
    public string $email;

    /** @var string Hash/valor de contraseña. */
    public string $password;

    /** @var string Rol del usuario (alumno|profesor). */
    public string $role;

    /**
     * Crea una instancia de usuario.
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @param string $role
     */
    public function __construct(string $name, string $email, string $password, string $role)
    {
        $this->name     = $name;
        $this->email    = $email;
        $this->password = $password;
        $this->role     = $role;
    }

    /**
     * Convierte el usuario a array para su persistencia o uso en vistas.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $this->password,
            'role'     => $this->role,
        ];
    }
}