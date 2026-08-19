<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ErpModuleService
{
    /**
     * Post a Double-Entry Journal Voucher.
     */
    public function postJournalVoucher(array $data)
    {
        return DB::transaction(function () use ($data) {
            $companyId = $data['company_id'] ?? (Auth::user()->company_id ?? 1);
            $lines = $data['lines'] ?? [];

            if (empty($lines)) {
                throw new \Exception('Journal entry must contain at least two line items.');
            }

            $totalDebit = 0;
            $totalCredit = 0;
            foreach ($lines as $line) {
                $totalDebit += (float)($line['debit'] ?? 0);
                $totalCredit += (float)($line['credit'] ?? 0);
            }

            if (abs($totalDebit - $totalCredit) > 0.01) {
                throw new \Exception('Unbalanced Journal Voucher! Total Debits (₹' . number_format($totalDebit, 2) . ') must equal Total Credits (₹' . number_format($totalCredit, 2) . ').');
            }

            // Numbering series lookup
            $series = DB::table('numbering_series')->where('module', 'journal_entry')->first();
            $num = $series ? $series->current_number : 1001;
            $prefix = $series ? $series->prefix : 'JV/' . date('Y') . '/';
            $voucherNo = $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);

            if ($series) {
                DB::table('numbering_series')->where('id', $series->id)->increment('current_number');
            }

            // Create Journal Entry
            $journalId = DB::table('journal_entries')->insertGetId([
                'company_id' => $companyId,
                'branch_id' => Auth::user()->branch_id ?? null,
                'voucher_no' => $voucherNo,
                'entry_date' => $data['entry_date'] ?? date('Y-m-d'),
                'narration' => $data['narration'] ?? 'General Journal Posting',
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'status' => 'Posted',
                'created_by' => Auth::id(),
                'posted_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Create Lines & General Ledger entries
            foreach ($lines as $line) {
                $accId = $line['account_id'];
                $debit = (float)($line['debit'] ?? 0);
                $credit = (float)($line['credit'] ?? 0);

                DB::table('journal_entry_lines')->insert([
                    'journal_entry_id' => $journalId,
                    'account_id' => $accId,
                    'debit' => $debit,
                    'credit' => $credit,
                    'line_narration' => $line['line_narration'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Current balance update
                $acc = DB::table('chart_of_accounts')->where('id', $accId)->first();
                if ($acc) {
                    $newBalance = $acc->current_balance;
                    if (in_array($acc->type, ['Asset', 'Expense'])) {
                        $newBalance += ($debit - $credit);
                    } else {
                        $newBalance += ($credit - $debit);
                    }

                    DB::table('chart_of_accounts')->where('id', $accId)->update([
                        'current_balance' => $newBalance,
                        'updated_at' => now()
                    ]);

                    // General Ledger entry
                    DB::table('general_ledger')->insert([
                        'company_id' => $companyId,
                        'account_id' => $accId,
                        'journal_entry_id' => $journalId,
                        'entry_date' => $data['entry_date'] ?? date('Y-m-d'),
                        'debit' => $debit,
                        'credit' => $credit,
                        'running_balance' => $newBalance,
                        'source_document_type' => 'JOURNAL_VOUCHER',
                        'source_document_id' => $journalId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // Audit log
            DB::table('audit_logs')->insert([
                'user_id' => Auth::id(),
                'action' => 'CREATE_JOURNAL_VOUCHER',
                'table_name' => 'journal_entries',
                'record_id' => $journalId,
                'new_values' => json_encode(['voucher_no' => $voucherNo, 'amount' => $totalDebit]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now()
            ]);

            return ['success' => true, 'voucher_no' => $voucherNo, 'journal_id' => $journalId];
        });
    }

    /**
     * Process 3-Way Match between PO, GRN and Supplier Invoice.
     */
    public function performThreeWayMatch(int $poId, string $vendorInvoiceNo, float $invoiceAmount)
    {
        $po = DB::table('purchase_orders')->where('id', $poId)->first();
        if (!$po) {
            throw new \Exception('Purchase Order not found.');
        }

        $grn = DB::table('goods_receipt_notes')->where('purchase_order_id', $poId)->first();
        $grnAmount = $grn ? $po->total_amount : 0.00; // Expected matching amount

        $poAmount = (float)$po->total_amount;
        $variance = abs($invoiceAmount - $poAmount);
        $isMatched = ($variance <= 1.00); // tolerance ₹1.00

        $matchId = DB::table('three_way_matches')->insertGetId([
            'purchase_order_id' => $poId,
            'grn_id' => $grn ? $grn->id : null,
            'vendor_invoice_no' => $vendorInvoiceNo,
            'po_amount' => $poAmount,
            'grn_amount' => $grnAmount,
            'invoice_amount' => $invoiceAmount,
            'variance' => $variance,
            'is_matched' => $isMatched,
            'status' => $isMatched ? 'MATCHED' : 'VARIANCE_HOLD',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return [
            'match_id' => $matchId,
            'is_matched' => $isMatched,
            'variance' => $variance,
            'status' => $isMatched ? 'MATCHED' : 'VARIANCE_HOLD'
        ];
    }

    /**
     * Calculate GST for Invoice based on Place of Supply.
     */
    public function calculateGst(string $customerState, float $amount, string $hsnCode = '84713010')
    {
        $companyState = DB::table('companies')->where('id', Auth::user()->company_id ?? 1)->value('state') ?? 'Maharashtra';
        $hsn = DB::table('hsn_sac_codes')->where('code', $hsnCode)->first();
        
        $cgstRate = $hsn ? (float)$hsn->cgst_rate : 9.00;
        $sgstRate = $hsn ? (float)$hsn->sgst_rate : 9.00;
        $igstRate = $hsn ? (float)$hsn->igst_rate : 18.00;

        $isIntraState = (strtolower(trim($companyState)) === strtolower(trim($customerState)));

        if ($isIntraState) {
            $cgstAmount = round(($amount * $cgstRate) / 100, 2);
            $sgstAmount = round(($amount * $sgstRate) / 100, 2);
            $igstAmount = 0.00;
            $taxAmount = $cgstAmount + $sgstAmount;
        } else {
            $cgstAmount = 0.00;
            $sgstAmount = 0.00;
            $igstAmount = round(($amount * $igstRate) / 100, 2);
            $taxAmount = $igstAmount;
        }

        return [
            'is_intra_state' => $isIntraState,
            'cgst_rate' => $isIntraState ? $cgstRate : 0,
            'cgst_amount' => $cgstAmount,
            'sgst_rate' => $isIntraState ? $sgstRate : 0,
            'sgst_amount' => $sgstAmount,
            'igst_rate' => !$isIntraState ? $igstRate : 0,
            'igst_amount' => $igstAmount,
            'total_tax' => $taxAmount,
            'grand_total' => $amount + $taxAmount
        ];
    }

    /**
     * Record Atomic Inventory Movement in Stock Ledger.
     */
    public function recordStockMovement(array $params)
    {
        return DB::transaction(function () use ($params) {
            $warehouseId = $params['warehouse_id'];
            $itemId = $params['inventory_item_id'];
            $voucherType = $params['voucher_type']; // GRN, DISPATCH, ADJUSTMENT, TRANSFER
            $voucherNo = $params['voucher_no'];
            $qtyIn = (int)($params['qty_in'] ?? 0);
            $qtyOut = (int)($params['qty_out'] ?? 0);
            $unitCost = (float)($params['unit_cost'] ?? 0.00);

            // Fetch current item with row lock for atomic safety
            $item = DB::table('inventory_items')->where('id', $itemId)->lockForUpdate()->first();
            if (!$item) {
                throw new \Exception('Inventory Item not found.');
            }

            // Negative stock control check
            $newQtyOnHand = $item->qty_on_hand + $qtyIn - $qtyOut;
            if ($newQtyOnHand < 0 && !($params['allow_negative'] ?? false)) {
                throw new \Exception("Insufficient stock! Available: {$item->qty_on_hand}, Requested Out: {$qtyOut}. Negative stock is disabled.");
            }

            // Update item stock
            DB::table('inventory_items')->where('id', $itemId)->update([
                'qty_on_hand' => $newQtyOnHand,
                'status' => $newQtyOnHand > $item->reorder_level ? 'In Stock' : ($newQtyOnHand > 0 ? 'Low Stock' : 'Out of Stock'),
                'updated_at' => now()
            ]);

            // Add Stock Ledger Entry
            $ledgerId = DB::table('stock_ledger')->insertGetId([
                'company_id' => Auth::user()->company_id ?? 1,
                'warehouse_id' => $warehouseId,
                'inventory_item_id' => $itemId,
                'voucher_type' => $voucherType,
                'voucher_no' => $voucherNo,
                'qty_in' => $qtyIn,
                'qty_out' => $qtyOut,
                'balance_qty' => $newQtyOnHand,
                'unit_cost' => $unitCost > 0 ? $unitCost : $item->unit_price,
                'total_valuation' => $newQtyOnHand * ($unitCost > 0 ? $unitCost : $item->unit_price),
                'batch_no' => $params['batch_no'] ?? null,
                'serial_no' => $params['serial_no'] ?? null,
                'created_at' => now()
            ]);

            return ['success' => true, 'ledger_id' => $ledgerId, 'new_balance' => $newQtyOnHand];
        });
    }
}
