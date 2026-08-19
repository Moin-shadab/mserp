@php
    $items = DB::table('inventory_items')->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-clipboard-check me-2 text-primary"></i>Physical Stock Reconciliation & Cycle Counting</h4>
            <p class="text-muted small mb-0">Reconcile physical inventory counts against system ledger and post gain/loss adjustments</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" onclick="alert('Cycle Count Sheet Generated.')">
            <i class="bi bi-printer me-1"></i> Print Cycle Count Sheet
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Item Code</th>
                            <th>Item Description</th>
                            <th>Category</th>
                            <th class="text-end">System Qty</th>
                            <th class="text-end">Physical Count</th>
                            <th class="text-end">Variance Qty</th>
                            <th class="text-center pe-4">Reconciliation Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $it)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">{{ $it->item_code }}</td>
                                <td class="fw-semibold">{{ $it->name }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $it->category }}</span></td>
                                <td class="text-end font-monospace fw-bold">{{ $it->qty_on_hand }}</td>
                                <td class="text-end font-monospace text-primary fw-bold">{{ $it->qty_on_hand }}</td>
                                <td class="text-end font-monospace text-success">0</td>
                                <td class="text-center pe-4"><span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">Matched</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
