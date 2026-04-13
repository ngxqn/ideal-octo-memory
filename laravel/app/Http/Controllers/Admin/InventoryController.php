<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryLog;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        // 1. Stock Snapshot Panel
        $products = Product::with('category')
            ->orderBy('stock_quantity', 'asc')
            ->get();

        // 2. Ledger Panel with Filtering
        $query = InventoryLog::with('product');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('ref_type')) {
            $query->where('reference_type', $request->ref_type);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(30)->withQueryString();

        return view('admin.inventory.index', compact('products', 'logs'));
    }

    /**
     * AJAX endpoint for date-range analytics (Accordion)
     */
    public function report(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $reportData = InventoryLog::selectRaw('
                product_id,
                SUM(CASE WHEN change_amount > 0 THEN change_amount ELSE 0 END) as total_in,
                SUM(CASE WHEN change_amount < 0 THEN ABS(change_amount) ELSE 0 END) as total_out
            ')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->groupBy('product_id')
            ->with('product:id,name,sku,stock_quantity')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reportData,
            'summary' => [
                'total_in' => $reportData->sum('total_in'),
                'total_out' => $reportData->sum('total_out'),
            ]
        ]);
    }
}
