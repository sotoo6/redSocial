<?php

/**
 * Modelo: Mensaje.
 *
 * Clase utilizada para estructurar datos y convertirlos a arrays para 
 * persistencia en repositorios.
 *
 * @package App\Models
 */

namespace App\Models;

/**
 * Representa un mensaje en la aplicación.
 *
 * @package App\\Models
 */
class Message
{
    /** @var string Identificador del mensaje. */
    public string $id;

    /** @var string Autor (nombre) del mensaje. */
    public string $author;

    /** @var string Asignatura/tema del mensaje. */
    public string $subject;

    /** @var string Contenido del mensaje. */
    public string $text;

    /** @var string Estado (pending|published|rejected|deleted). */
    public string $status;

    /** @var string Fecha/hora de creación. */
    public string $createdAt;


    /**
     * Crea una instancia de mensaje.
     *
     * @param string $id
     * @param string $author
     * @param string $subject
     * @param string $text
     * @param string $status
     * @param string|null $createdAt
     */
    public function __construct(
        string $id,
        string $author,
        string $subject,
        string $text,
        string $status = 'pending',
        string $createdAt = null
    ) {
        $this->id      = $id;
        $this->author  = $author;
        $this->subject = $subject;
        $this->text    = $text;
        $this->status  = $status;
        $this->createdAt = $createdAt ?? date('Y-m-d H:i:s');
    }

    /**
     * Convierte el mensaje a array para su persistencia o uso en vistas.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id'      => $this->id,
            'author'  => $this->author,
            'subject' => $this->subject,
            'text'    => $this->text,
            'status'  => $this->status,
            'createdAt' => $this->createdAt,
        ];
    }
}