/**
 * Universal Reusable AG Grid Helper Component
 * ERP System Core Library
 */

window.ErpGrid = {
    grids: {},

    /**
     * Create and initialize an AG Grid instance.
     * @param {Object} options Configuration options for the grid
     * @returns {Object} Grid instance wrapper
     */
    createGrid: function (options) {
        const gridId = options.id || 'erp-grid-' + Math.random().toString(36).substring(2, 9);
        const containerSelector = options.container || '#' + gridId;
        const container = typeof containerSelector === 'string' 
            ? document.querySelector(containerSelector) 
            : containerSelector;

        if (!container) {
            console.error(`ErpGrid: Container '${containerSelector}' not found.`);
            return null;
        }

        // Clean container if re-initializing
        container.innerHTML = '';
        container.classList.add('ag-theme-alpine');
        if (!container.style.height) {
            container.style.height = options.height || '500px';
        }
        container.style.width = options.width || '100%';

        const primaryKey = options.primaryKey || 'id';
        const dataUrl = options.dataUrl;

        // Build Column Definitions
        const columnDefs = [];

        // Checkbox selection column
        if (options.checkboxSelection) {
            columnDefs.push({
                headerCheckboxSelection: true,
                checkboxSelection: true,
                width: 50,
                pinned: 'left',
                resizable: false,
                sortable: false,
                filter: false
            });
        }

        // Process columns schema
        (options.columns || []).forEach(col => {
            const isWrapped = col.wrapText || options.wrapText || false;
            const autoHeight = col.autoHeight || options.autoRowHeight || false;

            const colDef = {
                field: col.field,
                headerName: col.headerName || col.label || col.field,
                width: col.width || undefined,
                flex: col.flex || (col.width ? undefined : 1),
                sortable: col.sortable !== false,
                filter: col.filter === false ? false : (col.filterType || 'agTextColumnFilter'),
                resizable: col.resizable !== false,
                hide: col.hide || false,
                wrapText: isWrapped,
                autoHeight: autoHeight,
                cellClass: col.cellClass || '',
            };

            // Custom formatting logic
            if (col.formatter) {
                if (typeof col.formatter === 'function') {
                    colDef.cellRenderer = col.formatter;
                } else if (col.formatter === 'badge') {
                    colDef.cellRenderer = function (params) {
                        if (params.value === null || params.value === undefined) return '';
                        const colorMap = col.badgeColors || {
                            'active': 'success', 'completed': 'success', 'paid': 'success', 'approved': 'success',
                            'pending': 'warning', 'in_progress': 'info', 'draft': 'secondary',
                            'inactive': 'danger', 'cancelled': 'danger', 'rejected': 'danger'
                        };
                        const valStr = String(params.value).toLowerCase();
                        const bg = colorMap[valStr] || 'primary';
                        return `<span class="badge bg-${bg}">${params.value}</span>`;
                    };
                } else if (col.formatter === 'currency') {
                    colDef.cellRenderer = function (params) {
                        if (params.value === null || params.value === undefined) return '';
                        const symbol = col.currencySymbol || '$';
                        return `${symbol}${parseFloat(params.value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                    };
                } else if (col.formatter === 'date') {
                    colDef.cellRenderer = function (params) {
                        if (!params.value) return '';
                        try {
                            const d = new Date(params.value);
                            return d.toLocaleDateString();
                        } catch (e) {
                            return params.value;
                        }
                    };
                } else if (col.formatter === 'boolean') {
                    colDef.cellRenderer = function (params) {
                        const isTrue = params.value == 1 || params.value === true || params.value === '1';
                        return isTrue 
                            ? `<span class="badge bg-success"><i class="bi bi-check-lg"></i> Yes</span>`
                            : `<span class="badge bg-secondary"><i class="bi bi-x-lg"></i> No</span>`;
                    };
                }
            }

            columnDefs.push(colDef);
        });

        // Add Action Column if enabled or buttons exist
        const showActions = options.showActions !== false && (
            options.onEdit || options.onDelete || (options.customActions && options.customActions.length > 0)
        );

        if (showActions) {
            columnDefs.push({
                headerName: 'Actions',
                field: '_actions',
                sortable: false,
                filter: false,
                resizable: false,
                width: options.actionColumnWidth || 140,
                pinned: options.actionPinned || 'right',
                cellRenderer: function (params) {
                    if (!params.data) return '';
                    const recordId = params.data[primaryKey];
                    let html = `<div class="d-flex gap-1 align-items-center h-100">`;

                    if (options.onEdit) {
                        html += `<button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 btn-erp-edit" style="font-size:0.75rem;" title="Edit" data-id="${recordId}"><i class="bi bi-pencil"></i></button>`;
                    }
                    if (options.onDelete) {
                        html += `<button type="button" class="btn btn-xs btn-outline-danger py-0 px-2 btn-erp-delete" style="font-size:0.75rem;" title="Delete" data-id="${recordId}"><i class="bi bi-trash"></i></button>`;
                    }

                    if (options.customActions && Array.isArray(options.customActions)) {
                        options.customActions.forEach((act, idx) => {
                            const btnClass = act.class || 'btn-outline-secondary';
                            const icon = act.icon ? `<i class="${act.icon}"></i>` : '';
                            html += `<button type="button" class="btn btn-xs ${btnClass} py-0 px-2 btn-erp-custom-${idx}" style="font-size:0.75rem;" title="${act.label || ''}" data-id="${recordId}">${icon} ${act.label || ''}</button>`;
                        });
                    }

                    html += `</div>`;
                    return html;
                }
            });
        }

        // AG Grid Configuration Object
        const gridOptions = {
            columnDefs: columnDefs,
            pagination: options.pagination !== false,
            paginationPageSize: options.pageSize || 25,
            rowModelType: options.rowModelType || 'clientSide',
            rowHeight: options.autoRowHeight ? undefined : (options.rowHeight || 40),
            animateRows: true,
            suppressCellFocus: true,
            defaultColDef: {
                resizable: true,
                sortable: true,
                filter: true,
                suppressMovable: false,
                ...options.defaultColDef
            },
            onGridReady: function (params) {
                if (options.onGridReady) {
                    options.onGridReady(params);
                }
                if (dataUrl) {
                    ErpGrid.refreshGrid(gridId);
                } else if (options.rowData) {
                    params.api.setRowData(options.rowData);
                }
            },
            onCellClicked: function (event) {
                if (event.colDef.field === '_actions' && event.event.target) {
                    const btnEdit = event.event.target.closest('.btn-erp-edit');
                    const btnDelete = event.event.target.closest('.btn-erp-delete');
                    
                    if (btnEdit && options.onEdit) {
                        options.onEdit(event.data);
                    } else if (btnDelete && options.onDelete) {
                        options.onDelete(event.data[primaryKey], event.data);
                    }

                    if (options.customActions && Array.isArray(options.customActions)) {
                        options.customActions.forEach((act, idx) => {
                            const btnCustom = event.event.target.closest(`.btn-erp-custom-${idx}`);
                            if (btnCustom && act.onClick) {
                                act.onClick(event.data);
                            }
                        });
                    }
                }
            }
        };

        // Create instance
        new window.agGrid.Grid(container, gridOptions);

        const instance = {
            id: gridId,
            options: options,
            gridOptions: gridOptions,
            container: container,
            refresh: function () {
                ErpGrid.refreshGrid(gridId);
            },
            setRowData: function (data) {
                if (gridOptions.api) {
                    gridOptions.api.setRowData(data);
                }
            },
            quickSearch: function (searchTerm) {
                if (gridOptions.api) {
                    gridOptions.api.setQuickFilter(searchTerm);
                }
            },
            getSelectedRows: function () {
                return gridOptions.api ? gridOptions.api.getSelectedRows() : [];
            }
        };

        ErpGrid.grids[gridId] = instance;
        return instance;
    },

    /**
     * Refresh data in a grid instance by ID or URL.
     */
    refreshGrid: function (gridId) {
        const instance = ErpGrid.grids[gridId];
        if (!instance) return;

        const { gridOptions, options } = instance;
        if (!gridOptions.api) return;

        gridOptions.api.showLoadingOverlay();

        const url = options.dataUrl;
        if (!url) return;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(resData => {
            const rows = Array.isArray(resData) ? resData : (resData.data || []);
            gridOptions.api.setRowData(rows);
            gridOptions.api.hideOverlay();
            if (rows.length === 0) {
                gridOptions.api.showNoRowsOverlay();
            }
        })
        .catch(err => {
            console.error('ErpGrid data load error:', err);
            gridOptions.api.setRowData([]);
            gridOptions.api.hideOverlay();
        });
    }
};
