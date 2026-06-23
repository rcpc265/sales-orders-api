<?php

use App\DTOs\OrderDTO;
use App\DTOs\OrderItemDTO;
use App\Enums\OrderStatus;
use App\Exceptions\InvalidOrderStatusTransitionException;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('creates an order with items and calculates total amount correctly', function () {
    // Given
    $service = new OrderService();
    $product1 = Product::create([
        'name' => 'Product 1',
        'sku' => 'SKU-001',
        'price' => 100.00,
        'stock' => 10,
    ]);
    $product2 = Product::create([
        'name' => 'Product 2',
        'sku' => 'SKU-002',
        'price' => 50.00,
        'stock' => 5,
    ]);

    $items = [
        new OrderItemDTO(productId: $product1->id, quantity: 2),
        new OrderItemDTO(productId: $product2->id, quantity: 1),
    ];
    $dto = new OrderDTO(customerName: 'John Doe', customerEmail: 'john@example.com', items: $items);

    // When
    $order = $service->createOrder($dto);

    // Then
    expect($order->customer_name)->toBe('John Doe');
    expect($order->status)->toBe(OrderStatus::PENDING);
    expect($order->total_amount)->toEqual(250.00); // (100 * 2) + (50 * 1)
    expect($order->items()->count())->toBe(2);
});

it('allows valid order status transitions', function () {
    // Given
    $service = new OrderService();
    $order = Order::create([
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
        'status' => OrderStatus::PENDING,
        'total_amount' => 0,
    ]);

    // When
    $service->updateOrderStatus($order, OrderStatus::CONFIRMED);

    // Then
    expect($order->fresh()->status)->toBe(OrderStatus::CONFIRMED);
});

it('throws exception for invalid order status transitions', function () {
    // Given
    $service = new OrderService();
    $order = Order::create([
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
        'status' => OrderStatus::PENDING,
        'total_amount' => 0,
    ]);

    // When / Then
    expect(fn () => $service->updateOrderStatus($order, OrderStatus::DELIVERED))
        ->toThrow(InvalidOrderStatusTransitionException::class);
});
