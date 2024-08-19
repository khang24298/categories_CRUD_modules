<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class IndexCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|integer|exists:categories,id',
            'level' => 'integer|min:1|max:3',
            'type' => 'string',
            'key' => 'string|unique:categories',
            'code' => 'string|unique:categories',
            'name' => 'json',
            'name.*' => 'string',
            'active' => 'boolean',
        ];
    }
}
