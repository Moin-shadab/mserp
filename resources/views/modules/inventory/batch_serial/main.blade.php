@php
    $batches = DB::table('item_batches')
        ->join('inventory_items', 'item_batches.inventory_item_id', '=', 'inventory_items.id')
        ->select('item_batches.*', 'inventory_items.name as item_name', 'inventory_items.item_code')
        ->get();
    $serials = DB::table('item_serials')
        ->join('inventory_items', 'item_serials.inventory_item_id', '=', 'inventory_items.id')
        ->select('item_serials.*', 'inventory_items.name as item_name', 'inventory_items.item_code')
        ->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-upc-scan me-2 text-primary"></i>Batch, Lot & Serial Tracking</h4>
            <p class="text-muted small mb-0">Track manufacture/expiry dates on item batches and unique serial numbers</p>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-pills mb-4">
        <li class="nav-item">
            <button class="nav-link active rounded-pill px-4 fw-bold" data-bs-toggle="tab" data-bs-target="#batchesTab">
                <i class="bi bi-boxes me-1"></i> Batch / Lot Numbers
            </button>
        </li>
        <li class="nav-item ms-2">
            <button class="nav-link rounded-pill px-4 fw-bold" data-bs-toggle="tab" data-bs-target="#serialsTab">
                <i class="bi bi-upc me-1"></i> Unique Serial Numbers
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Batches -->
        <div class="tab-pane fade show active" id="batchesTab">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Batch No</th>
                                <th>Item Code / Name</th>
                                <th>Mfg Date</th>
                                <th>Expiry Date</th>
                                <th class="text-end pe-4">Qty on Hand</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($batches as $b)
                                <tr>
                                    <td class="ps-4 font-monospace fw-bold text-primary">{{ $b->batch_no }}</td>
                                    <td><span class="font-monospace text-muted">{{ $b->item_code }}</span> - {{ $b->item_name }}</td>
                                    <td>{{ $b->manufacture_date ?? '2026-01-01' }}</td>
                                    <td><span class="badge bg-warning-subtle text-dark">{{ $b->expiry_date ?? '2028-12-31' }}</span></td>
                                    <td class="text-end font-monospace fw-bold pe-4">{{ $b->qty_on_hand }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted">No batch tracking items recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Serials -->
        <div class="tab-pane fade" id="serialsTab">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Serial Number</th>
                                <th>Item Code / Name</th>
                                <th class="text-center pe-4">Serial Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($serials as $s)
                                <tr>
                                    <td class="ps-4 font-monospace fw-bold text-primary">{{ $s->serial_no }}</td>
                                    <td><span class="font-monospace text-muted">{{ $s->item_code }}</span> - {{ $s->item_name }}</td>
                                    <td class="text-center pe-4"><span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">{{ $s->status }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center py-4 text-muted">No unique item serial numbers registered yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
