// Ensure CSRF tokens
function getHeadersJson() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    return {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken
    };
}

// User Assignment Dropdown State & Helpers
let selectedUserIds = new Set();
const allUsers = {!! json_encode($users) !!};

function showUserDropdown() {
    const list = document.getElementById('user-dropdown-list');
    if (list) list.style.display = 'block';
}

function hideUserDropdown() {
    const list = document.getElementById('user-dropdown-list');
    if (list) list.style.display = 'none';
}

function clearUserSearch() {
    const input = document.getElementById('user-search-input');
    if (input) {
        input.value = '';
        filterUserDropdown('');
    }
    const btn = document.getElementById('clear-search-btn');
    if (btn) btn.style.display = 'none';
}

function filterUserDropdown(query) {
    showUserDropdown();
    const cleanQuery = query.toLowerCase().trim();
    const items = document.querySelectorAll('.user-dropdown-item');
    const clearBtn = document.getElementById('clear-search-btn');
    
    if (clearBtn) {
        clearBtn.style.display = cleanQuery.length > 0 ? 'inline-block' : 'none';
    }

    items.forEach(item => {
        const name = item.getAttribute('data-name').toLowerCase();
        const email = item.getAttribute('data-email').toLowerCase();
        if (name.includes(cleanQuery) || email.includes(cleanQuery)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function toggleUserAssignment(userId, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    if (selectedUserIds.has(userId)) {
        selectedUserIds.delete(userId);
    } else {
        selectedUserIds.add(userId);
    }
    
    updateSelectedUsersChips();
}

function updateSelectedUsersChips() {
    const chipsContainer = document.getElementById('selected-users-chips');
    if (!chipsContainer) return;
    
    chipsContainer.innerHTML = '';
    
    if (selectedUserIds.size === 0) {
        const newLabel = document.createElement('span');
        newLabel.id = 'no-users-selected-label';
        newLabel.className = 'text-muted small px-1';
        newLabel.textContent = 'No users assigned';
        chipsContainer.appendChild(newLabel);
    } else {
        selectedUserIds.forEach(id => {
            const user = allUsers.find(u => u.id === id);
            if (user) {
                const chip = document.createElement('span');
                chip.className = 'badge bg-primary text-white d-inline-flex align-items-center gap-1 py-1 px-2 rounded';
                chip.style.fontSize = '0.78rem';
                chip.innerHTML = `
                    <span>${user.name}</span>
                    <button type="button" class="btn-close btn-close-white" style="font-size: 0.55rem; padding: 0.15rem;" onclick="toggleUserAssignment(${user.id}); event.stopPropagation();" aria-label="Remove"></button>
                `;
                chipsContainer.appendChild(chip);
            }
        });
    }

    // Update check icons in dropdown list
    allUsers.forEach(user => {
        const checkIcon = document.getElementById('check-icon-' + user.id);
        if (checkIcon) {
            if (selectedUserIds.has(user.id)) {
                checkIcon.classList.remove('d-none');
            } else {
                checkIcon.classList.add('d-none');
            }
        }
    });
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const container = document.getElementById('userSearchDropdownContainer');
    if (container && !container.contains(e.target)) {
        hideUserDropdown();
    }
});

// Open Add Account modal
function openAddAccountModal() {
    document.getElementById('emailAccountModalLabel').innerText = 'Add Email Account';
    document.getElementById('emailAccountForm').reset();
    document.getElementById('account-id').value = '';
    
    // Set Password field to required
    document.getElementById('acc-password').required = true;
    document.getElementById('acc-password-label').innerText = 'Password / App Password';

    // Hide Test Connection Result
    const testResultContainer = document.getElementById('test-connection-result-container');
    if (testResultContainer) testResultContainer.classList.add('d-none');

    // Hide Advanced configurations by default
    document.getElementById('show-advanced-switch').checked = false;
    document.getElementById('advanced-settings-container').classList.add('d-none');

    // Default configuration fields (Gmail defaults)
    document.getElementById('acc-imap-host').value = 'imap.gmail.com';
    document.getElementById('acc-imap-port').value = 993;
    document.getElementById('acc-imap-encryption').value = 'ssl';
    document.getElementById('acc-smtp-host').value = 'smtp.gmail.com';
    document.getElementById('acc-smtp-port').value = 465;
    document.getElementById('acc-smtp-encryption').value = 'ssl';

    // Clear selected users Set
    selectedUserIds.clear();
    updateSelectedUsersChips();

    const modal = new bootstrap.Modal(document.getElementById('emailAccountModal'));
    modal.show();
}

// Open Edit Account modal
function openEditAccountModal(acc) {
    document.getElementById('emailAccountModalLabel').innerText = 'Edit Connection Settings';
    document.getElementById('emailAccountForm').reset();
    
    const testResultContainer = document.getElementById('test-connection-result-container');
    if (testResultContainer) testResultContainer.classList.add('d-none');

    document.getElementById('account-id').value = acc.id;
    document.getElementById('acc-display-name').value = acc.display_name;
    document.getElementById('acc-email').value = acc.email;

    // Password is NOT required for edit updates
    document.getElementById('acc-password').required = false;
    document.getElementById('acc-password-label').innerText = 'New Password / App Password (Optional)';

    // Fill advanced configurations
    document.getElementById('acc-imap-host').value = acc.imap_host || 'imap.gmail.com';
    document.getElementById('acc-imap-port').value = acc.imap_port || 993;
    document.getElementById('acc-imap-encryption').value = acc.imap_encryption || 'ssl';
    document.getElementById('acc-smtp-host').value = acc.smtp_host || 'smtp.gmail.com';
    document.getElementById('acc-smtp-port').value = acc.smtp_port || 465;
    document.getElementById('acc-smtp-encryption').value = acc.smtp_encryption || 'ssl';

    // Always show advanced on edit so admin can inspect configurations
    document.getElementById('show-advanced-switch').checked = true;
    document.getElementById('advanced-settings-container').classList.remove('d-none');

    // Check mapped user chips
    selectedUserIds.clear();
    if (acc.user_ids && Array.isArray(acc.user_ids)) {
        acc.user_ids.forEach(id => selectedUserIds.add(parseInt(id)));
    }
    updateSelectedUsersChips();

    const modal = new bootstrap.Modal(document.getElementById('emailAccountModal'));
    modal.show();
}

// Auto-fill configs based on input domain via backend lookup
function handleEmailInput(email) {
    const showAdvanced = document.getElementById('show-advanced-switch').checked;
    if (showAdvanced) return; // Keep custom settings if admin opened advanced view

    if (!email || !email.includes('@')) return;

    fetch('/api/email-accounts/lookup-config?email=' + encodeURIComponent(email), {
        headers: getHeadersJson()
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.config) {
            const cfg = data.config;
            document.getElementById('acc-imap-host').value = cfg.imap_host;
            document.getElementById('acc-imap-port').value = cfg.imap_port;
            document.getElementById('acc-imap-encryption').value = cfg.imap_encryption;
            document.getElementById('acc-smtp-host').value = cfg.smtp_host;
            document.getElementById('acc-smtp-port').value = cfg.smtp_port;
            document.getElementById('acc-smtp-encryption').value = cfg.smtp_encryption;
        }
    })
    .catch(() => {});
}

// Toggle Advanced settings view
function toggleAdvancedSettings(show) {
    const container = document.getElementById('advanced-settings-container');
    if (show) {
        container.classList.remove('d-none');
    } else {
        container.classList.add('d-none');
        // Re-trigger auto-fill based on email address input
        handleEmailInput(document.getElementById('acc-email').value);
    }
}

// Test live socket connection for custom mail servers
function testConnectionLocal(event) {
    if (event) event.preventDefault();

    const testBtn = document.getElementById('test-connection-btn');
    const resultContainer = document.getElementById('test-connection-result-container');
    const resultBox = document.getElementById('test-connection-result-box');
    const resultTitle = document.getElementById('test-connection-title');
    const resultMsg = document.getElementById('test-connection-message');
    const resultLogs = document.getElementById('test-connection-logs');

    const email = document.getElementById('acc-email').value;
    const password = document.getElementById('acc-password').value;
    const id = document.getElementById('account-id').value || null;

    if (!email) {
        alert('Please enter an email address before running the connection test.');
        return;
    }

    testBtn.disabled = true;
    testBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Testing Socket...';

    resultContainer.classList.remove('d-none');
    resultBox.className = 'alert alert-info rounded-3 p-3 mb-0 border';
    resultTitle.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Testing IMAP & SMTP Socket Connections...';
    resultMsg.innerText = 'Connecting to IMAP (' + document.getElementById('acc-imap-host').value + ':' + document.getElementById('acc-imap-port').value + ') and SMTP (' + document.getElementById('acc-smtp-host').value + ':' + document.getElementById('acc-smtp-port').value + ')...';
    resultLogs.style.display = 'none';

    const payload = {
        id: id,
        email: email,
        password: password,
        imap_host: document.getElementById('acc-imap-host').value,
        imap_port: parseInt(document.getElementById('acc-imap-port').value),
        imap_encryption: document.getElementById('acc-imap-encryption').value,
        smtp_host: document.getElementById('acc-smtp-host').value,
        smtp_port: parseInt(document.getElementById('acc-smtp-port').value),
        smtp_encryption: document.getElementById('acc-smtp-encryption').value,
    };

    fetch('/api/email-accounts/test-connection', {
        method: 'POST',
        headers: getHeadersJson(),
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        testBtn.disabled = false;
        testBtn.innerHTML = '<i class="bi bi-lightning-charge-fill text-warning me-1"></i> Test Connection';

        if (data.success) {
            resultBox.className = 'alert alert-success rounded-3 p-3 mb-0 border';
            resultTitle.innerHTML = '<i class="bi bi-check-circle-fill text-success me-1"></i> Connection Test Passed';
        } else {
            resultBox.className = 'alert alert-danger rounded-3 p-3 mb-0 border';
            resultTitle.innerHTML = '<i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Connection Diagnostic Failed';
        }

        resultMsg.innerText = data.message || (data.success ? 'Socket connections verified successfully!' : 'Unable to establish socket connection.');
        
        const logs = [];
        if (data.imap_logs && data.imap_logs.length) logs.push('--- IMAP Logs ---', ...data.imap_logs);
        if (data.smtp_logs && data.smtp_logs.length) logs.push('--- SMTP Logs ---', ...data.smtp_logs);
        
        if (logs.length > 0) {
            resultLogs.innerText = logs.join('\n');
            resultLogs.style.display = 'block';
        }
    })
    .catch(err => {
        testBtn.disabled = false;
        testBtn.innerHTML = '<i class="bi bi-lightning-charge-fill text-warning me-1"></i> Test Connection';

        resultBox.className = 'alert alert-danger rounded-3 p-3 mb-0 border';
        resultTitle.innerHTML = '<i class="bi bi-x-circle-fill text-danger me-1"></i> Request Error';
        resultMsg.innerText = err.message || 'Failed to execute connection test.';
    });
}

// Submit Account Settings Save
function saveAccountLocal(event) {
    event.preventDefault();

    const submitBtn = document.getElementById('save-account-submit-btn');
    submitBtn.disabled = true;

    // Collect user assignments from Set
    const userIds = Array.from(selectedUserIds);

    const payload = {
        id: document.getElementById('account-id').value || null,
        display_name: document.getElementById('acc-display-name').value,
        email: document.getElementById('acc-email').value,
        password: document.getElementById('acc-password').value || null,
        imap_host: document.getElementById('acc-imap-host').value,
        imap_port: parseInt(document.getElementById('acc-imap-port').value),
        imap_encryption: document.getElementById('acc-imap-encryption').value,
        smtp_host: document.getElementById('acc-smtp-host').value,
        smtp_port: parseInt(document.getElementById('acc-smtp-port').value),
        smtp_encryption: document.getElementById('acc-smtp-encryption').value,
        smtp_user: document.getElementById('acc-email').value, // Use email as SMTP username
        imap_user: document.getElementById('acc-email').value, // Use email as IMAP username
        user_ids: userIds
    };

    fetch('/api/email-accounts/store', {
        method: 'POST',
        headers: getHeadersJson(),
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert('Error: ' + (data.error || 'Failed to save account settings'));
            submitBtn.disabled = false;
            return;
        }

        // Close Modal
        const modalEl = document.getElementById('emailAccountModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();
        submitBtn.disabled = false;

        // Refresh dynamic container view
        loadPage('/erp/email-accounts');
    })
    .catch(err => {
        console.error('Failed to save email account', err);
        alert('Server connection failed. Could not save changes.');
        submitBtn.disabled = false;
    });
}

// Delete Email Account
function deleteAccountLocal(id, email) {
    if (!confirm('Are you sure you want to delete email account "' + email + '"? All active user mappings for this account will be revoked.')) {
        return;
    }

    fetch('/api/email-accounts/delete/' + id, {
        method: 'DELETE',
        headers: getHeadersJson()
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert('Error: ' + (data.error || 'Failed to delete account'));
            return;
        }
        
        // Refresh dynamic view
        loadPage('/erp/email-accounts');
    })
    .catch(err => {
        console.error('Failed to delete email account', err);
        alert('Server connection error. Failed to delete account.');
    });
}

// Bind callbacks to window context for inline onclick triggers
window.openAddAccountModal = openAddAccountModal;
window.openEditAccountModal = openEditAccountModal;
window.handleEmailInput = handleEmailInput;
window.toggleAdvancedSettings = toggleAdvancedSettings;
window.saveAccountLocal = saveAccountLocal;
window.deleteAccountLocal = deleteAccountLocal;
window.showUserDropdown = showUserDropdown;
window.clearUserSearch = clearUserSearch;
window.filterUserDropdown = filterUserDropdown;
window.toggleUserAssignment = toggleUserAssignment;
