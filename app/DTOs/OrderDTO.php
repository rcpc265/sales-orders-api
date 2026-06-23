<?php

namespace App\DTOs;

readonly class OrderDTO
{
    /**
     * @param OrderItemDTO[] $items
     */
    public function __construct(
        public string $customerName,
        public string $customerEmail,
        public array $items,
    ) {
    }
}
