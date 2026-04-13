<?php

namespace App\Repositories\Interfaces;

use App\Models\GoodsReceiptDetail;

interface GoodsReceiptDetailRepositoryInterface
{
    /**
     * Create a new goods receipt detail.
     */
    public function create(array $data): GoodsReceiptDetail;
}
