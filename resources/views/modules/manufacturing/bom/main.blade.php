@php
    $boms = DB::table('boms')
        ->join('inventory_items', 'boms.item_id', '=', 'inventory_items.id')
        ->select('boms.*', 'inventory_items.name as item_name', 'inventory_items.item_code')
        ->orderBy('boms.id', 'desc')
        ->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-diagram-2 me-2 text-primary"></i>Bill of Materials (BOM) & Multi-Level BOM</h4>
            <p class="text-muted small mb-0">Multi-level raw material component listing, scrap % estimation & unit production cost</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" onclick="alert('Creating Bill of Materials...')">
            <i class="bi bi-plus-circle me-1"></i> Create BOM
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">BOM Number</th>
                            <th>Target Product</th>
                            <th class="text-end">Base Output Qty</th>
                            <th class="text-end">Estimated Unit Cost (₹)</th>
                            <th class="text-center pe-4">BOM Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($boms as $b)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">{{ $b->bom_no }}</td>
                                <td><span class="font-monospace text-muted">{{ $b->item_code }}</span> - {{ $b->item_name }}</td>
                                <td class="text-end font-monospace">{{ $b->qty }} Unit</td>
                                <td class="text-end font-monospace fw-bold text-success">₹{{ number_format($b->total_cost, 2) }}</td>
                                <td class="text-center pe-4"><span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">Active</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">No Bills of Materials configured yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
