<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-receipt-cutoff text-info me-2"></i>Expense Claims & Reimbursements</h3>
            <p class="text-muted mb-0">Submit employee expense claims, attach receipts, route for approval, and disburse payments.</p>
        </div>
        <button class="btn btn-info text-white btn-sm rounded-pill px-3">
            <i class="bi bi-plus-circle me-1"></i> Submit Expense Claim
        </button>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">Claim No</th>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-3 fw-bold text-primary">EXP-2026-001</td>
                        <td>Moin Shadab</td>
                        <td>2026-08-18</td>
                        <td><span class="badge bg-light text-dark border">Client Travel</span></td>
                        <td class="fw-bold">₹8,500.00</td>
                        <td>Flight tickets for client meeting in Bangalore</td>
                        <td><span class="badge bg-success">Paid</span></td>
                        <td class="text-end pe-3">
                            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-paperclip"></i> Receipt</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="ps-3 fw-bold text-primary">EXP-2026-002</td>
                        <td>Vikram Patel</td>
                        <td>2026-08-19</td>
                        <td><span class="badge bg-light text-dark border">Office Supplies</span></td>
                        <td class="fw-bold">₹2,300.00</td>
                        <td>Barcode printer ribbon cartridges</td>
                        <td><span class="badge bg-warning text-dark">Pending Approval</span></td>
                        <td class="text-end pe-3">
                            <button class="btn btn-sm btn-success me-1">Approve</button>
                            <button class="btn btn-sm btn-danger">Reject</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
