<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-cpu-fill me-2 text-primary"></i>MRP & Production Costing (WIP)</h4>
            <p class="text-muted small mb-0">Material Requirements Planning, raw material issues to WIP, scrap tracking & finished goods receipt</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" onclick="alert('Running Material Requirements Planning (MRP) Engine...')">
            <i class="bi bi-lightning me-1"></i> Run MRP Calculation Engine
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-3 p-4 mb-4 bg-light">
        <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2 text-primary"></i>Material Requirements Planning Engine Summary</h6>
        <p class="text-muted small mb-0">The MRP engine evaluates open Sales Orders, minimum reorder safety levels, and Bill of Materials component trees to calculate automated Purchase Requisitions for raw material deficits.</p>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0">Work In Progress (WIP) & Production Batches Log</h6>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Work Order</th>
                        <th>Target Product</th>
                        <th>Raw Materials Issued</th>
                        <th>Scrap Recorded</th>
                        <th class="text-end">WIP Valuation (₹)</th>
                        <th class="text-center pe-4">MRP Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-4 font-monospace fw-bold text-primary">WO-2026-0001</td>
                        <td>Personal Computer Workstations</td>
                        <td><span class="badge bg-success-subtle text-success">All Raw Materials Allocated</span></td>
                        <td>0.5% (Scrap within tolerance)</td>
                        <td class="text-end font-monospace fw-bold">₹450,000.00</td>
                        <td class="text-center pe-4"><span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1">In Progress</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
