<?php

namespace App\Services;

use App\DTOs\OrderDTO;
use App\Enums\OrderStatus;
use App\Exceptions\InvalidOrderStatusTransitionException;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(OrderDTO $dto): Order
    {
        return DB::transaction(function () use ($dto) {
            $order = Order::create([
                'customer_name' => $dto->customerName,
                'customer_email' => $dto->customerEmail,
                'status' => OrderStatus::PENDING,
                'total_amount' => 0,
            ]);

            $totalAmount = 0;

            foreach ($dto->items as $itemDto) {
                $product = Product::findOrFail($itemDto->productId);
                $subtotal = $product->price * $itemDto->quantity;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_price' => $product->price,
                    'quantity' => $itemDto->quantity,
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;
            }

            $order->update(['total_amount' => $totalAmount]);

            return $order;
        });
    }

    public function updateOrderStatus(Order $order, OrderStatus $newStatus): void
    {
        if (!$order->status->canTransitionTo($newStatus)) {
            throw new InvalidOrderStatusTransitionException("Cannot transition from {$order->status->value} to {$newStatus->value}");
        }

        $order->update(['status' => $newStatus]);
    }
}
