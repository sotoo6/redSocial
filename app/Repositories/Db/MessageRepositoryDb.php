<?php

/**
 * Repositorio de mensajes basado en Base de Datos.
 *
 * Implementación de {@see \App\Contracts\IMessageRepository} que utiliza el Query Builder
 * de Laravel para consultar e insertar datos en las tablas de MySQL/MariaDB.
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

use App\Contracts\IMessageRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use App\Exceptions\DatabaseUnavailableException;

/**
 * Repositorio de mensajes usando SGBD (MySQL/MariaDB).
 *
 * Proporciona operaciones de lectura y escritura sobre la tabla `messages`
 * (y `users` para obtener el nombre del autor) siguiendo un esquema de
 * borrado lógico mediante los campos `isDeleted` y `deletedAt`.
 */
class MessageRepositoryDb implements IMessageRepository
{
    /**
     * Obtiene todos los mensajes no borrados (independientemente de su estado).
     * Se usa para listados generales. Incluye el nombre del autor mediante join con `users`.
     *
     * @return array<int, array<string, mixed>> Lista de mensajes como arrays asociativos.
     * @throws DatabaseUnavailableException Si ocurre un error de base de datos.
     */
    public function all(): array
    {
        try {
            return DB::table('messages')
                ->join('users', 'messages.idUser', '=', 'users.idUser')
                ->where('messages.isDeleted', 0)
                ->orderByDesc('messages.idMessage')
                ->get([
                    'messages.idMessage as id',
                    'messages.idUser as idUser',
                    'users.name as author',
                    'messages.subject',
                    'messages.content',
                    'messages.status',
                    'messages.createdAt',
                ])
                ->map(fn ($m) => (array) $m)
                ->toArray();
        } catch (QueryException $e) {
            Log::error('DB error en MessageRepositoryDb::all', ['error' => $e->getMessage()]);
            throw new DatabaseUnavailableException('No se ha podido acceder a la base de datos. Inténtalo más tarde.');
        }
    }

    /**
     * Busca un mensaje por su identificador.
     *
     * Incluye información de borrado lógico (`isDeleted`, `deletedAt`) para permitir
     * comprobaciones de permisos o depuración.
     *
     * @param string $id Identificador del mensaje (se castea a int para la consulta).
     * @return array<string, mixed>|null Mensaje encontrado o null si no existe.
     * @throws DatabaseUnavailableException Si ocurre un error de base de datos.
     */
    public function find(string $id): ?array
    {
        try {
            $row = DB::table('messages')
                ->join('users', 'messages.idUser', '=', 'users.idUser')
                ->where('messages.idMessage', (int) $id)
                ->first([
                    'messages.idMessage as id',
                    'messages.idUser as idUser',
                    'users.name as author',
                    'messages.subject',
                    'messages.content',
                    'messages.status',
                    'messages.createdAt',
                    'messages.isDeleted',
                    'messages.deletedAt',
                ]);

            return $row ? (array) $row : null;
        } catch (QueryException $e) {
            Log::error('DB error en MessageRepositoryDb::find', ['error' => $e->getMessage()]);
            throw new DatabaseUnavailableException('No se ha podido acceder a la base de datos. Inténtalo más tarde.');
        }
    }

    /**
     * Inserta un mensaje nuevo en la tabla `messages`.
     *
     * Campos esperados en $message:
     * - idUser (int|string)
     * - subject (string)
     * - content (string)
     * - status (string) [opcional, por defecto 'pending']
     *
     * @param array<string, mixed> $message Datos del mensaje a insertar.
     * @return void
     * @throws DatabaseUnavailableException Si ocurre un error de base de datos.
     */
    public function save(array $message): void
    {
        try {
            DB::table('messages')->insert([
                'idUser'    => $message['idUser'],
                'subject'   => $message['subject'],
                'content'   => $message['content'],
                'status'    => $message['status'] ?? 'pending',
                'createdAt' => now(),
                'isDeleted' => 0,
                'deletedAt' => null,
            ]);
        } catch (QueryException $e) {
            Log::error('DB error en MessageRepositoryDb::save', ['error' => $e->getMessage()]);
            throw new DatabaseUnavailableException('No se ha podido guardar cambios en la base de datos. Inténtalo más tarde.');
        }
    }

    /**
     * Actualiza un mensaje existente.
     *
     * Compatibilidad de claves:
     * - id: acepta 'id' o 'idMessage'
     * - contenido: acepta 'content' o 'text'
     *
     * Solo actualiza las claves presentes (subject/content/status). Si no hay campos a actualizar,
     * o si no hay id, no realiza ninguna operación.
     *
     * @param array<string, mixed> $message Datos del mensaje a actualizar.
     * @return void
     * @throws DatabaseUnavailableException Si ocurre un error de base de datos.
     */
    public function update(array $message): void
    {
        $id = $message['idMessage'] ?? $message['id'] ?? null;
        if ($id === null) return;

        $content = $message['content'] ?? $message['text'] ?? null;

        $update = [];
        if (isset($message['subject'])) $update['subject'] = $message['subject'];
        if ($content !== null) $update['content'] = $content;
        if (isset($message['status'])) $update['status'] = $message['status'];

        if (empty($update)) return;

        try {
            DB::table('messages')
                ->where('idMessage', (int)$id)
                ->update($update);
        } catch (QueryException $e) {
            Log::error('DB error en MessageRepositoryDb::update', ['error' => $e->getMessage()]);
            throw new DatabaseUnavailableException('No se ha podido guardar cambios en la base de datos. Inténtalo más tarde.');
        }
    }

    /**
     * Obtiene los mensajes publicados y no borrados.
     *
     * Se usa típicamente en la portada (home).
     *
     * @return array<int, array<string, mixed>> Lista de mensajes publicados.
     * @throws DatabaseUnavailableException Si ocurre un error de base de datos.
     */
    public function getPublished(): array
    {
        try {
            return DB::table('messages')
                ->join('users', 'messages.idUser', '=', 'users.idUser')
                ->where('messages.status', 'published')
                ->where('messages.isDeleted', 0)
                ->orderByDesc('messages.idMessage')
                ->get([
                    'messages.idMessage as id',
                    'messages.idUser as idUser',
                    'users.name as author',
                    'messages.subject',
                    'messages.content',
                    'messages.status',
                    'messages.createdAt',
                ])
                ->map(fn($m) => (array)$m)
                ->toArray();
        } catch (QueryException $e) {
            Log::error('DB error en MessageRepositoryDb::getPublished', ['error' => $e->getMessage()]);
            throw new DatabaseUnavailableException('No se ha podido acceder a la base de datos. Inténtalo más tarde.');
        }
    }

    /**
     * Obtiene los mensajes pendientes y no borrados.
     *
     * Se usa en la vista de moderación.
     *
     * @return array<int, array<string, mixed>> Lista de mensajes pendientes.
     * @throws DatabaseUnavailableException Si ocurre un error de base de datos.
     */
    public function getPending(): array
    {
        try {
            return DB::table('messages')
                ->join('users', 'messages.idUser', '=', 'users.idUser')
                ->where('messages.status', 'pending')
                ->where('messages.isDeleted', 0)
                ->orderByDesc('messages.idMessage')
                ->get([
                    'messages.idMessage as id',
                    'messages.idUser as idUser',
                    'users.name as author',
                    'messages.subject',
                    'messages.content',
                    'messages.status',
                    'messages.createdAt',
                ])
                ->map(fn($m) => (array)$m)
                ->toArray();
        } catch (QueryException $e) {
            Log::error('DB error en MessageRepositoryDb::getPending', ['error' => $e->getMessage()]);
            throw new DatabaseUnavailableException('No se ha podido acceder a la base de datos. Inténtalo más tarde.');
        }
    }

    /**
     * Obtiene los mensajes rechazados y no borrados.
     *
     * @return array<int, array<string, mixed>> Lista de mensajes rechazados.
     * @throws DatabaseUnavailableException Si ocurre un error de base de datos.
     */
    public function getRejected(): array
    {
        try {
            return DB::table('messages')
                ->join('users', 'messages.idUser', '=', 'users.idUser')
                ->where('messages.status', 'rejected')
                ->where('messages.isDeleted', 0)
                ->orderByDesc('messages.idMessage')
                ->get([
                    'messages.idMessage as id',
                    'messages.idUser as idUser',
                    'users.name as author',
                    'messages.subject',
                    'messages.content',
                    'messages.status',
                    'messages.createdAt',
                ])
                ->map(fn($m) => (array)$m)
                ->toArray();
        } catch (QueryException $e) {
            Log::error('DB error en MessageRepositoryDb::getRejected', ['error' => $e->getMessage()]);
            throw new DatabaseUnavailableException('No se ha podido acceder a la base de datos. Inténtalo más tarde.');
        }
    }

    /**
     * Aprueba un mensaje (cambia su estado a 'published').
     *
     * @param string|int $id Identificador del mensaje.
     * @return void
     * @throws DatabaseUnavailableException Si ocurre un error de base de datos.
     */
    public function approve(string|int $id): void
    {
        try {
            DB::table('messages')
                ->where('idMessage', (int)$id)
                ->update(['status' => 'published']);
        } catch (QueryException $e) {
            Log::error('DB error en MessageRepositoryDb::approve', ['error' => $e->getMessage()]);
            throw new DatabaseUnavailableException('No se ha podido guardar cambios en la base de datos. Inténtalo más tarde.');
        }
    }

    /**
     * Rechaza un mensaje (cambia su estado a 'rejected').
     *
     * @param string|int $id Identificador del mensaje.
     * @return void
     * @throws DatabaseUnavailableException Si ocurre un error de base de datos.
     */
    public function reject(string|int $id): void
    {
        try {
            DB::table('messages')
                ->where('idMessage', (int) $id)
                ->update(['status' => 'rejected']);
        } catch (QueryException $e) {
            Log::error('DB error en MessageRepositoryDb::reject', ['error' => $e->getMessage()]);
            throw new DatabaseUnavailableException('No se ha podido guardar cambios en la base de datos. Inténtalo más tarde.');
        }
    }

    /**
     * Realiza el borrado lógico de un mensaje.
     *
     * Cambios aplicados:
     * - isDeleted = 1
     * - deletedAt = now()
     * - status = 'deleted'
     *
     * @param string|int $id Identificador del mensaje.
     * @return void
     * @throws DatabaseUnavailableException Si ocurre un error de base de datos.
     */
    public function delete(string|int $id): void
    {
        try {
            DB::table('messages')
                ->where('idMessage', (int)$id)
                ->update([
                    'isDeleted' => 1,
                    'deletedAt' => now(),
                    'status'    => 'deleted',
                ]);
        } catch (QueryException $e) {
            Log::error('DB error en MessageRepositoryDb::delete', ['error' => $e->getMessage()]);
            throw new DatabaseUnavailableException('No se ha podido guardar cambios en la base de datos. Inténtalo más tarde.');
        }
    }
}