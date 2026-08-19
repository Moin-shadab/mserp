@php
    $uoms = DB::table('uoms')->get();
    $costCenters = DB::table('cost_centers')->get();
    $series = DB::table('numbering_series')->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-folder2-open me-2 text-primary"></i>Master Data Catalog</h4>
            <p class="text-muted small mb-0">Centralized setup for Units of Measure (UOM), Cost Centers & Document Numbering Series</p>
        </div>
    </div>

    <!-- Master Data Tabs -->
    <ul class="nav nav-pills mb-4">
        <li class="nav-item"><button class="nav-link active rounded-pill px-4 fw-bold" data-bs-toggle="tab" data-bs-target="#uomTab">Units of Measure (UOM)</button></li>
        <li class="nav-item ms-2"><button class="nav-link rounded-pill px-4 fw-bold" data-bs-toggle="tab" data-bs-target="#costTab">Cost Centers</button></li>
        <li class="nav-item ms-2"><button class="nav-link rounded-pill px-4 fw-bold" data-bs-toggle="tab" data-bs-target="#seriesTab">Document Numbering Series</button></li>
    </ul>

    <div class="tab-content">
        <!-- UOMs -->
        <div class="tab-pane fade show active" id="uomTab">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">UOM Code</th>
                                <th>Name</th>
                                <th class="text-center pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($uoms as $u)
                                <tr>
                                    <td class="ps-4 font-monospace fw-bold text-primary">{{ $u->code }}</td>
                                    <td>{{ $u->name }}</td>
                                    <td class="text-center pe-4"><span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">Active</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Cost Centers -->
        <div class="tab-pane fade" id="costTab">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Code</th>
                                <th>Cost Center Name</th>
                                <th>Description</th>
                                <th class="text-center pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($costCenters as $cc)
                                <tr>
                                    <td class="ps-4 font-monospace fw-bold text-primary">{{ $cc->code }}</td>
                                    <td class="fw-semibold">{{ $cc->name }}</td>
                                    <td>{{ $cc->description ?? 'N/A' }}</td>
                                    <td class="text-center pe-4"><span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">Active</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Numbering Series -->
        <div class="tab-pane fade" id="seriesTab">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Module</th>
                                <th>Prefix</th>
                                <th>Current Counter</th>
                                <th class="text-end pe-4">Next Generated Format</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($series as $s)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">{{ strtoupper(str_replace('_', ' ', $s->module)) }}</td>
                                    <td class="font-monospace text-primary fw-bold">{{ $s->prefix }}</td>
                                    <td class="font-monospace">{{ $s->current_number }}</td>
                                    <td class="text-end font-monospace text-success fw-bold pe-4">{{ $s->prefix }}{{ str_pad($s->current_number, $s->padding, '0', STR_PAD_LEFT) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
