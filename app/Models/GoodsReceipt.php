<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceipt extends Model
{
    protected $table = 'goods_receipts';

    protected $fillable = [
        'created_by',
        'status',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details()
    {
        return $this->hasMany(GoodsReceiptDetail::class);
    }
}
