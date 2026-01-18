<?php

namespace App\Repositories\Json;

use App\Contracts\IMessageRepository;

class MessageRepositoryJson implements IMessageRepository
{
    private string $file;

    public function __construct()
    {
        $this->file = storage_path('app/data/messages.json');

        // Si no existe el archivo, lo creamos vacío (evita errores)
        if (!file_exists($this->file)) {
            @mkdir(dirname($this->file), 0775, true);
            file_put_contents($this->file, json_encode([], JSON_PRETTY_PRINT));
        }
    }

    public function all(): array
    {
        $data = json_decode(file_get_contents($this->file) ?: '[]', true);
        return is_array($data) ? $data : [];
    }

    public function find(string $id): ?array
    {
        foreach ($this->all() as $m) {
            if ((string)($m['id'] ?? '') === (string)$id) {
                return $m;
            }
        }
        return null;
    }

    public function save(array $message): void
    {
        $messages = $this->all();

        // Si el mensaje ya tiene un ID (por ejemplo uniqid), no lo tocamos
        if (!isset($message['id'])) {
            // id autoincremental solo si no hay id previo
            $nextId = 1;
            if (!empty($messages)) {
                $ids = array_filter(array_map(fn($m) => $m['id'] ?? null, $messages));
                $numericIds = array_filter($ids, 'is_numeric');
                if (!empty($numericIds)) {
                    $nextId = max($numericIds) + 1;
                }
            }
            $message['id'] = $nextId;
        }

        $messages[] = $message;

        file_put_contents($this->file, json_encode($messages, JSON_PRETTY_PRINT));
    }

    public function update(array $message): void
    {
        $messages = $this->all();

        foreach ($messages as &$m) {
            if ((string)($m['id'] ?? '') === (string)($message['id'] ?? '')) {
                $m = $message;
                break;
            }
        }

        file_put_contents($this->file, json_encode($messages, JSON_PRETTY_PRINT));
    }

    public function getPublished(): array
    {
        return array_values(
            array_filter($this->all(), fn($m) => ($m['status'] ?? '') === 'published')
        );
    }

    public function getPending(): array
    {
        return array_values(
            array_filter($this->all(), fn($m) => ($m['status'] ?? '') === 'pending')
        );
    }

    public function approve(string|int $id): void
    {
        $msg = $this->find((string)$id);
        if (!$msg) return;

        $msg['status'] = 'published';
        $this->update($msg);
    }

    public function reject(string|int $id): void
    {
        $msg = $this->find((string)$id);
        if (!$msg) return;

        $msg['status'] = 'rejected';
        $this->update($msg);
    }

    public function getRejected(): array
    {
        return array_values(
            array_filter($this->all(), fn($m) => ($m['status'] ?? '') === 'rejected')
        );
    }
}