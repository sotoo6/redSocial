<?php

namespace App\Contracts;

interface IUserRepository
{
    public function all(): array;
    public function findByEmail(string $email): ?array;
    public function save(array $user): void;
    public function update(array $user): void;
}