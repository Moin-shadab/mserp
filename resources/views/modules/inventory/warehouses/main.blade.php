@php
    $warehouses = DB::table('warehouses')->orderBy('code')->get();
    $bins = DB::table('warehouse_bins')
        ->join('warehouses', 'warehouse_bins.warehouse_id', '=', 'warehouses.id')
        ->select('warehouse_bins.*', 'warehouses.name as warehouse_name', 'warehouses.code as warehouse_code')
        ->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-building-gear me-2 text-primary"></i>Warehouses & Location Bins</h4>
            <p class="text-muted small mb-0">Multi-warehouse facility structure, aisles, racks, shelves & bin locations</p>
        </div>
        <button class="btn btn-primary rounded-pill shadow-sm" onclick="alert('Creating Warehouse Location...')">
            <i class="bi bi-plus-circle me-1"></i> Add Warehouse
        </button>
    </div>

    <!-- Warehouses Grid -->
    <div class="row g-3 mb-4">
        @foreach($warehouses as $wh)
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-primary-subtle text-primary font-monospace px-2 py-1 mb-1">{{ $wh->code }}</span>
                            <h5 class="fw-bold mb-0">{{ $wh->name }}</h5>
                        </div>
                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">Active</span>
                    </div>
                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i>{{ $wh->address }}</p>
                    <div class="border-top pt-2 d-flex justify-content-between text-muted small">
                        <span>Facility Manager</span>
                        <span class="fw-bold text-dark">{{ $wh->manager_name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Location Bins Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0">Aisle / Rack / Shelf / Bin Locations</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Bin Code</th>
                            <th>Warehouse</th>
                            <th>Aisle</th>
                            <th>Rack</th>
                            <th>Shelf</th>
                            <th class="text-center pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bins as $b)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">{{ $b->bin_code }}</td>
                                <td>{{ $b->warehouse_name }} ({{ $b->warehouse_code }})</td>
                                <td>{{ $b->aisle ?? '-' }}</td>
                                <td>{{ $b->rack ?? '-' }}</td>
                                <td>{{ $b->shelf ?? '-' }}</td>
                                <td class="text-center pe-4"><span class="badge bg-success-subtle text-success rounded-pill px-2 py-1">Available</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
