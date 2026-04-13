<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function index()
    {
        // Fetch all products (including hidden ones) with their categories
        $products = Product::with('category')->get();
        return view('admin.pricing.index', compact('products'));
    }

    public function update(\App\Http\Requests\Admin\UpdatePricingRequest $request, Product $product)
    {
        // Update the profit margin. 'sell_price' is automatically recalculated by MySQL.
        $product->update([
            'profit_margin' => $request->profit_margin
        ]);

        // Refresh the product to retrieve the newly generated 'sell_price' from the database
        $product->refresh();

        // Return a JSON response for the frontend AJAX call
        return response()->json([
            'success' => true,
            'message' => "Đã lưu giá bán cho {$product->name}",
            'product' => [
                'id' => $product->id,
                'profit_margin' => $product->profit_margin,
                'sell_price' => $product->sell_price
            ]
        ]);
    }
}
