@php
    $wcs = DB::table('work_centers')->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-tools me-2 text-primary"></i>Work Centers & Operations Capacity</h4>
            <p class="text-muted small mb-0">Machinery capacity, hourly machine rates, labor cost & station assignment</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" onclick="alert('Adding Work Center...')">
            <i class="bi bi-plus-circle me-1"></i> Add Work Center
        </button>
    </div>

    <div class="row g-3 mb-4">
        @foreach($wcs as $wc)
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-primary-subtle text-primary font-monospace px-2 py-1 mb-1">{{ $wc->code }}</span>
                            <h5 class="fw-bold mb-0">{{ $wc->name }}</h5>
                        </div>
                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">Operational</span>
                    </div>
                    <div class="row g-2 mt-3 text-center">
                        <div class="col-4 bg-light rounded p-2">
                            <span class="text-muted small d-block">Capacity/Day</span>
                            <span class="fw-bold text-dark fs-6">{{ $wc->capacity_per_day }} Hours</span>
                        </div>
                        <div class="col-4 bg-light rounded p-2">
                            <span class="text-muted small d-block">Hourly Machine Rate</span>
                            <span class="fw-bold text-primary fs-6">₹{{ number_format($wc->hourly_cost, 2) }}</span>
                        </div>
                        <div class="col-4 bg-light rounded p-2">
                            <span class="text-muted small d-block">Labor Rate/Hr</span>
                            <span class="fw-bold text-dark fs-6">₹{{ number_format($wc->labor_cost, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
