@php
    $ledger = DB::table('stock_ledger')
        ->join('inventory_items', 'stock_ledger.inventory_item_id', '=', 'inventory_items.id')
        ->join('warehouses', 'stock_ledger.warehouse_id', '=', 'warehouses.id')
        ->select('stock_ledger.*', 'inventory_items.name as item_name', 'inventory_items.item_code', 'warehouses.name as warehouse_name')
        ->orderBy('stock_ledger.id', 'desc')
        ->get();
    $totalValuation = DB::table('inventory_items')->sum(DB::raw('qty_on_hand * unit_price'));
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-journal-text me-2 text-primary"></i>Stock Ledger & Inventory Valuation</h4>
            <p class="text-muted small mb-0">Movement audit log (IN/OUT/TRANSFER), Valuation modes (FIFO, Weighted Average)</p>
        </div>
        <button class="btn btn-outline-primary rounded-pill shadow-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Export Stock Valuation
        </button>
    </div>

    <!-- Total Valuation Widget -->
    <div class="card border-0 shadow-sm p-4 bg-primary text-white mb-4 rounded-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="opacity-75">Total Enterprise Stock Asset Valuation (Weighted Average)</span>
                <h2 class="fw-bold mb-0 mt-1">₹{{ number_format($totalValuation, 2) }}</h2>
            </div>
            <i class="bi bi-boxes fs-1 opacity-50"></i>
        </div>
    </div>

    <!-- Stock Ledger Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Time</th>
                            <th>Item Code / Name</th>
                            <th>Warehouse</th>
                            <th>Voucher Type & No</th>
                            <th class="text-end text-success">Qty In</th>
                            <th class="text-end text-danger">Qty Out</th>
                            <th class="text-end fw-bold">Balance Qty</th>
                            <th class="text-end pe-4">Total Valuation (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ledger as $sl)
                            <tr>
                                <td class="ps-4 text-muted small">{{ $sl->created_at }}</td>
                                <td><span class="font-monospace text-primary fw-bold">{{ $sl->item_code }}</span> - {{ $sl->item_name }}</td>
                                <td>{{ $sl->warehouse_name }}</td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-dark me-1">{{ $sl->voucher_type }}</span>
                                    <span class="font-monospace text-muted">{{ $sl->voucher_no }}</span>
                                </td>
                                <td class="text-end font-monospace text-success fw-bold">{{ $sl->qty_in > 0 ? '+' . $sl->qty_in : '-' }}</td>
                                <td class="text-end font-monospace text-danger fw-bold">{{ $sl->qty_out > 0 ? '-' . $sl->qty_out : '-' }}</td>
                                <td class="text-end font-monospace fw-bold">{{ $sl->balance_qty }}</td>
                                <td class="text-end font-monospace fw-bold pe-4">₹{{ number_format($sl->total_valuation, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-4 text-muted">No stock movements recorded in ledger yet. Stock entries auto-record on GRN and dispatches.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
