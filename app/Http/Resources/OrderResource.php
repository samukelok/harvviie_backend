<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OrderResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="status", type="string"),
 *     @OA\Property(property="amount_cents", type="integer"),
 *     @OA\Property(property="placed_at", type="string", format="date-time"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="customer_name", type="string"),
 *     @OA\Property(property="customer_email", type="string"),
 *     @OA\Property(property="shipping_address", type="string"),
 *     @OA\Property(property="user_id", type="integer")
 * )
 */

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'user_id' => $this->user_id,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'items' => $this->items,
            'amount_cents' => $this->amount_cents,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'shipping_address' => $this->shipping_address,
            'placed_at' => $this->placed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}