<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $docTitle }} - {{ $invoice->{$docNoKey} }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 12px;
            color: #333333;
            background: #f8fafc;
            padding: 40px 0;
        }
        .invoice-box {
            max-width: 900px;
            margin: auto;
            padding: 40px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            border-radius: 8px;
        }
        .invoice-title {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        .text-header-label {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
        }
        .table-gst {
            border: 1px solid #cbd5e1;
        }
        .table-gst th {
            background-color: #f1f5f9;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            font-size: 10px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
            text-align: center;
        }
        .table-gst td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            vertical-align: middle;
        }
        .border-top-double {
            border-top: 3px double #cbd5e1 !important;
        }
        .sign-area {
            border-top: 1px solid #e2e8f0;
            margin-top: 40px;
            padding-top: 10px;
            text-align: center;
        }
        @media print {
            body {
                background: none;
                padding: 0;
            }
            .invoice-box {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="container mb-4 no-print text-center">
    <button class="btn btn-primary px-4 py-2 fw-semibold" onclick="window.print()">
        Print / Save PDF
    </button>
</div>

<div class="invoice-box">
    <!-- Invoice Header -->
    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <h1 class="invoice-title mb-1">{{ $docTitle }}</h1>
            <p class="text-muted mb-0 small">Indian GST Compliant Document</p>
        </div>
        <div class="col-sm-6 text-sm-end">
            <!-- If Purchase Document, Acme is Buyer, Contact is Seller -->
            <!-- If Sales Document, Acme is Seller, Contact is Buyer -->
            @if(in_array($docType, ['purchase-orders', 'purchase-invoices']))
                <div class="fw-bold text-dark fs-5">{{ $contact->name }}</div>
                <div class="text-secondary">{{ $contact->address }}</div>
                <div class="text-secondary">GSTIN: <span class="fw-semibold">{{ $contact->gstin ?? 'Unregistered' }}</span></div>
                <div class="text-secondary">State: <span class="fw-semibold">{{ $contact->state }}</span></div>
            @else
                <div class="fw-bold text-dark fs-5">{{ $company->name }}</div>
                <div class="text-secondary">{{ $company->address }}</div>
                <div class="text-secondary">GSTIN: <span class="fw-semibold">{{ $company->gstin ?? '27AAACA1234A1Z5' }}</span></div>
                <div class="text-secondary">State: <span class="fw-semibold">{{ $company->state ?? 'Maharashtra' }}</span></div>
            @endif
        </div>
    </div>

    <hr class="my-4" style="border-color: #cbd5e1;">

    <!-- Billing metadata -->
    <div class="row g-4 mb-4">
        <div class="col-sm-4">
            <div class="text-header-label mb-2">Document Metadata</div>
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <td class="text-muted p-0" style="width: 45%;">Doc Number:</td>
                    <td class="fw-bold text-dark p-0">{{ $invoice->{$docNoKey} }}</td>
                </tr>
                <tr>
                    <td class="text-muted p-0">Issue Date:</td>
                    <td class="fw-medium text-dark p-0">
                        @php
                            $dateCol = ($docType === 'purchase-invoices') ? 'bill_date' : (($docType === 'sales-orders') ? 'order_date' : (($docType === 'purchase-orders') ? 'po_date' : (($docType === 'sales-quotations') ? 'quote_date' : 'invoice_date')));
                        @endphp
                        {{ \Carbon\Carbon::parse($invoice->{$dateCol})->format('d-M-Y') }}
                    </td>
                </tr>
                <tr>
                    <td class="text-muted p-0">
                        {{ ($docType === 'sales-quotations') ? 'Valid Until:' : (in_array($docType, ['sales-orders', 'purchase-orders']) ? 'Delivery Date:' : 'Due Date:') }}
                    </td>
                    <td class="fw-medium text-dark p-0">
                        @php
                            $dueCol = ($docType === 'sales-quotations') ? 'valid_until' : (in_array($docType, ['sales-orders', 'purchase-orders']) ? 'delivery_date' : 'due_date');
                        @endphp
                        {{ $invoice->{$dueCol} ? \Carbon\Carbon::parse($invoice->{$dueCol})->format('d-M-Y') : 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <td class="text-muted p-0">Payment Terms:</td>
                    <td class="fw-medium text-dark p-0">{{ $invoice->payment_terms ?? 'Net 30' }}</td>
                </tr>
            </table>
        </div>

        <div class="col-sm-4">
            <!-- Swap Buyer / Seller labels -->
            @if(in_array($docType, ['purchase-orders', 'purchase-invoices']))
                <div class="text-header-label mb-2">Deliver To / Buyer</div>
                <div class="fw-bold text-dark mb-1">{{ $company->name }}</div>
                <div class="text-secondary mb-2" style="white-space: pre-line;">{{ $invoice->billing_address ?? $company->address }}</div>
                <div class="small">
                    <strong>GSTIN:</strong> {{ $company->gstin ?? '27AAACA1234A1Z5' }}<br>
                    <strong>State:</strong> {{ $company->state ?? 'Maharashtra' }} (GST Code: {{ substr($company->gstin ?? '27', 0, 2) }})
                </div>
            @else
                <div class="text-header-label mb-2">Bill To / Recipient</div>
                <div class="fw-bold text-dark mb-1">{{ $contact->name }}</div>
                <div class="text-secondary mb-2" style="white-space: pre-line;">{{ $invoice->billing_address ?? $contact->address }}</div>
                <div class="small">
                    <strong>GSTIN:</strong> {{ $contact->gstin ?? 'Unregistered' }}<br>
                    <strong>State:</strong> {{ $contact->state }} (GST Code: {{ $contact->gstin ? substr($contact->gstin, 0, 2) : 'N/A' }})
                </div>
            @endif
        </div>

        <div class="col-sm-4">
            <div class="text-header-label mb-2">Supply Tax Context</div>
            <div class="fw-semibold text-dark mb-1">{{ $contact->city ?? 'India' }}, {{ $contact->state }}</div>
            <div class="text-muted small">
                Tax calculated based on supply location.<br>
                Type: <strong>{{ ($invoice->igst && $invoice->igst > 0) ? 'Inter-State (IGST)' : 'Intra-State (CGST + SGST)' }}</strong>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="table-responsive mb-4">
        <table class="table table-gst mb-0">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th class="text-start" style="width: 30%;">Product Description</th>
                    <th style="width: 10%;">HSN/SAC</th>
                    <th style="width: 8%;">Qty</th>
                    <th class="text-end" style="width: 12%;">Rate (₹)</th>
                    <th class="text-end" style="width: 12%;">Taxable Value</th>
                    @if($invoice->igst && $invoice->igst > 0)
                        <th class="text-end" style="width: 15%;">IGST</th>
                    @else
                        <th class="text-end" style="width: 10%;">CGST</th>
                        <th class="text-end" style="width: 10%;">SGST</th>
                    @endif
                    <th class="text-end" style="width: 15%;">Total (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $idx => $item)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="fw-semibold text-dark">{{ $item->item_name }}</td>
                        <td class="text-center">{{ $item->hsn_sac ?? '8471' }}</td>
                        <td class="text-center">{{ $item->qty }}</td>
                        <td class="text-end">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end">₹{{ number_format($item->subtotal, 2) }}</td>
                        @if($invoice->igst && $invoice->igst > 0)
                            <td class="text-end">
                                <div class="text-muted small">{{ number_format($item->igst_rate, 1) }}%</div>
                                <div>₹{{ number_format($item->igst_amount, 2) }}</div>
                            </td>
                        @else
                            <td class="text-end">
                                <div class="text-muted small">{{ number_format($item->cgst_rate, 1) }}%</div>
                                <div>₹{{ number_format($item->cgst_amount, 2) }}</div>
                            </td>
                            <td class="text-end">
                                <div class="text-muted small">{{ number_format($item->sgst_rate, 1) }}%</div>
                                <div>₹{{ number_format($item->sgst_amount, 2) }}</div>
                            </td>
                        @endif
                        <td class="text-end fw-bold text-dark">₹{{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                @endforeach

                <!-- Totals row -->
                <tr class="fw-bold text-dark border-top-double">
                    <td colspan="3" class="text-end">Total Summary:</td>
                    <td class="text-center">{{ $items->sum('qty') }}</td>
                    <td></td>
                    <td class="text-end">₹{{ number_format($invoice->amount, 2) }}</td>
                    @if($invoice->igst && $invoice->igst > 0)
                        <td class="text-end">₹{{ number_format($invoice->igst, 2) }}</td>
                    @else
                        <td class="text-end">₹{{ number_format($invoice->cgst, 2) }}</td>
                        <td class="text-end">₹{{ number_format($invoice->sgst, 2) }}</td>
                    @endif
                    <td class="text-end fs-6 text-primary">₹{{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Amount in Words -->
    <div class="card p-3 border-0 bg-light mb-4">
        <div class="row g-2">
            <div class="col-sm-3 text-muted text-uppercase fw-bold small">Amount in Words:</div>
            <div class="col-sm-9 fw-semibold text-dark">{{ $totalInWords }}</div>
        </div>
    </div>

    <!-- Bank Details & Signature -->
    <div class="row g-4 mt-2">
        <div class="col-sm-7 small">
            <div class="text-header-label mb-2">Our Bank Details</div>
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <td class="text-muted p-0" style="width: 35%;">Bank Name:</td>
                    <td class="fw-semibold text-dark p-0">{{ $company->bank_name ?? 'HDFC Bank Ltd' }}</td>
                </tr>
                <tr>
                    <td class="text-muted p-0">Account Number:</td>
                    <td class="fw-semibold text-dark p-0">{{ $company->bank_acc_no ?? '50100203495834' }}</td>
                </tr>
                <tr>
                    <td class="text-muted p-0">IFSC Code:</td>
                    <td class="fw-semibold text-dark p-0">{{ $company->bank_ifsc ?? 'HDFC0000060' }}</td>
                </tr>
                <tr>
                    <td class="text-muted p-0">PAN Number:</td>
                    <td class="fw-semibold text-dark p-0">{{ $company->pan ?? 'AAACA1234A' }}</td>
                </tr>
            </table>

            <div class="text-header-label mt-4 mb-2">Terms & Conditions</div>
            <ol class="text-muted ps-3 mb-0" style="font-size: 10px;">
                <li>Payment must be completed within the specified due date.</li>
                <li>Interest @ 18% p.a. will be charged for delayed payments.</li>
                <li>All disputes are subject to Mumbai Jurisdiction.</li>
            </ol>
        </div>

        <div class="col-sm-5 d-flex flex-column justify-content-between align-items-end text-end">
            <div>
                <!-- If Purchase, signed on behalf of Vendor. If Sales, signed on behalf of Acme -->
                @if(in_array($docType, ['purchase-orders', 'purchase-invoices']))
                    <div class="text-muted small">For <strong>{{ $contact->name }}</strong></div>
                @else
                    <div class="text-muted small">For <strong>{{ $company->name }}</strong></div>
                @endif
                <br><br><br>
            </div>
            <div class="text-center w-70">
                <hr class="mb-1" style="width: 150px; border-color: #94a3b8;">
                <div class="fw-semibold text-dark small">Authorized Signatory</div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
