<div class="container-fluid p-4">
    <!-- Header banner -->
    <div class="card bg-gradient-primary text-white border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
            <div>
                <span class="badge bg-white text-primary fw-bold text-uppercase px-2 py-1 mb-2">Developer Tooling</span>
                <h3 class="fw-bold mb-1"><i class="bi bi-code-slash me-2"></i>SQL to AG Grid & CRUD Generator Studio</h3>
                <p class="mb-0 text-white-50">Paste any SQL query to auto-detect columns, data types, and primary key, then visually generate high-performance AG Grid CRUD pages.</p>
            </div>
            <div>
                <span class="badge bg-success px-3 py-2 fs-6"><i class="bi bi-lightning-charge-fill me-1"></i> Auto-Code & Metadata Engine</span>
            </div>
        </div>
    </div>

    <!-- Wizard Steps Nav -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center text-center position-relative">
                <div class="wizard-step active" id="step-btn-1" style="flex:1;">
                    <div class="badge rounded-circle bg-primary p-3 mb-1 fs-6">1</div>
                    <div class="fw-bold text-dark small">Target Module & Page</div>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
                <div class="wizard-step" id="step-btn-2" style="flex:1;">
                    <div class="badge rounded-circle bg-secondary p-3 mb-1 fs-6">2</div>
                    <div class="fw-bold text-muted small">SQL Query & Auto-Detect</div>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
                <div class="wizard-step" id="step-btn-3" style="flex:1;">
                    <div class="badge rounded-circle bg-secondary p-3 mb-1 fs-6">3</div>
                    <div class="fw-bold text-muted small">AG Grid Configurator</div>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
                <div class="wizard-step" id="step-btn-4" style="flex:1;">
                    <div class="badge rounded-circle bg-secondary p-3 mb-1 fs-6">4</div>
                    <div class="fw-bold text-muted small">Form & Validation Config</div>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
                <div class="wizard-step" id="step-btn-5" style="flex:1;">
                    <div class="badge rounded-circle bg-secondary p-3 mb-1 fs-6">5</div>
                    <div class="fw-bold text-muted small">Generate & Deploy</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wizard Content Cards -->
    <form id="dev-generator-form" onsubmit="submitDevGenerator(event)">
        
        <!-- STEP 1: Target Module & Page -->
        <div class="card border-0 shadow-sm mb-4 dev-step-card" id="step-card-1">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-folder-plus me-2"></i>Step 1: Module & Page Configuration</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Select Existing Module or Enter New</label>
                        <select class="form-select" id="existing-module-select" onchange="onModuleSelectChange()">
                            <option value="">-- Create New Module --</option>
                            @foreach($modules as $mod)
                                <option value="{{ \Illuminate\Support\Str::slug($mod->name) }}" data-name="{{ $mod->name }}">{{ $mod->name }} ({{ \Illuminate\Support\Str::slug($mod->name) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Module Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="module_name" name="module_name" placeholder="e.g. Task Manager" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Module Slug</label>
                        <input type="text" class="form-control" id="module_slug" name="module_slug" placeholder="task-manager" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Page Title / Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="page_name" name="page_name" placeholder="e.g. Todo List" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Page Slug</label>
                        <input type="text" class="form-control" id="page_slug" name="page_slug" placeholder="todo-list" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Icon Class</label>
                        <input type="text" class="form-control" id="page_icon" name="page_icon" value="bi-table" placeholder="bi-check2-square">
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-primary px-4" onclick="goToStep(2)">Next: SQL & Auto-Detect <i class="bi bi-arrow-right ms-1"></i></button>
                </div>
            </div>
        </div>

        <!-- STEP 2: SQL Query & Auto Detection -->
        <div class="card border-0 shadow-sm mb-4 dev-step-card d-none" id="step-card-2">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-database-check me-2"></i>Step 2: Paste SQL Query & Detect Schema</h5>
                <button type="button" class="btn btn-sm btn-outline-success" onclick="analyzeSqlQuery()"><i class="bi bi-magic me-1"></i> Auto-Detect Columns</button>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Paste SQL Query <span class="text-danger">*</span></label>
                    <textarea class="form-control font-monospace" id="sql_query" name="sql_query" rows="5" placeholder="SELECT * FROM customers WHERE is_active = 1..." required></textarea>
                    <div class="form-text">Support standard MySQL SELECT statements or table joins. The system will inspect columns and data types automatically.</div>
                </div>

                <div class="row g-3 d-none" id="sql-analysis-results">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Detected Physical Table</label>
                        <input type="text" class="form-control" id="db_table" name="db_table" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Choose Primary Key (PK) for CRUD Operations <span class="text-danger">*</span></label>
                        <select class="form-select" id="primary_key" name="primary_key" required>
                            <!-- Populated dynamically -->
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="alert alert-info py-2 small mb-0">
                            <i class="bi bi-info-circle-fill me-1"></i> Detected <strong id="detected-count">0</strong> columns. You can customize visible columns, form fields, inputs, dropdowns, and validation in the next steps.
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary" onclick="goToStep(1)"><i class="bi bi-arrow-left me-1"></i> Back</button>
                    <button type="button" class="btn btn-primary px-4" onclick="proceedToStep3()">Next: Configure AG Grid <i class="bi bi-arrow-right ms-1"></i></button>
                </div>
            </div>
        </div>

        <!-- STEP 3: AG Grid Configurator -->
        <div class="card border-0 shadow-sm mb-4 dev-step-card d-none" id="step-card-3">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-layout-three-columns me-2"></i>Step 3: AG Grid Table Customizer</h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">Configure column headers, visibility, widths, text wrapping, badge formatters, and table features.</p>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle border">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">Show</th>
                                <th>Field Name</th>
                                <th>Header Label</th>
                                <th style="width: 140px;">Formatter</th>
                                <th style="width: 100px;">Sortable</th>
                                <th style="width: 100px;">Filter</th>
                                <th style="width: 100px;">Wrap Text</th>
                                <th style="width: 100px;">Auto Height</th>
                            </tr>
                        </thead>
                        <tbody id="grid-columns-config-body">
                            <!-- Rows rendered dynamically -->
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary" onclick="goToStep(2)"><i class="bi bi-arrow-left me-1"></i> Back</button>
                    <button type="button" class="btn btn-primary px-4" onclick="goToStep(4)">Next: Configure Form Components <i class="bi bi-arrow-right ms-1"></i></button>
                </div>
            </div>
        </div>

        <!-- STEP 4: Form & Validation Configurator -->
        <div class="card border-0 shadow-sm mb-4 dev-step-card d-none" id="step-card-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-ui-checks me-2"></i>Step 4: Reusable Form Components & Validation</h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">Choose input types (Text, Number, Date, Select Dropdown, Searchable Dropdown, Multiselect, Checkbox, File) and validation rules.</p>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle border">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">Include</th>
                                <th>Field</th>
                                <th>Form Label</th>
                                <th style="width: 200px;">Component Type</th>
                                <th style="width: 120px;">Width (1-12)</th>
                                <th style="width: 80px;">Required</th>
                                <th>Options / Dropdown Source</th>
                            </tr>
                        </thead>
                        <tbody id="form-fields-config-body">
                            <!-- Rows rendered dynamically -->
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary" onclick="goToStep(3)"><i class="bi bi-arrow-left me-1"></i> Back</button>
                    <button type="button" class="btn btn-primary px-4" onclick="goToStep(5)">Next: Review & Deploy <i class="bi bi-arrow-right ms-1"></i></button>
                </div>
            </div>
        </div>

        <!-- STEP 5: Generate & Deploy -->
        <div class="card border-0 shadow-sm mb-4 dev-step-card d-none" id="step-card-5">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-rocket-takeoff me-2"></i>Step 5: Code Generation Mode & Deployment</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card border-primary h-100 p-3 shadow-sm cursor-pointer gen-mode-card active" onclick="setGenMode('metadata')" id="mode-card-metadata">
                            <div class="d-flex align-items-center mb-2">
                                <input class="form-check-input me-2" type="radio" name="generation_mode" value="metadata" checked>
                                <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-lightning-fill me-1"></i> Low-Code Metadata Engine</h6>
                            </div>
                            <p class="text-muted small mb-0">Instantly creates the page record in the database metadata system (`pages` table). Fastest execution, zero filesystem clutter, dynamically loaded by `DynamicCrudController`.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border h-100 p-3 shadow-sm cursor-pointer gen-mode-card" onclick="setGenMode('isolated_code')" id="mode-card-isolated_code">
                            <div class="d-flex align-items-center mb-2">
                                <input class="form-check-input me-2" type="radio" name="generation_mode" value="isolated_code">
                                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-code me-1"></i> Standalone Isolated Files Generator</h6>
                            </div>
                            <p class="text-muted small mb-0">Safely generates isolated Blade views, JS code, and Controllers in `routes/generated_modules.php`. Ensures zero breaking changes or overwrites to existing modules.</p>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning py-3">
                    <h6><i class="bi bi-shield-check me-2"></i>Safety Guarantee</h6>
                    <p class="small mb-0">All generated pages and routes are isolated in dedicated sub-paths. Super Admin and Admin permissions will be automatically granted to access the new CRUD page immediately upon creation.</p>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary" onclick="goToStep(4)"><i class="bi bi-arrow-left me-1"></i> Back</button>
                    <button type="submit" class="btn btn-success btn-lg px-5 fw-bold" id="btn-generate-submit"><i class="bi bi-check-circle-fill me-2"></i> Generate & Publish Page</button>
                </div>
            </div>
        </div>

    </form>
</div>

<script>
(function() {
    let currentStep = 1;
    let analyzedColumns = [];

    // Auto-populate slugs when names change
    document.getElementById('module_name').addEventListener('input', function(e) {
        if (!document.getElementById('existing-module-select').value) {
            document.getElementById('module_slug').value = slugify(e.target.value);
        }
    });

    document.getElementById('page_name').addEventListener('input', function(e) {
        document.getElementById('page_slug').value = slugify(e.target.value);
    });

    window.slugify = function(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-')
            .replace(/^-+/, '')
            .replace(/-+$/, '');
    };

    window.onModuleSelectChange = function() {
        const select = document.getElementById('existing-module-select');
        const selectedOpt = select.options[select.selectedIndex];
        if (select.value) {
            document.getElementById('module_name').value = selectedOpt.getAttribute('data-name');
            document.getElementById('module_slug').value = select.value;
        } else {
            document.getElementById('module_name').value = '';
            document.getElementById('module_slug').value = '';
        }
    };

    window.goToStep = function(stepNum) {
        document.querySelectorAll('.dev-step-card').forEach(c => c.classList.add('d-none'));
        document.getElementById(`step-card-${stepNum}`).classList.remove('d-none');

        for (let i = 1; i <= 5; i++) {
            const btn = document.getElementById(`step-btn-${i}`);
            const badge = btn.querySelector('.badge');
            if (i === stepNum) {
                badge.className = 'badge rounded-circle bg-primary p-3 mb-1 fs-6';
                btn.querySelector('.small').className = 'fw-bold text-dark small';
            } else if (i < stepNum) {
                badge.className = 'badge rounded-circle bg-success p-3 mb-1 fs-6';
                btn.querySelector('.small').className = 'fw-bold text-success small';
            } else {
                badge.className = 'badge rounded-circle bg-secondary p-3 mb-1 fs-6';
                btn.querySelector('.small').className = 'fw-bold text-muted small';
            }
        }
        currentStep = stepNum;
    };

    window.analyzeSqlQuery = function() {
        const sql = document.getElementById('sql_query').value.trim();
        if (!sql) {
            showToast('danger', 'Please enter a SQL query first.');
            return;
        }

        fetch('/api/developer/analyze-query', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ sql: sql })
        })
        .then(res => res.json())
        .then(resData => {
            if (!resData.success) throw new Error(resData.error);
            const data = resData.data;

            analyzedColumns = data.columns;
            document.getElementById('db_table').value = data.table_name || 'users';

            // Populate Primary Key dropdown
            const pkSelect = document.getElementById('primary_key');
            pkSelect.innerHTML = '';
            data.columns.forEach(col => {
                const opt = document.createElement('option');
                opt.value = col.field;
                opt.textContent = `${col.field} (${col.native_type})`;
                if (col.field === data.primary_key) opt.selected = true;
                pkSelect.appendChild(opt);
            });

            document.getElementById('detected-count').textContent = data.columns.length;
            document.getElementById('sql-analysis-results').classList.remove('d-none');

            renderGridConfigTable();
            renderFormConfigTable();
            showToast('success', `Detected ${data.columns.length} columns successfully.`);
        })
        .catch(err => {
            showToast('danger', err.message || 'Failed to analyze SQL query.');
        });
    };

    window.proceedToStep3 = function() {
        if (analyzedColumns.length === 0) {
            analyzeSqlQuery();
        }
        goToStep(3);
    };

    function renderGridConfigTable() {
        const tbody = document.getElementById('grid-columns-config-body');
        tbody.innerHTML = '';

        analyzedColumns.forEach((col, idx) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="checkbox" class="form-check-input col-show-grid" data-idx="${idx}" checked></td>
                <td><code>${col.field}</code></td>
                <td><input type="text" class="form-control form-control-sm col-header-grid" data-idx="${idx}" value="${col.headerName}"></td>
                <td>
                    <select class="form-select form-select-sm col-formatter-grid" data-idx="${idx}">
                        <option value="" ${!col.formatter ? 'selected' : ''}>None</option>
                        <option value="badge" ${col.formatter === 'badge' ? 'selected' : ''}>Badge Pill</option>
                        <option value="currency" ${col.formatter === 'currency' ? 'selected' : ''}>Currency ($)</option>
                        <option value="date" ${col.type === 'date' || col.type === 'datetime' ? 'selected' : ''}>Date Format</option>
                        <option value="boolean" ${col.type === 'checkbox' ? 'selected' : ''}>Boolean Check</option>
                    </select>
                </td>
                <td><input type="checkbox" class="form-check-input col-sortable-grid" data-idx="${idx}" checked></td>
                <td><input type="checkbox" class="form-check-input col-filter-grid" data-idx="${idx}" checked></td>
                <td><input type="checkbox" class="form-check-input col-wrap-grid" data-idx="${idx}"></td>
                <td><input type="checkbox" class="form-check-input col-autoheight-grid" data-idx="${idx}"></td>
            `;
            tbody.appendChild(tr);
        });
    }

    function renderFormConfigTable() {
        const tbody = document.getElementById('form-fields-config-body');
        tbody.innerHTML = '';

        analyzedColumns.forEach((col, idx) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="checkbox" class="form-check-input field-include-form" data-idx="${idx}" ${col.field === 'id' ? '' : 'checked'}></td>
                <td><code>${col.field}</code></td>
                <td><input type="text" class="form-control form-control-sm field-label-form" data-idx="${idx}" value="${col.headerName}"></td>
                <td>
                    <select class="form-select form-select-sm field-type-form" data-idx="${idx}">
                        <option value="text" ${col.type === 'text' ? 'selected' : ''}>Text Input</option>
                        <option value="number" ${col.type === 'number' ? 'selected' : ''}>Number Input</option>
                        <option value="decimal" ${col.type === 'decimal' ? 'selected' : ''}>Decimal / Currency</option>
                        <option value="date" ${col.type === 'date' ? 'selected' : ''}>Date Picker</option>
                        <option value="datetime" ${col.type === 'datetime' ? 'selected' : ''}>Date & Time Picker</option>
                        <option value="dropdown" ${col.type === 'dropdown' ? 'selected' : ''}>Select Dropdown</option>
                        <option value="searchable_dropdown">Searchable Dropdown</option>
                        <option value="multiselect">Multiselect Tags</option>
                        <option value="checkbox" ${col.type === 'checkbox' ? 'selected' : ''}>Checkbox Switch</option>
                        <option value="textarea" ${col.type === 'textarea' ? 'selected' : ''}>Textarea</option>
                        <option value="file">File Input</option>
                    </select>
                </td>
                <td><input type="number" class="form-control form-control-sm field-width-form" data-idx="${idx}" value="${col.grid_width || 6}" min="1" max="12"></td>
                <td><input type="checkbox" class="form-check-input field-required-form" data-idx="${idx}" ${col.required ? 'checked' : ''}></td>
                <td><input type="text" class="form-control form-control-sm field-options-form" data-idx="${idx}" placeholder="Active,Inactive or val:label"></td>
            `;
            tbody.appendChild(tr);
        });
    }

    window.setGenMode = function(mode) {
        document.querySelectorAll('.gen-mode-card').forEach(c => c.classList.remove('active', 'border-primary'));
        document.getElementById(`mode-card-${mode}`).classList.add('active', 'border-primary');
        document.querySelector(`input[name="generation_mode"][value="${mode}"]`).checked = true;
    };

    window.submitDevGenerator = function(e) {
        e.preventDefault();

        // Extract Grid Schema
        const gridSchema = [];
        document.querySelectorAll('#grid-columns-config-body tr').forEach((tr, idx) => {
            const isShow = tr.querySelector('.col-show-grid').checked;
            if (isShow) {
                const orig = analyzedColumns[idx];
                gridSchema.push({
                    field: orig.field,
                    headerName: tr.querySelector('.col-header-grid').value,
                    formatter: tr.querySelector('.col-formatter-grid').value || null,
                    sortable: tr.querySelector('.col-sortable-grid').checked,
                    filter: tr.querySelector('.col-filter-grid').checked,
                    wrapText: tr.querySelector('.col-wrap-grid').checked,
                    autoHeight: tr.querySelector('.col-autoheight-grid').checked
                });
            }
        });

        // Extract Form Schema
        const formSchema = [];
        document.querySelectorAll('#form-fields-config-body tr').forEach((tr, idx) => {
            const isInclude = tr.querySelector('.field-include-form').checked;
            if (isInclude) {
                const orig = analyzedColumns[idx];
                const rawOptions = tr.querySelector('.field-options-form').value.trim();
                let optionsArr = [];

                if (rawOptions) {
                    optionsArr = rawOptions.split(',').map(item => {
                        item = item.trim();
                        if (item.includes(':')) {
                            const [v, l] = item.split(':');
                            return { value: v.trim(), label: l.trim() };
                        }
                        return { value: item, label: item };
                    });
                }

                formSchema.push({
                    name: orig.field,
                    label: tr.querySelector('.field-label-form').value,
                    type: tr.querySelector('.field-type-form').value,
                    grid_width: parseInt(tr.querySelector('.field-width-form').value) || 6,
                    required: tr.querySelector('.field-required-form').checked,
                    options: optionsArr
                });
            }
        });

        const payload = {
            module_name: document.getElementById('module_name').value,
            module_slug: document.getElementById('module_slug').value,
            page_name: document.getElementById('page_name').value,
            page_slug: document.getElementById('page_slug').value,
            page_icon: document.getElementById('page_icon').value,
            sql_query: document.getElementById('sql_query').value,
            db_table: document.getElementById('db_table').value,
            primary_key: document.getElementById('primary_key').value,
            generation_mode: document.querySelector('input[name="generation_mode"]:checked').value,
            grid_schema: gridSchema,
            form_schema: formSchema
        };

        const btn = document.getElementById('btn-generate-submit');
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status"></span> Publishing Page...`;

        fetch('/api/developer/generate-page', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i> Generate & Publish Page`;

            if (data.error) throw new Error(data.error);

            showToast('success', data.message || 'Page published successfully!');
            // Refresh navigation panel and load generated page
            if (typeof fetchDynamicNavigation === 'function') {
                fetchDynamicNavigation();
            }
            setTimeout(() => {
                loadPage(data.url);
            }, 500);
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i> Generate & Publish Page`;
            showToast('danger', err.message || 'Error generating page.');
        });
    };
})();
</script>
