<?php

namespace App\DTO;

class ResetPasswordData
{
    public function __construct(
        public string $email
    ) {}
}
