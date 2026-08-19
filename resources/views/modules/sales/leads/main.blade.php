@php
    $leads = DB::table('leads')->orderBy('id', 'desc')->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-person-badge me-2 text-primary"></i>CRM Leads & Opportunity Pipeline</h4>
            <p class="text-muted small mb-0">Track prospect pipeline stages and convert qualified leads to customers</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#newLeadModal">
            <i class="bi bi-plus-circle me-1"></i> Add New Lead
        </button>
    </div>

    <!-- Lead Pipeline Grid -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-light border-top border-primary border-4 rounded-3">
                <span class="fw-bold text-primary">NEW PROSPECTS</span>
                <h3 class="fw-bold my-2">{{ $leads->where('stage', 'New')->count() }}</h3>
                <span class="small text-muted">Est. ₹{{ number_format($leads->where('stage', 'New')->sum('estimated_value'), 2) }}</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-light border-top border-warning border-4 rounded-3">
                <span class="fw-bold text-warning">CONTACTED</span>
                <h3 class="fw-bold my-2">{{ $leads->where('stage', 'Contacted')->count() }}</h3>
                <span class="small text-muted">Est. ₹{{ number_format($leads->where('stage', 'Contacted')->sum('estimated_value'), 2) }}</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-light border-top border-info border-4 rounded-3">
                <span class="fw-bold text-info">QUALIFIED</span>
                <h3 class="fw-bold my-2">{{ $leads->where('stage', 'Qualified')->count() }}</h3>
                <span class="small text-muted">Est. ₹{{ number_format($leads->where('stage', 'Qualified')->sum('estimated_value'), 2) }}</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-light border-top border-success border-4 rounded-3">
                <span class="fw-bold text-success">CONVERTED</span>
                <h3 class="fw-bold my-2">{{ $leads->where('stage', 'Converted')->count() }}</h3>
                <span class="small text-muted">Est. ₹{{ number_format($leads->where('stage', 'Converted')->sum('estimated_value'), 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Leads Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Title</th>
                            <th>Company / Contact</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th class="text-end">Est. Value (₹)</th>
                            <th>Pipeline Stage</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">{{ $lead->title }}</td>
                                <td>{{ $lead->company_name ?? $lead->contact_name }}</td>
                                <td>{{ $lead->email }}</td>
                                <td>{{ $lead->phone }}</td>
                                <td class="text-end font-monospace fw-semibold">₹{{ number_format($lead->estimated_value, 2) }}</td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1">{{ $lead->stage }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="alert('Converted Lead to Active Customer Account.')">Convert</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">No CRM leads recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
