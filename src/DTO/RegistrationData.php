<?php

namespace App\DTO;

class RegistrationData
{
    public function __construct(
        public string $username,
        public string $email,
        public string $password
    ) {}
}
