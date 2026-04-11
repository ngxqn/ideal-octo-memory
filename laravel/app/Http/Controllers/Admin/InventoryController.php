<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryLog;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $logs = InventoryLog::with('product')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.inventory.index', compact('logs'));
    }
}
