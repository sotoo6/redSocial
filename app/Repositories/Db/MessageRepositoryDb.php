<?php

namespace App\Repositories\Db;

use App\Contracts\IMessageRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

/**
 * Repositorio de mensajes usando SGBD (MySQL/MariaDB) con especialización de borrado:
 * - messages.isDeleted = 1
 * - messages.deletedAt guarda cuándo se eliminó
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
            // Si falla la BD, devolvemos array vacío para no romper la app
            return [];
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
                ->where('messages.idMessage', (int)$id)
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
            // Si falla la consulta, devolvemos null
            return null;
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
            // Si falla la inserción, no hacemos nada (evita pantalla de error)
        }
    }

    /**
     * Actualiza un mensaje existente.
     * Acepta el id con clave 'id' o 'idMessage' (compatibilidad).
     * También acepta 'text' (antiguo) como equivalente de 'content'.
     */
    public function update(array $message): void
    {
        // Sacamos el id del mensaje (puede venir como 'id' o 'idMessage')
        $id = $message['idMessage'] ?? $message['id'] ?? null;
        if ($id === null) return;

        // Si viene 'text' (de P11), lo tratamos como 'content'
        $content = $message['content'] ?? $message['text'] ?? null;

        // Construimos el update solo con los campos presentes
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
            // Si falla el update, no hacemos nada (evita pantalla de error)
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
            return [];
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
            return [];
        }
    }

    /**
     * Devuelve mensajes rechazados (status='rejected') y NO borrados.
     * Se usa en listados de rechazados (si tu interfaz lo muestra).
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
            return [];
        }
    }

    /**
     * Aprueba un mensaje: cambia su status a 'published'.
     * Acción típica de moderación (rol profesor).
     */
    public function approve(string|int $id): void
    {
        try {
            DB::table('messages')
                ->where('idMessage', (int)$id)
                ->update(['status' => 'published']);
        } catch (QueryException $e) {
            // no-op
        }
    }

    /**
     * Rechaza un mensaje: cambia su status a 'rejected'.
     * Acción típica de moderación (rol profesor).
     */
    public function reject(string|int $id): void
    {
        try {
            DB::table('messages')
                ->where('idMessage', (int)$id)
                ->update(['status' => 'rejected']);
        } catch (QueryException $e) {
            // no-op
        }
    }

    /**
     * Borrado lógico del mensaje (especialización):
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
                    'status' => 'deleted',
                ]);
        } catch (QueryException $e) {
            // no-op (o registrar el error en logs si se quiere)
        }
    }
}
