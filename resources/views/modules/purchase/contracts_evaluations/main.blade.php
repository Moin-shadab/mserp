<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-file-earmark-text-fill text-purple me-2"></i>Vendor Contracts & Performance Evaluation</h3>
            <p class="text-muted mb-0">Manage long-term supplier blanket contracts, expiry alerts, and supplier performance scoring.</p>
        </div>
        <button class="btn btn-dark btn-sm rounded-pill px-3">
            <i class="bi bi-plus-circle me-1"></i> New Vendor Contract
        </button>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Active Purchase Contracts</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">Contract No</th>
                        <th>Vendor Name</th>
                        <th>Validity Period</th>
                        <th>Contract Value</th>
                        <th>Unutilized Amount</th>
                        <th>Vendor Score</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-3 fw-bold text-primary">CTR-VEND-2026-01</td>
                        <td class="fw-bold">Tata Steel Industries Ltd</td>
                        <td>2026-01-01 to 2026-12-31</td>
                        <td>₹1,00,00,000.00</td>
                        <td class="text-success fw-bold">₹64,50,000.00</td>
                        <td><span class="badge bg-success"><i class="bi bi-star-fill me-1"></i> 9.2 / 10</span></td>
                        <td><span class="badge bg-success">Active</span></td>
                        <td class="text-end pe-3">
                            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i> Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="ps-3 fw-bold text-primary">CTR-VEND-2026-02</td>
                        <td class="fw-bold">Reliance Petrochemicals Corp</td>
                        <td>2026-02-01 to 2026-08-30</td>
                        <td>₹50,00,000.00</td>
                        <td class="text-danger fw-bold">₹2,10,000.00</td>
                        <td><span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i> 8.1 / 10</span></td>
                        <td><span class="badge bg-warning text-dark">Expiring Soon</span></td>
                        <td class="text-end pe-3">
                            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i> Details</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
