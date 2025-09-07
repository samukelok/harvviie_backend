<?php

namespace App\Http\Requests\Collection;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCollectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $collectionId = $this->route('collection')->id;

        return [
            'name' => 'sometimes|required|string|max:255',
            'slug' => ['nullable', 'string', Rule::unique('collections', 'slug')->ignore($collectionId)],
            'description' => 'nullable|string',
            'cover_image' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}