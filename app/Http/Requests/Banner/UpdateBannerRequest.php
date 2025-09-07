<?php

namespace App\Http\Requests\Banner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'tagline' => 'nullable|string|max:512',
            'image' => 'sometimes|required|string',
            'position' => 'integer|min:0',
            'is_active' => 'boolean',
        ];
    }
}