<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'slug' => 'required|string|unique:products,slug',
            'name' => 'required|string|min:4|max:255',
            'description' => 'nullable|string|max:64000',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'meta_key' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'categories' => 'array|required|min:1',
            'categories.*' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpg,jpeg,png,gif,bmp,webp|max:2048',
        ];
    }
}
