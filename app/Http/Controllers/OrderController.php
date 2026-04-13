<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected \App\Repositories\Interfaces\OrderRepositoryInterface $orderRepo;

    public function __construct(\App\Repositories\Interfaces\OrderRepositoryInterface $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    /**
     * Lịch sử mua hàng của người dùng.
     */
    public function index(Request $request)
    {
        $orders = $this->orderRepo->findByUser($request->user()->id);
        return view('orders.index', compact('orders'));
    }

    /**
     * Chi tiết đơn hàng của người dùng.
     */
    public function show($id, Request $request)
    {
        $order = $this->orderRepo->findById((int)$id);

        if (!$order || $order->user_id !== $request->user()->id) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }

        // Tải kèm orderDetails và product để hiển thị
        $order->load('orderDetails.product');

        return view('orders.show', compact('order'));
    }
}
