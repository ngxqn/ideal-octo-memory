<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return "Trang Sản Phẩm (Sẽ hoàn thiện ở Batch 2)";
    }

    public function show($id)
    {
        return "Chi tiết Sản Phẩm ID: $id (Sẽ hoàn thiện ở Batch 2)";
    }
}
