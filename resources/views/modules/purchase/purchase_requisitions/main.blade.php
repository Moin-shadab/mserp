@php
    $reqs = DB::table('purchase_requisitions')
        ->join('users', 'purchase_requisitions.requested_by', '=', 'users.id')
        ->select('purchase_requisitions.*', 'users.name as requester_name')
        ->orderBy('purchase_requisitions.id', 'desc')
        ->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Purchase Requisitions & RFQs</h4>
            <p class="text-muted small mb-0">Internal material requests & Request for Quotation (RFQ) supplier matrix</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" onclick="alert('Creating Purchase Requisition...')">
            <i class="bi bi-plus-circle me-1"></i> New Purchase Request
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Requisition No</th>
                            <th>Requested By</th>
                            <th>Required Date</th>
                            <th>Notes / Purpose</th>
                            <th class="text-center pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reqs as $r)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">{{ $r->requisition_no }}</td>
                                <td class="fw-semibold">{{ $r->requester_name }}</td>
                                <td>{{ $r->required_date }}</td>
                                <td>{{ $r->notes ?? 'Raw material restocking for Q3 production' }}</td>
                                <td class="text-center pe-4"><span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1">{{ $r->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">No purchase requisitions recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
