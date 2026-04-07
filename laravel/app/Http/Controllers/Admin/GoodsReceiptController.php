<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGoodsReceiptRequest;
use App\Services\GoodsReceiptService;
use Exception;

class GoodsReceiptController extends Controller
{
    protected GoodsReceiptService $goodsReceiptService;

    public function __construct(GoodsReceiptService $goodsReceiptService)
    {
        $this->goodsReceiptService = $goodsReceiptService;
    }

    public function index()
    {
        // Placeholder cho bước render UI
        return view('admin.goods-receipts.index');
    }

    public function store(StoreGoodsReceiptRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Extract attributes from items
            $receiptData = array_diff_key($data, array_flip(['items']));
            $itemsData = $data['items'];

            $receipt = $this->goodsReceiptService->createReceipt($request->user(), $receiptData, $itemsData);

            return redirect()->route('admin.goods-receipts.index')
                             ->with('success', "Phiếu nhập hàng #{$receipt->id} đã được tạo thành công.");
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
