<?php

/**
 * Contrato del repositorio de usuarios.
 *
 * Define las operaciones básicas para gestionar usuarios independientemente
 * del sistema de almacenamiento (Base de Datos).
 *
 * @package App\Contracts
 */

namespace App\Contracts;

/**
 * Interfaz para repositorios de usuarios.
 *
 * @package App\Contracts
 */
interface IUserRepository
{
    /**
     * Obtiene todos los usuarios.
     * @return array<int, array<string, mixed>>
     */
    public function all(): array;

    /**
     * Busca un usuario por email.
     *
     * @param string $email Email del usuario.
     * @return array<string, mixed>|null
     */
    public function findByEmail(string $email): ?array;

    /**
     * Guarda un usuario nuevo.
     *
     * @param array<string, mixed> $user Datos del usuario.
     * @return void
     */
    public function save(array $user): void;

    /**
     * Actualiza un usuario existente.
     *
     * @param array<string, mixed> $user Datos del usuario.
     * @return void
     */
    public function update(array $user): void;
}