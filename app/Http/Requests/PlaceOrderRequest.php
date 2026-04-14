<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:255',
            'shipping_commune' => 'required|string|max:100',
            'shipping_city' => 'required|string|max:100',
            'payment_method' => 'required|string|in:cod,transfer,online',
            'note' => 'nullable|string|max:1000',
            'save_for_future' => 'nullable|boolean',
        ];
    }
}
