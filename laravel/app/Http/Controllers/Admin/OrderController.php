<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Exceptions\OrderAlreadyCancelledException;
use Exception;

class OrderController extends Controller
{
    protected OrderService $orderService;
    protected OrderRepositoryInterface $orderRepo;

    public function __construct(OrderService $orderService, OrderRepositoryInterface $orderRepo)
    {
        $this->orderService = $orderService;
        $this->orderRepo = $orderRepo;
    }

    public function index()
    {
        // Placeholder cho bước render UI
        return view('admin.orders.index');
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $status = $request->validated()['status'];

        try {
            if ($status === 'cancelled') {
                // Sử dụng Guard C1 bọc trong Service cho logic hủy đơn
                $this->orderService->cancelOrder($order->id, $request->user());
                return redirect()->back()->with('success', "Đơn hàng #{$order->id} đã được hủy và hoàn kho thành công.");
            }

            // Đối với các trạng thái khác, cập nhật bình thường (giả sử không có validation state machine phức tạp cho pending->confirmed ngoài DB constraint)
            if ($order->status === 'cancelled' || $order->status === 'delivered') {
                throw new Exception("Không thể thay đổi trạng thái của đơn hàng đã hủy hoặc đã giao.");
            }

            $this->orderRepo->updateStatus($order->id, $status);
            
            return redirect()->back()->with('success', "Trạng thái đơn hàng #{$order->id} đã được cập nhật thành: {$status}.");
            
        } catch (OrderAlreadyCancelledException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
