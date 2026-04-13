<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'description',
        'image',
        'supplier',
        'base_price',
        'profit_margin',
        // 'sell_price' is intentionally omitted because it is a GENERATED COLUMN
        'stock_quantity',
        'low_stock_threshold',
        'is_hidden',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'profit_margin' => 'decimal:2',
            'sell_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'low_stock_threshold' => 'integer',
            'is_hidden' => 'boolean',
        ];
    }

    public function getSellPriceAttribute($value)
    {
        // DO NOT calculate this in PHP. Return what is in the DB.
        return $value;
    }

    public function scopeActive($query)
    {
        return $query->where('products.is_hidden', 0);
    }

    public function scopeVisible($query)
    {
        return $query->where('products.is_hidden', 0)
                     ->join('categories', 'products.category_id', '=', 'categories.id')
                     ->where('categories.is_hidden', 0)
                     ->select('products.*');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function goodsReceiptDetails()
    {
        return $this->hasMany(GoodsReceiptDetail::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
