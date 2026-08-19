@php
    $totalSales = DB::table('sales_invoices')->sum('total_amount');
    $totalPurchases = DB::table('purchase_orders')->sum('total_amount');
    $inventoryValuation = DB::table('inventory_items')->sum(DB::raw('qty_on_hand * unit_price'));
    $totalAudits = DB::table('audit_logs')->count();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-speedometer2 me-2 text-primary"></i>Executive Reporting & Analytics Hub</h4>
            <p class="text-muted small mb-0">Cross-functional business intelligence, profitability, inventory valuation & cash flow metrics</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print Executive Summary
        </button>
    </div>

    <!-- Executive KPI Grid -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-primary text-white">
                <span class="small opacity-75">Gross Billing Sales</span>
                <h3 class="fw-bold mb-0 mt-1">₹{{ number_format($totalSales, 2) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-dark text-white">
                <span class="small opacity-75">Procurement Commitments</span>
                <h3 class="fw-bold mb-0 mt-1">₹{{ number_format($totalPurchases, 2) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-success text-white">
                <span class="small opacity-75">Inventory Stock Valuation</span>
                <h3 class="fw-bold mb-0 mt-1">₹{{ number_format($inventoryValuation, 2) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-warning text-dark">
                <span class="small opacity-75">System Audit Events Logged</span>
                <h3 class="fw-bold mb-0 mt-1">{{ number_format($totalAudits) }}</h3>
            </div>
        </div>
    </div>

    <!-- Analytics Charts & Detail Modules -->
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-3 p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-bar-chart me-2 text-primary"></i>Operational Reports Catalog</h5>
                <div class="list-group list-group-flush">
                    <a href="/erp/general-ledger" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h6 class="fw-bold mb-0"><i class="bi bi-book me-2 text-primary"></i>General Ledger Statement</h6>
                            <span class="small text-muted">Complete debit/credit transaction audit trail for all accounts</span>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="/erp/trial-balance" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h6 class="fw-bold mb-0"><i class="bi bi-scale me-2 text-primary"></i>Trial Balance Summary</h6>
                            <span class="small text-muted">Period closing trial balance statement</span>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="/erp/financial-statements" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h6 class="fw-bold mb-0"><i class="bi bi-graph-up me-2 text-primary"></i>Balance Sheet & P&L Statement</h6>
                            <span class="small text-muted">Financial performance & asset liability balance</span>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="/erp/ap-ar-management" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h6 class="fw-bold mb-0"><i class="bi bi-cash-coin me-2 text-primary"></i>AR / AP Ageing Report</h6>
                            <span class="small text-muted">Customer receivables & vendor payables ageing buckets</span>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="/erp/stock-ledger" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h6 class="fw-bold mb-0"><i class="bi bi-boxes me-2 text-primary"></i>Stock Valuation & Movement Ledger</h6>
                            <span class="small text-muted">FIFO & Weighted Average stock asset valuation report</span>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="/erp/gstr-reports" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h6 class="fw-bold mb-0"><i class="bi bi-receipt me-2 text-primary"></i>GSTR-1 & GSTR-3B Tax Return Summary</h6>
                            <span class="small text-muted">Output tax liability & Input Tax Credit (ITC) reconciliation</span>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-4 bg-light">
                <h6 class="fw-bold mb-3"><i class="bi bi-trophy me-2 text-warning"></i>Customer Profitability Highlights</h6>
                @foreach(DB::table('customers')->limit(4)->get() as $cust)
                    <div class="p-3 bg-white rounded shadow-sm mb-2">
                        <h6 class="fw-bold mb-1 text-primary">{{ $cust->name }}</h6>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Margin %:</span>
                            <span class="fw-bold text-success">32.4%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
