<?php

/**
 * Repositorio de mensajes basado en JSON.
 *
 * Implementa IMessageRepository persistiendo datos en un fichero JSON dentro de
 * storage/app/data.
 *
 * @package App\Repositories\Json
 */

namespace App\Repositories\Json;

use App\Contracts\IMessageRepository;

/**
 * Repositorio que implementa IMessageRepository guardando los mensajes en un archivo JSON.
 * Sustituye una BD por almacenamiento en: storage/app/data/messages.json
 */
class MessageRepositoryJson implements IMessageRepository
{
    // Ruta absoluta al archivo JSON donde se guardan los mensajes
    /** @var string Ruta absoluta al fichero JSON de mensajes. */
    private string $file;
    private string $deletedFile;

    /**
     * Inicializa la ruta del fichero JSON de mensajes.
     * @return void
     */

    public function __construct()
    {
        $this->file = storage_path('app/data/messages.json');
        $this->deletedFile = storage_path('app/data/deleted_messages.json');

        // Asegurar carpeta
        $dir = dirname($this->file);
        if (!file_exists($dir)) {
            mkdir($dir, 0775, true);
        }

        // Crear archivos si no existen
        if (!file_exists($this->file)) {
            file_put_contents($this->file, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
        }

        if (!file_exists($this->deletedFile)) {
            file_put_contents($this->deletedFile, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
        }
    }

    /**
     * Devuelve todos los mensajes del JSON como array.
     * Si el archivo está vacío o no es JSON válido, devuelve [].
     */
    public function all(): array
    {
        return $this->readJsonFile($this->file);
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

        if (!isset($message['id'])) {
            $nextId = 1;

            if (!empty($messages)) {
                $ids = array_map(fn($m) => $m['id'] ?? null, $messages);
                $numericIds = array_filter($ids, 'is_numeric');

                if (!empty($numericIds)) {
                    $nextId = max($numericIds) + 1;
                }
            }

            $message['id'] = $nextId;
        }

        $messages[] = $message;
        $this->writeJsonFile($this->file, $messages);
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

        $this->writeJsonFile($this->file, $messages);
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

    /**
     * Elimina (lógicamente) un mensaje o lo marca como borrado.
     *
     * @param string|int $id
     * @return void
     */

    public function delete(string|int $id): void
    {
        $id = (string)$id;

        $messages = $this->all();

        $deletedMsg = null;
        $remaining = [];

        foreach ($messages as $m) {
            if ((string)($m['id'] ?? '') === $id) {
                $deletedMsg = $m;
            } else {
                $remaining[] = $m;
            }
        }

        if ($deletedMsg === null) {
            return;
        }

        // Guardar activos sin el borrado
        $this->writeJsonFile($this->file, array_values($remaining));

        // Añadir a borrados
        $deleted = $this->readJsonFile($this->deletedFile);

        // marcar estado y fecha de borrado
        $deletedMsg['status'] = 'delete';
        $deletedMsg['deleted_at'] = date('c');

        $deleted[] = $deletedMsg;

        $this->writeJsonFile($this->deletedFile, $deleted);
    }

    /**
     * Devuelve mensajes con status 'rejected'.
     * @return array<int, array<string, mixed>>
     */

    public function getRejected(): array
    {
        return array_values(
            array_filter($this->all(), fn($m) => ($m['status'] ?? '') === 'rejected')
        );
    }

    private function readJsonFile(string $path): array
    {
        if (!file_exists($path)) {
            return [];
        }

        $raw = file_get_contents($path);
        if ($raw === false || trim($raw) === '') {
            return [];
        }

        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    private function writeJsonFile(string $path, array $data): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            $json = '[]';
        }

        file_put_contents($path, $json, LOCK_EX);
    }
}