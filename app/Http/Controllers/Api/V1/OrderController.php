<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\OrderDTO;
use App\DTOs\OrderItemDTO;
use App\Enums\OrderStatus;
use App\Exceptions\InvalidOrderStatusTransitionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $items = array_map(function ($item) {
            return new OrderItemDTO($item['product_id'], $item['quantity']);
        }, $validated['items']);

        $dto = new OrderDTO(
            customerName: $validated['customer_name'],
            customerEmail: $validated['customer_email'],
            items: $items
        );

        $order = $this->orderService->createOrder($dto);

        return response()->json([
            'data' => new OrderResource($order->load('items'))
        ], 201);
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json([
            'data' => new OrderResource($order->load('items'))
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        try {
            $this->orderService->updateOrderStatus($order, OrderStatus::from($request->validated('status')));
            return response()->json(['message' => 'Status updated successfully']);
        } catch (InvalidOrderStatusTransitionException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
