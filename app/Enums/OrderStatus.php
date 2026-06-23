<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::PENDING => [self::CONFIRMED, self::CANCELLED],
            self::CONFIRMED => [self::SHIPPED, self::CANCELLED],
            self::SHIPPED => [self::DELIVERED, self::CANCELLED],
            self::DELIVERED => [],
            self::CANCELLED => [],
        };
    }

    public function canTransitionTo(self $nextStatus): bool
    {
        return in_array($nextStatus, $this->allowedTransitions(), true);
    }
}
