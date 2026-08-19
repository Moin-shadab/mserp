<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-pie-chart-fill text-success me-2"></i>Budgeting & Expenditure Variance</h3>
            <p class="text-muted mb-0">Set departmental fiscal budgets, track actual GL expenditures in real time, and trigger variance alerts.</p>
        </div>
        <button class="btn btn-success btn-sm rounded-pill px-3">
            <i class="bi bi-plus-circle me-1"></i> Create Department Budget
        </button>
    </div>

    <!-- Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-primary">
                <div class="small text-muted text-uppercase fw-bold">FY 2026-2027 Total Allocated Budget</div>
                <div class="fs-3 fw-bolder text-primary mt-1">₹1,50,000,000.00</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-warning">
                <div class="small text-muted text-uppercase fw-bold">Actual YTD Spent (GL)</div>
                <div class="fs-3 fw-bolder text-warning mt-1">₹42,850,000.00</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-success">
                <div class="small text-muted text-uppercase fw-bold">Remaining Budget Balance</div>
                <div class="fs-3 fw-bolder text-success mt-1">₹1,07,150,000.00</div>
            </div>
        </div>
    </div>

    <!-- Budget Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">Department</th>
                        <th>Account Description</th>
                        <th>Allocated Budget</th>
                        <th>Actual Spent (GL)</th>
                        <th>Remaining</th>
                        <th>Utilization %</th>
                        <th class="text-end pe-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-3 fw-bold">Engineering & IT</td>
                        <td>Software & Infrastructure (5010)</td>
                        <td class="fw-bold">₹50,00,000.00</td>
                        <td>₹18,50,000.00</td>
                        <td class="text-success fw-bold">₹31,50,000.00</td>
                        <td>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: 37%;"></div>
                            </div>
                            <div class="small text-muted mt-1">37.0%</div>
                        </td>
                        <td class="text-end pe-3"><span class="badge bg-success">On Track</span></td>
                    </tr>
                    <tr>
                        <td class="ps-3 fw-bold">Sales & Marketing</td>
                        <td>Advertising & Campaigns (5020)</td>
                        <td class="fw-bold">₹30,00,000.00</td>
                        <td>₹24,10,000.00</td>
                        <td class="text-warning fw-bold">₹5,90,000.00</td>
                        <td>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" style="width: 80.3%;"></div>
                            </div>
                            <div class="small text-muted mt-1">80.3%</div>
                        </td>
                        <td class="text-end pe-3"><span class="badge bg-warning text-dark">Near Threshold</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
