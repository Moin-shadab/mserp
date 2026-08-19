@php
    $pos = DB::table('purchase_orders')->join('vendors', 'purchase_orders.vendor_id', '=', 'vendors.id')->select('purchase_orders.*', 'vendors.name as vendor_name')->get();
    $matches = DB::table('three_way_matches')->orderBy('id', 'desc')->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-check2-all me-2 text-primary"></i>Three-Way Matching Verification</h4>
            <p class="text-muted small mb-0">Automated 3-way match validation between Purchase Order, Goods Receipt (GRN) & Vendor Invoice</p>
        </div>
    </div>

    <!-- Match Form Card -->
    <div class="card border-0 shadow-sm rounded-3 p-4 mb-4 bg-light">
        <h6 class="fw-bold mb-3"><i class="bi bi-shield-check me-2 text-primary"></i>Verify Vendor Invoice Against PO & GRN</h6>
        <form id="threeWayForm">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Select Purchase Order</label>
                    <select name="purchase_order_id" class="form-select" required>
                        <option value="">Select PO...</option>
                        @foreach($pos as $po)
                            <option value="{{ $po->id }}">{{ $po->po_no }} - {{ $po->vendor_name }} (₹{{ number_format($po->total_amount, 2) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Supplier Invoice No</label>
                    <input type="text" name="vendor_invoice_no" class="form-control" placeholder="e.g. INV-VENDOR-8821" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Billed Invoice Amount (₹)</label>
                    <input type="number" step="0.01" name="invoice_amount" class="form-control" placeholder="Amount (₹)" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill"><i class="bi bi-play-circle me-1"></i> Perform Match</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Matches Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0">Three-Way Verification Audit Log</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">PO ID</th>
                            <th>Vendor Invoice No</th>
                            <th class="text-end">PO Amount (₹)</th>
                            <th class="text-end">GRN Value (₹)</th>
                            <th class="text-end">Vendor Billed (₹)</th>
                            <th class="text-end">Variance (₹)</th>
                            <th class="text-center pe-4">Verification Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($matches as $m)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">PO #{{ $m->purchase_order_id }}</td>
                                <td class="font-monospace fw-semibold">{{ $m->vendor_invoice_no }}</td>
                                <td class="text-end font-monospace">₹{{ number_format($m->po_amount, 2) }}</td>
                                <td class="text-end font-monospace">₹{{ number_format($m->grn_amount, 2) }}</td>
                                <td class="text-end font-monospace fw-bold">₹{{ number_format($m->invoice_amount, 2) }}</td>
                                <td class="text-end font-monospace {{ $m->variance > 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format($m->variance, 2) }}</td>
                                <td class="text-center pe-4">
                                    @if($m->is_matched)
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1"><i class="bi bi-check-circle-fill me-1"></i>MATCHED (OK)</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1"><i class="bi bi-exclamation-triangle-fill me-1"></i>VARIANCE HOLD</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">No 3-way matches executed yet. Use the form above to verify a vendor invoice.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('threeWayForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('/api/erp/purchase/3way-match', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.is_matched) {
            alert('3-Way Match Passed! Variance: ₹' + data.variance + '. Payment release approved.');
            location.reload();
        } else {
            alert('3-Way Match Failed! Variance detected: ₹' + data.variance + '. Placed on Variance Hold.');
            location.reload();
        }
    })
    .catch(err => alert('Error executing 3-way match.'));
});
</script>
