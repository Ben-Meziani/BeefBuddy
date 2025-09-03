<?php

namespace App\DTO;

class ResetPasswordFormData
{
    public function __construct(
        public string $password
    ) {}
}
