@php
    $transfers = DB::table('stock_transfers')->orderBy('id', 'desc')->get();
    $warehouses = DB::table('warehouses')->get();
    $items = DB::table('inventory_items')->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Stock Transfers & Reservations</h4>
            <p class="text-muted small mb-0">Inter-warehouse stock dispatches, sales order stock reservations & negative stock controls</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#newTransferModal">
            <i class="bi bi-plus-circle me-1"></i> New Inter-Warehouse Transfer
        </button>
    </div>

    <!-- Negative Stock Control Badge -->
    <div class="alert alert-info border-0 shadow-sm rounded-3 d-flex align-items-center mb-4">
        <i class="bi bi-shield-check fs-4 me-3 text-info"></i>
        <div>
            <h6 class="fw-bold mb-0">Negative-Stock Control Policy Enforced</h6>
            <span class="small opacity-75">Transactions attempting to reduce inventory below zero are atomically blocked in DB.</span>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Transfer Voucher</th>
                            <th>Transfer Date</th>
                            <th>Origin Warehouse</th>
                            <th>Destination Warehouse</th>
                            <th class="text-center pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $t)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">{{ $t->transfer_no }}</td>
                                <td>{{ $t->transfer_date }}</td>
                                <td><span class="badge bg-light text-dark border">WH-MUM-MAIN</span></td>
                                <td><span class="badge bg-light text-dark border">WH-BLR-TECH</span></td>
                                <td class="text-center pe-4"><span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">{{ $t->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">No inter-warehouse stock transfers recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="newTransferModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Inter-Warehouse Stock Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="transferForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Source Warehouse (Dispatch)</label>
                            <select name="warehouse_id" class="form-select" required>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Destination Warehouse (Receipt)</label>
                            <select class="form-select" required>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Select Item to Transfer</label>
                            <select name="inventory_item_id" class="form-select" required>
                                @foreach($items as $it)
                                    <option value="{{ $it->id }}">{{ $it->item_code }} - {{ $it->name }} (Qty: {{ $it->qty_on_hand }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Transfer Quantity</label>
                            <input type="number" name="qty_out" class="form-control" placeholder="Qty" min="1" required>
                            <input type="hidden" name="voucher_type" value="INTER_WH_TRANSFER">
                            <input type="hidden" name="voucher_no" value="TRF-{{ date('Ymd-His') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Execute Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('transferForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('/api/erp/stock/record', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert('Stock Transfer Executed! New Balance: ' + data.new_balance);
            location.reload();
        } else {
            alert('Transfer Failed: ' + data.error);
        }
    })
    .catch(err => alert('Error executing stock transfer.'));
});
</script>
