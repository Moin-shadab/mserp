@php
    $customers = DB::table('customers')->limit(10)->get();
    $vendors = DB::table('vendors')->limit(10)->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-cash-stack me-2 text-primary"></i>Accounts Payable & Receivable (AP/AR)</h4>
            <p class="text-muted small mb-0">Ageing analysis (0-30, 31-60, 61-90, 90+ days) and payment allocations</p>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="aparTabs">
        <li class="nav-item">
            <button class="nav-link active fw-bold" data-bs-toggle="tab" data-bs-target="#ar">
                <i class="bi bi-arrow-down-left-circle me-1 text-success"></i> Accounts Receivable (AR)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#ap">
                <i class="bi bi-arrow-up-right-circle me-1 text-danger"></i> Accounts Payable (AP)
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- AR Tab -->
        <div class="tab-pane fade show active" id="ar">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Customer Name</th>
                                    <th>GSTIN</th>
                                    <th class="text-end">Current (0-30 days)</th>
                                    <th class="text-end">31-60 Days</th>
                                    <th class="text-end">61-90 Days</th>
                                    <th class="text-end">90+ Days</th>
                                    <th class="text-end pe-4">Total Receivable (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customers as $c)
                                    @php
                                        $val = rand(15000, 250000);
                                    @endphp
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary">{{ $c->name }}</td>
                                        <td class="font-monospace text-muted">{{ $c->gstin ?? 'N/A' }}</td>
                                        <td class="text-end font-monospace">₹{{ number_format($val * 0.6, 2) }}</td>
                                        <td class="text-end font-monospace">₹{{ number_format($val * 0.25, 2) }}</td>
                                        <td class="text-end font-monospace">₹{{ number_format($val * 0.1, 2) }}</td>
                                        <td class="text-end font-monospace text-danger">₹{{ number_format($val * 0.05, 2) }}</td>
                                        <td class="text-end font-monospace fw-bold pe-4 text-success">₹{{ number_format($val, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- AP Tab -->
        <div class="tab-pane fade" id="ap">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Vendor / Supplier Name</th>
                                    <th>GSTIN</th>
                                    <th class="text-end">Current (0-30 days)</th>
                                    <th class="text-end">31-60 Days</th>
                                    <th class="text-end">61-90 Days</th>
                                    <th class="text-end pe-4">Total Payable (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vendors as $v)
                                    @php $pVal = rand(25000, 450000); @endphp
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark">{{ $v->name }}</td>
                                        <td class="font-monospace text-muted">{{ $v->gstin ?? 'N/A' }}</td>
                                        <td class="text-end font-monospace">₹{{ number_format($pVal * 0.7, 2) }}</td>
                                        <td class="text-end font-monospace">₹{{ number_format($pVal * 0.2, 2) }}</td>
                                        <td class="text-end font-monospace">₹{{ number_format($pVal * 0.1, 2) }}</td>
                                        <td class="text-end font-monospace fw-bold pe-4 text-danger">₹{{ number_format($pVal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
