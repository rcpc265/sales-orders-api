<?php

use App\Enums\OrderStatus;

it('allows pending to transition to confirmed', function () {
    // Given
    $status = OrderStatus::PENDING;

    // When
    $canTransition = $status->canTransitionTo(OrderStatus::CONFIRMED);

    // Then
    expect($canTransition)->toBeTrue();
});

it('prevents pending from transitioning to delivered', function () {
    // Given
    $status = OrderStatus::PENDING;

    // When
    $canTransition = $status->canTransitionTo(OrderStatus::DELIVERED);

    // Then
    expect($canTransition)->toBeFalse();
});

it('prevents delivered from transitioning to any status', function () {
    // Given
    $status = OrderStatus::DELIVERED;

    // When
    $canTransitionToCancelled = $status->canTransitionTo(OrderStatus::CANCELLED);
    $canTransitionToPending = $status->canTransitionTo(OrderStatus::PENDING);

    // Then
    expect($canTransitionToCancelled)->toBeFalse();
    expect($canTransitionToPending)->toBeFalse();
});
