<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function index()
    {
        $products = Product::active()->get();
        return view('admin.pricing.index', compact('products'));
    }

    public function update(Request $request, Product $product)
    {
        // Update profit margin logic in Batch 6.3
    }
}
