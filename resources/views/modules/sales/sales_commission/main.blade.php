@php
    $reps = DB::table('users')->where('is_active', true)->limit(6)->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-percent me-2 text-primary"></i>Sales Commission & Price Lists</h4>
            <p class="text-muted small mb-0">Salesperson performance commissions, customer price lists & discount rules</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        @foreach($reps as $rep)
            @php $salesVal = rand(500000, 2500000); $comm = $salesVal * 0.025; @endphp
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 p-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar bg-primary-subtle text-primary rounded-circle p-2 me-3 fw-bold fs-5">
                            {{ substr($rep->name, 0, 1) }}
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">{{ $rep->name }}</h6>
                            <span class="small text-muted">{{ $rep->email }}</span>
                        </div>
                    </div>
                    <div class="border-top pt-2 d-flex justify-content-between text-muted small">
                        <span>Total Sales Value</span>
                        <span class="fw-bold text-dark">₹{{ number_format($salesVal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                        <span class="fw-bold text-success">Commission (2.5%)</span>
                        <span class="font-monospace fw-bold fs-6 text-success">₹{{ number_format($comm, 2) }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
