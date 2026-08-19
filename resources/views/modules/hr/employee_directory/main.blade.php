<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-people-fill text-primary me-2"></i>Employee Directory & HR Master</h3>
            <p class="text-muted mb-0">Manage employee profiles, designations, department structures, and base salaries.</p>
        </div>
        <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#newEmployeeModal">
            <i class="bi bi-person-plus-fill me-1"></i> Add Employee
        </button>
    </div>

    <!-- Summary Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-primary text-white">
                <div class="small opacity-75 text-uppercase fw-bold">Total Headcount</div>
                <div class="fs-3 fw-bolder mt-1">24 Active</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-success text-white">
                <div class="small opacity-75 text-uppercase fw-bold">Present Today</div>
                <div class="fs-3 fw-bolder mt-1">22 Staff</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-warning text-dark">
                <div class="small opacity-75 text-uppercase fw-bold">On Approved Leave</div>
                <div class="fs-3 fw-bolder mt-1">2 Staff</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-info text-white">
                <div class="small opacity-75 text-uppercase fw-bold">Monthly Payroll Base</div>
                <div class="fs-3 fw-bolder mt-1">₹12,45,000</div>
            </div>
        </div>
    </div>

    <!-- Employee Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Emp Code</th>
                            <th>Full Name</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Joining Date</th>
                            <th>Basic Salary</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-3 fw-bold text-primary">EMP-001</td>
                            <td>
                                <div class="fw-bold">Moin Shadab</div>
                                <div class="small text-muted">moin@mserp.com</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">Engineering</span></td>
                            <td>Lead Systems Architect</td>
                            <td>2024-01-15</td>
                            <td class="fw-bold">₹1,20,000.00</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td class="text-end pe-3">
                                <button class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-3 fw-bold text-primary">EMP-002</td>
                            <td>
                                <div class="fw-bold">Ananya Sharma</div>
                                <div class="small text-muted">ananya@mserp.com</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">Finance</span></td>
                            <td>Senior Accountant</td>
                            <td>2024-03-01</td>
                            <td class="fw-bold">₹75,000.00</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td class="text-end pe-3">
                                <button class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-3 fw-bold text-primary">EMP-003</td>
                            <td>
                                <div class="fw-bold">Vikram Patel</div>
                                <div class="small text-muted">vikram@mserp.com</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">Operations</span></td>
                            <td>Warehouse Manager</td>
                            <td>2024-05-10</td>
                            <td class="fw-bold">₹60,000.00</td>
                            <td><span class="badge bg-warning text-dark">On Leave</span></td>
                            <td class="text-end pe-3">
                                <button class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
