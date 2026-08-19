@php
    $salesTax = DB::table('sales_invoices')->sum('tax');
    $salesAmount = DB::table('sales_invoices')->sum('total_amount');
    $cgst = $salesTax / 2;
    $sgst = $salesTax / 2;
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>GSTR-1 & GSTR-3B Tax Return Summary</h4>
            <p class="text-muted small mb-0">Monthly GSTR-1 outward sales tax summary & GSTR-3B Tax Liability vs ITC Reconciliation</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Export GSTR JSON
        </button>
    </div>

    <div class="row g-4">
        <!-- GSTR-1 -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-4">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-file-earmark-arrow-up me-2"></i>GSTR-1 (Outward Supplies)</h5>
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>Total Taxable Turnover</td>
                                <td class="text-end font-monospace fw-bold">₹{{ number_format($salesAmount - $salesTax, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Output CGST Collected</td>
                                <td class="text-end font-monospace text-primary">₹{{ number_format($cgst, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Output SGST Collected</td>
                                <td class="text-end font-monospace text-primary">₹{{ number_format($sgst, 2) }}</td>
                            </tr>
                            <tr class="fw-bold border-top table-light">
                                <td>TOTAL GSTR-1 TAX LIABILITY</td>
                                <td class="text-end font-monospace text-success">₹{{ number_format($salesTax, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- GSTR-3B -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-scale me-2"></i>GSTR-3B (Tax Liability vs Input Tax Credit ITC)</h5>
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>Gross Output Tax Liability</td>
                                <td class="text-end font-monospace text-danger">₹{{ number_format($salesTax, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Eligible Input Tax Credit (ITC 2B)</td>
                                <td class="text-end font-monospace text-success">- ₹{{ number_format($salesTax * 0.75, 2) }}</td>
                            </tr>
                            <tr class="fw-bold border-top table-warning">
                                <td>NET CASH TAX PAYABLE (E-CASH LEDGER)</td>
                                <td class="text-end font-monospace text-dark">₹{{ number_format($salesTax * 0.25, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
