<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Product;
use App\Models\Category;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CatalogueController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Display the unified catalogue page (Products + Categories tabs).
     */
    public function index()
    {
        $products = Product::with('category')
            ->withCount(['orderDetails', 'goodsReceiptDetails'])
            ->latest()
            ->get();
            
        $categories = Category::withCount('products')
            ->latest()
            ->get();
        
        return view('admin.catalogue.index', compact('products', 'categories'));
    }

    /**
     * Store a newly created product.
     */
    public function storeProduct(StoreProductRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $this->inventoryService->createProduct($data);

        return redirect()->route('admin.catalogue.index')->with('success', 'Sản phẩm đã được tạo thành công.');
    }

    /**
     * Update the specified product.
     */
    public function updateProduct(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image if it exists in storage
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.catalogue.index')->with('success', 'Sản phẩm đã được cập nhật.');
    }

    /**
     * Toggle visibility of a product.
     */
    public function toggleProductVisibility(Product $product)
    {
        $product->update(['is_hidden' => !$product->is_hidden]);
        $status = $product->is_hidden ? 'ẩn' : 'hiển thị';
        return redirect()->route('admin.catalogue.index')->with('success', "Sản phẩm đã được {$status}.");
    }

    /**
     * Remove the specified product (Hard delete if no trans, otherwise hide).
     */
    public function destroyProduct(Product $product)
    {
        $hasTransactions = DB::table('order_details')->where('product_id', $product->id)->exists() 
                        || DB::table('goods_receipt_details')->where('product_id', $product->id)->exists();

        if ($hasTransactions) {
            $product->update(['is_hidden' => true]);
            return redirect()->route('admin.catalogue.index')->with('success', 'Sản phẩm có giao dịch nên đã được chuyển sang trạng thái ẩn.');
        }

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();
        return redirect()->route('admin.catalogue.index')->with('success', 'Sản phẩm đã được xóa vĩnh viễn.');
    }

    /**
     * Store a newly created category.
     */
    public function storeCategory(StoreCategoryRequest $request)
    {
        Category::create($request->validated());
        return redirect()->route('admin.catalogue.index')->with('success', 'Loại sản phẩm đã được tạo.');
    }

    /**
     * Update the specified category.
     */
    public function updateCategory(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        return redirect()->route('admin.catalogue.index')->with('success', 'Loại sản phẩm đã được cập nhật.');
    }

    /**
     * Toggle visibility of a category.
     */
    public function toggleCategoryVisibility(Category $category)
    {
        $category->update(['is_hidden' => !$category->is_hidden]);
        $status = $category->is_hidden ? 'ẩn' : 'hoạt động';
        return redirect()->route('admin.catalogue.index')->with('success', "Loại sản phẩm đã được {$status}.");
    }

    /**
     * Remove the specified category (Hide if has products, else delete).
     */
    public function destroyCategory(Category $category)
    {
        if ($category->products()->exists()) {
            $category->update(['is_hidden' => true]);
            return redirect()->route('admin.catalogue.index')->with('success', 'Loại này có sản phẩm nên đã được ẩn đi.');
        }

        $category->delete();
        return redirect()->route('admin.catalogue.index')->with('success', 'Loại sản phẩm đã được xóa.');
    }
}
