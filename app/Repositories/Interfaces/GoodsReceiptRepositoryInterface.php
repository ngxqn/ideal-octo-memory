<?php

namespace App\Repositories\Interfaces;

use App\Models\GoodsReceipt;

interface GoodsReceiptRepositoryInterface
{
    /**
     * Create a new goods receipt.
     */
    public function create(array $data): GoodsReceipt;
}
