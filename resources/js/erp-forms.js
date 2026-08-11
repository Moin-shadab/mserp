/**
 * Universal Reusable Form Library Component
 * ERP System Core Library
 */

window.ErpForms = {
    /**
     * Render HTML for a list of form fields based on field definitions schema.
     * @param {Array} fields List of field definitions
     * @param {Object} values Optional initial/existing values
     * @returns {string} Form fields HTML string
     */
    renderFieldsHtml: function (fields, values = {}) {
        let html = '<div class="row g-3">';

        fields.forEach(field => {
            const fieldId = field.id || `field_${field.name}`;
            const label = field.label || field.name;
            const name = field.name;
            const type = field.type || 'text';
            const value = values[name] !== undefined && values[name] !== null ? values[name] : (field.default || '');
            const required = field.required || (field.validation && field.validation.includes('required'));
            const reqMark = required ? '<span class="text-danger">*</span>' : '';
            const width = field.grid_width || field.width || 12; // Bootstrap col width (1-12)
            const helpText = field.helpText ? `<div class="form-text">${field.helpText}</div>` : '';

            html += `<div class="col-md-${width}">`;
            html += `<label for="${fieldId}" class="form-label font-weight-bold" style="font-size:0.85rem; font-weight:600;">${label} ${reqMark}</label>`;

            switch (type) {
                case 'textarea':
                    html += `<textarea class="form-control" id="${fieldId}" name="${name}" rows="${field.rows || 3}" placeholder="${field.placeholder || ''}" ${required ? 'required' : ''}>${value}</textarea>`;
                    break;

                case 'select':
                case 'dropdown':
                    html += `<select class="form-select" id="${fieldId}" name="${name}" ${required ? 'required' : ''}>`;
                    html += `<option value="">${field.placeholder || '-- Select ' + label + ' --'}</option>`;
                    (field.options || []).forEach(opt => {
                        const optVal = typeof opt === 'object' ? opt.value : opt;
                        const optLbl = typeof opt === 'object' ? opt.label : opt;
                        const selected = String(optVal) === String(value) ? 'selected' : '';
                        html += `<option value="${optVal}" ${selected}>${optLbl}</option>`;
                    });
                    html += `</select>`;
                    break;

                case 'searchable_select':
                case 'searchable_dropdown':
                    html += `
                    <div class="erp-searchable-select-wrapper position-relative" data-field-name="${name}">
                        <input type="text" class="form-control erp-searchable-input" id="${fieldId}_search" placeholder="Search ${label}..." value="" autocomplete="off">
                        <input type="hidden" id="${fieldId}" name="${name}" value="${value}" ${required ? 'required' : ''}>
                        <div class="dropdown-menu w-100 p-0 shadow-sm erp-searchable-dropdown-list" style="max-height: 200px; overflow-y: auto;">
                    `;
                    (field.options || []).forEach(opt => {
                        const optVal = typeof opt === 'object' ? opt.value : opt;
                        const optLbl = typeof opt === 'object' ? opt.label : opt;
                        html += `<a class="dropdown-item py-2 px-3 erp-searchable-item" href="#" data-value="${optVal}">${optLbl}</a>`;
                    });
                    html += `
                        </div>
                    </div>`;
                    break;

                case 'multiselect':
                    const selectedVals = Array.isArray(value) ? value.map(String) : (typeof value === 'string' && value ? value.split(',') : []);
                    html += `
                    <div class="erp-multiselect-wrapper border rounded p-2 bg-white" data-field-name="${name}">
                        <input type="hidden" id="${fieldId}" name="${name}" value="${selectedVals.join(',')}" ${required ? 'required' : ''}>
                        <div class="d-flex flex-wrap gap-1 mb-2 erp-multiselect-tags"></div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown">
                                Select ${label}...
                            </button>
                            <ul class="dropdown-menu w-100 p-2 shadow-sm" style="max-height:200px; overflow-y:auto;">
                    `;
                    (field.options || []).forEach(opt => {
                        const optVal = typeof opt === 'object' ? opt.value : opt;
                        const optLbl = typeof opt === 'object' ? opt.label : opt;
                        const isChecked = selectedVals.includes(String(optVal)) ? 'checked' : '';
                        html += `
                        <li>
                            <div class="form-check">
                                <input class="form-check-input erp-multiselect-check" type="checkbox" value="${optVal}" data-label="${optLbl}" id="${fieldId}_opt_${optVal}" ${isChecked}>
                                <label class="form-check-label w-100" for="${fieldId}_opt_${optVal}">${optLbl}</label>
                            </div>
                        </li>`;
                    });
                    html += `
                            </ul>
                        </div>
                    </div>`;
                    break;

                case 'checkbox':
                    const isChecked = value == 1 || value === true || value === '1' ? 'checked' : '';
                    html += `
                    <div class="form-check form-switch pt-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="${fieldId}" name="${name}" value="1" ${isChecked}>
                        <label class="form-check-label" for="${fieldId}">${field.checkboxLabel || 'Enable'}</label>
                    </div>`;
                    break;

                case 'radio':
                    html += `<div class="pt-1">`;
                    (field.options || []).forEach((opt, idx) => {
                        const optVal = typeof opt === 'object' ? opt.value : opt;
                        const optLbl = typeof opt === 'object' ? opt.label : opt;
                        const radChecked = String(optVal) === String(value) ? 'checked' : '';
                        html += `
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="${name}" id="${fieldId}_rad_${idx}" value="${optVal}" ${radChecked}>
                            <label class="form-check-label" for="${fieldId}_rad_${idx}">${optLbl}</label>
                        </div>`;
                    });
                    html += `</div>`;
                    break;

                case 'file':
                    html += `<input class="form-control" type="file" id="${fieldId}" name="${name}" ${field.accept ? 'accept="' + field.accept + '"' : ''} ${required ? 'required' : ''}>`;
                    break;

                case 'number':
                case 'decimal':
                    html += `<input type="number" step="${field.step || (type === 'decimal' ? '0.01' : '1')}" class="form-control" id="${fieldId}" name="${name}" value="${value}" placeholder="${field.placeholder || ''}" ${required ? 'required' : ''}>`;
                    break;

                case 'date':
                    html += `<input type="date" class="form-control" id="${fieldId}" name="${name}" value="${value}" ${required ? 'required' : ''}>`;
                    break;

                case 'datetime':
                case 'datetime-local':
                    const dtVal = value ? String(value).replace(' ', 'T').substring(0, 16) : '';
                    html += `<input type="datetime-local" class="form-control" id="${fieldId}" name="${name}" value="${dtVal}" ${required ? 'required' : ''}>`;
                    break;

                case 'password':
                    html += `<input type="password" class="form-control" id="${fieldId}" name="${name}" placeholder="${field.placeholder || '••••••••'}" ${required ? 'required' : ''}>`;
                    break;

                default: // text, email, url, phone, etc.
                    html += `<input type="${type}" class="form-control" id="${fieldId}" name="${name}" value="${value}" placeholder="${field.placeholder || ''}" ${required ? 'required' : ''}>`;
                    break;
            }

            html += helpText;
            html += `</div>`;
        });

        html += '</div>';
        return html;
    },

    /**
     * Attach interactive JS behavior to searchable dropdowns and multiselect controls.
     * @param {Element} container Element containing form fields
     */
    bindInteractiveFields: function (container = document) {
        // Searchable Dropdowns
        container.querySelectorAll('.erp-searchable-select-wrapper').forEach(wrapper => {
            const searchInput = wrapper.querySelector('.erp-searchable-input');
            const hiddenInput = wrapper.querySelector('input[type="hidden"]');
            const dropdownList = wrapper.querySelector('.erp-searchable-dropdown-list');
            const items = wrapper.querySelectorAll('.erp-searchable-item');

            // Set initial search input text if hidden input has value
            const currentVal = hiddenInput.value;
            if (currentVal) {
                const activeItem = wrapper.querySelector(`.erp-searchable-item[data-value="${currentVal}"]`);
                if (activeItem) searchInput.value = activeItem.textContent.trim();
            }

            searchInput.addEventListener('focus', () => dropdownList.classList.add('show'));
            searchInput.addEventListener('input', (e) => {
                dropdownList.classList.add('show');
                const term = e.target.value.toLowerCase();
                items.forEach(item => {
                    const txt = item.textContent.toLowerCase();
                    item.style.display = txt.includes(term) ? 'block' : 'none';
                });
            });

            items.forEach(item => {
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    const val = item.getAttribute('data-value');
                    const txt = item.textContent.trim();
                    hiddenInput.value = val;
                    searchInput.value = txt;
                    dropdownList.classList.remove('show');
                });
            });

            document.addEventListener('click', (e) => {
                if (!wrapper.contains(e.target)) dropdownList.classList.remove('show');
            });
        });

        // Multiselect Controls
        container.querySelectorAll('.erp-multiselect-wrapper').forEach(wrapper => {
            const hiddenInput = wrapper.querySelector('input[type="hidden"]');
            const tagsContainer = wrapper.querySelector('.erp-multiselect-tags');
            const checkboxes = wrapper.querySelectorAll('.erp-multiselect-check');

            function updateTags() {
                const selectedVals = [];
                tagsContainer.innerHTML = '';
                checkboxes.forEach(cb => {
                    if (cb.checked) {
                        selectedVals.push(cb.value);
                        const tag = document.createElement('span');
                        tag.className = 'badge bg-primary d-inline-flex align-items-center gap-1';
                        tag.innerHTML = `${cb.getAttribute('data-label')} <i class="bi bi-x ms-1 cursor-pointer" style="font-size:0.9rem;" data-remove="${cb.value}"></i>`;
                        tagsContainer.appendChild(tag);
                    }
                });
                hiddenInput.value = selectedVals.join(',');
            }

            checkboxes.forEach(cb => cb.addEventListener('change', updateTags));
            tagsContainer.addEventListener('click', (e) => {
                if (e.target.hasAttribute('data-remove')) {
                    const removeVal = e.target.getAttribute('data-remove');
                    const targetCb = wrapper.querySelector(`.erp-multiselect-check[value="${removeVal}"]`);
                    if (targetCb) {
                        targetCb.checked = false;
                        updateTags();
                    }
                }
            });

            updateTags();
        });
    },

    /**
     * Validate a form container against field rules.
     * @param {Element|string} formTarget Form element or selector
     * @returns {boolean} True if valid
     */
    validateForm: function (formTarget) {
        const form = typeof formTarget === 'string' ? document.querySelector(formTarget) : formTarget;
        if (!form) return true;

        let isValid = true;
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        inputs.forEach(input => {
            if (!input.value || !input.value.trim()) {
                input.classList.add('is-invalid');
                isValid = false;
            }
        });

        return isValid;
    }
};
