<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
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
            'receiver_name' => 'required|string|max:100',
            'receiver_phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'commune' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'is_default' => 'nullable|boolean',
        ];
    }
}
