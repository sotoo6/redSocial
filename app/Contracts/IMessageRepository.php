<?php

namespace App\Contracts;

interface IMessageRepository
{
    public function all(): array;
    public function find(string $id): ?array;
    public function save(array $message): void;
    public function update(array $message): void;
    public function getPublished(): array;
    public function getPending(): array;
    // Acciones de moderación
    public function approve(string|int $id): void;
    public function reject(string|int $id): void;
}