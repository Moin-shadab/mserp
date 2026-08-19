@php
    $hsns = DB::table('hsn_sac_codes')->orderBy('code')->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-hash me-2 text-primary"></i>HSN/SAC & India GST Rates Engine</h4>
            <p class="text-muted small mb-0">HSN/SAC classification, Place of Supply state tax rules (CGST+SGST vs IGST) & GSTIN pattern validator</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" onclick="alert('Adding HSN/SAC Code...')">
            <i class="bi bi-plus-circle me-1"></i> Add HSN/SAC Code
        </button>
    </div>

    <!-- GST Tax Calculator Test Box -->
    <div class="card border-0 shadow-sm rounded-3 p-4 mb-4 bg-light border-start border-primary border-4">
        <h6 class="fw-bold mb-2"><i class="bi bi-calculator me-2 text-primary"></i>Test Place of Supply GST Calculation</h6>
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Customer State</label>
                <select id="calcState" class="form-select">
                    <option value="Maharashtra">Maharashtra (Intra-State -> CGST + SGST)</option>
                    <option value="Karnataka">Karnataka (Inter-State -> IGST)</option>
                    <option value="Delhi">Delhi (Inter-State -> IGST)</option>
                    <option value="Gujarat">Gujarat (Inter-State -> IGST)</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Taxable Amount (₹)</label>
                <input type="number" id="calcAmount" class="form-control" value="100000">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">HSN Code</label>
                <select id="calcHsn" class="form-select">
                    @foreach($hsns as $h)
                        <option value="{{ $h->code }}">{{ $h->code }} - {{ $h->description }} ({{ $h->igst_rate }}%)</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-primary w-100 rounded-pill" onclick="testGstCalc()"><i class="bi bi-cpu me-1"></i> Calculate GST</button>
            </div>
        </div>
        <div id="gstResult" class="mt-3 p-3 bg-white rounded border d-none"></div>
    </div>

    <!-- HSN Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0">HSN/SAC Codes & Tax Rate Catalog</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Code</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th class="text-end">CGST Rate</th>
                            <th class="text-end">SGST Rate</th>
                            <th class="text-end">IGST Rate</th>
                            <th class="text-center pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hsns as $h)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">{{ $h->code }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $h->type }}</span></td>
                                <td>{{ $h->description }}</td>
                                <td class="text-end font-monospace text-primary">{{ $h->cgst_rate }}%</td>
                                <td class="text-end font-monospace text-primary">{{ $h->sgst_rate }}%</td>
                                <td class="text-end font-monospace text-danger fw-bold">{{ $h->igst_rate }}%</td>
                                <td class="text-center pe-4"><span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">Active</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function testGstCalc() {
    const state = document.getElementById('calcState').value;
    const amount = document.getElementById('calcAmount').value;
    const hsn = document.getElementById('calcHsn').value;

    fetch(`/api/erp/tax/calculate-gst?customer_state=${state}&amount=${amount}&hsn_code=${hsn}`, {
        headers: {'Accept': 'application/json'}
    })
    .then(res => res.json())
    .then(data => {
        const box = document.getElementById('gstResult');
        box.classList.remove('d-none');
        box.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge ${data.is_intra_state ? 'bg-primary' : 'bg-danger'} me-2">${data.is_intra_state ? 'INTRA-STATE (CGST + SGST)' : 'INTER-STATE (IGST)'}</span>
                    <strong>Place of Supply Result:</strong>
                </div>
                <div class="font-monospace fs-5 fw-bold text-success">Grand Total: ₹${data.grand_total.toLocaleString('en-IN', {minimumFractionDigits: 2})}</div>
            </div>
            <div class="mt-2 text-muted small">
                CGST (${data.cgst_rate}%): ₹${data.cgst_amount} | SGST (${data.sgst_rate}%): ₹${data.sgst_amount} | IGST (${data.igst_rate}%): ₹${data.igst_amount} | Total Tax: ₹${data.total_tax}
            </div>
        `;
    });
}
</script>
