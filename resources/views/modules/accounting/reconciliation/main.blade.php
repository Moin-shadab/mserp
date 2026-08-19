<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-bank me-2 text-primary"></i>Bank Reconciliation Workspace</h4>
            <p class="text-muted small mb-0">Reconcile bank account feeds against book entries and uncleared cheques</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" onclick="alert('Bank Statement Feed Imported successfully.')">
            <i class="bi bi-upload me-1"></i> Import Bank Statement (OFX/CSV)
        </button>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-light">
                <span class="text-muted small">HDFC Operating Bank Statement Balance</span>
                <h4 class="fw-bold mb-0 text-primary mt-1">₹2,450,000.00</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-light">
                <span class="text-muted small">General Ledger Book Balance</span>
                <h4 class="fw-bold mb-0 text-dark mt-1">₹2,450,000.00</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-light">
                <span class="text-muted small">Unreconciled Difference</span>
                <h4 class="fw-bold mb-0 text-success mt-1">₹0.00 (Balanced)</h4>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0">Uncleared Cheques & Bank Feed Lines</h6>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Trans Date</th>
                        <th>Reference / Cheque No</th>
                        <th>Description</th>
                        <th class="text-end">Debit (Out)</th>
                        <th class="text-end">Credit (In)</th>
                        <th class="text-center pe-4">Reconcile Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-4">2026-08-15</td>
                        <td class="font-monospace">CHQ-883921</td>
                        <td>Vendor Payment - Express Cargo</td>
                        <td class="text-end font-monospace text-danger">₹45,000.00</td>
                        <td class="text-end font-monospace">-</td>
                        <td class="text-center pe-4"><span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">Matched</span></td>
                    </tr>
                    <tr>
                        <td class="ps-4">2026-08-18</td>
                        <td class="font-monospace">NEFT-002931</td>
                        <td>Customer Receipt - Global Tech</td>
                        <td class="text-end font-monospace">-</td>
                        <td class="text-end font-monospace text-success">₹180,000.00</td>
                        <td class="text-center pe-4"><span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">Matched</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
