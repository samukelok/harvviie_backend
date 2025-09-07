<?php

namespace App\Http\Requests\About;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAboutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string',
            'milestones' => 'nullable|array',
            'milestones.*.year' => 'required_with:milestones|integer',
            'milestones.*.title' => 'required_with:milestones|string',
            'milestones.*.description' => 'nullable|string',
        ];
    }
}