<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-building text-primary me-2"></i>Fixed Asset Register & Depreciation</h3>
            <p class="text-muted mb-0">Track capital assets, straight-line & WDV depreciation schedules, asset disposal, and automatic GL posting.</p>
        </div>
        <button class="btn btn-primary btn-sm rounded-pill px-3">
            <i class="bi bi-plus-circle me-1"></i> Register New Asset
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-primary">
                <div class="small text-muted text-uppercase fw-bold">Total Asset Purchase Cost</div>
                <div class="fs-3 fw-bolder text-primary mt-1">₹45,00,000.00</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-warning">
                <div class="small text-muted text-uppercase fw-bold">Accumulated Depreciation</div>
                <div class="fs-3 fw-bolder text-warning mt-1">₹11,25,000.00</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-success">
                <div class="small text-muted text-uppercase fw-bold">Net Current Book Value</div>
                <div class="fs-3 fw-bolder text-success mt-1">₹33,75,000.00</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-info">
                <div class="small text-muted text-uppercase fw-bold">Depreciation Method</div>
                <div class="fs-3 fw-bolder text-info mt-1">Straight Line (SLM)</div>
            </div>
        </div>
    </div>

    <!-- Asset Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Fixed Asset Register</h5>
            <button class="btn btn-outline-primary btn-sm rounded-pill"><i class="bi bi-calculator me-1"></i> Post Monthly Depreciation to GL</button>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">Asset Code</th>
                        <th>Asset Name</th>
                        <th>Category</th>
                        <th>Purchase Date</th>
                        <th>Purchase Cost</th>
                        <th>Useful Life</th>
                        <th>Accumulated Dep.</th>
                        <th>Book Value</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-3 fw-bold text-primary">AST-MACH-001</td>
                        <td class="fw-bold">CNC Milling Machine X500</td>
                        <td><span class="badge bg-light text-dark border">Machinery</span></td>
                        <td>2024-04-01</td>
                        <td>₹25,00,000.00</td>
                        <td>10 Years</td>
                        <td class="text-warning">₹5,00,000.00</td>
                        <td class="fw-bold text-success">₹20,00,000.00</td>
                        <td class="text-end pe-3">
                            <button class="btn btn-sm btn-outline-primary"><i class="bi bi-calendar3"></i> Schedule</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="ps-3 fw-bold text-primary">AST-IT-002</td>
                        <td class="fw-bold">Dell PowerEdge Server Rack</td>
                        <td><span class="badge bg-light text-dark border">IT Equipment</span></td>
                        <td>2025-01-10</td>
                        <td>₹8,00,000.00</td>
                        <td>5 Years</td>
                        <td class="text-warning">₹2,40,000.00</td>
                        <td class="fw-bold text-success">₹5,60,000.00</td>
                        <td class="text-end pe-3">
                            <button class="btn btn-sm btn-outline-primary"><i class="bi bi-calendar3"></i> Schedule</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
