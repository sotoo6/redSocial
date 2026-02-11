<?php

/**
 * Contrato del repositorio de mensajes.
 *
 * Define las operaciones necesarias para gestionar mensajes independientemente
 * del sistema de almacenamiento (Base de Datos).
 *
 * @package App\Contracts
 */

namespace App\Contracts;

/**
 * Interfaz para repositorios de mensajes.
 *
 * @package App\Contracts
 */
interface IMessageRepository
{
    /**
     * Obtiene todos los mensajes (según implementación).
     * @return array<int, array<string, mixed>>
     * @throws App\Exceptions\DatabaseUnavailableException
     */
    public function all(): array;

    /**
     * Busca un mensaje por su identificador.
     *
     * @param string $id Identificador del mensaje.
     * @return array<string, mixed>|null
     * @throws App\Exceptions\DatabaseUnavailableException
     */
    public function find(string $id): ?array;

    /**
     * Guarda un mensaje nuevo.
     *
     * @param array<string, mixed> $message Datos del mensaje.
     * @return void
     * @throws App\Exceptions\DatabaseUnavailableException
     */
    public function save(array $message): void;

    /**
     * Actualiza un mensaje existente.
     *
     * @param array<string, mixed> $message Datos del mensaje.
     * @return void
     * @throws App\Exceptions\DatabaseUnavailableException
     */
    public function update(array $message): void;

    /**
     * Obtiene mensajes publicados.
     * @return array<int, array<string, mixed>>
     * @throws App\Exceptions\DatabaseUnavailableException
     */
    public function getPublished(): array;

    /**
     * Obtiene mensajes pendientes de moderación.
     * @return array<int, array<string, mixed>>
     * @throws App\Exceptions\DatabaseUnavailableException
     */
    public function getPending(): array;

    /**
     * Obtiene mensajes rechazados.
     * @return array<int, array<string, mixed>>
     * @throws App\Exceptions\DatabaseUnavailableException
     */
    public function getRejected(): array;

    // Acciones de moderación
    /**
     * Aprueba un mensaje (lo publica).
     *
     * @param string|int $id Identificador del mensaje.
     * @return void
     * @throws App\Exceptions\DatabaseUnavailableException
     */
    public function approve(string|int $id): void;

    /**
     * Rechaza un mensaje.
     *
     * @param string|int $id Identificador del mensaje.
     * @return void
     * @throws App\Exceptions\DatabaseUnavailableException
     */
    public function reject(string|int $id): void;

    /**
     * Realiza el borrado (lógico o físico) de un mensaje.
     *
     * @param string|int $id Identificador del mensaje.
     * @return void
     * @throws App\Exceptions\DatabaseUnavailableException
     */
    public function delete(string|int $id): void;
}