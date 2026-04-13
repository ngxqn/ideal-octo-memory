<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Admin middleware already applied
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:pending,confirmed,delivered,cancelled',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Trạng thái không hợp lệ. Chỉ chấp nhận: pending, confirmed, delivered, cancelled.'
        ];
    }
}
