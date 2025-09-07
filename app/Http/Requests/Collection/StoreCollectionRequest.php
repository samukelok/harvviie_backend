<?php

namespace App\Http\Requests\Collection;

use Illuminate\Foundation\Http\FormRequest;

class StoreCollectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:collections,slug',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}