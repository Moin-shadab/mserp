@php
    $accounts = DB::table('chart_of_accounts')->orderBy('code')->get();
    $summary = [
        'Asset' => $accounts->where('type', 'Asset')->sum('current_balance'),
        'Liability' => $accounts->where('type', 'Liability')->sum('current_balance'),
        'Equity' => $accounts->where('type', 'Equity')->sum('current_balance'),
        'Income' => $accounts->where('type', 'Income')->sum('current_balance'),
        'Expense' => $accounts->where('type', 'Expense')->sum('current_balance'),
    ];
@endphp

<div class="p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-diagram-3 me-2 text-primary"></i>Chart of Accounts</h4>
            <p class="text-muted small mb-0">Hierarchical double-entry account classification & live balances</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addAccountModal">
            <i class="bi bi-plus-circle me-1"></i> Add Account
        </button>
    </div>

    <!-- Summary Widgets -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm bg-primary text-white p-3 rounded-3">
                <span class="small opacity-75">Assets Balance</span>
                <h5 class="fw-bold mb-0 mt-1">₹{{ number_format($summary['Asset'], 2) }}</h5>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm bg-danger text-white p-3 rounded-3">
                <span class="small opacity-75">Liabilities</span>
                <h5 class="fw-bold mb-0 mt-1">₹{{ number_format($summary['Liability'], 2) }}</h5>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm bg-dark text-white p-3 rounded-3">
                <span class="small opacity-75">Equity</span>
                <h5 class="fw-bold mb-0 mt-1">₹{{ number_format($summary['Equity'], 2) }}</h5>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white p-3 rounded-3">
                <span class="small opacity-75">Revenue Income</span>
                <h5 class="fw-bold mb-0 mt-1">₹{{ number_format($summary['Income'], 2) }}</h5>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-dark p-3 rounded-3">
                <span class="small opacity-75">Total Expenses</span>
                <h5 class="fw-bold mb-0 mt-1">₹{{ number_format($summary['Expense'], 2) }}</h5>
            </div>
        </div>
    </div>

    <!-- Accounts Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Code</th>
                            <th>Account Name</th>
                            <th>Class / Type</th>
                            <th class="text-end">Opening Balance</th>
                            <th class="text-end">Current Balance (₹)</th>
                            <th class="text-center pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $acc)
                            @php
                                $badgeClass = match($acc->type) {
                                    'Asset' => 'bg-primary-subtle text-primary',
                                    'Liability' => 'bg-danger-subtle text-danger',
                                    'Equity' => 'bg-dark-subtle text-dark',
                                    'Income' => 'bg-success-subtle text-success',
                                    'Expense' => 'bg-warning-subtle text-warning',
                                    default => 'bg-secondary-subtle text-secondary'
                                };
                            @endphp
                            <tr>
                                <td class="ps-4 fw-mono text-secondary fw-semibold">{{ $acc->code }}</td>
                                <td class="fw-semibold">{{ $acc->name }}</td>
                                <td><span class="badge {{ $badgeClass }} px-2 py-1 rounded-pill">{{ $acc->type }}</span></td>
                                <td class="text-end font-monospace">₹{{ number_format($acc->opening_balance, 2) }}</td>
                                <td class="text-end fw-bold font-monospace">₹{{ number_format($acc->current_balance, 2) }}</td>
                                <td class="text-center pe-4">
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1">Active</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
