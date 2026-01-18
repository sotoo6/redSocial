<?php

namespace App\Models;

class User
{
    public string $name;
    public string $email;
    public string $password;
    public string $role;

    public function __construct(string $name, string $email, string $password, string $role)
    {
        $this->name     = $name;
        $this->email    = $email;
        $this->password = $password;
        $this->role     = $role;
    }

    public function toArray(): array
    {
        return [
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $this->password,
            'role'     => $this->role,
        ];
    }
}