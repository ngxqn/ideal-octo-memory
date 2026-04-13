<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|max:50|unique:products,sku',
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'profit_margin' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'supplier' => 'nullable|string|max:200',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'is_hidden' => 'nullable|boolean',
        ];
    }
}
