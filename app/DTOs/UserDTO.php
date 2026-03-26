<?php

namespace App\DTOs;

class UserDTO
{
    public string $name;
    public string $email;
    public string $pass;
    public string $role;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->pass = $data['pass'];
        $this->role = $data['role'] ?? 'user';
    }
}
