<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);

it('creates an order successfully', function () {
    // Given
    $product1 = Product::create([
        'name' => 'Test Product 1',
        'sku' => 'TEST-001',
        'price' => 100.00,
        'stock' => 10,
    ]);
    
    $payload = [
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'items' => [
            [
                'product_id' => $product1->id,
                'quantity' => 2,
            ],
        ],
    ];

    // When
    $response = $this->postJson('/api/v1/orders', $payload);

    // Then
    $response->assertStatus(201)
             ->assertJsonStructure([
                 'data' => [
                     'id',
                     'customer_name',
                     'customer_email',
                     'status',
                     'total_amount',
                     'items' => [
                         '*' => [
                             'id',
                             'product_id',
                             'product_name',
                             'quantity',
                             'subtotal',
                         ],
                     ],
                 ],
             ]);

    $this->assertDatabaseHas('orders', [
        'customer_name' => 'John Doe',
        'total_amount' => 200.00,
    ]);
});

it('returns validation errors when creating an order with invalid data', function () {
    // Given
    $payload = [
        'customer_name' => '', // Invalid
        'customer_email' => 'not-an-email', // Invalid
        'items' => [], // Invalid
    ];

    // When
    $response = $this->postJson('/api/v1/orders', $payload);

    // Then
    $response->assertStatus(422)
             ->assertJsonValidationErrors(['customer_name', 'customer_email', 'items']);
});

it('updates order status successfully', function () {
    // Given
    $order = Order::create([
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
        'status' => OrderStatus::PENDING,
        'total_amount' => 0,
    ]);

    $payload = [
        'status' => OrderStatus::CONFIRMED->value,
    ];

    // When
    $response = $this->patchJson("/api/v1/orders/{$order->id}/status", $payload);

    // Then
    $response->assertStatus(200);
    expect($order->fresh()->status)->toBe(OrderStatus::CONFIRMED);
});

it('fails to update order to an invalid state transition', function () {
    // Given
    $order = Order::create([
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
        'status' => OrderStatus::PENDING,
        'total_amount' => 0,
    ]);

    $payload = [
        'status' => OrderStatus::DELIVERED->value, // Invalid transition from PENDING
    ];

    // When
    $response = $this->patchJson("/api/v1/orders/{$order->id}/status", $payload);

    // Then
    $response->assertStatus(422)
             ->assertJsonFragment(['message' => 'Cannot transition from pending to delivered']);
});
