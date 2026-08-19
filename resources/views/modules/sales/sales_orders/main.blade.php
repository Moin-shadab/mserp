@php
    $orders = DB::table('sales_orders')
        ->join('customers', 'sales_orders.customer_id', '=', 'customers.id')
        ->select('sales_orders.*', 'customers.name as customer_name')
        ->orderBy('sales_orders.id', 'desc')
        ->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-cart4 me-2 text-primary"></i>Sales Quotations & Sales Orders</h4>
            <p class="text-muted small mb-0">Manage customer orders, credit limits check, pricing lists & discounts</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#newOrderModal">
            <i class="bi bi-plus-circle me-1"></i> Create Sales Order
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Order No</th>
                            <th>Customer Name</th>
                            <th>Order Date</th>
                            <th>Delivery Date</th>
                            <th class="text-end">Subtotal (₹)</th>
                            <th class="text-end">Tax (₹)</th>
                            <th class="text-end">Total (₹)</th>
                            <th class="text-center">Credit Approved</th>
                            <th class="text-center pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $so)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">{{ $so->order_no }}</td>
                                <td class="fw-semibold">{{ $so->customer_name }}</td>
                                <td>{{ $so->order_date }}</td>
                                <td>{{ $so->delivery_date ?? 'N/A' }}</td>
                                <td class="text-end font-monospace">₹{{ number_format($so->subtotal, 2) }}</td>
                                <td class="text-end font-monospace">₹{{ number_format($so->tax_amount, 2) }}</td>
                                <td class="text-end font-monospace fw-bold">₹{{ number_format($so->total_amount, 2) }}</td>
                                <td class="text-center">
                                    <span class="badge bg-success-subtle text-success"><i class="bi bi-shield-check me-1"></i>Pass</span>
                                </td>
                                <td class="text-center pe-4">
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1">{{ $so->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center py-4 text-muted">No sales orders created yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
