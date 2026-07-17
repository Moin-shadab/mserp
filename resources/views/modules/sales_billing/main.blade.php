<div class="container-fluid p-4">
    <!-- View 1: Document List Grid -->
    <div id="invoice-list-view">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 text-dark fw-bold" id="doc-title-main">GST Billings & Invoices</h4>
                <p class="text-muted small mb-0" id="doc-desc-main">Manage customer billing records, automatic state-wise GST taxation, and inventory stock tracking.</p>
            </div>
            <button class="btn btn-primary d-flex align-items-center gap-2 fw-semibold px-3 py-2" onclick="showCreateInvoiceForm()">
                <i class="bi bi-plus-circle"></i> <span id="btn-create-text">Create GST Invoice</span>
            </button>
        </div>

        <div class="card billing-card shadow-sm border-0">
            <div class="billing-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold text-dark" id="doc-registry-title">Document Registry</h5>
                <span class="badge bg-light text-dark border px-2 py-1 small" id="invoices-count">0 Documents</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="invoices-table">
                        <thead class="table-light text-uppercase font-monospace small">
                            <tr>
                                <th class="ps-4" id="grid-header-no">Invoice No</th>
                                <th id="grid-header-contact">Customer Name</th>
                                <th id="grid-header-date">Billing Date</th>
                                <th>Subtotal (₹)</th>
                                <th>GST (₹)</th>
                                <th>Grand Total (₹)</th>
                                <th>Tax Type</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="invoices-list-tbody">
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                    Loading records...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- View 2: Document Creator Form -->
    <div id="invoice-create-view" style="display: none;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 text-dark fw-bold" id="doc-creator-title">Create GST Tax Invoice</h4>
                <p class="text-muted small mb-0" id="doc-creator-desc">Generate a standard Indian GST compliant commercial invoice and deduct stock levels.</p>
            </div>
            <button class="btn btn-outline-secondary d-flex align-items-center gap-2 px-3 py-2 fw-semibold" onclick="hideCreateInvoiceForm()">
                <i class="bi bi-arrow-left"></i> Back to Registry
            </button>
        </div>

        <form id="create-invoice-form" onsubmit="submitInvoiceForm(event)">
            <div class="row g-4">
                <!-- Left: Form Input Fields -->
                <div class="col-lg-8">
                    <div class="card billing-card shadow-sm border-0 p-4 mb-4">
                        <h5 class="fw-semibold text-dark mb-4 border-bottom pb-2" id="doc-section-title">Billing Information</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted" id="form-label-contact">Select Customer <span class="text-danger">*</span></label>
                                <select class="form-select" id="invoice-customer-select" onchange="onCustomerSelect()" required>
                                    <option value="">-- Choose Contact --</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted" id="form-label-date">Billing Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-select" id="invoice-date-input" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Payment Terms</label>
                                <select class="form-select" id="invoice-terms-input">
                                    <option value="Due on Receipt">Due on Receipt</option>
                                    <option value="Net 15">Net 15 Days</option>
                                    <option value="Net 30" selected>Net 30 Days</option>
                                    <option value="Net 60">Net 60 Days</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted" id="form-label-due">Due Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-select" id="invoice-due-input" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-muted" id="form-label-address">Billing Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="invoice-address-input" rows="2" placeholder="Enter full address..." required></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Line Items Table -->
                    <div class="card billing-card shadow-sm border-0 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-semibold text-dark mb-0">Line Items</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" onclick="addInvoiceLineItem()">
                                <i class="bi bi-plus-circle"></i> Add Item Line
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-invoice-items" id="invoice-lines-table">
                                <thead>
                                    <tr>
                                        <th style="width: 35%;">Item Name</th>
                                        <th style="width: 15%;">HSN/SAC</th>
                                        <th style="width: 12%;">Qty</th>
                                        <th style="width: 18%;">Unit Rate (₹)</th>
                                        <th style="width: 10%;">GST (%)</th>
                                        <th style="width: 18%;">Amount (₹)</th>
                                        <th style="width: 8%;" class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody id="invoice-lines-tbody">
                                    <!-- Dynamic rows injected here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right: Seller entity info & totals summary -->
                <div class="col-lg-4">
                    <!-- Seller Details Card -->
                    <div class="card billing-card border-0 shadow-sm p-4 mb-4" style="background-color: #fafbfc;">
                        <h6 class="fw-bold text-dark text-uppercase small tracking-wider mb-3" id="sender-header-title">Billing From (Seller)</h6>
                        <div class="small">
                            <div class="fw-bold text-primary mb-1">Acme Corporation (India)</div>
                            <div class="text-muted mb-2">Express Towers, Nariman Point, Mumbai, MH</div>
                            <div class="row g-1 mb-1">
                                <div class="col-4 text-muted fw-medium">GSTIN:</div>
                                <div class="col-8 fw-semibold" id="company-gstin-display">27AAACA1234A1Z5</div>
                            </div>
                            <div class="row g-1">
                                <div class="col-4 text-muted fw-medium">State:</div>
                                <div class="col-8 fw-semibold" id="company-state-display">Maharashtra (Code: 27)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer/Vendor GST Summary Card -->
                    <div id="customer-gst-summary" class="card billing-card border-0 shadow-sm p-4 mb-4 d-none" style="background-color: #f8fafc;">
                        <h6 class="fw-bold text-dark text-uppercase small mb-3" id="recipient-summary-title">Customer Tax Context</h6>
                        <div class="small">
                            <div class="row g-1 mb-1">
                                <div class="col-4 text-muted">GSTIN:</div>
                                <div class="col-8 fw-semibold" id="cust-gstin-display">-</div>
                            </div>
                            <div class="row g-1 mb-2">
                                <div class="col-4 text-muted">State:</div>
                                <div class="col-8 fw-semibold" id="cust-state-display">-</div>
                            </div>
                            <div id="tax-type-indicator" class="alert alert-info py-2 px-3 mb-0 small border-0 fw-semibold">
                                -
                            </div>
                        </div>
                    </div>

                    <!-- Totals Summary Card -->
                    <div class="card billing-card shadow-sm border-0 p-4">
                        <h5 class="fw-semibold text-dark mb-4 border-bottom pb-2">Summary</h5>
                        <div class="row g-3 small">
                            <div class="col-6 summary-label">Subtotal Amount</div>
                            <div class="col-6 summary-val" id="summary-subtotal">₹0.00</div>

                            <!-- GST splits -->
                            <div class="col-6 summary-label text-muted d-none" id="label-cgst">CGST</div>
                            <div class="col-6 summary-val text-muted d-none" id="val-cgst">₹0.00</div>

                            <div class="col-6 summary-label text-muted d-none" id="label-sgst">SGST</div>
                            <div class="col-6 summary-val text-muted d-none" id="val-sgst">₹0.00</div>

                            <div class="col-6 summary-label text-muted d-none" id="label-igst">IGST</div>
                            <div class="col-6 summary-val text-muted d-none" id="val-igst">₹0.00</div>

                            <div class="col-6 summary-label">Total Tax Value</div>
                            <div class="col-6 summary-val" id="summary-tax">₹0.00</div>

                            <hr class="my-2">

                            <div class="col-6 summary-label fw-bold fs-6">Grand Total</div>
                            <div class="col-6 summary-total fs-5 text-primary" id="summary-total">₹0.00</div>
                        </div>

                        <div class="mt-4 d-grid gap-2">
                            <button type="submit" class="btn btn-primary fw-semibold py-2" id="btn-submit-form">
                                <i class="bi bi-check-circle"></i> Save & Issue Document
                            </button>
                            <button type="button" class="btn btn-outline-danger fw-semibold py-2" onclick="hideCreateInvoiceForm()">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
