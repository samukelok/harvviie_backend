<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();

        // Apply filters
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        if ($request->filled('q')) {
            $query->search($request->q);
        }

        $orders = $query->orderBy('placed_at', 'desc')
                       ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully',
            'data' => [
                'orders' => OrderResource::collection($orders),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'total_pages' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                ]
            ]
        ]);
    }

    public function show(Order $order)
    {
        return response()->json([
            'success' => true,
            'message' => 'Order retrieved successfully',
            'data' => [
                'order' => new OrderResource($order)
            ]
        ]);
    }

    public function store(StoreOrderRequest $request)
    {
        $order = Order::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => [
                'order' => new OrderResource($order)
            ]
        ], 201);
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
            'data' => [
                'order' => new OrderResource($order)
            ]
        ]);
    }

    public function destroy(Order $order)
    {
        // Instead of hard delete, mark as cancelled
        $order->update(['status' => Order::STATUS_CANCELLED]);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => null
        ]);
    }
}