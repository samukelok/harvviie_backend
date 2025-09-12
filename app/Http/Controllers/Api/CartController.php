<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/cart",
     *     tags={"Cart"},
     *     summary="Get current user's cart",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Cart retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cart retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="cart", ref="#/components/schemas/CartResource")
     *             )
     *         )
     *     )
     * )
     */

    public function show(Request $request)
    {
        $cart = $this->getOrCreateCart($request);
        $cart->load(['items.product.images']);

        return response()->json([
            'success' => true,
            'message' => 'Cart retrieved successfully',
            'data' => [
                'cart' => new CartResource($cart)
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/cart/items",
     *     tags={"Cart"},
     *     summary="Add item to cart",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id", "quantity"},
     *             @OA\Property(property="product_id", type="integer", example=5),
     *             @OA\Property(property="quantity", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item added to cart successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Item added to cart successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="cart", ref="#/components/schemas/CartResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Insufficient stock"
     *     )
     * )
     */

    public function addItem(AddToCartRequest $request)
    {
        DB::beginTransaction();

        try {
            $cart = $this->getOrCreateCart($request);
            $product = Product::findOrFail($request->product_id);

            // Check stock availability
            if ($product->stock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available',
                    'data' => [
                        'available_stock' => $product->stock
                    ]
                ], 400);
            }

            // Check if item already exists in cart
            $cartItem = $cart->items()->where('product_id', $product->id)->first();

            if ($cartItem) {
                // Update existing item
                $newQuantity = $cartItem->quantity + $request->quantity;

                if ($product->stock < $newQuantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot add more items. Insufficient stock available',
                        'data' => [
                            'current_in_cart' => $cartItem->quantity,
                            'available_stock' => $product->stock
                        ]
                    ], 400);
                }

                $cartItem->update([
                    'quantity' => $newQuantity,
                    'unit_price_cents' => $product->discounted_price_cents
                ]);
            } else {
                // Create new cart item
                $cartItem = $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                    'unit_price_cents' => $product->discounted_price_cents
                ]);
            }

            $cart->load(['items.product.images']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart successfully',
                'data' => [
                    'cart' => new CartResource($cart)
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to cart',
                'data' => null
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/cart/items/{id}",
     *     tags={"Cart"},
     *     summary="Update cart item quantity",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"quantity"},
     *             @OA\Property(property="quantity", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart item updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cart item updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="cart", ref="#/components/schemas/CartResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Cart item not found")
     * )
     */

    public function updateItem(UpdateCartItemRequest $request, CartItem $cartItem)
    {
        // Ensure the cart item belongs to the current user's cart
        $cart = $this->getOrCreateCart($request);
        
        if ($cartItem->cart_id !== $cart->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found',
                'data' => null
            ], 404);
        }

        // Check stock availability
        if ($cartItem->product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available',
                'data' => [
                    'available_stock' => $cartItem->product->stock
                ]
            ], 400);
        }

        $cartItem->update([
            'quantity' => $request->quantity,
            'unit_price_cents' => $cartItem->product->discounted_price_cents
        ]);

        $cart->load(['items.product.images']);

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated successfully',
            'data' => [
                'cart' => new CartResource($cart)
            ]
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/cart/items/{id}",
     *     tags={"Cart"},
     *     summary="Remove item from cart",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item removed from cart successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Item removed from cart successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="cart", ref="#/components/schemas/CartResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Cart item not found")
     * )
     */

    public function removeItem(Request $request, CartItem $cartItem)
    {
        // Ensure the cart item belongs to the current user's cart
        $cart = $this->getOrCreateCart($request);
        
        if ($cartItem->cart_id !== $cart->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found',
                'data' => null
            ], 404);
        }

        $cartItem->delete();

        $cart->load(['items.product.images']);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart successfully',
            'data' => [
                'cart' => new CartResource($cart)
            ]
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/cart/clear",
     *     tags={"Cart"},
     *     summary="Clear all items from cart",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Cart cleared successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cart cleared successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="cart", ref="#/components/schemas/CartResource")
     *             )
     *         )
     *     )
     * )
     */

    public function clear(Request $request)
    {
        $cart = $this->getOrCreateCart($request);
        $cart->items()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully',
            'data' => [
                'cart' => new CartResource($cart)
            ]
        ]);
    }

    private function getOrCreateCart(Request $request)
    {
        if ($request->user()) {
            // Always get existing user cart first, don't create new one
            $cart = Cart::where('user_id', $request->user()->id)
                       ->where('status', Cart::STATUS_ACTIVE)
                       ->first();
            
            if (!$cart) {
                $cart = Cart::create([
                    'user_id' => $request->user()->id,
                    'status' => Cart::STATUS_ACTIVE
                ]);
            }
            
            return $cart;
        } else {
            $sessionId = $request->header('X-Cart-Session') ?? $request->ip();
            return Cart::getOrCreateForSession($sessionId);
        }
    }
}