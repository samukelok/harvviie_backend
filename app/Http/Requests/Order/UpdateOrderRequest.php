<?php

namespace App\Http\Requests\Order;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'sometimes',
                'required',
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