<?php

namespace App\Repositories\Json;

use App\Contracts\IUserRepository;

class UserRepositoryJson implements IUserRepository
{
    private string $file;

    public function __construct()
    {
        $this->file = storage_path('app/data/users.json');
    }

    public function all(): array
    {
        if (!file_exists($this->file)) return [];
        return json_decode(file_get_contents($this->file), true) ?? [];
    }

    public function findByEmail(string $email): ?array
    {
        foreach ($this->all() as $u) {
            if ($u['email'] === $email) {
                return $u;
            }
        }
        return null;
    }

    public function save(array $user): void
    {
        $users = $this->all();
        $users[] = $user;
        file_put_contents($this->file, json_encode($users, JSON_PRETTY_PRINT));
    }
}