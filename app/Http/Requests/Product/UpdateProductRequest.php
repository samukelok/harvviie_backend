<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')->id;

        return [
            'name' => 'sometimes|required|string|max:255',
            'sku' => ['nullable', 'string', Rule::unique('products', 'sku')->ignore($productId)],
            'slug' => ['nullable', 'string', Rule::unique('products', 'slug')->ignore($productId)],
            'description' => 'nullable|string',
            'price_cents' => 'sometimes|required|integer|min:0',
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'stock' => 'integer|min:0',
            'is_active' => 'boolean',
            'metadata' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }
}