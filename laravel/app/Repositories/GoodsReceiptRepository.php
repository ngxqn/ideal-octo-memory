<?php

namespace App\Repositories;

use App\Models\GoodsReceipt;
use App\Repositories\Interfaces\GoodsReceiptRepositoryInterface;

class GoodsReceiptRepository implements GoodsReceiptRepositoryInterface
{
    public function create(array $data): GoodsReceipt
    {
        return GoodsReceipt::create($data);
    }
}
