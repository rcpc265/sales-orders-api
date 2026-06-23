<?php

namespace Tests\Unit\Domain;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class OrderItemTest extends TestCase
{
    public function test_order_item_has_expected_fillable_attributes(): void
    {
        // Given
        $item = new OrderItem();

        // When
        $fillable = $item->getFillable();

        // Then
        $this->assertEquals(['order_id', 'product_id', 'product_name', 'product_price', 'quantity', 'subtotal'], $fillable);
    }

    public function test_order_item_belongs_to_order(): void
    {
        // Given
        $item = new OrderItem();

        // When
        $relation = $item->order();

        // Then
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(Order::class, $relation->getRelated());
    }
}
