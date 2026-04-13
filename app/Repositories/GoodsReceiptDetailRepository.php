<?php

namespace App\Repositories;

use App\Models\GoodsReceiptDetail;
use App\Repositories\Interfaces\GoodsReceiptDetailRepositoryInterface;

class GoodsReceiptDetailRepository implements GoodsReceiptDetailRepositoryInterface
{
    public function create(array $data): GoodsReceiptDetail
    {
        return GoodsReceiptDetail::create($data);
    }
}
