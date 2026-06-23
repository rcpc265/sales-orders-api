<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 20 products
        $products = Product::factory(20)->create();

        // Create 10 orders
        Order::factory(10)->create()->each(function (Order $order) use ($products) {
            // Give each order 1 to 5 random items
            $itemCount = rand(1, 5);
            $orderProducts = $products->random($itemCount);

            $totalAmount = 0;

            foreach ($orderProducts as $product) {
                $quantity = rand(1, 4);
                $subtotal = $product->price * $quantity;
                $totalAmount += $subtotal;

                OrderItem::factory()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_price' => $product->price,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ]);
            }

            // Update order total
            $order->update(['total_amount' => $totalAmount]);
        });
    }
}
