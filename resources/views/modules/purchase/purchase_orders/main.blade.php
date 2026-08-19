@php
    $pos = DB::table('purchase_orders')
        ->join('vendors', 'purchase_orders.vendor_id', '=', 'vendors.id')
        ->select('purchase_orders.*', 'vendors.name as vendor_name')
        ->orderBy('purchase_orders.id', 'desc')
        ->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-bag-dash me-2 text-primary"></i>Purchase Orders & Goods Receipt Notes (GRN)</h4>
            <p class="text-muted small mb-0">Supplier procurement orders & warehouse receipt inspection</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" onclick="alert('Creating Purchase Order...')">
            <i class="bi bi-plus-circle me-1"></i> Create Purchase Order
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">PO Number</th>
                            <th>Vendor / Supplier</th>
                            <th>PO Date</th>
                            <th>Expected Delivery</th>
                            <th class="text-end">Subtotal (₹)</th>
                            <th class="text-end">Tax (₹)</th>
                            <th class="text-end">Total Amount (₹)</th>
                            <th class="text-center pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pos as $po)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">{{ $po->po_no }}</td>
                                <td class="fw-semibold">{{ $po->vendor_name }}</td>
                                <td>{{ $po->po_date }}</td>
                                <td>{{ $po->expected_delivery_date ?? 'N/A' }}</td>
                                <td class="text-end font-monospace">₹{{ number_format($po->subtotal, 2) }}</td>
                                <td class="text-end font-monospace">₹{{ number_format($po->tax_amount, 2) }}</td>
                                <td class="text-end font-monospace fw-bold">₹{{ number_format($po->total_amount, 2) }}</td>
                                <td class="text-center pe-4">
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">{{ $po->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-4 text-muted">No purchase orders created yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
