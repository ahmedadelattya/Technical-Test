<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'slug' => 'sometimes|string|unique:products,slug,' . $this->route('product')->id,
            'name' => 'sometimes|string|min:4|max:255',
            'description' => 'nullable|string|max:64000',
            'price' => 'sometimes|numeric|min:0',
            'stock_quantity' => 'sometimes|integer|min:0',
            'meta_key' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'categories' => 'array|exclude_if:categories,null|nullable',
            'categories.*' => 'exists:categories,id|exclude_if:categories.*,null|nullable',
            'image' => 'sometimes|image|mimes:jpg,jpeg,png,gif,bmp,webp|max:2048',
        ];
    }
}
