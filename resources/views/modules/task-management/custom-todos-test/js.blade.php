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
if (formBody) {
    formBody.innerHTML = ErpForms.renderFieldsHtml(formFields);
    ErpForms.bindInteractiveFields(formBody);
}

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
const searchInput = document.getElementById(`custom-grid-search-${pageSlug}`);
if (searchInput) {
    searchInput.addEventListener('input', function(e) {
        if (gridInstance) gridInstance.quickSearch(e.target.value);
    });
}

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