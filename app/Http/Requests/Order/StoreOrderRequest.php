<?php

namespace App\Http\Requests\Order;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price_cents' => 'required|integer|min:0',
            'amount_cents' => 'required|integer|min:0',
            'status' => [
                'nullable',
                Rule::in([
                    Order::STATUS_PENDING,
                    Order::STATUS_PROCESSING,
                    Order::STATUS_SHIPPED,
                    Order::STATUS_DELIVERED,
                    Order::STATUS_CANCELLED,
                ])
            ],
            'shipping_address' => 'nullable|array',
            'shipping_address.name' => 'required_with:shipping_address|string',
            'shipping_address.address_line_1' => 'required_with:shipping_address|string',
            'shipping_address.address_line_2' => 'nullable|string',
            'shipping_address.city' => 'required_with:shipping_address|string',
            'shipping_address.postal_code' => 'required_with:shipping_address|string',
            'shipping_address.country' => 'required_with:shipping_address|string',
        ];
    }
}