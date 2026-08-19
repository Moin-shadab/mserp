<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-arrow-counterclockwise me-2 text-primary"></i>Purchase Returns & Vendor Payments</h4>
            <p class="text-muted small mb-0">Record rejected goods returns to suppliers and vendor payment vouchers</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" onclick="alert('Creating Purchase Return Note...')">
            <i class="bi bi-plus-circle me-1"></i> New Purchase Return
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Return No</th>
                            <th>Vendor Name</th>
                            <th>Return Date</th>
                            <th>Reason / Inspection Note</th>
                            <th class="text-end">Total Refund / Debit Value (₹)</th>
                            <th class="text-center pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4 font-monospace fw-bold text-primary">PR-2026-001</td>
                            <td class="fw-semibold">Cisco Systems India Pvt Ltd</td>
                            <td>2026-08-10</td>
                            <td>Damaged ethernet ports on switch shipment</td>
                            <td class="text-end font-monospace fw-bold text-danger">₹125,000.00</td>
                            <td class="text-center pe-4"><span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">Debit Note Issued</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
