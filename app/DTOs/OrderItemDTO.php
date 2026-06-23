<?php

namespace App\DTOs;

readonly class OrderItemDTO
{
    public function __construct(
        public int $productId,
        public int $quantity,
    ) {
    }
}
