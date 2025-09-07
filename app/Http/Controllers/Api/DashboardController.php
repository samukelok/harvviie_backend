<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Dashboard",
 *     description="Endpoints for dashboard metrics and reports"
 * )
 */
class DashboardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/dashboard/summary",
     *     tags={"Dashboard"},
     *     summary="Get dashboard summary (sales, orders, recent orders)",
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard summary retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="sales_summary",
     *                     type="object",
     *                     @OA\Property(
     *                         property="today",
     *                         type="object",
     *                         @OA\Property(property="amount_cents", type="integer"),
     *                         @OA\Property(property="amount", type="number", format="float")
     *                     ),
     *                     @OA\Property(
     *                         property="week",
     *                         type="object",
     *                         @OA\Property(property="amount_cents", type="integer"),
     *                         @OA\Property(property="amount", type="number", format="float")
     *                     ),
     *                     @OA\Property(
     *                         property="month",
     *                         type="object",
     *                         @OA\Property(property="amount_cents", type="integer"),
     *                         @OA\Property(property="amount", type="number", format="float")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="orders_summary",
     *                     type="object",
     *                     @OA\Property(property="pending_count", type="integer"),
     *                     @OA\Property(property="total_count", type="integer")
     *                 ),
     *                 @OA\Property(
     *                     property="recent_orders",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="status", type="string"),
     *                         @OA\Property(property="amount_cents", type="integer"),
     *                         @OA\Property(property="placed_at", type="string", format="date-time")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function summary(Request $request)
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();

        $todaySales = Order::whereDate('placed_at', $today)
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->sum('amount_cents');

        $weekSales = Order::where('placed_at', '>=', $weekStart)
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->sum('amount_cents');

        $monthSales = Order::where('placed_at', '>=', $monthStart)
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->sum('amount_cents');

        $pendingOrdersCount = Order::where('status', Order::STATUS_PENDING)->count();
        $totalOrdersCount = Order::count();
        $recentOrders = Order::orderBy('created_at', 'desc')->limit(5)->get();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard summary retrieved successfully',
            'data' => [
                'sales_summary' => [
                    'today' => ['amount_cents' => $todaySales, 'amount' => $todaySales / 100],
                    'week' => ['amount_cents' => $weekSales, 'amount' => $weekSales / 100],
                    'month' => ['amount_cents' => $monthSales, 'amount' => $monthSales / 100],
                ],
                'orders_summary' => [
                    'pending_count' => $pendingOrdersCount,
                    'total_count' => $totalOrdersCount,
                ],
                'recent_orders' => $recentOrders,
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/top-products",
     *     tags={"Dashboard"},
     *     summary="Get top-selling products",
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of products to return",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Top products retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="top_products",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="product", type="object"),
     *                         @OA\Property(property="total_quantity", type="integer"),
     *                         @OA\Property(property="order_count", type="integer")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function topProducts(Request $request)
    {
        $limit = $request->input('limit', 10);

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

        $productIds = $topProducts->pluck('product_id')->filter();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $topProductsData = $topProducts->map(function ($item) use ($products) {
            $product = $products->get($item->product_id);
            return [
                'product' => $product,
                'total_quantity' => (int) $item->total_quantity,
                'order_count' => (int) $item->order_count,
            ];
        })->filter(fn($item) => $item['product'] !== null);

        return response()->json([
            'success' => true,
            'message' => 'Top products retrieved successfully',
            'data' => ['top_products' => $topProductsData]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/pending-orders",
     *     tags={"Dashboard"},
     *     summary="Get pending orders",
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of orders to return",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pending orders retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="pending_orders",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="status", type="string"),
     *                         @OA\Property(property="amount_cents", type="integer"),
     *                         @OA\Property(property="placed_at", type="string", format="date-time")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
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
            'data' => ['pending_orders' => $pendingOrders]
        ]);
    }
}
