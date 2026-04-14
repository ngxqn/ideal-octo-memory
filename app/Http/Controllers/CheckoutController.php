<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceOrderRequest;
use App\Services\OrderService;
use App\Models\Cart;
use Illuminate\Http\Request;
use Exception;

class CheckoutController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $cart = $user->cart;

        if (!$cart || $cart->cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('warning', 'Giỏ hàng của bạn đang trống.');
        }

        foreach ($cart->cartItems as $item) {
            if (!$item->product || $item->product->is_hidden || ($item->product->category && $item->product->category->is_hidden)) {
                return redirect()->route('cart.index')->with('warning', 'Giỏ hàng chứa sản phẩm không khả dụng. Vui lòng kiểm tra lại giỏ hàng của bạn.');
            }
        }

        $total = $cart->cartItems->reduce(function ($carry, $item) {
            return $carry + ($item->product->sell_price * $item->quantity);
        }, 0);

        $addresses = $user->addresses()->orderBy('is_default', 'desc')->get();

        return view('checkout.index', compact('user', 'cart', 'total', 'addresses'));
    }

    public function store(PlaceOrderRequest $request)
    {
        try {
            $user = $request->user();
            $cart = Cart::where('user_id', $user->id)->firstOrFail();

            $order = $this->orderService->placeOrder($user, $cart, $request->validated());

            return redirect()->route('orders.show', $order->id)
                             ->with('success', 'Đơn hàng của bạn đã được đặt thành công.');
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['checkout' => $e->getMessage()]);
        }
    }
}
