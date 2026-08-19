@php
    $glEntries = DB::table('general_ledger')
        ->join('chart_of_accounts', 'general_ledger.account_id', '=', 'chart_of_accounts.id')
        ->select('general_ledger.*', 'chart_of_accounts.name as account_name', 'chart_of_accounts.code as account_code')
        ->orderBy('general_ledger.id', 'desc')
        ->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-book-half me-2 text-primary"></i>General Ledger</h4>
            <p class="text-muted small mb-0">Detailed transaction ledger & running balance audit trail</p>
        </div>
        <button class="btn btn-outline-secondary rounded-pill shadow-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print Statement
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Date</th>
                            <th>Account</th>
                            <th>Source Document</th>
                            <th class="text-end">Debit (₹)</th>
                            <th class="text-end">Credit (₹)</th>
                            <th class="text-end pe-4">Running Balance (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($glEntries as $gl)
                            <tr>
                                <td class="ps-4 text-muted">{{ $gl->entry_date }}</td>
                                <td><span class="font-monospace text-primary fw-bold">{{ $gl->account_code }}</span> - {{ $gl->account_name }}</td>
                                <td><span class="badge bg-secondary-subtle text-secondary">{{ $gl->source_document_type ?? 'MANUAL' }}</span></td>
                                <td class="text-end font-monospace text-success">₹{{ number_format($gl->debit, 2) }}</td>
                                <td class="text-end font-monospace text-danger">₹{{ number_format($gl->credit, 2) }}</td>
                                <td class="text-end font-monospace fw-bold pe-4">₹{{ number_format($gl->running_balance, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">No General Ledger postings recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
