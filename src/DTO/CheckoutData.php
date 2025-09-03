<?php

namespace App\DTO;

class CheckoutData
{
    public function __construct(
        public string $fighterId,
        public int $totalPrice
    ) {}
}
