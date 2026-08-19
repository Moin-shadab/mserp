@php
    $wos = DB::table('work_orders')
        ->join('inventory_items', 'work_orders.item_id', '=', 'inventory_items.id')
        ->join('boms', 'work_orders.bom_id', '=', 'boms.id')
        ->select('work_orders.*', 'inventory_items.name as item_name', 'inventory_items.item_code', 'boms.bom_no')
        ->orderBy('work_orders.id', 'desc')
        ->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-cpu me-2 text-primary"></i>Work Orders & Production Routing</h4>
            <p class="text-muted small mb-0">Production scheduling, routing operations through work centers & status tracking</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" onclick="alert('Creating Work Order...')">
            <i class="bi bi-plus-circle me-1"></i> Release Work Order
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Work Order No</th>
                            <th>BOM Reference</th>
                            <th>Item to Produce</th>
                            <th class="text-end">Batch Target Qty</th>
                            <th>Planned Dates</th>
                            <th class="text-center pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wos as $wo)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">{{ $wo->work_order_no }}</td>
                                <td><span class="font-monospace text-muted">{{ $wo->bom_no }}</span></td>
                                <td><span class="font-monospace text-muted">{{ $wo->item_code }}</span> - {{ $wo->item_name }}</td>
                                <td class="text-end font-monospace fw-bold">{{ $wo->qty }} Units</td>
                                <td>{{ $wo->start_date }} → {{ $wo->completion_date }}</td>
                                <td class="text-center pe-4">
                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1">{{ $wo->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">No active work orders.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
