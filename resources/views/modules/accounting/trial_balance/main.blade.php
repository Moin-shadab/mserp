@php
    $accounts = DB::table('chart_of_accounts')->orderBy('code')->get();
    $totalDebit = 0;
    $totalCredit = 0;
    foreach ($accounts as $a) {
        if (in_array($a->type, ['Asset', 'Expense'])) {
            $totalDebit += max(0, $a->current_balance);
            $totalCredit += abs(min(0, $a->current_balance));
        } else {
            $totalCredit += max(0, $a->current_balance);
            $totalDebit += abs(min(0, $a->current_balance));
        }
    }
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-scale me-2 text-primary"></i>Trial Balance</h4>
            <p class="text-muted small mb-0">Summary of account debit & credit balances for period closing</p>
        </div>
        <div>
            <button class="btn btn-outline-primary rounded-pill shadow-sm me-2" onclick="window.print()">
                <i class="bi bi-file-earmark-pdf me-1"></i> Export Report
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Account Code</th>
                            <th>Account Name</th>
                            <th>Type</th>
                            <th class="text-end">Debit Balance (₹)</th>
                            <th class="text-end pe-4">Credit Balance (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $acc)
                            @php
                                $isDebitType = in_array($acc->type, ['Asset', 'Expense']);
                                $debit = $isDebitType ? max(0, $acc->current_balance) : 0;
                                $credit = !$isDebitType ? max(0, $acc->current_balance) : 0;
                            @endphp
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">{{ $acc->code }}</td>
                                <td class="fw-semibold">{{ $acc->name }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $acc->type }}</span></td>
                                <td class="text-end font-monospace">{{ $debit > 0 ? '₹' . number_format($debit, 2) : '-' }}</td>
                                <td class="text-end font-monospace pe-4">{{ $credit > 0 ? '₹' . number_format($credit, 2) : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light fw-bold">
                        <tr>
                            <td colspan="3" class="ps-4 text-uppercase">Total Trial Balance</td>
                            <td class="text-end font-monospace text-primary">₹{{ number_format($totalDebit, 2) }}</td>
                            <td class="text-end font-monospace text-primary pe-4">₹{{ number_format($totalCredit, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
