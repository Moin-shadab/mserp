@php
    $notes = DB::table('credit_debit_notes')->orderBy('id', 'desc')->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-card-checklist me-2 text-primary"></i>Credit & Debit Notes</h4>
            <p class="text-muted small mb-0">Sales returns customer credit notes & vendor debit notes management</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#newNoteModal">
            <i class="bi bi-plus-circle me-1"></i> Issue Credit/Debit Note
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Note No</th>
                            <th>Type</th>
                            <th>Party Type</th>
                            <th>Reference Invoice</th>
                            <th class="text-end">Base Amount (₹)</th>
                            <th class="text-end">GST Tax (₹)</th>
                            <th class="text-end">Total Amount (₹)</th>
                            <th class="text-center pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notes as $n)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">{{ $n->note_no }}</td>
                                <td>
                                    <span class="badge {{ $n->note_type === 'Credit Note' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                        {{ $n->note_type }}
                                    </span>
                                </td>
                                <td>{{ $n->party_type }}</td>
                                <td>{{ $n->reference_invoice_no ?? 'N/A' }}</td>
                                <td class="text-end font-monospace">₹{{ number_format($n->amount, 2) }}</td>
                                <td class="text-end font-monospace">₹{{ number_format($n->gst_amount, 2) }}</td>
                                <td class="text-end font-monospace fw-bold">₹{{ number_format($n->total_amount, 2) }}</td>
                                <td class="text-center pe-4"><span class="badge bg-info-subtle text-info rounded-pill px-2 py-1">{{ $n->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-4 text-muted">No credit or debit notes issued yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
