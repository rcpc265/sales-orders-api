<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);

it('can create a product using factory', function () {
    $product = Product::factory()->create();

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
    ]);
});

it('can create an order using factory', function () {
    $order = Order::factory()->create();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
    ]);
});

it('can create an order item using factory', function () {
    $orderItem = OrderItem::factory()->create();

    $this->assertDatabaseHas('order_items', [
        'id' => $orderItem->id,
    ]);
});

it('can seed the database', function () {
    $this->artisan('db:seed')->assertSuccessful();

    $this->assertDatabaseCount('products', 20);
    $this->assertDatabaseCount('orders', 10);
    // Order items count is random, but should be at least 10 (since 10 orders * 1 min item)
    expect(OrderItem::count())->toBeGreaterThanOrEqual(10);
});
