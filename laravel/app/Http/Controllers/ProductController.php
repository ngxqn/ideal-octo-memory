<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return "Danh Sách Sản Phẩm (Đang hoàn thiện ở Batch 2)";
    }

    public function show($product)
    {
        return "Chi tiết sản phẩm " . $product;
    }
}
