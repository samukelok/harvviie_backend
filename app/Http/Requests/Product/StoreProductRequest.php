<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:products,sku',
            'slug' => 'nullable|string|unique:products,slug',
            'description' => 'nullable|string',
            'price_cents' => 'required|integer|min:0',
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'stock' => 'integer|min:0',
            'is_active' => 'boolean',
            'metadata' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }
}