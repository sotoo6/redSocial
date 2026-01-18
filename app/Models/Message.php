<?php

namespace App\Models;

class Message
{
    public string $id;
    public string $author;
    public string $subject;
    public string $text;
    public string $status;
    public string $createdAt;

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