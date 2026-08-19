@php
    $deliveries = DB::table('delivery_notes')
        ->join('customers', 'delivery_notes.customer_id', '=', 'customers.id')
        ->select('delivery_notes.*', 'customers.name as customer_name')
        ->orderBy('delivery_notes.id', 'desc')
        ->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-truck me-2 text-primary"></i>Deliveries, Dispatches & Backorders</h4>
            <p class="text-muted small mb-0">Delivery Challans, vehicle dispatches, LR tracking & partial deliveries</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" onclick="alert('Creating Delivery Challan...')">
            <i class="bi bi-plus-circle me-1"></i> New Delivery Note
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Delivery No</th>
                            <th>Customer</th>
                            <th>Delivery Date</th>
                            <th>Vehicle No</th>
                            <th>LR / Transport No</th>
                            <th class="text-center pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deliveries as $dn)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">{{ $dn->delivery_no }}</td>
                                <td class="fw-semibold">{{ $dn->customer_name }}</td>
                                <td>{{ $dn->delivery_date }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $dn->vehicle_no ?? 'MH-04-AZ-9921' }}</span></td>
                                <td><span class="font-monospace text-muted">{{ $dn->lr_no ?? 'LR-88291' }}</span></td>
                                <td class="text-center pe-4"><span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">{{ $dn->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">No delivery challans issued yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
