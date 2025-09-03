<?php

namespace App\DTO;

class ReservationData
{
    public function __construct(
        public string $fighterId,
        public string $userId,
        public int $totalPrice,
        public array $dates
    ) {}
}
