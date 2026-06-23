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

/**
 * @group Orders
 *
 * API endpoints for managing sales orders.
 */
class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {
    }

    /**
     * Create a new Order
     *
     * Creates a new sales order with the provided items and calculates the total amount automatically.
     *
     * @response 201 {"data": {"id": 1, "customer_name": "John Doe", "customer_email": "john@example.com", "status": "pending", "total_amount": 200, "items": [{"id": 1, "product_id": 1, "product_name": "Test Product", "product_price": 100, "quantity": 2, "subtotal": 200}], "created_at": "...", "updated_at": "..."}}
     */
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

    /**
     * Get Order details
     *
     * Retrieves the details of a specific order, including its items.
     */
    public function show(Order $order): JsonResponse
    {
        return response()->json([
            'data' => new OrderResource($order->load('items'))
        ]);
    }

    /**
     * Update Order Status
     *
     * Updates the status of an order following the state machine rules.
     *
     * @response 200 {"message": "Status updated successfully"}
     * @response 422 {"message": "Cannot transition from pending to delivered"}
     */
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
