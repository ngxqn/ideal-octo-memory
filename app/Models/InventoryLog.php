<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    protected $table = 'inventory_logs';

    const UPDATED_AT = null;

    protected $fillable = [
        'product_id',
        'change_amount',
        'unit_price',
        'reference_type',
        'reference_id',
    ];

    protected function casts(): array
    {
        return [
            'change_amount' => 'integer',
            'unit_price' => 'decimal:2',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
