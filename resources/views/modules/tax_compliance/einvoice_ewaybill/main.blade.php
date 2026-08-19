<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-qr-code-scan me-2 text-primary"></i>E-Invoicing (IRN) & E-Way Bill Integration Architecture</h4>
            <p class="text-muted small mb-0">NIC E-Invoice IRN payload generation, B2B QR Code visualizer & E-Way Bill JSON export hub</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" onclick="alert('Generating E-Way Bill JSON Payload for NIC Portal...')">
            <i class="bi bi-file-earmark-code me-1"></i> Generate E-Way Bill JSON
        </button>
    </div>

    <!-- Live IRN Generator Box -->
    <div class="card border-0 shadow-sm rounded-3 p-4 mb-4 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0"><i class="bi bi-shield-check me-2 text-success"></i>E-Invoice IRN Signature Payload Preview</h6>
            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">GSSP NIC API Sandbox Active</span>
        </div>
        <div class="row g-3">
            <div class="col-md-8">
                <div class="bg-dark text-success font-monospace p-3 rounded small" style="max-height: 180px; overflow-y: auto;">
{
  "Version": "1.1",
  "TranDtls": { "TaxSch": "GST", "SupTyp": "B2B", "RegRev": "Y" },
  "DocDtls": { "Typ": "INV", "No": "INV/2026/0001", "Dt": "19/08/2026" },
  "SellerDtls": { "Gstin": "27AAACA1234A1Z5", "LglNm": "Acme Corp India", "Stcd": "27" },
  "BuyerDtls": { "Gstin": "29BBBCB5678B1Z2", "LglNm": "TechSoft Pvt Ltd", "Stcd": "29" },
  "ItemList": [{ "SlNo": "1", "PrdDesc": "Laptops", "HsnCd": "84713010", "Qty": 10, "UnitPrice": 45000 }]
}
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="border rounded bg-white p-3 shadow-sm">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=IRN:e78b20a911f92a3b04c89" alt="E-Invoice B2B Signed QR Code" class="img-fluid mb-2">
                    <span class="d-block text-muted x-small">NIC Signed QR Code Payload</span>
                    <span class="badge bg-primary-subtle text-primary font-monospace mt-1">IRN: e78b20a91...3b04c89</span>
                </div>
            </div>
        </div>
    </div>

    <!-- E-Way Bill Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0">E-Way Bills Generated Log (Threshold > ₹50,000)</h6>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">E-Way Bill No</th>
                        <th>Delivery Note</th>
                        <th>Generated Date</th>
                        <th>Valid Until</th>
                        <th>Distance (KM)</th>
                        <th class="text-center pe-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-4 font-monospace fw-bold text-primary">EWB-281920391209</td>
                        <td class="font-monospace">DN/2026/0001</td>
                        <td>2026-08-19 10:30 AM</td>
                        <td>2026-08-21 11:59 PM</td>
                        <td>340 KM</td>
                        <td class="text-center pe-4"><span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">ACTIVE (VALID)</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
