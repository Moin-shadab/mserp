<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-lock-fill me-2 text-primary"></i>Concurrency & Document Posting Controls</h4>
            <p class="text-muted small mb-0">Atomic stock transaction locking, idempotency token validator & posted document immutability controls</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-4">
                <h5 class="fw-bold text-primary mb-2"><i class="bi bi-cpu me-2"></i>Atomic Stock Movement Locks</h5>
                <p class="text-muted small">Pessimistic DB row-level locking (`SELECT ... FOR UPDATE`) prevents concurrent stock deductions from resulting in negative stock conditions.</p>
                <div class="p-3 bg-light rounded font-monospace small text-dark">
                    DB::transaction(function() {<br>
                    &nbsp;&nbsp;$item = DB::table('inventory_items')->where('id', $itemId)->lockForUpdate()->first();<br>
                    &nbsp;&nbsp;if ($item->qty_on_hand < $qtyOut) throw Exception("Insufficient Stock");<br>
                    });
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-4">
                <h5 class="fw-bold text-success mb-2"><i class="bi bi-shield-check me-2"></i>Immutable Posted Document Integrity</h5>
                <p class="text-muted small">Posted financial vouchers and invoices cannot be edited or hard-deleted. Corrections require cancellation vouchers or Credit/Debit notes.</p>
                <div class="alert alert-success border-0 rounded-3 mb-0 small">
                    <i class="bi bi-check-circle-fill me-2"></i> Document Cancellation Policy & Revision History Active
                </div>
            </div>
        </div>
    </div>
</div>
