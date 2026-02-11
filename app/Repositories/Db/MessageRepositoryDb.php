<?php

namespace App\Repositories\Db;

use App\Contracts\IMessageRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use App\Exceptions\DatabaseUnavailableException;

/**
 * Repositorio de mensajes usando SGBD (MySQL/MariaDB).
 *
 * Protección contra SQL Injection:
 * - Se usa Query Builder (DB::table()) con condiciones parametrizadas.
 * - No se concatenan strings SQL con inputs del usuario.
 *
 * Manejo de errores de conexión/consulta:
 * - Captura QueryException, registra el detalle en logs y lanza DatabaseUnavailableException
 *   con mensaje genérico (no expone SQL/host/credenciales).
 */
class MessageRepositoryDb implements IMessageRepository
{
    /**
     * Devuelve todos los mensajes NO borrados (cualquier estado).
     * Se usa para listados generales.
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
                ->map(fn($m) => (array)$m)
                ->toArray();
        } catch (QueryException $e) {
            Log::error('DB error en MessageRepositoryDb::all', ['error' => $e->getMessage()]);
            throw new DatabaseUnavailableException('No se ha podido acceder a la base de datos. Inténtalo más tarde.');
        }
    }

    /**
     * Busca un mensaje por id.
     * Incluye el estado de borrado (isDeleted y deletedAt) por si hay que comprobar permisos o depurar.
     */
    public function find(string $id): ?array
    {
        try {
            $row = DB::table('messages')
                ->join('users', 'messages.idUser', '=', 'users.idUser')
                ->where('messages.idMessage', (int)$id) // cast + parámetro (evita injection)
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

            return $row ? (array)$row : null;
        } catch (QueryException $e) {
            Log::error('DB error en MessageRepositoryDb::find', ['error' => $e->getMessage()]);
            throw new DatabaseUnavailableException('No se ha podido acceder a la base de datos. Inténtalo más tarde.');
        }
    }

    /**
     * Inserta un mensaje nuevo en la tabla `messages`.
     * Espera: idUser, subject, content y opcional status.
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
     * Acepta el id con clave 'id' o 'idMessage' (compatibilidad).
     * También acepta 'text' (antiguo) como equivalente de 'content'.
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
     * Devuelve mensajes publicados (status='published') y NO borrados.
     * Se usa en la portada (home).
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
     * Devuelve mensajes pendientes (status='pending') y NO borrados.
     * Se usa en la vista de moderación del profesor.
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
     * Devuelve mensajes rechazados (status='rejected') y NO borrados.
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
     * Aprueba un mensaje: cambia su status a 'published'.
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
     * Rechaza un mensaje: cambia su status a 'rejected'.
     */
    public function reject(string|int $id): void
    {
        try {
            DB::table('messages')
                ->where('idMessage', (int)$id)
                ->update(['status' => 'rejected']);
        } catch (QueryException $e) {
            Log::error('DB error en MessageRepositoryDb::reject', ['error' => $e->getMessage()]);
            throw new DatabaseUnavailableException('No se ha podido guardar cambios en la base de datos. Inténtalo más tarde.');
        }
    }

    /**
     * Borrado lógico del mensaje:
     * - isDeleted = 1
     * - deletedAt = NOW()
     * - status = 'deleted'
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
