<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-7">
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                Welcome to your Dashboard
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fs-7 fw-semibold">Interactive</span>
            </h4>
            <p class="text-muted small mb-0">Drag & drop any widget card to customize your layout. Drag handle <i class="bi bi-grip-vertical"></i> enabled.</p>
        </div>
        <div class="col-md-5 text-md-end mt-2 mt-md-0 d-flex justify-content-md-end align-items-center gap-2">
            <button class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" onclick="resetDashboardLayout()" title="Reset to Default Layout">
                <i class="bi bi-arrow-counterclockwise"></i> Reset Layout
            </button>
            <button class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1" onclick="toggleCommandPalette()" title="Press Cmd+K for Command Palette">
                <i class="bi bi-command"></i> Command Palette <kbd class="bg-white text-dark ms-1 px-1 py-0 rounded" style="font-size:0.7rem;">⌘K</kbd>
            </button>
        </div>
    </div>

    <!-- KPI Cards Row (Draggable Grid) -->
    <div class="row mb-4" id="kpi-cards-grid">
        <!-- Sales Card -->
        <div class="col-md-3 mb-3 mb-md-0 drag-widget" data-widget-id="kpi-sales" draggable="true" ondragstart="handleDragStart(event)" ondragover="handleDragOver(event)" ondrop="handleDrop(event)" ondragend="handleDragEnd(event)">
            <div class="card p-3 border-0 bg-white shadow-sm h-100 position-relative overflow-hidden cursor-move">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-grip-vertical text-muted drag-handle me-1" style="cursor:grab;" title="Drag to reorder"></i>
                            <span class="text-muted small fw-semibold">APPROVED SALES</span>
                        </div>
                        <h3 class="fw-bold mt-1 mb-0">₹{{ number_format($totalSales, 2) }}</h3>
                    </div>
                    <div class="bg-primary-subtle p-3 rounded-3 text-primary">
                        <i class="bi bi-currency-rupee fs-4"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>

        <!-- Users Card -->
        <div class="col-md-3 mb-3 mb-md-0 drag-widget" data-widget-id="kpi-users" draggable="true" ondragstart="handleDragStart(event)" ondragover="handleDragOver(event)" ondrop="handleDrop(event)" ondragend="handleDragEnd(event)">
            <div class="card p-3 border-0 bg-white shadow-sm h-100 position-relative overflow-hidden cursor-move">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-grip-vertical text-muted drag-handle me-1" style="cursor:grab;" title="Drag to reorder"></i>
                            <span class="text-muted small fw-semibold">ACTIVE USERS</span>
                        </div>
                        <h3 class="fw-bold mt-1 mb-0">{{ $usersCount }}</h3>
                    </div>
                    <div class="bg-success-subtle p-3 rounded-3 text-success">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>

        <!-- Pending Workflows Card -->
        <div class="col-md-3 mb-3 mb-md-0 drag-widget" data-widget-id="kpi-workflows" draggable="true" ondragstart="handleDragStart(event)" ondragover="handleDragOver(event)" ondrop="handleDrop(event)" ondragend="handleDragEnd(event)">
            <div class="card p-3 border-0 bg-white shadow-sm h-100 position-relative overflow-hidden cursor-move">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-grip-vertical text-muted drag-handle me-1" style="cursor:grab;" title="Drag to reorder"></i>
                            <span class="text-muted small fw-semibold">PENDING APPROVALS</span>
                        </div>
                        <h3 class="fw-bold mt-1 mb-0">{{ count($myPendingApprovals) }} <span class="text-muted fs-6">/ {{ $pendingWorkflows }}</span></h3>
                    </div>
                    <div class="bg-warning-subtle p-3 rounded-3 text-warning">
                        <i class="bi bi-diagram-3 fs-4"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 45%" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>

        <!-- System Audit Log Card -->
        <div class="col-md-3 mb-3 mb-md-0 drag-widget" data-widget-id="kpi-audits" draggable="true" ondragstart="handleDragStart(event)" ondragover="handleDragOver(event)" ondrop="handleDrop(event)" ondragend="handleDragEnd(event)">
            <div class="card p-3 border-0 bg-white shadow-sm h-100 position-relative overflow-hidden cursor-move">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-grip-vertical text-muted drag-handle me-1" style="cursor:grab;" title="Drag to reorder"></i>
                            <span class="text-muted small fw-semibold">SYSTEM AUDIT TRAIL</span>
                        </div>
                        <h3 class="fw-bold mt-1 mb-0">{{ $totalAudits }}</h3>
                    </div>
                    <div class="bg-danger-subtle p-3 rounded-3 text-danger">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 90%" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Approvals (Actionable Workflow Center) -->
    @if(!empty($myPendingApprovals))
    <div class="row mb-4" id="workflow-center-grid">
        <div class="col-12 drag-widget" data-widget-id="widget-workflow" draggable="true" ondragstart="handleDragStart(event)" ondragover="handleDragOver(event)" ondrop="handleDrop(event)" ondragend="handleDragEnd(event)">
            <div class="card">
                <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-primary d-flex align-items-center gap-2">
                        <i class="bi bi-grip-vertical text-muted drag-handle" style="cursor:grab;" title="Drag to reorder"></i>
                        <i class="bi bi-check2-square me-1"></i>Workflow Approvals Center
                    </h5>
                    <span class="badge bg-warning text-dark fw-bold">{{ count($myPendingApprovals) }} Action Required</span>
                </div>
                <div class="table-responsive p-3 pt-0">
                    <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
                        <thead>
                            <tr class="table-light">
                                <th>Workflow Name</th>
                                <th>Record Reference</th>
                                <th>Pending Step</th>
                                <th>Requested</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myPendingApprovals as $appr)
                            <tr id="wf-row-{{ $appr['id'] }}">
                                <td class="fw-semibold">{{ $appr['workflow_name'] }}</td>
                                <td>{{ $appr['record_summary'] }}</td>
                                <td><span class="badge bg-warning text-dark">{{ $appr['current_step_name'] }}</span></td>
                                <td class="text-muted">{{ $appr['time_ago'] }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <input type="text" class="form-control form-control-sm border" placeholder="Comments..." id="wf-comments-{{ $appr['id'] }}" style="max-width:180px;">
                                        <button class="btn btn-sm btn-success" onclick="processApproval({{ $appr['id'] }}, 'approve')"><i class="bi bi-check-lg"></i> Approve</button>
                                        <button class="btn btn-sm btn-danger" onclick="processApproval({{ $appr['id'] }}, 'reject')"><i class="bi bi-x-lg"></i> Reject</button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Charts & Details Grid -->
    <div class="row mb-4" id="main-widgets-grid">
        <!-- Sales Chart -->
        <div class="col-lg-8 mb-4 mb-lg-0 drag-widget" data-widget-id="widget-sales-chart" draggable="true" ondragstart="handleDragStart(event)" ondragover="handleDragOver(event)" ondrop="handleDrop(event)" ondragend="handleDragEnd(event)">
            <div class="card h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-grip-vertical text-muted drag-handle" style="cursor:grab;" title="Drag to reorder"></i>
                        Monthly Sales Summary
                    </h5>
                    <span class="badge bg-primary-subtle text-primary fw-bold">Live Data</span>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 250px; width: 100%;">
                        <canvas id="salesSummaryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Diagnostics (Health) -->
        <div class="col-lg-4 drag-widget" data-widget-id="widget-system-health" draggable="true" ondragstart="handleDragStart(event)" ondragover="handleDragOver(event)" ondrop="handleDrop(event)" ondragend="handleDragEnd(event)">
            <div class="card h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-grip-vertical text-muted drag-handle" style="cursor:grab;" title="Drag to reorder"></i>
                        System Diagnostics
                    </h5>
                    <span class="badge bg-success-subtle text-success fw-bold">Healthy</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="font-size:0.85rem;">
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <h6 class="mb-0 fw-semibold">Database Engine</h6>
                                <small class="text-muted">Database name: {{ $systemHealth['db_name'] }}</small>
                            </div>
                            <span class="badge bg-success-subtle text-success py-2 px-3 fw-bold">{{ $systemHealth['db_size'] }}</span>
                        </div>
                        <div class="list-group-item py-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 fw-semibold">Server Storage Disk</h6>
                                <small class="text-muted">{{ $systemHealth['disk_used'] }} / {{ $systemHealth['disk_total'] }}</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $systemHealth['disk_percent'] }}%" aria-valuenow="{{ $systemHealth['disk_percent'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <h6 class="mb-0 fw-semibold">PHP Environment</h6>
                                <small class="text-muted">PHP Version</small>
                            </div>
                            <span class="badge bg-light text-dark border py-2 px-3">{{ $systemHealth['php_version'] }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <h6 class="mb-0 fw-semibold">MySQL Engine Version</h6>
                                <small class="text-muted">Database Version</small>
                            </div>
                            <span class="badge bg-light text-dark border py-2 px-3">{{ $systemHealth['mysql_version'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Audit Logs Grid -->
    <div class="row mb-4" id="audit-logs-grid">
        <div class="col-12 drag-widget" data-widget-id="widget-audit-logs" draggable="true" ondragstart="handleDragStart(event)" ondragover="handleDragOver(event)" ondrop="handleDrop(event)" ondragend="handleDragEnd(event)">
            <div class="card">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-grip-vertical text-muted drag-handle" style="cursor:grab;" title="Drag to reorder"></i>
                        System Audit Logs Trail
                    </h5>
                    <span class="badge bg-light text-dark border">Recent 10 Actions</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                                <th>Target Entity</th>
                                <th>Record ID</th>
                                <th>IP Address</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentAudits as $log)
                            <tr>
                                <td class="fw-semibold">{{ $log->user_name ?: 'System Job' }}</td>
                                <td>
                                    <span class="badge bg-{{ $log->action === 'CREATE' ? 'success' : ($log->action === 'UPDATE' ? 'info' : 'danger') }} px-2 py-1">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="font-monospace text-muted">{{ $log->table_name }}</td>
                                <td>#{{ $log->record_id }}</td>
                                <td>{{ $log->ip_address }}</td>
                                <td>{{ $log->time_ago }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dynamic Chart & Drag-and-Drop Scripting -->
<script>
    initSalesChart();
    restoreDashboardLayout();

    function initSalesChart() {
        const canvas = document.getElementById('salesSummaryChart');
        if (!canvas) return;

        if (typeof Chart === 'undefined') {
            setTimeout(initSalesChart, 50);
            return;
        }

        const ctx = canvas.getContext('2d');
        const chartData = @json($monthlySales);
        const labels = Object.keys(chartData);
        const values = Object.values(chartData);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels.length ? labels : ['No Data'],
                datasets: [{
                    label: 'Approved Sales Volume (₹)',
                    data: values.length ? values : [0],
                    backgroundColor: 'rgba(37, 99, 235, 0.85)',
                    borderColor: 'rgb(37, 99, 235)',
                    borderWidth: 1,
                    borderRadius: 6,
                    barThickness: 24
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { 
                            font: { family: 'Plus Jakarta Sans' },
                            callback: function(value) {
                                return '₹' + value.toLocaleString('en-IN');
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Plus Jakarta Sans' } }
                    }
                }
            }
        });
    }

    // HTML5 Drag and Drop Handlers for Dashboard Widgets
    let dragSrcElement = null;

    function handleDragStart(e) {
        dragSrcElement = e.currentTarget;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', e.currentTarget.getAttribute('data-widget-id'));
        e.currentTarget.classList.add('opacity-50', 'border-dashed');
    }

    function handleDragOver(e) {
        if (e.preventDefault) {
            e.preventDefault();
        }
        e.dataTransfer.dropEffect = 'move';
        return false;
    }

    function handleDrop(e) {
        if (e.stopPropagation) {
            e.stopPropagation();
        }
        const targetElement = e.currentTarget;

        if (dragSrcElement && dragSrcElement !== targetElement) {
            // Check if both elements share the same parent row container
            if (dragSrcElement.parentNode === targetElement.parentNode) {
                const parent = dragSrcElement.parentNode;
                const children = Array.from(parent.children);
                const srcIdx = children.indexOf(dragSrcElement);
                const targetIdx = children.indexOf(targetElement);

                if (srcIdx < targetIdx) {
                    parent.insertBefore(dragSrcElement, targetElement.nextSibling);
                } else {
                    parent.insertBefore(dragSrcElement, targetElement);
                }

                saveDashboardLayout();
            }
        }
        return false;
    }

    function handleDragEnd(e) {
        e.currentTarget.classList.remove('opacity-50', 'border-dashed');
        document.querySelectorAll('.drag-widget').forEach(el => el.classList.remove('opacity-50', 'border-dashed'));
    }

    function saveDashboardLayout() {
        const order = [];
        document.querySelectorAll('.drag-widget').forEach(el => {
            order.push(el.getAttribute('data-widget-id'));
        });

        localStorage.setItem('erp_dashboard_layout', JSON.stringify(order));

        fetch('/api/user/save-dashboard-layout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ layout: order })
        }).catch(err => console.log('Layout save error:', err));
    }

    function restoreDashboardLayout() {
        const saved = localStorage.getItem('erp_dashboard_layout');
        if (!saved) return;

        try {
            const order = JSON.parse(saved);
            order.forEach(widgetId => {
                const widget = document.querySelector(`[data-widget-id="${widgetId}"]`);
                if (widget && widget.parentNode) {
                    widget.parentNode.appendChild(widget);
                }
            });
        } catch(e) {}
    }

    function resetDashboardLayout() {
        localStorage.removeItem('erp_dashboard_layout');
        if (window.showToast) showToast('info', 'Dashboard layout reset to default.');
        loadDashboard();
    }
    window.resetDashboardLayout = resetDashboardLayout;

    // Process Inline approvals
    function processApproval(instanceId, action) {
        const comments = document.getElementById('wf-comments-' + instanceId).value;
        const endpoint = action === 'approve' 
            ? '/api/workflow/approve/' + instanceId 
            : '/api/workflow/reject/' + instanceId;

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ comments: comments })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast('success', data.message);
                loadDashboard();
            } else {
                showToast('danger', data.error || 'Workflow action failed.');
            }
        })
        .catch(err => {
            showToast('danger', 'Network error executing approval.');
        });
    }
</script>
