<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class CatalogueController extends Controller
{
    /**
     * Display the unified catalogue page (Products + Categories tabs).
     */
    public function index()
    {
        $products = Product::active()->with('category')->get();
        $categories = Category::active()->get();
        
        return view('admin.catalogue.index', compact('products', 'categories'));
    }

    public function storeProduct(Request $request) { /* Implementation in Batch 6.2 */ }
    public function updateProduct(Request $request, Product $product) { /* Implementation in Batch 6.2 */ }
    public function destroyProduct(Product $product) { /* Implementation in Batch 6.2 */ }

    public function storeCategory(Request $request) { /* Implementation in Batch 6.2 */ }
    public function updateCategory(Request $request, Category $category) { /* Implementation in Batch 6.2 */ }
    public function destroyCategory(Category $category) { /* Implementation in Batch 6.2 */ }
}
