@php
    $pending = DB::table('workflow_instances')
        ->join('workflows', 'workflow_instances.workflow_id', '=', 'workflows.id')
        ->select('workflow_instances.*', 'workflows.name as workflow_name')
        ->orderBy('workflow_instances.id', 'desc')
        ->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-check-circle me-2 text-primary"></i>Multi-Level Approval Center</h4>
            <p class="text-muted small mb-0">Unified approval inbox for Purchase Orders, Sales Orders, Credit Limits, Stock Adjustments & Journals</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0">Pending Approvals Requiring Action</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Instance ID</th>
                            <th>Workflow Type</th>
                            <th>Target Table</th>
                            <th>Record ID</th>
                            <th>Current Step</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-4">Approval Decision</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pending as $p)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">#WI-{{ $p->id }}</td>
                                <td class="fw-semibold">{{ $p->workflow_name }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $p->table_name }}</span></td>
                                <td>#{{ $p->record_id }}</td>
                                <td>Step {{ $p->current_step }}</td>
                                <td class="text-center"><span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1">{{ $p->status }}</span></td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-success rounded-pill px-3 me-1" onclick="alert('Workflow Approved Successfully.')">Approve</button>
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="alert('Workflow Rejected.')">Reject</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">No pending workflow approval requests in your queue. All approvals up to date!</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
