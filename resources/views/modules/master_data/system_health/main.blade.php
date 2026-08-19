<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-cpu-fill text-danger me-2"></i>System Health, API Rate Limits & Reliability</h3>
            <p class="text-muted mb-0">Monitor server uptime, background queue jobs, database connection latency, webhook delivery logs, and API throttling.</p>
        </div>
        <a href="/api/v1/health" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill px-3">
            <i class="bi bi-activity me-1"></i> Open /api/v1/health JSON
        </a>
    </div>

    <!-- Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-success text-white">
                <div class="small opacity-75 text-uppercase fw-bold">Database Latency</div>
                <div class="fs-3 fw-bolder mt-1">1.2 ms (Healthy)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-primary text-white">
                <div class="small opacity-75 text-uppercase fw-bold">Queue Worker Status</div>
                <div class="fs-3 fw-bolder mt-1">Active (0 Pending)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-info text-white">
                <div class="small opacity-75 text-uppercase fw-bold">Webhook Success Rate</div>
                <div class="fs-3 fw-bolder mt-1">99.8% Delivered</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-dark text-white">
                <div class="small opacity-75 text-uppercase fw-bold">API Rate Limit Policy</div>
                <div class="fs-3 fw-bolder mt-1">1000 req/min</div>
            </div>
        </div>
    </div>

    <!-- Webhook Subscriptions -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Active Webhook Subscriptions</h5>
            <button class="btn btn-primary btn-sm rounded-pill">+ Register Webhook</button>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">Event Name</th>
                        <th>Payload Target URL</th>
                        <th>Secret Key</th>
                        <th>Last Delivery</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-3 fw-bold text-primary">sales.invoice.posted</td>
                        <td><code>https://api.accounting.org/v1/webhooks/sales</code></td>
                        <td><code>whsec_98f7e6a5...</code></td>
                        <td>2026-08-19 22:15:00</td>
                        <td><span class="badge bg-success">Active (200 OK)</span></td>
                    </tr>
                    <tr>
                        <td class="ps-3 fw-bold text-primary">inventory.stock.low</td>
                        <td><code>https://inventory-alerts.company.com/notify</code></td>
                        <td><code>whsec_12a3b4c5...</code></td>
                        <td>2026-08-19 21:40:12</td>
                        <td><span class="badge bg-success">Active (200 OK)</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
