<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Custom Todos</h4>
            <p class="text-muted small mb-0">Developer Generated Module Page</p>
        </div>
        <div class="d-flex gap-2">
            <input type="text" id="custom-grid-search-custom-todos-test" class="form-control form-control-sm" style="width: 220px;" placeholder="Search records...">
            <button type="button" class="btn btn-sm btn-primary" onclick="openCustomCreateModal_custom_todos_test()"><i class="bi bi-plus-lg"></i> Add Record</button>
        </div>
    </div>

    <!-- AG Grid Container -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div id="grid-custom-todos-test" style="height: 520px; width: 100%;"></div>
        </div>
    </div>

    <!-- CRUD Form Modal -->
    <div class="modal fade" id="modal-custom-todos-test" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modal-title-custom-todos-test">Add Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-custom-todos-test" onsubmit="saveCustomRecord_custom_todos_test(event)">
                    <div class="modal-body p-4" id="form-body-custom-todos-test"></div>
                    <div class="modal-footer bg-light px-4">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const pageSlug = "custom-todos-test";
    const moduleSlug = "task-management";
    const primaryKey = "id";
    const gridColumns = [
    {
        "field": "id",
        "headerName": "ID"
    },
    {
        "field": "name",
        "headerName": "Name"
    }
];
    const formFields = [
    {
        "name": "name",
        "label": "Name",
        "type": "text",
        "required": true
    }
];
    
    let gridInstance = null;
    const modalEl = document.getElementById(`modal-${pageSlug}`);
    const bsModal = new bootstrap.Modal(modalEl);
    let activeRecordId = null;

    // Render form fields
    const formBody = document.getElementById(`form-body-${pageSlug}`);
    formBody.innerHTML = ErpForms.renderFieldsHtml(formFields);
    ErpForms.bindInteractiveFields(formBody);

    // Initialize ErpGrid
    gridInstance = ErpGrid.createGrid({
        id: `grid-${pageSlug}`,
        primaryKey: primaryKey,
        dataUrl: `/api/custom/${moduleSlug}/${pageSlug}/data`,
        columns: gridColumns,
        wrapText: true,
        autoRowHeight: true,
        onEdit: function(rowData) {
            openCustomEditModal_custom_todos_test(rowData);
        },
        onDelete: function(id) {
            deleteCustomRecord_custom_todos_test(id);
        }
    });

    // Quick Search Listener
    document.getElementById(`custom-grid-search-${pageSlug}`).addEventListener('input', function(e) {
        if (gridInstance) gridInstance.quickSearch(e.target.value);
    });

    window.openCustomCreateModal_custom_todos_test = function() {
        activeRecordId = null;
        document.getElementById(`modal-title-${pageSlug}`).textContent = 'Add Record';
        document.getElementById(`form-${pageSlug}`).reset();
        bsModal.show();
    };

    window.openCustomEditModal_custom_todos_test = function(rowData) {
        activeRecordId = rowData[primaryKey];
        document.getElementById(`modal-title-${pageSlug}`).textContent = 'Edit Record';
        formBody.innerHTML = ErpForms.renderFieldsHtml(formFields, rowData);
        ErpForms.bindInteractiveFields(formBody);
        bsModal.show();
    };

    window.saveCustomRecord_custom_todos_test = function(e) {
        e.preventDefault();
        const form = document.getElementById(`form-${pageSlug}`);
        if (!ErpForms.validateForm(form)) return;

        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());

        const url = activeRecordId 
            ? `/api/custom/${moduleSlug}/${pageSlug}/update/${activeRecordId}`
            : `/api/custom/${moduleSlug}/${pageSlug}/store`;

        fetch(url, {
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
            if (data.error) throw new Error(data.error);
            bsModal.hide();
            gridInstance.refresh();
            showToast('success', 'Record saved successfully.');
        })
        .catch(err => {
            showToast('danger', err.message || 'Error saving record.');
        });
    };

    window.deleteCustomRecord_custom_todos_test = function(id) {
        if (!confirm('Are you sure you want to delete this record?')) return;
        fetch(`/api/custom/${moduleSlug}/${pageSlug}/destroy/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            gridInstance.refresh();
            showToast('success', 'Record deleted.');
        })
        .catch(err => showToast('danger', 'Failed to delete record.'));
    };
})();
</script>