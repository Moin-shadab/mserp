(function() {
    const docType = "{{ $pageConfig->slug }}"; // sales-invoices, sales-orders, purchase-orders, purchase-invoices, sales-quotations
    let contactsCache = [];
    let itemsCache = [];
    const sellerState = "maharashtra"; // Company state
    let isIntraState = true; 
    let rowCounter = 0;

    // Configuration mapper for dynamic UI labels
    const uiConfig = {
        'sales-invoices': {
            title: 'GST Sales Invoices',
            desc: 'Manage customer invoices, auto-taxation, and real-time stock levels.',
            btnCreate: 'Create Sales Invoice',
            registry: 'Invoice Registry',
            headerNo: 'Invoice No',
            headerContact: 'Customer Name',
            headerDate: 'Billing Date',
            creatorTitle: 'Create GST Sales Invoice',
            creatorDesc: 'Issue a tax invoice to a customer (reduces inventory stock).',
            sectionTitle: 'Billing to (Customer)',
            labelContact: 'Select Customer <span class="text-danger">*</span>',
            labelDate: 'Billing Date <span class="text-danger">*</span>',
            labelDue: 'Due Date <span class="text-danger">*</span>',
            labelAddress: 'Billing Address <span class="text-danger">*</span>',
            senderTitle: 'Seller Details (From)',
            recipientSummary: 'Customer GST Context',
            btnSubmit: 'Save & Issue Invoice'
        },
        'sales-orders': {
            title: 'Sales Orders Manager',
            desc: 'Track sales confirmations and order reservations from customers.',
            btnCreate: 'Create Sales Order',
            registry: 'Sales Orders Registry',
            headerNo: 'Order No',
            headerContact: 'Customer Name',
            headerDate: 'Order Date',
            creatorTitle: 'New Sales Order Confirmation',
            creatorDesc: 'Register a sales order confirmation from a customer.',
            sectionTitle: 'Customer Details',
            labelContact: 'Select Customer <span class="text-danger">*</span>',
            labelDate: 'Order Date <span class="text-danger">*</span>',
            labelDue: 'Delivery Date <span class="text-danger">*</span>',
            labelAddress: 'Delivery Address <span class="text-danger">*</span>',
            senderTitle: 'Seller Details (From)',
            recipientSummary: 'Customer GST Context',
            btnSubmit: 'Save & Issue Sales Order'
        },
        'purchase-orders': {
            title: 'Purchase Orders (PO)',
            desc: 'Issue official purchase orders to vendors to procure raw goods/services.',
            btnCreate: 'Create Purchase Order',
            registry: 'Purchase Orders Registry',
            headerNo: 'PO No',
            headerContact: 'Vendor Name',
            headerDate: 'PO Date',
            creatorTitle: 'Issue Purchase Order to Vendor',
            creatorDesc: 'Generate a procurement PO to send to a vendor.',
            sectionTitle: 'Vendor Details',
            labelContact: 'Select Vendor <span class="text-danger">*</span>',
            labelDate: 'PO Date <span class="text-danger">*</span>',
            labelDue: 'Expected Delivery Date <span class="text-danger">*</span>',
            labelAddress: 'Vendor Address <span class="text-danger">*</span>',
            senderTitle: 'Buyer Details (From)',
            recipientSummary: 'Vendor GST Context',
            btnSubmit: 'Save & Send Purchase Order'
        },
        'purchase-invoices': {
            title: 'Vendor Bills & Purchase Invoices',
            desc: 'Log received invoices/bills from vendors to increment inventory stock.',
            btnCreate: 'Record Vendor Bill',
            registry: 'Vendor Bills Registry',
            headerNo: 'Bill No',
            headerContact: 'Vendor Name',
            headerDate: 'Bill Date',
            creatorTitle: 'Record Vendor Purchase Bill',
            creatorDesc: 'Record vendor bills (adds products directly to inventory stock).',
            sectionTitle: 'Billing from (Vendor)',
            labelContact: 'Select Vendor <span class="text-danger">*</span>',
            labelDate: 'Bill Date <span class="text-danger">*</span>',
            labelDue: 'Due Date <span class="text-danger">*</span>',
            labelAddress: 'Billing Address <span class="text-danger">*</span>',
            senderTitle: 'Buyer Details (From)',
            recipientSummary: 'Vendor GST Context',
            btnSubmit: 'Save & Record Vendor Bill'
        },
        'sales-quotations': {
            title: 'Sales Quotations & Estimates',
            desc: 'Manage customer quotes, pricing templates, and estimates.',
            btnCreate: 'Create Sales Quote',
            registry: 'Quotation Registry',
            headerNo: 'Quote No',
            headerContact: 'Customer Name',
            headerDate: 'Quote Date',
            creatorTitle: 'New Sales Quotation / Estimate',
            creatorDesc: 'Create a dynamic GST-compliant price estimate for a customer.',
            sectionTitle: 'Customer Details',
            labelContact: 'Select Customer <span class="text-danger">*</span>',
            labelDate: 'Quote Date <span class="text-danger">*</span>',
            labelDue: 'Valid Until <span class="text-danger">*</span>',
            labelAddress: 'Billing Address <span class="text-danger">*</span>',
            senderTitle: 'Seller Details (From)',
            recipientSummary: 'Customer GST Context',
            btnSubmit: 'Save & Send Quote'
        }
    };

    const c = uiConfig[docType] || uiConfig['sales-invoices'];

    // Apply labels to UI
    document.getElementById('doc-title-main').textContent = c.title;
    document.getElementById('doc-desc-main').textContent = c.desc;
    document.getElementById('btn-create-text').textContent = c.btnCreate;
    document.getElementById('doc-registry-title').textContent = c.registry;
    document.getElementById('grid-header-no').textContent = c.headerNo;
    document.getElementById('grid-header-contact').textContent = c.headerContact;
    document.getElementById('grid-header-date').textContent = c.headerDate;
    document.getElementById('doc-creator-title').textContent = c.creatorTitle;
    document.getElementById('doc-creator-desc').textContent = c.creatorDesc;
    document.getElementById('doc-section-title').textContent = c.sectionTitle;
    document.getElementById('form-label-contact').innerHTML = c.labelContact;
    document.getElementById('form-label-date').innerHTML = c.labelDate;
    document.getElementById('form-label-due').innerHTML = c.labelDue;
    document.getElementById('form-label-address').innerHTML = c.labelAddress;
    document.getElementById('sender-header-title').textContent = c.senderTitle;
    document.getElementById('recipient-summary-title').textContent = c.recipientSummary;
    document.getElementById('btn-submit-form').innerHTML = `<i class="bi bi-check-circle"></i> ${c.btnSubmit}`;

    // Load dynamic documents grid list
    loadDocuments();

    function loadDocuments() {
        const tbody = document.getElementById('invoices-list-tbody');
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Loading documents...
                </td>
            </tr>
        `;

        fetch(`/api/billing/${docType}/data`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            const docs = data.data || data;
            document.getElementById('invoices-count').textContent = `${docs.length} Documents`;
            
            if (docs.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-receipt fs-2 d-block mb-2 text-secondary"></i>
                            No records found. Click "+ ${c.btnCreate}" to create a new one.
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = '';
            docs.forEach(doc => {
                const taxType = (doc.igst && parseFloat(doc.igst) > 0) ? 'IGST (Inter-state)' : 'CGST + SGST (Intra-state)';
                let statusBadge = '';
                
                if (doc.status === 'Paid' || doc.status === 'Closed' || doc.status === 'Accepted') {
                    statusBadge = `<span class="invoice-badge-status bg-badge-paid">${doc.status}</span>`;
                } else if (doc.status === 'Approved' || doc.status === 'Sent') {
                    statusBadge = `<span class="invoice-badge-status bg-badge-approved">${doc.status === 'Approved' ? 'Issued' : doc.status}</span>`;
                } else {
                    statusBadge = `<span class="invoice-badge-status bg-badge-draft">${doc.status}</span>`;
                }

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="ps-4 fw-bold text-dark">${doc.document_no}</td>
                    <td><div class="fw-semibold">${doc.contact_name || 'Generic'}</div></td>
                    <td>${doc.document_date}</td>
                    <td class="fw-medium">₹${parseFloat(doc.amount || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                    <td class="text-muted">₹${parseFloat(doc.tax || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                    <td class="fw-bold text-dark">₹${parseFloat(doc.total_amount || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                    <td class="small text-secondary">${taxType}</td>
                    <td>${statusBadge}</td>
                    <td class="text-end pe-4">
                        <div class="btn-group gap-1">
                            <a href="/erp/${docType}/print/${doc.id}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Print/Download PDF">
                                <i class="bi bi-printer"></i>
                            </a>
                            ${(doc.status !== 'Paid' && doc.status !== 'Closed' && doc.status !== 'Accepted') ? `
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="markPaid(${doc.id})" title="Mark Completed/Accepted">
                                    <i class="bi bi-check2-circle"></i>
                                </button>
                            ` : ''}
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="cancelDocument(${doc.id})" title="Cancel Document">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(err => {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5 text-danger">
                        <i class="bi bi-exclamation-triangle fs-2 d-block mb-2"></i>
                        Failed to fetch documents: ${err.message}
                    </td>
                </tr>
            `;
        });
    }

    function showCreateInvoiceForm() {
        document.getElementById('invoice-list-view').style.display = 'none';
        document.getElementById('invoice-create-view').style.display = 'block';

        const today = new Date().toISOString().split('T')[0];
        document.getElementById('invoice-date-input').value = today;
        
        const thirtyDaysLater = new Date();
        thirtyDaysLater.setDate(thirtyDaysLater.getDate() + 30);
        document.getElementById('invoice-due-input').value = thirtyDaysLater.toISOString().split('T')[0];

        document.getElementById('create-invoice-form').reset();
        document.getElementById('invoice-lines-tbody').innerHTML = '';
        document.getElementById('customer-gst-summary').classList.add('d-none');
        calculateInvoiceTotals();

        if (contactsCache.length === 0 || itemsCache.length === 0) {
            Promise.all([
                fetch(`/api/billing/contacts/${docType}`).then(res => res.json()),
                fetch('/api/billing/items').then(res => res.json())
            ])
            .then(([contacts, items]) => {
                contactsCache = contacts;
                itemsCache = items;
                populateDropdowns();
                addInvoiceLineItem();
            })
            .catch(err => alert('Failed to load data caches: ' + err.message));
        } else {
            populateDropdowns();
            addInvoiceLineItem();
        }
    }

    function hideCreateInvoiceForm() {
        document.getElementById('invoice-create-view').style.display = 'none';
        document.getElementById('invoice-list-view').style.display = 'block';
        loadDocuments();
    }

    function populateDropdowns() {
        const contactSelect = document.getElementById('invoice-customer-select');
        contactSelect.innerHTML = `<option value="">-- Choose ${docType.includes('purchase') ? 'Vendor' : 'Customer'} --</option>`;
        contactsCache.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = `${c.name} (${c.city || 'India'})`;
            contactSelect.appendChild(opt);
        });
    }

    function onCustomerSelect() {
        const contactId = document.getElementById('invoice-customer-select').value;
        const card = document.getElementById('customer-gst-summary');
        
        if (!contactId) {
            card.classList.add('d-none');
            return;
        }

        const contact = contactsCache.find(c => c.id == contactId);
        if (!contact) return;

        document.getElementById('invoice-address-input').value = contact.address || '';

        const contactState = (contact.state || 'maharashtra').trim().toLowerCase();
        isIntraState = (contactState === sellerState);

        document.getElementById('cust-gstin-display').textContent = contact.gstin || 'Unregistered / None';
        document.getElementById('cust-state-display').textContent = `${contact.state || 'N/A'} (GST Code: ${contact.gstin ? contact.gstin.substring(0,2) : 'None'})`;

        const indicator = document.getElementById('tax-type-indicator');
        if (isIntraState) {
            indicator.className = "alert alert-success py-2 px-3 mb-0 small border-0 fw-semibold";
            indicator.innerHTML = '<i class="bi bi-building-check"></i> Intra-State Transaction (CGST + SGST 50/50 split)';
        } else {
            indicator.className = "alert alert-warning py-2 px-3 mb-0 small border-0 fw-semibold";
            indicator.innerHTML = '<i class="bi bi-globe2"></i> Inter-State Transaction (100% IGST applied)';
        }

        card.classList.remove('d-none');
        calculateInvoiceTotals();
    }

    function addInvoiceLineItem() {
        const tbody = document.getElementById('invoice-lines-tbody');
        const idx = rowCounter++;

        const tr = document.createElement('tr');
        tr.id = `line-row-${idx}`;
        
        let itemOptions = '<option value="">-- Select Product --</option>';
        itemsCache.forEach(itm => {
            itemOptions += `<option value="${itm.id}">[${itm.item_code}] ${itm.name} (Stock: ${itm.qty_on_hand})</option>`;
        });

        tr.innerHTML = `
            <td>
                <select class="form-select" id="line-item-${idx}" onchange="onLineItemChange(${idx})" required>
                    ${itemOptions}
                </select>
                <div class="small text-muted mt-1 d-none" id="line-stock-info-${idx}"></div>
            </td>
            <td>
                <input type="text" class="form-control bg-light" id="line-hsn-${idx}" readonly>
            </td>
            <td>
                <input type="number" class="form-control" id="line-qty-${idx}" value="1" min="1" oninput="calculateInvoiceTotals()" required>
            </td>
            <td>
                <input type="number" class="form-control" id="line-price-${idx}" step="0.01" min="0" oninput="calculateInvoiceTotals()" required>
            </td>
            <td>
                <span class="badge bg-secondary" id="line-taxrate-display-${idx}">18%</span>
                <input type="hidden" id="line-taxrate-${idx}" value="18">
            </td>
            <td>
                <div class="fw-bold text-dark text-end pt-2" id="line-total-display-${idx}">₹0.00</div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeInvoiceLine(${idx})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        calculateInvoiceTotals();
    }

    function removeInvoiceLine(idx) {
        const row = document.getElementById(`line-row-${idx}`);
        if (row) {
            row.remove();
            calculateInvoiceTotals();
        }
    }

    function onLineItemChange(idx) {
        const itemId = document.getElementById(`line-item-${idx}`).value;
        const stockInfo = document.getElementById(`line-stock-info-${idx}`);
        
        if (!itemId) {
            document.getElementById(`line-hsn-${idx}`).value = '';
            document.getElementById(`line-price-${idx}`).value = '';
            document.getElementById(`line-taxrate-${idx}`).value = 18;
            document.getElementById(`line-taxrate-display-${idx}`).textContent = '18%';
            stockInfo.classList.add('d-none');
            calculateInvoiceTotals();
            return;
        }

        const item = itemsCache.find(i => i.id == itemId);
        if (!item) return;

        document.getElementById(`line-hsn-${idx}`).value = item.hsn_sac || '8471';
        document.getElementById(`line-price-${idx}`).value = item.unit_price || '0.00';
        document.getElementById(`line-taxrate-${idx}`).value = item.tax_rate || 18.00;
        document.getElementById(`line-taxrate-display-${idx}`).textContent = `${item.tax_rate || 18.00}%`;

        stockInfo.textContent = `In Stock: ${item.qty_on_hand}`;
        stockInfo.className = item.qty_on_hand <= item.reorder_level ? 'small text-warning mt-1 fw-medium' : 'small text-success mt-1';
        stockInfo.classList.remove('d-none');

        calculateInvoiceTotals();
    }

    function calculateInvoiceTotals() {
        let subtotal = 0;
        let totalTax = 0;
        let cgst = 0;
        let sgst = 0;
        let igst = 0;

        const rows = document.querySelectorAll('#invoice-lines-tbody tr');
        rows.forEach(row => {
            const idParts = row.id.split('-');
            const idx = idParts[idParts.length - 1];

            const itemSelect = document.getElementById(`line-item-${idx}`);
            if (!itemSelect || !itemSelect.value) return;

            const qty = parseInt(document.getElementById(`line-qty-${idx}`).value) || 0;
            const rate = parseFloat(document.getElementById(`line-price-${idx}`).value) || 0.00;
            const taxRate = parseFloat(document.getElementById(`line-taxrate-${idx}`).value) || 0.00;

            const lineSubtotal = qty * rate;
            let lineTax = 0;
            let lineCgst = 0;
            let lineSgst = 0;
            let lineIgst = 0;

            if (isIntraState) {
                lineCgst = (lineSubtotal * (taxRate / 2)) / 100;
                lineSgst = (lineSubtotal * (taxRate / 2)) / 100;
                lineTax = lineCgst + lineSgst;
                cgst += lineCgst;
                sgst += lineSgst;
            } else {
                lineIgst = (lineSubtotal * taxRate) / 100;
                lineTax = lineIgst;
                igst += lineIgst;
            }

            const lineTotal = lineSubtotal + lineTax;
            subtotal += lineSubtotal;
            totalTax += lineTax;

            document.getElementById(`line-total-display-${idx}`).textContent = `₹${lineTotal.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
        });

        const grandTotal = subtotal + totalTax;

        document.getElementById('summary-subtotal').textContent = `₹${subtotal.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
        document.getElementById('summary-tax').textContent = `₹${totalTax.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
        document.getElementById('summary-total').textContent = `₹${grandTotal.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;

        const lblCgst = document.getElementById('label-cgst');
        const valCgst = document.getElementById('val-cgst');
        const lblSgst = document.getElementById('label-sgst');
        const valSgst = document.getElementById('val-sgst');
        const lblIgst = document.getElementById('label-igst');
        const valIgst = document.getElementById('val-igst');

        if (isIntraState) {
            lblCgst.classList.remove('d-none');
            valCgst.classList.remove('d-none');
            valCgst.textContent = `₹${cgst.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;

            lblSgst.classList.remove('d-none');
            valSgst.classList.remove('d-none');
            valSgst.textContent = `₹${sgst.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;

            lblIgst.classList.add('d-none');
            valIgst.classList.add('d-none');
        } else {
            lblCgst.classList.add('d-none');
            valCgst.classList.add('d-none');
            
            lblSgst.classList.add('d-none');
            valSgst.classList.add('d-none');

            lblIgst.classList.remove('d-none');
            valIgst.classList.remove('d-none');
            valIgst.textContent = `₹${igst.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
        }
    }

    function submitInvoiceForm(event) {
        event.preventDefault();

        const contactId = document.getElementById('invoice-customer-select').value;
        const documentDate = document.getElementById('invoice-date-input').value;
        const dueDate = document.getElementById('invoice-due-input').value;
        const billingAddress = document.getElementById('invoice-address-input').value;
        const paymentTerms = document.getElementById('invoice-terms-input').value;

        const items = [];
        const rows = document.querySelectorAll('#invoice-lines-tbody tr');
        let hasValidationError = false;

        rows.forEach(row => {
            const idParts = row.id.split('-');
            const idx = idParts[idParts.length - 1];

            const itemSelect = document.getElementById(`line-item-${idx}`);
            if (!itemSelect || !itemSelect.value) return;

            const qty = parseInt(document.getElementById(`line-qty-${idx}`).value) || 0;
            const price = parseFloat(document.getElementById(`line-price-${idx}`).value) || 0;

            if (docType === 'sales-invoices') {
                const item = itemsCache.find(i => i.id == itemSelect.value);
                if (item && item.qty_on_hand < qty) {
                    alert(`Error: Stock overflow for item "${item.name}". Only ${item.qty_on_hand} available, but you requested ${qty}. Please correct the quantity.`);
                    hasValidationError = true;
                    return;
                }
            }

            items.push({
                inventory_item_id: parseInt(itemSelect.value),
                qty: qty,
                unit_price: price
            });
        });

        if (hasValidationError) return;

        if (items.length === 0) {
            alert('Please add at least one item line.');
            return;
        }

        const payload = {
            contact_id: parseInt(contactId),
            document_date: documentDate,
            due_date: dueDate,
            billing_address: billingAddress,
            payment_terms: paymentTerms,
            items: items
        };

        fetch(`/api/billing/${docType}/invoice/store`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(res => {
            if (res.error) {
                alert('Operation failed: ' + res.error);
            } else {
                alert(`Success! Document ${res.invoice_no} saved successfully.`);
                itemsCache = []; // reset cache
                hideCreateInvoiceForm();
            }
        })
        .catch(err => alert('Server connection error: ' + err.message));
    }

    function markPaid(id) {
        let confirmationText = '';
        if (docType === 'sales-quotations') {
            confirmationText = 'Are you sure you want to mark this Quote as ACCEPTED?';
        } else if (in_array(docType, ['sales-orders', 'purchase-orders'])) {
            confirmationText = 'Are you sure you want to mark this Order as CLOSED?';
        } else {
            confirmationText = 'Are you sure you want to mark this document as PAID?';
        }

        if (!confirm(confirmationText)) return;

        fetch(`/api/billing/${docType}/invoice/pay/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                loadDocuments();
            } else {
                alert('Error: ' + res.error);
            }
        })
        .catch(err => alert('Failed to update: ' + err.message));
    }

    function cancelDocument(id) {
        const message = docType === 'purchase-invoices' 
            ? 'CAUTION: Cancelling this Vendor Bill will delete the record and automatically subtract the added products from inventory stock!' 
            : (docType === 'sales-invoices' 
                ? 'CAUTION: Cancelling this Sales Invoice will delete the record and automatically restore products back to inventory stock!' 
                : 'Are you sure you want to cancel this document?');

        if (!confirm(message)) return;

        fetch(`/api/billing/${docType}/invoice/destroy/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                loadDocuments();
            } else {
                alert('Error cancelling document: ' + res.error);
            }
        })
        .catch(err => alert('Failed to cancel: ' + err.message));
    }

    function in_array(needle, haystack) {
        return haystack.indexOf(needle) !== -1;
    }

    // Expose all required view functions to global scope to bypass IIFE enclosure scoping
    window.loadDocuments = loadDocuments;
    window.showCreateInvoiceForm = showCreateInvoiceForm;
    window.hideCreateInvoiceForm = hideCreateInvoiceForm;
    window.populateDropdowns = populateDropdowns;
    window.onCustomerSelect = onCustomerSelect;
    window.addInvoiceLineItem = addInvoiceLineItem;
    window.removeInvoiceLine = removeInvoiceLine;
    window.onLineItemChange = onLineItemChange;
    window.calculateInvoiceTotals = calculateInvoiceTotals;
    window.submitInvoiceForm = submitInvoiceForm;
    window.markPaid = markPaid;
    window.cancelDocument = cancelDocument;
})();
