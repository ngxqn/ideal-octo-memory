<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Hiển thị trang giỏ hàng.
     */
    public function index()
    {
        $cart = $this->cartService->getCartForUser(Auth::user());
        $cart->load('cartItems.product.category');
        
        $hasRemovedItems = false;
        foreach ($cart->cartItems as $item) {
            if (!$item->product || $item->product->is_hidden || ($item->product->category && $item->product->category->is_hidden)) {
                $this->cartService->removeFromCart($item->id);
                $hasRemovedItems = true;
            }
        }
        
        if ($hasRemovedItems) {
            $cart->refresh();
            $cart->load('cartItems.product.category');
            session()->flash('warning', 'Một số sản phẩm trong giỏ hàng không còn khả dụng và đã được tự động loại bỏ.');
        }

        $total = $this->cartService->getCartTotal($cart);

        return view('cart.index', compact('cart', 'total'));
    }

    /**
     * Thêm sản phẩm vào giỏ hàng.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $product = \App\Models\Product::visible()->find($request->product_id);
            if (!$product) {
                throw new Exception('Sản phẩm không khả dụng hoặc đã bị ẩn.');
            }

            $this->cartService->addToCart(
                Auth::user(),
                $request->product_id,
                $request->quantity
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã thêm sản phẩm vào giỏ hàng.',
                ]);
            }

            return redirect()->route('cart.index')->with('success', 'Đã thêm sản phẩm vào giỏ hàng.');
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cập nhật số lượng sản phẩm.
     */
    public function update(Request $request, $cartItemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $this->cartService->updateQuantity($cartItemId, $request->quantity);

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật số lượng.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng.
     */
    public function destroy($cartItemId)
    {
        try {
            $this->cartService->removeFromCart($cartItemId);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa sản phẩm.',
            ], 400);
        }
    }

    /**
     * Lấy tổng số lượng sản phẩm trong giỏ hàng.
     */
    public function getCount()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }

        $cart = $this->cartService->getCartForUser(Auth::user());
        $count = $cart->cartItems->sum('quantity');

        return response()->json(['count' => $count]);
    }
}
