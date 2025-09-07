<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function summary(Request $request)
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();

        // Sales summary
        $todaySales = Order::whereDate('placed_at', $today)
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->sum('amount_cents');

        $weekSales = Order::where('placed_at', '>=', $weekStart)
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->sum('amount_cents');

        $monthSales = Order::where('placed_at', '>=', $monthStart)
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->sum('amount_cents');

        // Order counts
        $pendingOrdersCount = Order::where('status', Order::STATUS_PENDING)->count();
        $totalOrdersCount = Order::count();

        // Recent orders for quick view
        $recentOrders = Order::orderBy('created_at', 'desc')->limit(5)->get();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard summary retrieved successfully',
            'data' => [
                'sales_summary' => [
                    'today' => [
                        'amount_cents' => $todaySales,
                        'amount' => $todaySales / 100,
                    ],
                    'week' => [
                        'amount_cents' => $weekSales,
                        'amount' => $weekSales / 100,
                    ],
                    'month' => [
                        'amount_cents' => $monthSales,
                        'amount' => $monthSales / 100,
                    ],
                ],
                'orders_summary' => [
                    'pending_count' => $pendingOrdersCount,
                    'total_count' => $totalOrdersCount,
                ],
                'recent_orders' => $recentOrders,
            ]
        ]);
    }

    public function topProducts(Request $request)
    {
        $limit = $request->input('limit', 10);

        // Get top products by counting occurrences in order items
        $topProducts = DB::table('orders')
            ->select(DB::raw('JSON_UNQUOTE(JSON_EXTRACT(item.value, "$.product_id")) as product_id'))
            ->selectRaw('SUM(JSON_UNQUOTE(JSON_EXTRACT(item.value, "$.quantity"))) as total_quantity')
            ->selectRaw('COUNT(*) as order_count')
            ->crossJoin(DB::raw('JSON_TABLE(orders.items, "$[*]" COLUMNS (value JSON PATH "$")) as item'))
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->get();

        // Get product details
        $productIds = $topProducts->pluck('product_id')->filter();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $topProductsData = $topProducts->map(function ($item) use ($products) {
            $product = $products->get($item->product_id);
            return [
                'product' => $product,
                'total_quantity' => (int) $item->total_quantity,
                'order_count' => (int) $item->order_count,
            ];
        })->filter(function ($item) {
            return $item['product'] !== null;
        });

        return response()->json([
            'success' => true,
            'message' => 'Top products retrieved successfully',
            'data' => [
                'top_products' => $topProductsData
            ]
        ]);
    }

    public function pendingOrders(Request $request)
    {
        $limit = $request->input('limit', 10);

        $pendingOrders = Order::where('status', Order::STATUS_PENDING)
            ->orderBy('placed_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Pending orders retrieved successfully',
            'data' => [
                'pending_orders' => $pendingOrders
            ]
        ]);
    }
}