<?php

namespace App\Repositories\Db;

use App\Contracts\IMessageRepository;
use Illuminate\Support\Facades\DB;

/**
 * Repositorio de mensajes usando SGBD (MySQL/MariaDB) con especialización de borrado:
 * - messages.isDeleted = 1
 * - deletedMessages guarda deletedAt con la misma PK (idMessage)
 */
class MessageRepositoryDb implements IMessageRepository
{
    /**
     * Devuelve todos los mensajes NO borrados (cualquier estado).
     */
    public function all(): array
    {
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
    }

    /**
     * Busca un mensaje por id (incluye borrados).
     */
    public function find(string $id): ?array
    {
        $row = DB::table('messages')
            ->join('users', 'messages.idUser', '=', 'users.idUser')
            ->where('messages.idMessage', $id)
            ->first([
                'messages.idMessage as id',
                'messages.idUser as idUser',
                'users.name as author',
                'messages.subject',
                'messages.content',
                'messages.status',
                'messages.createdAt',
                'messages.isDeleted',
            ]);

        return $row ? (array)$row : null;
    }

    /**
     * Guarda un mensaje nuevo.
     * Espera $message['idUser'], ['subject'], ['content'] y opcional ['status'].
     */
    public function save(array $message): void
    {
        DB::table('messages')->insert([
            'idUser' => $message['idUser'],
            'subject' => $message['subject'],
            'content' => $message['content'],
            'status' => $message['status'] ?? 'pending',
            'createdAt' => now(),
            'isDeleted' => 0,
        ]);
    }

    /**
     * Actualiza subject/content/status de un mensaje.
     * Acepta id con clave 'id' o 'idMessage' (por compatibilidad).
     */
    public function update(array $message): void
    {
        $id = $message['idMessage'] ?? $message['id'] ?? null;
        if ($id === null) return;

        // si viene 'text' (viejo) lo usamos como content
        $content = $message['content'] ?? $message['text'] ?? null;

        $update = [];

        if (isset($message['subject'])) $update['subject'] = $message['subject'];
        if ($content !== null) $update['content'] = $content;

        // No tocar status si no viene (para no romper moderación)
        if (isset($message['status'])) $update['status'] = $message['status'];

        if (empty($update)) return;

        DB::table('messages')
            ->where('idMessage', $id)
            ->update($update);
    }

    /**
     * Mensajes publicados NO borrados.
     */
    public function getPublished(): array
    {
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
    }

    /**
     * Mensajes pendientes NO borrados.
     */
    public function getPending(): array
    {
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
    }

    public function getRejected(): array
    {
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
    }

    public function approve(string|int $id): void
    {
        DB::table('messages')
            ->where('idMessage', $id)
            ->update(['status' => 'published']);
    }

    public function reject(string|int $id): void
    {
        DB::table('messages')
            ->where('idMessage', $id)
            ->update(['status' => 'rejected']);
    }

    /**
     * "Borrado" por especialización:
     * - marca isDeleted=1 en messages
     * - crea/actualiza fila en deletedMessages con deletedAt
     */
    public function delete(string|int $id): void
    {
        DB::transaction(function () use ($id) {
            DB::table('messages')
                ->where('idMessage', $id)
                ->update(['isDeleted' => 1]);

            DB::table('deletedMessages')->updateOrInsert(
                ['idMessage' => $id],
                ['deletedAt' => now()]
            );
        });
    }
}
