@php
    $audits = DB::table('audit_logs')
        ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
        ->select('audit_logs.*', 'users.name as user_name')
        ->orderBy('audit_logs.id', 'desc')
        ->limit(20)
        ->get();
@endphp

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-shield-lock me-2 text-primary"></i>Security, RBAC & Audit Trails</h4>
            <p class="text-muted small mb-0">Role inheritance, record-level permissions, MFA settings & immutable audit log explorer</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0">Immutable Field-Level System Audit Log (Who, What, When, IP)</h6>
            <span class="badge bg-primary-subtle text-primary">Real-Time Audit Trailing</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Timestamp</th>
                            <th>User</th>
                            <th>Action Type</th>
                            <th>Target Table & Record</th>
                            <th>IP Address</th>
                            <th class="pe-4">Audit Record Payload</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($audits as $a)
                            <tr>
                                <td class="ps-4 text-muted small">{{ $a->created_at }}</td>
                                <td class="fw-semibold">{{ $a->user_name ?? 'System Admin' }}</td>
                                <td><span class="badge bg-primary-subtle text-primary font-monospace">{{ $a->action }}</span></td>
                                <td><span class="font-monospace text-dark">{{ $a->table_name ?? 'system' }}</span> #{{ $a->record_id ?? '-' }}</td>
                                <td class="font-monospace text-muted">{{ $a->ip_address ?? '127.0.0.1' }}</td>
                                <td class="pe-4"><code class="text-muted small">{{ Str::limit($a->new_values, 60) }}</code></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">No audit logs captured yet. System actions are automatically logged.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
