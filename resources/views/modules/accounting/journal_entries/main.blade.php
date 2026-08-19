@php
    $journals = DB::table('journal_entries')
        ->leftJoin('users', 'journal_entries.created_by', '=', 'users.id')
        ->select('journal_entries.*', 'users.name as creator_name')
        ->orderBy('journal_entries.id', 'desc')
        ->get();
    $accounts = DB::table('chart_of_accounts')->where('is_active', true)->orderBy('code')->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-journal-bookmark me-2 text-primary"></i>Journal Entries (Vouchers)</h4>
            <p class="text-muted small mb-0">Double-entry accounting journal vouchers with real-time debit/credit balancing</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#newJournalModal">
            <i class="bi bi-plus-circle me-1"></i> New Journal Entry
        </button>
    </div>

    <!-- Journal Vouchers List -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Voucher No</th>
                            <th>Entry Date</th>
                            <th>Narration</th>
                            <th class="text-end">Total Debit (₹)</th>
                            <th class="text-end">Total Credit (₹)</th>
                            <th>Posted By</th>
                            <th class="text-center pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($journals as $jv)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">{{ $jv->voucher_no }}</td>
                                <td>{{ $jv->entry_date }}</td>
                                <td>{{ $jv->narration }}</td>
                                <td class="text-end fw-semibold">₹{{ number_format($jv->total_debit, 2) }}</td>
                                <td class="text-end fw-semibold">₹{{ number_format($jv->total_credit, 2) }}</td>
                                <td>{{ $jv->creator_name ?? 'System Admin' }}</td>
                                <td class="text-center pe-4">
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">
                                        <i class="bi bi-check-lock me-1"></i>{{ $jv->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">No journal vouchers created yet. Click New Journal Entry to record a voucher.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Dialog for Creating Journal Entry -->
<div class="modal fade" id="newJournalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-journal-plus me-2 text-primary"></i>Post Double-Entry Journal Voucher</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="journalForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Entry Date</label>
                            <input type="date" name="entry_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">General Narration</label>
                            <input type="text" name="narration" class="form-control" placeholder="e.g. Office rent & utility payment" required>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-2">Voucher Debit & Credit Lines</h6>
                    <div id="journalLines">
                        <!-- Line 1: Debit -->
                        <div class="row g-2 mb-2 align-items-center journal-line">
                            <div class="col-md-5">
                                <select name="lines[0][account_id]" class="form-select" required>
                                    <option value="">Select Debit Account...</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ $acc->type }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" step="0.01" name="lines[0][debit]" class="form-control debit-input" placeholder="Debit (₹)" value="0">
                            </div>
                            <div class="col-md-3">
                                <input type="number" step="0.01" name="lines[0][credit]" class="form-control credit-input" placeholder="Credit (₹)" value="0" readonly>
                            </div>
                        </div>
                        <!-- Line 2: Credit -->
                        <div class="row g-2 mb-2 align-items-center journal-line">
                            <div class="col-md-5">
                                <select name="lines[1][account_id]" class="form-select" required>
                                    <option value="">Select Credit Account...</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ $acc->type }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" step="0.01" name="lines[1][debit]" class="form-control debit-input" placeholder="Debit (₹)" value="0" readonly>
                            </div>
                            <div class="col-md-3">
                                <input type="number" step="0.01" name="lines[1][credit]" class="form-control credit-input" placeholder="Credit (₹)" value="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-check2-circle me-1"></i> Post Journal Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('journalForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('/api/erp/journal/store', {
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
            alert('Journal Entry Posted Successfully! Voucher: ' + data.voucher_no);
            location.reload();
        } else {
            alert('Posting Failed: ' + (data.error || 'Check balance.'));
        }
    })
    .catch(err => alert('Error submitting journal voucher.'));
});
</script>
