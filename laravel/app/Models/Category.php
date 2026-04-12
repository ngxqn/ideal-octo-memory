<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'is_hidden',
    ];

    protected function casts(): array
    {
        return [
            'is_hidden' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_hidden', 0);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
