<?php

namespace App\Http\Controllers;

use App\Services\ErpModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ErpModuleController extends Controller
{
    protected $erpService;

    public function __construct(ErpModuleService $erpService)
    {
        $this->erpService = $erpService;
    }

    /**
     * Post a Journal Voucher.
     */
    public function storeJournalVoucher(Request $request)
    {
        $request->validate([
            'entry_date' => 'required|date',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|integer',
            'lines.*.debit' => 'numeric|min:0',
            'lines.*.credit' => 'numeric|min:0'
        ]);

        try {
            $res = $this->erpService->postJournalVoucher($request->all());
            return response()->json($res);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Perform 3-Way Match validation.
     */
    public function matchThreeWay(Request $request)
    {
        $request->validate([
            'purchase_order_id' => 'required|integer',
            'vendor_invoice_no' => 'required|string',
            'invoice_amount' => 'required|numeric|min:0'
        ]);

        try {
            $res = $this->erpService->performThreeWayMatch(
                $request->input('purchase_order_id'),
                $request->input('vendor_invoice_no'),
                (float)$request->input('invoice_amount')
            );
            return response()->json($res);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Record Stock Movement.
     */
    public function recordStock(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|integer',
            'inventory_item_id' => 'required|integer',
            'voucher_type' => 'required|string',
            'voucher_no' => 'required|string',
        ]);

        try {
            $res = $this->erpService->recordStockMovement($request->all());
            return response()->json($res);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Calculate GST rates and amounts.
     */
    public function calculateGst(Request $request)
    {
        $state = $request->input('customer_state', 'Maharashtra');
        $amount = (float)$request->input('amount', 0.00);
        $hsn = $request->input('hsn_code', '84713010');

        $calc = $this->erpService->calculateGst($state, $amount, $hsn);
        return response()->json($calc);
    }
}
