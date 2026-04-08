<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * User's order history.
     */
    public function index()
    {
        return "Lịch sử mua hàng (Đang hoàn thiện ở Batch 4)";
    }

    /**
     * User's order detail.
     */
    public function show($id)
    {
        return "Chi tiết đơn hàng ID: $id (Đang hoàn thiện ở Batch 4)";
    }
}
