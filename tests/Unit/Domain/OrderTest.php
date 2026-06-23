<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

uses(TestCase::class);

it('has expected fillable attributes', function () {
    // Given
    $order = new Order();

    // When
    $fillable = $order->getFillable();

    // Then
    expect($fillable)->toEqual(['customer_name', 'customer_email', 'status', 'total_amount']);
});

it('has many items', function () {
    // Given
    $order = new Order();

    // When
    $relation = $order->items();

    // Then
    expect($relation)->toBeInstanceOf(HasMany::class);
    expect($relation->getRelated())->toBeInstanceOf(OrderItem::class);
});

it('casts status to enum', function () {
    // Given
    $order = new Order();

    // When
    $casts = $order->getCasts();

    // Then
    expect($casts)->toHaveKey('status');
    expect($casts['status'])->toBe(OrderStatus::class);
});
