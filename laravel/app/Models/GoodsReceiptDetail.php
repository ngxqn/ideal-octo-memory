<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceiptDetail extends Model
{
    protected $table = 'goods_receipt_details';

    const UPDATED_AT = null;

    protected $fillable = [
        'goods_receipt_id',
        'product_id',
        'quantity',
        'import_price',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'import_price' => 'decimal:2',
        ];
    }

    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
