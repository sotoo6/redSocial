<?php

namespace App\Repositories\Json;

use App\Contracts\IMessageRepository;

/**
 * Repositorio que implementa IMessageRepository guardando los mensajes en un archivo JSON.
 * Sustituye una BD por almacenamiento en: storage/app/data/messages.json
 */
class MessageRepositoryJson implements IMessageRepository
{
    // Ruta absoluta al archivo JSON donde se guardan los mensajes
    private string $file;

    public function __construct()
    {
        // storage_path() construye una ruta dentro de /storage en Laravel
        $this->file = storage_path('app/data/messages.json');

        // Si no existe el archivo, lo creamos vacío
        if (!file_exists($this->file)) {
            @mkdir(dirname($this->file), 0775, true);
            file_put_contents($this->file, json_encode([], JSON_PRETTY_PRINT));
        }
    }

    /**
     * Devuelve todos los mensajes del JSON como array.
     * Si el archivo está vacío o no es JSON válido, devuelve [].
     */
    public function all(): array
    {
        $data = json_decode(file_get_contents($this->file) ?: '[]', true);
        return is_array($data) ? $data : [];
    }

    /**
     * Busca un mensaje por id.
     * Compara como string para evitar problemas si el id es numérico o string.
     */
    public function find(string $id): ?array
    {
        foreach ($this->all() as $m) {
            if ((string)($m['id'] ?? '') === (string)$id) {
                return $m;
            }
        }
        return null;
    }

    /**
     * Guarda un mensaje nuevo.
     * Si no trae 'id', se lo asigna autoincremental (max(id)+1).
     * Luego lo añade al final y reescribe el JSON completo.
     */
    public function save(array $message): void
    {
        $messages = $this->all();

        // Si el mensaje ya tiene un ID, no lo tocamos
        if (!isset($message['id'])) {
            // id autoincremental solo si no hay id previo
            $nextId = 1;
            if (!empty($messages)) {
                // extrae ids existentes, se queda con los numéricos y calcula el siguiente
                $ids = array_filter(array_map(fn($m) => $m['id'] ?? null, $messages));
                $numericIds = array_filter($ids, 'is_numeric');
                if (!empty($numericIds)) {
                    $nextId = max($numericIds) + 1;
                }
            }
            $message['id'] = $nextId;
        }

        $messages[] = $message;

        // Persiste todo el array en el archivo (sobrescribe el contenido)
        file_put_contents($this->file, json_encode($messages, JSON_PRETTY_PRINT));
    }

    /**
     * Actualiza un mensaje existente: recorre todos, encuentra el mismo id y lo reemplaza.
     * Después vuelve a escribir el JSON completo.
     */
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

    /**
     * Devuelve mensajes con status = 'published'.
     * array_values() reindexa el array (0,1,2...) tras el filtrado.
     */
    public function getPublished(): array
    {
        return array_values(
            array_filter($this->all(), fn($m) => ($m['status'] ?? '') === 'published')
        );
    }

    /** Devuelve mensajes con status = 'pending'. */
    public function getPending(): array
    {
        return array_values(
            array_filter($this->all(), fn($m) => ($m['status'] ?? '') === 'pending')
        );
    }

    /**
     * Aprueba un mensaje: lo busca por id, cambia status a 'published' y lo actualiza.
     * Si no existe, no hace nada.
     */
    public function approve(string|int $id): void
    {
        $msg = $this->find((string)$id);
        if (!$msg) return;

        $msg['status'] = 'published';
        $this->update($msg);
    }

    /**
     * Rechaza un mensaje: lo busca por id, cambia status a 'rejected' y lo actualiza.
     */
    public function reject(string|int $id): void
    {
        $msg = $this->find((string)$id);
        if (!$msg) return;

        $msg['status'] = 'rejected';
        $this->update($msg);
    }

     /** Devuelve mensajes con status = 'rejected'. */
    public function getRejected(): array
    {
        return array_values(
            array_filter($this->all(), fn($m) => ($m['status'] ?? '') === 'rejected')
        );
    }
}