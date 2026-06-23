<?php

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

uses(TestCase::class);

it('has expected fillable attributes', function () {
    // Given
    $item = new OrderItem();

    // When
    $fillable = $item->getFillable();

    // Then
    expect($fillable)->toEqual(['order_id', 'product_id', 'product_name', 'product_price', 'quantity', 'subtotal']);
});

it('belongs to order', function () {
    // Given
    $item = new OrderItem();

    // When
    $relation = $item->order();

    // Then
    expect($relation)->toBeInstanceOf(BelongsTo::class);
    expect($relation->getRelated())->toBeInstanceOf(Order::class);
});
