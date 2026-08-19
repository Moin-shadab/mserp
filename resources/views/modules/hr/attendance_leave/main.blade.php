<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-calendar-check text-success me-2"></i>Attendance & Leave Workflow</h3>
            <p class="text-muted mb-0">Track daily check-ins/outs, biometric logs, and multi-level leave approvals.</p>
        </div>
        <button class="btn btn-success btn-sm rounded-pill px-3">
            <i class="bi bi-clock-history me-1"></i> Clock In / Clock Out
        </button>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4 border-bottom-0" id="hrTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-bold border-0 border-bottom border-3 border-success me-2" id="att-tab" data-bs-toggle="tab" data-bs-target="#att" type="button">Daily Attendance</button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold border-0 me-2 text-secondary" id="leave-tab" data-bs-toggle="tab" data-bs-target="#leave" type="button">Leave Applications</button>
        </li>
    </ul>

    <div class="tab-content" id="hrTabContent">
        <!-- Attendance Tab -->
        <div class="tab-pane fade show active" id="att">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Today's Attendance Log ({{ date('d M Y') }})</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">Employee</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Total Hours</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-3 fw-bold">Moin Shadab (EMP-001)</td>
                                <td>09:00 AM</td>
                                <td>06:00 PM</td>
                                <td>9.00 hrs</td>
                                <td><span class="badge bg-success">Present</span></td>
                            </tr>
                            <tr>
                                <td class="ps-3 fw-bold">Ananya Sharma (EMP-002)</td>
                                <td>09:15 AM</td>
                                <td>06:15 PM</td>
                                <td>9.00 hrs</td>
                                <td><span class="badge bg-success">Present</span></td>
                            </tr>
                            <tr>
                                <td class="ps-3 fw-bold">Vikram Patel (EMP-003)</td>
                                <td>-</td>
                                <td>-</td>
                                <td>0.00 hrs</td>
                                <td><span class="badge bg-warning text-dark">Casual Leave</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Leave Tab -->
        <div class="tab-pane fade" id="leave">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Pending Leave Requests</h5>
                    <button class="btn btn-outline-primary btn-sm rounded-pill">+ Request Leave</button>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">Applicant</th>
                                <th>Leave Type</th>
                                <th>Dates</th>
                                <th>Days</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-3 fw-bold">Vikram Patel</td>
                                <td><span class="badge bg-info text-dark">Casual</span></td>
                                <td>2026-08-20 to 2026-08-21</td>
                                <td>2 Days</td>
                                <td>Personal work</td>
                                <td><span class="badge bg-warning text-dark">Pending</span></td>
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
    </div>
</div>
