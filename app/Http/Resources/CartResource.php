<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CartResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", nullable=true, example=3),
 *     @OA\Property(property="session_id", type="string", nullable=true, example="abcd1234"),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(
 *         property="items",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/CartItemResource")
 *     ),
 *     @OA\Property(property="total_cents", type="integer", example=2400)
 * )
 */

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'session_id' => $this->session_id,
            'status' => $this->status,
            'items' => CartItemResource::collection($this->whenLoaded('items')),
            'total_items' => $this->total_items,
            'subtotal_cents' => $this->subtotal_cents,
            'subtotal' => $this->subtotal,
            'tax_cents' => $this->tax_cents,
            'tax' => $this->tax,
            'total_cents' => $this->total_cents,
            'total' => $this->total,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}