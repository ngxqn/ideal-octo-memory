<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Exceptions\OrderAlreadyCancelledException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    protected OrderService $orderService;
    protected OrderRepositoryInterface $orderRepo;

    public function __construct(OrderService $orderService, OrderRepositoryInterface $orderRepo)
    {
        $this->orderService = $orderService;
        $this->orderRepo = $orderRepo;
    }

    public function index(Request $request)
    {
        $query = Order::query();

        // Keyword search
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function($q) use ($keyword) {
                $q->where('id', 'like', "%{$keyword}%")
                  ->orWhere('receiver_name', 'like', "%{$keyword}%")
                  ->orWhere('receiver_phone', 'like', "%{$keyword}%");
            });
        }

        // Status filter (all, pending, confirmed, delivered, cancelled)
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Sorting logic
        $allowedSorts = [
            'id', 'receiver_name', 'receiver_phone', 
            'shipping_address', 'shipping_commune', 'shipping_city', 
            'total_amount', 'status', 'created_at'
        ];

        $sort = in_array($request->sort, $allowedSorts) ? $request->sort : 'created_at';
        $direction = in_array($request->direction, ['asc', 'desc']) ? $request->direction : 'desc';

        $query->orderBy($sort, $direction);
        
        // Secondary sort by ID DESC for stable pagination
        if ($sort !== 'id') {
            $query->orderBy('id', 'desc');
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): JsonResponse
    {
        $order->load('orderDetails');
        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        $newStatus = $request->validated()['status'];
        $currentStatus = $order->status;

        try {
            // Strict 1-Way State Machine Check
            $isValidTransition = false;
            
            if ($currentStatus === 'pending') {
                $isValidTransition = in_array($newStatus, ['confirmed', 'cancelled']);
            } elseif ($currentStatus === 'confirmed') {
                $isValidTransition = in_array($newStatus, ['delivered', 'cancelled']);
            } elseif ($currentStatus === 'delivered' || $currentStatus === 'cancelled') {
                $isValidTransition = false; // Terminal states
            }

            if (!$isValidTransition) {
                throw new Exception("Chuyển đổi trạng thái không hợp lệ: Không thể chuyển từ '{$currentStatus}' sang '{$newStatus}'.");
            }

            // Execute Transition
            if ($newStatus === 'cancelled') {
                // Guard C1 implementation located inside OrderService
                $this->orderService->cancelOrder($order->id, $request->user());
                return response()->json([
                    'success' => true,
                    'message' => "Đơn hàng #{$order->id} đã được hủy và hoàn kho thành công.",
                    'status' => 'cancelled'
                ]);
            }

            $this->orderRepo->updateStatus($order->id, $newStatus);
            
            return response()->json([
                'success' => true,
                'message' => "Trạng thái đơn hàng #{$order->id} đã được cập nhật thành: {$newStatus}.",
                'status' => $newStatus
            ]);
            
        } catch (OrderAlreadyCancelledException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
