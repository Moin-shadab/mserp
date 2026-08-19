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