<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGoodsReceiptRequest;
use App\Http\Requests\Admin\UpdateGoodsReceiptRequest;
use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Services\GoodsReceiptService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoodsReceiptController extends Controller
{
    protected GoodsReceiptService $goodsReceiptService;

    public function __construct(GoodsReceiptService $goodsReceiptService)
    {
        $this->goodsReceiptService = $goodsReceiptService;
    }

    public function index()
    {
        $receipts = GoodsReceipt::with('details.product')->orderBy('id', 'desc')->get();
        // Get all products that are NOT hidden so the admin can select them for purchasing
        $products = Product::where('is_hidden', false)->get(['id', 'name', 'base_price', 'sku']);

        return view('admin.goods-receipts.index', compact('receipts', 'products'));
    }

    public function store(StoreGoodsReceiptRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $receiptData = [
                'supplier' => $data['supplier'],
                'note' => $data['note'] ?? null,
                'status' => $data['status']
            ];
            
            $receipt = $this->goodsReceiptService->createReceipt($request->user(), $receiptData, $data['items']);

            return response()->json([
                'success' => true,
                'message' => "Phiếu nhập hàng #{$receipt->id} đã được tạo thành công.",
                'receipt' => $receipt
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function show(GoodsReceipt $goodsReceipt): JsonResponse
    {
        $goodsReceipt->load('details.product');
        return response()->json([
            'success' => true,
            'receipt' => $goodsReceipt
        ]);
    }

    public function update(UpdateGoodsReceiptRequest $request, GoodsReceipt $goodsReceipt): JsonResponse
    {
        try {
            $data = $request->validated();
            $receiptData = [
                'supplier' => $data['supplier'],
                'note' => $data['note'] ?? null,
                'status' => $data['status']
            ];
            
            $receipt = $this->goodsReceiptService->updateReceipt($goodsReceipt->id, $receiptData, $data['items']);

            return response()->json([
                'success' => true,
                'message' => "Phiếu nhập hàng #{$receipt->id} đã được cập nhật thành công.",
                'receipt' => $receipt
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function complete(GoodsReceipt $goodsReceipt): JsonResponse
    {
        try {
            $receipt = $this->goodsReceiptService->completeReceipt($goodsReceipt->id);

            return response()->json([
                'success' => true,
                'message' => "Phiếu nhập hàng #{$receipt->id} đã hoàn thành, tồn kho và giá cả đã được đồng bộ.",
                'receipt' => $receipt
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy(GoodsReceipt $goodsReceipt): JsonResponse
    {
        try {
            $this->goodsReceiptService->deleteReceipt($goodsReceipt->id);

            return response()->json([
                'success' => true,
                'message' => "Phiếu nhập hàng đã được xóa thành công."
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
