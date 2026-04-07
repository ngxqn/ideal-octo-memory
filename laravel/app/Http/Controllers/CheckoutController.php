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

    public function index()
    {
        // Placeholder cho bước render UI
        return view('pages.checkout');
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
