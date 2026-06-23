<?php

namespace Tests\Unit\Domain;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class OrderTest extends TestCase
{
    public function test_order_has_expected_fillable_attributes(): void
    {
        // Given
        $order = new Order();

        // When
        $fillable = $order->getFillable();

        // Then
        $this->assertEquals(['customer_name', 'customer_email', 'status', 'total_amount'], $fillable);
    }

    public function test_order_has_many_items(): void
    {
        // Given
        $order = new Order();

        // When
        $relation = $order->items();

        // Then
        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertInstanceOf(OrderItem::class, $relation->getRelated());
    }

    public function test_order_status_is_cast_to_enum(): void
    {
        // Given
        $order = new Order();

        // When
        $casts = $order->getCasts();

        // Then
        $this->assertArrayHasKey('status', $casts);
        $this->assertEquals(OrderStatus::class, $casts['status']);
    }
}
