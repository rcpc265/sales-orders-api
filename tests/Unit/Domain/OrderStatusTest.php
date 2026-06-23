<?php

namespace Tests\Unit\Domain;

use App\Enums\OrderStatus;
use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    public function test_pending_can_transition_to_confirmed(): void
    {
        // Given
        $status = OrderStatus::PENDING;

        // When
        $canTransition = $status->canTransitionTo(OrderStatus::CONFIRMED);

        // Then
        $this->assertTrue($canTransition);
    }

    public function test_pending_cannot_transition_to_delivered(): void
    {
        // Given
        $status = OrderStatus::PENDING;

        // When
        $canTransition = $status->canTransitionTo(OrderStatus::DELIVERED);

        // Then
        $this->assertFalse($canTransition);
    }

    public function test_delivered_cannot_transition_to_any_status(): void
    {
        // Given
        $status = OrderStatus::DELIVERED;

        // When
        $canTransitionToCancelled = $status->canTransitionTo(OrderStatus::CANCELLED);
        $canTransitionToPending = $status->canTransitionTo(OrderStatus::PENDING);

        // Then
        $this->assertFalse($canTransitionToCancelled);
        $this->assertFalse($canTransitionToPending);
    }
}
