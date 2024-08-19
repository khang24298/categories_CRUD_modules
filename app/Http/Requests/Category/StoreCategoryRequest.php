<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Replace with actual authorization logic
    }

    public function rules()
    {
        return [
            'parent_id' => 'nullable|integer|exists:categories,id',
            'level' => 'required|integer|min:1|max:3',
            'type' => 'required|string',
            'key' => 'required|string|unique:categories',
            'code' => 'required|string|unique:categories',
            'name' => 'required|json',
            'name.*' => 'required|string',
            'active' => 'boolean',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(
            response()->json(['data' => $errors], 422)
        );
    }
}
