<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-cash-stack text-warning me-2"></i>Payroll & Statutory Deductions Engine</h3>
            <p class="text-muted mb-0">Process monthly salaries, Provident Fund (PF 12%), ESI (0.75%), Professional Tax (PT), and payslips.</p>
        </div>
        <button class="btn btn-warning text-dark fw-bold btn-sm rounded-pill px-3">
            <i class="bi bi-play-circle-fill me-1"></i> Run Monthly Payroll
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-primary">
                <div class="small text-muted text-uppercase fw-bold">Gross Salary</div>
                <div class="fs-3 fw-bolder text-primary mt-1">₹12,45,000.00</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-danger">
                <div class="small text-muted text-uppercase fw-bold">PF Deductions (12%)</div>
                <div class="fs-3 fw-bolder text-danger mt-1">₹1,49,400.00</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-info">
                <div class="small text-muted text-uppercase fw-bold">ESI & PT Deductions</div>
                <div class="fs-3 fw-bolder text-info mt-1">₹14,137.50</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-success">
                <div class="small text-muted text-uppercase fw-bold">Net Salary Payable</div>
                <div class="fs-3 fw-bolder text-success mt-1">₹10,81,462.50</div>
            </div>
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">August 2026 Payroll Register</h5>
            <span class="badge bg-success px-3 py-2 fs-6">Status: Approved</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Employee</th>
                            <th>Basic Pay</th>
                            <th>HRA (40%)</th>
                            <th>Allowances</th>
                            <th>PF (12%)</th>
                            <th>ESI / PT</th>
                            <th>Net Salary</th>
                            <th class="text-end pe-3">Payslip</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-3 fw-bold">
                                <div>Moin Shadab</div>
                                <div class="small text-muted">EMP-001</div>
                            </td>
                            <td>₹1,20,000.00</td>
                            <td>₹48,000.00</td>
                            <td>₹12,000.00</td>
                            <td class="text-danger">-₹14,400.00</td>
                            <td class="text-danger">-₹1,100.00</td>
                            <td class="fw-bold text-success">₹1,64,500.00</td>
                            <td class="text-end pe-3">
                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-file-earmark-pdf me-1"></i> Download</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-3 fw-bold">
                                <div>Ananya Sharma</div>
                                <div class="small text-muted">EMP-002</div>
                            </td>
                            <td>₹75,000.00</td>
                            <td>₹30,000.00</td>
                            <td>₹8,000.00</td>
                            <td class="text-danger">-₹9,000.00</td>
                            <td class="text-danger">-₹762.50</td>
                            <td class="fw-bold text-success">₹1,03,237.50</td>
                            <td class="text-end pe-3">
                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-file-earmark-pdf me-1"></i> Download</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
