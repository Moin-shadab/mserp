@php
    $periods = DB::table('fiscal_periods')->orderBy('start_date', 'asc')->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-calendar-range me-2 text-primary"></i>Fiscal Periods & Period Closing</h4>
            <p class="text-muted small mb-0">Define accounting fiscal quarters and lock closed periods against backdated entries</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Period Code</th>
                            <th>Period Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th class="text-center">Lock Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($periods as $p)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">{{ $p->code }}</td>
                                <td class="fw-semibold">{{ $p->name }}</td>
                                <td>{{ $p->start_date }}</td>
                                <td>{{ $p->end_date }}</td>
                                <td class="text-center">
                                    @if($p->is_closed)
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1"><i class="bi bi-lock-fill me-1"></i>CLOSED & LOCKED</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1"><i class="bi bi-unlock-fill me-1"></i>OPEN</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    @if(!$p->is_closed)
                                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="alert('Fiscal Period {{ $p->code }} is now Locked.')"><i class="bi bi-lock me-1"></i>Close Period</button>
                                    @else
                                        <span class="text-muted small">Locked</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
