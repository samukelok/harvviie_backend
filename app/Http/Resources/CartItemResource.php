<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CartItemResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=10),
 *     @OA\Property(property="product_id", type="integer", example=5),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="unit_price_cents", type="integer", example=1200),
 *     @OA\Property(property="product", type="object",
 *         @OA\Property(property="id", type="integer", example=5),
 *         @OA\Property(property="name", type="string", example="Nike Air Zoom"),
 *         @OA\Property(property="stock", type="integer", example=50),
 *         @OA\Property(property="price_cents", type="integer", example=1500),
 *         @OA\Property(property="images", type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="url", type="string", example="https://example.com/image.jpg")
 *             )
 *         )
 *     )
 * )
 */

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cart_id' => $this->cart_id,
            'product_id' => $this->product_id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'quantity' => $this->quantity,
            'unit_price_cents' => $this->unit_price_cents,
            'unit_price' => $this->unit_price,
            'total_cents' => $this->total_cents,
            'total' => $this->total,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}