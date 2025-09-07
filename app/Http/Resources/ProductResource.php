<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price_cents' => $this->price_cents,
            'price' => $this->price,
            'discount_percent' => $this->discount_percent,
            'discounted_price_cents' => $this->discounted_price_cents,
            'discounted_price' => $this->discounted_price,
            'stock' => $this->stock,
            'is_active' => $this->is_active,
            'metadata' => $this->metadata,
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'collections' => CollectionResource::collection($this->whenLoaded('collections')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}