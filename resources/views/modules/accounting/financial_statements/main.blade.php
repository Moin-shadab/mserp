@php
    $income = DB::table('chart_of_accounts')->where('type', 'Income')->sum('current_balance');
    $expenses = DB::table('chart_of_accounts')->where('type', 'Expense')->sum('current_balance');
    $netProfit = $income - $expenses;

    $assets = DB::table('chart_of_accounts')->where('type', 'Asset')->get();
    $liabilities = DB::table('chart_of_accounts')->where('type', 'Liability')->get();
    $equity = DB::table('chart_of_accounts')->where('type', 'Equity')->get();

    $totalAssets = $assets->sum('current_balance');
    $totalLiabilities = $liabilities->sum('current_balance');
    $totalEquity = $equity->sum('current_balance') + $netProfit;
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Financial Statements</h4>
            <p class="text-muted small mb-0">Profit & Loss Statement (Income vs Expense) and Balance Sheet</p>
        </div>
        <button class="btn btn-outline-primary rounded-pill shadow-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Export Financial Report
        </button>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-4" id="finTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active rounded-pill px-4 fw-bold" id="pnl-tab" data-bs-toggle="tab" data-bs-target="#pnl">
                <i class="bi bi-file-spreadsheet me-1"></i> Profit & Loss Statement
            </button>
        </li>
        <li class="nav-item ms-2">
            <button class="nav-link rounded-pill px-4 fw-bold" id="bs-tab" data-bs-toggle="tab" data-bs-target="#bs">
                <i class="bi bi-pie-chart me-1"></i> Balance Sheet
            </button>
        </li>
    </ul>

    <div class="tab-content" id="finTabsContent">
        <!-- P&L Tab -->
        <div class="tab-pane fade show active" id="pnl">
            <div class="card border-0 shadow-sm rounded-3 p-4">
                <h5 class="fw-bold mb-3">Profit & Loss Statement</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Category / Account</th>
                                <th class="text-end">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="fw-bold table-success"><td colspan="2">INCOME & REVENUE</td></tr>
                            @foreach(DB::table('chart_of_accounts')->where('type', 'Income')->get() as $inc)
                                <tr>
                                    <td class="ps-4">{{ $inc->name }}</td>
                                    <td class="text-end font-monospace">₹{{ number_format($inc->current_balance, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="fw-bold border-top">
                                <td>TOTAL REVENUE</td>
                                <td class="text-end font-monospace text-success">₹{{ number_format($income, 2) }}</td>
                            </tr>

                            <tr class="fw-bold table-warning mt-3"><td colspan="2">OPERATING EXPENSES</td></tr>
                            @foreach(DB::table('chart_of_accounts')->where('type', 'Expense')->get() as $exp)
                                <tr>
                                    <td class="ps-4">{{ $exp->name }}</td>
                                    <td class="text-end font-monospace">₹{{ number_format($exp->current_balance, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="fw-bold border-top">
                                <td>TOTAL EXPENSES</td>
                                <td class="text-end font-monospace text-danger">₹{{ number_format($expenses, 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-dark text-white fw-bold fs-6">
                            <tr>
                                <td class="ps-3">NET OPERATING PROFIT / (LOSS)</td>
                                <td class="text-end font-monospace pe-3">₹{{ number_format($netProfit, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Balance Sheet Tab -->
        <div class="tab-pane fade" id="bs">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 p-4 h-100">
                        <h5 class="fw-bold text-primary mb-3"><i class="bi bi-box-seam me-2"></i>ASSETS</h5>
                        <ul class="list-group list-group-flush mb-3">
                            @foreach($assets as $ast)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>{{ $ast->name }}</span>
                                    <span class="font-monospace fw-bold">₹{{ number_format($ast->current_balance, 2) }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center fw-bold fs-5 text-primary">
                            <span>TOTAL ASSETS</span>
                            <span>₹{{ number_format($totalAssets, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 p-4 h-100">
                        <h5 class="fw-bold text-danger mb-3"><i class="bi bi-shield-exclamation me-2"></i>LIABILITIES & EQUITY</h5>
                        <h6 class="fw-bold text-muted mt-2">Liabilities</h6>
                        <ul class="list-group list-group-flush mb-3">
                            @foreach($liabilities as $lia)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>{{ $lia->name }}</span>
                                    <span class="font-monospace fw-bold">₹{{ number_format($lia->current_balance, 2) }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <h6 class="fw-bold text-muted mt-2">Equity & Retained Earnings</h6>
                        <ul class="list-group list-group-flush mb-3">
                            @foreach($equity as $eq)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>{{ $eq->name }}</span>
                                    <span class="font-monospace fw-bold">₹{{ number_format($eq->current_balance, 2) }}</span>
                                </li>
                            @endforeach
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-light text-success">
                                <span>Current Period Net Income</span>
                                <span class="font-monospace fw-bold">₹{{ number_format($netProfit, 2) }}</span>
                            </li>
                        </ul>

                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center fw-bold fs-5 text-danger">
                            <span>TOTAL LIABILITIES & EQUITY</span>
                            <span>₹{{ number_format($totalLiabilities + $totalEquity, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
