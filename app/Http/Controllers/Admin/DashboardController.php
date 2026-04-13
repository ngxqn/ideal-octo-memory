<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $lowStockThreshold = 20;

        $totalProducts = Product::active()->count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalRevenue = Order::where('status', 'delivered')->sum('total_amount');

        $lowStockProducts = Product::active()
            ->where('stock_quantity', '<', $lowStockThreshold)
            ->orderBy('stock_quantity', 'asc')
            ->take(5)
            ->get();

        $lowProfitProducts = Product::active()
            ->where('profit_margin', '<=', 10)
            ->orderBy('profit_margin', 'asc')
            ->take(5)
            ->get();

        $recentOrders = Order::latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'pendingOrders',
            'totalRevenue',
            'lowStockProducts',
            'lowProfitProducts',
            'recentOrders',
            'lowStockThreshold'
        ));
    }
}
