// Email Inbox Thunderbird / Apple Mail Client JavaScript Logic
if (window.emailAutoSyncInterval) {
    clearInterval(window.emailAutoSyncInterval);
    window.emailAutoSyncInterval = null;
}

var activeEmailAccountId = {{ $account->id ?? 'null' }};
var emailsList = [];
var selectedEmailIds = new Set();
var activeFolder = 'INBOX';
var activeLabel = null;
var activeQuickFilter = 'all';
var activeEmail = null;
var quickReplyAutoSaveTimer = null;
var quickReplyFiles = [];
var userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';

// Initial Load
document.addEventListener('DOMContentLoaded', function() {
    initEmailInboxApp();
});

// For dynamic SPA loader execution
initEmailInboxApp();

function initEmailInboxApp() {
    setupDragAndDropFolderTargets();
    setupQuickReplyDropzone();
    loadEmailsList();
    startLiveAutoSync();
}

/**
 * Format ISO date timestamp to User's Local Timezone with relative & absolute options.
 */
function formatLocalTimezoneDate(isoString, type = 'relative') {
    if (!isoString) return '';
    const date = new Date(isoString);
    if (isNaN(date.getTime())) return isoString;

    const now = new Date();
    const diffMs = now - date;
    const diffSec = Math.floor(diffMs / 1000);
    const diffMin = Math.floor(diffSec / 60);
    const diffHours = Math.floor(diffMin / 60);
    const diffDays = Math.floor(diffHours / 24);

    if (type === 'relative') {
        if (diffSec < 60) return 'Just now';
        if (diffMin < 60) return `${diffMin}m ago`;
        if (diffHours < 24 && date.getDate() === now.getDate()) {
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
        if (diffDays === 1 || (diffHours < 48 && date.getDate() === now.getDate() - 1)) {
            return 'Yesterday ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
        if (date.getFullYear() === now.getFullYear()) {
            return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
        }
        return date.toLocaleDateString([], { year: 'numeric', month: 'short', day: 'numeric' });
    }

    if (type === 'full') {
        const localFormatted = date.toLocaleString([], {
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            timeZoneName: 'short'
        });
        return `${localFormatted} (${userTimezone})`;
    }
}

/**
 * Fetch and render Emails Feed
 */
function loadEmailsList() {
    const feedContainer = document.getElementById('email-list-feed');
    if (!feedContainer) return;

    feedContainer.innerHTML = `
        <div class="text-center py-5 text-muted">
            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
            <span class="small">Fetching messages...</span>
        </div>
    `;

    let url = `/api/email/list?filter=${activeQuickFilter}`;
    if (activeFolder === 'LABEL' && activeLabel) {
        url += `&label=${encodeURIComponent(activeLabel)}`;
    } else {
        url += `&folder=${activeFolder}`;
    }

    const searchVal = document.getElementById('email-search')?.value || '';
    if (searchVal.trim()) {
        url += `&search=${encodeURIComponent(searchVal)}`;
    }

    const advFrom = document.getElementById('adv-search-from')?.value;
    const advTo = document.getElementById('adv-search-to')?.value;
    const advSub = document.getElementById('adv-search-subject')?.value;
    const advDateFrom = document.getElementById('adv-search-date-from')?.value;
    const advDateTo = document.getElementById('adv-search-date-to')?.value;

    if (advDateFrom) url += `&date_from=${encodeURIComponent(advDateFrom)}`;
    if (advDateTo) url += `&date_to=${encodeURIComponent(advDateTo)}`;

    fetch(url)
        .then(r => r.json())
        .then(res => {
            emailsList = res.data || [];
            selectedEmailIds.clear();
            updateBulkSelectionUI();
            renderEmailCardsFeed();
        })
        .catch(err => {
            console.error('Error loading emails:', err);
            feedContainer.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-exclamation-circle text-danger fs-3 d-block mb-2"></i>
                    <span class="small">Failed to load emails. Click Sync to try again.</span>
                </div>
            `;
        });
}

/**
 * Render Email Cards List
 */
function renderEmailCardsFeed() {
    const container = document.getElementById('email-list-feed');
    if (!container) return;

    if (emailsList.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5 text-muted px-3">
                <i class="bi bi-inbox fs-2 text-secondary d-block mb-2"></i>
                <h6 class="fw-bold mb-1">No Messages Found</h6>
                <p class="small text-muted mb-0">This folder is currently empty.</p>
            </div>
        `;
        return;
    }

    let html = '';
    emailsList.forEach(email => {
        const isSelected = selectedEmailIds.has(email.id);
        const isUnreadClass = email.is_read ? 'read' : 'unread';
        const isSelectedClass = isSelected ? 'selected' : '';
        const initial = getContactInitial(email.from_name || email.from_address);
        const avatarBg = getAvatarBgColor(email.from_address);
        const formattedDate = formatLocalTimezoneDate(email.date_sent_iso || email.date_sent, 'relative');

        let labelsHtml = '';
        if (email.labels && email.labels.length > 0) {
            email.labels.forEach(lbl => {
                labelsHtml += `<span class="email-label-badge me-1" style="background-color: ${lbl.color}20; color: ${lbl.color}; border: 1px solid ${lbl.color}40;">${escapeHtml(lbl.name)}</span>`;
            });
        }

        let attachmentBadge = '';
        if (email.has_attachments) {
            const count = email.attachments ? email.attachments.length : 1;
            attachmentBadge = `<span class="badge bg-light text-muted border ms-1" style="font-size:0.68rem;" title="${count} Attachment(s)"><i class="bi bi-paperclip me-1"></i>${count}</span>`;
        }

        html += `
            <div class="email-card-item ${isUnreadClass} ${isSelectedClass}" data-email-id="${email.id}" draggable="true" ondragstart="window.onEmailCardDragStart(event, ${email.id})" onclick="window.onEmailCardClick(event, ${email.id})">
                <div class="d-flex align-items-start gap-2">
                    <div class="pt-1" onclick="event.stopPropagation();">
                        <input type="checkbox" class="form-check-input row-select-checkbox" ${isSelected ? 'checked' : ''} onchange="window.toggleSelectEmailRow(${email.id}, this.checked)">
                    </div>
                    
                    <div class="avatar-initial" style="background-color: ${avatarBg};">
                        ${initial}
                    </div>

                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="d-flex align-items-center text-truncate">
                                ${!email.is_read ? '<span class="unread-indicator-dot" title="Unread"></span>' : ''}
                                <span class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;">${escapeHtml(email.from_name || email.from_address)}</span>
                            </div>
                            <span class="small text-muted ms-2 flex-shrink-0" style="font-size: 0.72rem;">${formattedDate}</span>
                        </div>

                        <div class="email-subject text-truncate mb-1" style="font-size: 0.82rem;">
                            ${escapeHtml(email.subject || '(No Subject)')}
                        </div>

                        <div class="text-muted text-truncate mb-1" style="font-size: 0.76rem; line-height: 1.3;">
                            ${escapeHtml(email.snippet || '')}
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <div>${labelsHtml}${attachmentBadge}</div>
                            <div onclick="event.stopPropagation();">
                                <i class="bi ${email.is_starred ? 'bi-star-fill starred' : 'bi-star'} star-toggle-btn" onclick="window.toggleStarRow(event, ${email.id})"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

/**
 * Handle Card Click to Load Reader Pane
 */
function onEmailCardClick(event, emailId) {
    const email = emailsList.find(e => e.id === emailId);
    if (!email) return;

    activeEmail = email;
    populateThreadLabelDropdown(email);

    // Highlight row
    document.querySelectorAll('.email-card-item').forEach(el => el.classList.remove('selected'));
    const clickedCard = document.querySelector(`.email-card-item[data-email-id="${emailId}"]`);
    if (clickedCard) clickedCard.classList.add('selected');

    // Show reader pane
    const placeholder = document.getElementById('preview-placeholder');
    if (placeholder) {
        placeholder.classList.add('d-none');
        placeholder.classList.remove('d-flex');
    }

    const previewPane = document.getElementById('preview-pane');
    if (previewPane) {
        previewPane.classList.remove('d-none');
        previewPane.classList.add('d-flex');
    }

    const subEl = document.getElementById('preview-subject');
    if (subEl) subEl.textContent = email.subject || '(No Subject)';
    
    // Render labels in reader header
    const labelsContainer = document.getElementById('preview-labels-container');
    if (labelsContainer) {
        labelsContainer.innerHTML = '';
        if (email.labels && email.labels.length > 0) {
            email.labels.forEach(lbl => {
                labelsContainer.innerHTML += `<span class="email-label-badge" style="background-color: ${lbl.color}20; color: ${lbl.color}; border: 1px solid ${lbl.color}40;">${escapeHtml(lbl.name)}</span>`;
            });
        }
    }

    // Configure Star button
    const starBtn = document.getElementById('preview-star-btn');
    if (starBtn) {
        starBtn.innerHTML = email.is_starred ? `<i class="bi bi-star-fill text-warning"></i>` : `<i class="bi bi-star"></i>`;
    }

    const container = document.getElementById('preview-messages-container');
    if (!container) return;

    container.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
            <span class="small text-muted">Loading email conversation...</span>
        </div>
    `;

    fetch('/api/email/thread/' + email.thread_id)
        .then(r => r.json())
        .then(res => {
            container.innerHTML = '';
            (res.messages || []).forEach(msg => {
                const msgCard = document.createElement('div');
                msgCard.className = 'card border-0 shadow-sm mb-3 rounded-3 overflow-hidden';

                const fullDateFormatted = formatLocalTimezoneDate(msg.date_sent, 'full');
                const initial = getContactInitial(msg.from_name || msg.from_address);
                const avatarBg = getAvatarBgColor(msg.from_address);

                let attachmentsHtml = '';
                if (msg.attachments && msg.attachments.length > 0) {
                    attachmentsHtml = `
                        <div class="mt-3 pt-3 border-top bg-light p-3 rounded-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <strong class="small text-dark"><i class="bi bi-paperclip me-1 text-primary"></i> ${msg.attachments.length} Attachment(s)</strong>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                    `;
                    msg.attachments.forEach(att => {
                        const isImg = (att.mime_type || '').startsWith('image/');
                        const isPdf = (att.mime_type || '').includes('pdf');
                        const icon = isImg ? 'bi-image text-success' : (isPdf ? 'bi-file-pdf text-danger' : 'bi-file-earmark-text text-primary');
                        
                        attachmentsHtml += `
                            <div class="border bg-white rounded p-2 d-flex align-items-center gap-2 shadow-sm" style="min-width: 180px;">
                                <i class="bi ${icon} fs-4"></i>
                                <div class="overflow-hidden flex-grow-1">
                                    <div class="text-truncate small fw-bold text-dark" style="font-size: 0.78rem;">${escapeHtml(att.filename)}</div>
                                    <div class="text-muted" style="font-size: 0.68rem;">${(att.file_size / 1024).toFixed(1)} KB</div>
                                </div>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-xs btn-light border" onclick="window.previewAttachmentInline(${att.id}, '${escapeHtml(att.filename)}', '${att.mime_type}')" title="Preview"><i class="bi bi-eye"></i></button>
                                    <a href="/api/email/attachment/${att.id}" target="_blank" class="btn btn-xs btn-light border" title="Download"><i class="bi bi-download"></i></a>
                                </div>
                            </div>
                        `;
                    });
                    attachmentsHtml += `</div></div>`;
                }

                // Render body safely inside iframe
                const iframeId = `email-iframe-${msg.id}`;
                msgCard.innerHTML = `
                    <div class="card-header bg-white border-bottom p-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-initial" style="background-color: ${avatarBg}; width: 32px; height: 32px; font-size: 0.75rem;">${initial}</div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 0.85rem;">${escapeHtml(msg.from_name || msg.from_address)}</div>
                                    <div class="text-muted small" style="font-size: 0.74rem;">To: ${escapeHtml(msg.to_address)}</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <button type="button" class="btn btn-xs btn-light border text-dark" onclick="window.replySpecificMessage(${msg.id}, 'reply')" title="Reply to sender"><i class="bi bi-reply-fill me-1 text-primary"></i>Reply</button>
                                <button type="button" class="btn btn-xs btn-light border text-dark" onclick="window.replySpecificMessage(${msg.id}, 'reply_all')" title="Reply All"><i class="bi bi-reply-all-fill me-1 text-primary"></i>Reply All</button>
                                <button type="button" class="btn btn-xs btn-light border text-dark" onclick="window.replySpecificMessage(${msg.id}, 'forward')" title="Forward Message"><i class="bi bi-forward-fill me-1 text-primary"></i>Forward</button>
                                <span class="badge bg-light text-dark border px-2 py-1 ms-1" style="font-size: 0.7rem;" title="Converted to your local timezone"><i class="bi bi-clock me-1 text-primary"></i>${fullDateFormatted}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <iframe id="${iframeId}" class="email-iframe-container" srcdoc="${escapeAttr(renderSafeEmailBodyHtml(msg.body_html || nl2br(msg.body_text)))}" onload="window.resizeEmailIframe(this)"></iframe>
                        ${attachmentsHtml}
                    </div>
                `;
                container.appendChild(msgCard);
            });

            // Mark email as read in local array & server
            if (!email.is_read) {
                email.is_read = true;
                if (clickedCard) {
                    clickedCard.classList.remove('unread');
                    clickedCard.classList.add('read');
                    const dot = clickedCard.querySelector('.unread-indicator-dot');
                    if (dot) dot.remove();
                }
                updateFolderCountBadge('INBOX', -1);
            }
        });
}

function renderSafeEmailBodyHtml(htmlOrText) {
    if (!htmlOrText) return '<div style="font-family:-apple-system,BlinkMacSystemFont,sans-serif; font-size:14px; color:#64748b;">(No Content)</div>';
    return `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px; line-height: 1.6; color: #1e293b; margin: 0; padding: 10px; }
                img { max-width: 100% !important; height: auto !important; }
                table { max-width: 100% !important; }
                a { color: #2563eb; }
            </style>
        </head>
        <body>${htmlOrText}</body>
        </html>
    `;
}

function resizeEmailIframe(iframe) {
    try {
        if (iframe.contentWindow && iframe.contentWindow.document.body) {
            iframe.style.height = (iframe.contentWindow.document.body.scrollHeight + 30) + 'px';
        }
    } catch (e) {}
}

/**
 * Drag & Drop Logic for Emails Feed to Sidebar Folders
 */
function onEmailCardDragStart(event, emailId) {
    event.dataTransfer.setData('text/plain', emailId.toString());
    event.dataTransfer.effectAllowed = 'move';
}

function setupDragAndDropFolderTargets() {
    const folderItems = document.querySelectorAll('.email-folder-item');
    folderItems.forEach(item => {
        item.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            this.classList.add('drag-over-folder');
        });

        item.addEventListener('dragleave', function(e) {
            this.classList.remove('drag-over-folder');
        });

        item.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over-folder');
            
            const draggedEmailId = e.dataTransfer.getData('text/plain');
            const targetFolder = this.getAttribute('data-folder');
            const targetLabelName = this.getAttribute('data-label-name');

            let idsToMove = [];
            if (selectedEmailIds.has(parseInt(draggedEmailId))) {
                idsToMove = Array.from(selectedEmailIds);
            } else if (draggedEmailId) {
                idsToMove = [parseInt(draggedEmailId)];
            }

            if (idsToMove.length === 0) return;

            if (targetFolder) {
                applyBulkActionOnIds(idsToMove, targetFolder.toLowerCase());
            } else if (targetLabelName) {
                const labelId = this.getAttribute('data-label-id');
                applyBulkActionOnIds(idsToMove, 'apply_label', labelId);
            }
        });
    });
}

/**
 * Quick Filter Pills Toggle
 */
function setQuickFilter(filterType, element) {
    document.querySelectorAll('#quick-filters-bar .filter-pill').forEach(el => el.classList.remove('active'));
    element.classList.add('active');
    activeQuickFilter = filterType;
    loadEmailsList();
}

/**
 * Folder Switcher
 */
function switchFolder(element, folder) {
    if (window.event) window.event.preventDefault();
    document.querySelectorAll('#folder-list .email-folder-item').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('#label-list .email-folder-item').forEach(el => el.classList.remove('active'));
    if (element) element.classList.add('active');

    activeFolder = folder;
    activeLabel = null;
    const titleEl = document.getElementById('current-folder-title');
    if (titleEl) titleEl.textContent = folder.charAt(0) + folder.slice(1).toLowerCase();

    // Reset preview
    const previewPane = document.getElementById('preview-pane');
    if (previewPane) {
        previewPane.classList.add('d-none');
        previewPane.classList.remove('d-flex');
    }

    const placeholder = document.getElementById('preview-placeholder');
    if (placeholder) {
        placeholder.classList.remove('d-none');
        placeholder.classList.add('d-flex');
    }

    loadEmailsList();
}

function switchLabel(element, labelName) {
    if (window.event) window.event.preventDefault();
    document.querySelectorAll('#folder-list .email-folder-item').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('#label-list .email-folder-item').forEach(el => el.classList.remove('active'));
    if (element) element.classList.add('active');

    activeFolder = 'LABEL';
    activeLabel = labelName;
    const titleEl = document.getElementById('current-folder-title');
    if (titleEl) titleEl.textContent = 'Label: ' + labelName;

    loadEmailsList();
}

/**
 * Multi-Select Row Handlers
 */
function toggleSelectEmailRow(emailId, checked) {
    if (checked) {
        selectedEmailIds.add(emailId);
    } else {
        selectedEmailIds.delete(emailId);
    }
    updateBulkSelectionUI();
    renderEmailCardsFeed();
}

function toggleSelectAllRows(checked) {
    if (checked) {
        emailsList.forEach(e => selectedEmailIds.add(e.id));
    } else {
        selectedEmailIds.clear();
    }
    updateBulkSelectionUI();
    renderEmailCardsFeed();
}

function selectRowsByCondition(condition) {
    selectedEmailIds.clear();
    if (condition === 'all') {
        emailsList.forEach(e => selectedEmailIds.add(e.id));
    } else if (condition === 'unread') {
        emailsList.filter(e => !e.is_read).forEach(e => selectedEmailIds.add(e.id));
    } else if (condition === 'starred') {
        emailsList.filter(e => e.is_starred).forEach(e => selectedEmailIds.add(e.id));
    }
    updateBulkSelectionUI();
    renderEmailCardsFeed();
}

function updateBulkSelectionUI() {
    const count = selectedEmailIds.size;
    const label = document.getElementById('selected-count-label');
    const group = document.getElementById('bulk-actions-group');
    const selectAllCheckbox = document.getElementById('select-all-checkbox');

    if (label) label.textContent = `${count} selected`;

    if (count > 0) {
        group?.classList.remove('d-none');
        group?.classList.add('d-flex');
    } else {
        group?.classList.add('d-none');
        group?.classList.remove('d-flex');
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.checked = count > 0 && count === emailsList.length;
    }
}

/**
 * Bulk Action API Dispatcher
 */
function applyBulkAction(action, extraParam) {
    const ids = Array.from(selectedEmailIds);
    if (ids.length === 0) return;
    applyBulkActionOnIds(ids, action, extraParam);
}

function applyBulkActionOnIds(ids, action, extraParam) {
    fetch('/api/email/bulk-action', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
            ids: ids,
            action: action,
            label_id: extraParam
        })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            selectedEmailIds.clear();
            loadEmailsList();
            refreshFolderCounts();
            if (typeof showToast === 'function') showToast('success', 'Bulk action completed.');
        }
    });
}

function toggleStarRow(event, emailId) {
    if (event) event.stopPropagation();
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    fetch(`/api/email/toggle-star/${emailId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        }
    })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                const email = emailsList.find(e => e.id === emailId);
                if (email) email.is_starred = res.is_starred;
                renderEmailCardsFeed();
                refreshFolderCounts();
            }
        });
}

function toggleActiveStar() {
    if (!activeEmail) return;
    toggleStarRow(window.event || {}, activeEmail.id);
}

function moveActiveEmail(folder) {
    if (!activeEmail) return;
    applyBulkActionOnIds([activeEmail.id], folder.toLowerCase());
}

/**
 * Quick Reply & Auto Save Draft
 */
function formatQuickReplyText(cmd, val = null) {
    document.execCommand(cmd, false, val);
}

function triggerQuickReplyAutoSave() {
    clearTimeout(quickReplyAutoSaveTimer);
    const status = document.getElementById('quick-reply-save-status');
    if (status) status.textContent = 'Saving draft...';

    quickReplyAutoSaveTimer = setTimeout(() => {
        saveQuickReplyDraft();
    }, 3000);
}

function saveQuickReplyDraft() {
    if (!activeEmail) return;
    const content = document.getElementById('quick-reply-editor')?.innerHTML || '';
    if (!content.trim()) return;

    fetch('/api/email/save-draft', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
            to: activeEmail.from_address,
            subject: 'Re: ' + (activeEmail.subject || ''),
            body_html: content
        })
    })
    .then(r => r.json())
    .then(res => {
        const status = document.getElementById('quick-reply-save-status');
        if (status) status.textContent = 'Draft auto-saved';
        refreshFolderCounts();
    });
}

function setupQuickReplyDropzone() {
    const zone = document.getElementById('quick-reply-dropzone');
    if (!zone) return;

    zone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('drag-over-dropzone');
    });
    zone.addEventListener('dragleave', function(e) {
        this.classList.remove('drag-over-dropzone');
    });
    zone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('drag-over-dropzone');
        if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            handleQuickReplyFiles(e.dataTransfer.files);
        }
    });
}

function handleQuickReplyFileSelect(event) {
    if (event.target.files) {
        handleQuickReplyFiles(event.target.files);
    }
}

function handleQuickReplyFiles(files) {
    const preview = document.getElementById('quick-reply-attachments-preview');
    for (let i = 0; i < files.length; i++) {
        const f = files[i];
        quickReplyFiles.push(f);
        if (preview) {
            preview.innerHTML += `
                <span class="badge bg-light text-dark border p-1" style="font-size:0.75rem;">
                    <i class="bi bi-paperclip me-1"></i> ${escapeHtml(f.name)} (${(f.size/1024).toFixed(1)} KB)
                </span>
            `;
        }
    }
}

function sendQuickReply() {
    if (!activeEmail) return;
    const content = document.getElementById('quick-reply-editor')?.innerHTML || '';
    if (!content.trim()) {
        alert('Please enter a message to send.');
        return;
    }

    const btn = document.getElementById('quick-reply-send-btn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `<div class="spinner-border spinner-border-sm me-1"></div> Sending...`;
    }

    const formData = new FormData();
    formData.append('to', activeEmail.from_address);
    formData.append('subject', 'Re: ' + (activeEmail.subject || ''));
    formData.append('body_html', content);
    formData.append('in_reply_to', activeEmail.message_id || '');
    formData.append('thread_id', activeEmail.thread_id || '');

    quickReplyFiles.forEach(file => {
        formData.append('attachments[]', file);
    });

    fetch('/api/email/send', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = `<i class="bi bi-send-fill me-1"></i> Send Reply`;
        }
        if (res.success) {
            document.getElementById('quick-reply-editor').innerHTML = '';
            quickReplyFiles = [];
            document.getElementById('quick-reply-attachments-preview').innerHTML = '';
            document.getElementById('quick-reply-save-status').textContent = '';
            onEmailCardClick({}, activeEmail.id);
            refreshFolderCounts();
            if (typeof showToast === 'function') showToast('success', 'Reply transmitted.');
        } else {
            alert('Failed to send email: ' + (res.error || 'Unknown error'));
        }
    });
}

function popoutToFullCompose(mode = 'reply') {
    if (!activeEmail) return;
    if (typeof loadEmailApp === 'function') {
        loadEmailApp(null, 'compose', {
            reply_to: activeEmail.id,
            mode: mode
        });
    }
}

function replySpecificMessage(msgId, mode = 'reply') {
    if (typeof loadEmailApp === 'function') {
        loadEmailApp(null, 'compose', {
            reply_to: msgId,
            mode: mode
        });
    }
}

function replyCurrentThread(mode = 'reply') {
    if (!activeEmail) return;
    replySpecificMessage(activeEmail.id, mode);
}

/**
 * Inline Attachment Previewer Modal
 */
function previewAttachmentInline(attId, filename, mimeType) {
    const modalEl = document.getElementById('attachmentPreviewModal');
    const title = document.getElementById('attachment-preview-filename');
    const downloadBtn = document.getElementById('attachment-download-btn');
    const body = document.getElementById('attachment-preview-body');

    if (!modalEl) return;

    if (title) title.textContent = filename;
    if (downloadBtn) downloadBtn.href = `/api/email/attachment/${attId}`;

    const url = `/api/email/attachment/${attId}?inline=1`;
    if (mimeType.startsWith('image/')) {
        body.innerHTML = `<img src="${url}" class="img-fluid" style="max-height: 75vh; object-fit: contain;">`;
    } else if (mimeType.includes('pdf')) {
        body.innerHTML = `<iframe src="${url}" style="width: 100%; height: 75vh; border: none;"></iframe>`;
    } else {
        body.innerHTML = `
            <div class="text-center p-5 text-light">
                <i class="bi bi-file-earmark-arrow-down fs-1 d-block mb-3 text-primary"></i>
                <h6>Preview not available inline for this file type (${mimeType}).</h6>
                <a href="${url}" class="btn btn-primary btn-sm mt-2" target="_blank">Download File</a>
            </div>
        `;
    }

    document.body.appendChild(modalEl);
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

/**
 * Omnibox & Advanced Search Modal
 */
var searchTimeout = null;
function onEmailSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadEmailsList();
    }, 400);
}

function openAdvancedSearchModal() {
    const modalEl = document.getElementById('advancedSearchModal');
    if (!modalEl) return;
    document.body.appendChild(modalEl);
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

function executeAdvancedSearch(event) {
    event.preventDefault();
    const modalEl = document.getElementById('advancedSearchModal');
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();
    loadEmailsList();
}

function resetAdvancedSearch() {
    const fromEl = document.getElementById('adv-search-from');
    const toEl = document.getElementById('adv-search-to');
    const subEl = document.getElementById('adv-search-subject');
    const dateFromEl = document.getElementById('adv-search-date-from');
    const dateToEl = document.getElementById('adv-search-date-to');
    const attEl = document.getElementById('adv-search-has-attachment');

    if (fromEl) fromEl.value = '';
    if (toEl) toEl.value = '';
    if (subEl) subEl.value = '';
    if (dateFromEl) dateFromEl.value = '';
    if (dateToEl) dateToEl.value = '';
    if (attEl) attEl.checked = false;

    loadEmailsList();
}

/**
 * Mail Settings Modal & Accounts
 */
function openEmailSettingsModal() {
    const modalEl = document.getElementById('emailSettingsModal');
    if (!modalEl) return;
    document.body.appendChild(modalEl);

    fetch('/api/email/settings')
        .then(r => r.json())
        .then(data => {
            const emailInput = document.getElementById('settings-email');
            const nameInput = document.getElementById('settings-display-name');
            if (emailInput) emailInput.value = data.email || '';
            if (nameInput) nameInput.value = data.display_name || '';

            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        });
}

function saveEmailSettings(event) {
    event.preventDefault();
    const email = document.getElementById('settings-email')?.value;
    const name = document.getElementById('settings-display-name')?.value;

    fetch('/api/email/settings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ email: email, display_name: name })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const modalEl = document.getElementById('emailSettingsModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            if (typeof showToast === 'function') showToast('success', 'Email account settings saved.');
            if (typeof loadPage === 'function') loadPage('/erp/email-inbox');
        } else {
            alert('Failed to save settings: ' + (res.error || 'Unknown error'));
        }
    });
}

function switchEmailAccountLocal(event, accId, email) {
    if (event) event.preventDefault();

    fetch('/api/email/switch-account', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ email_account_id: accId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (typeof loadPage === 'function') loadPage('/erp/email-inbox');
            if (typeof showToast === 'function') showToast('success', 'Switched to account: ' + email);
        } else {
            alert('Failed to switch account.');
        }
    });
}

/**
 * Labels Management Modals
 */
function openCreateLabelModal() {
    const modalEl = document.getElementById('createLabelModal');
    if (!modalEl) return;
    document.body.appendChild(modalEl);

    document.getElementById('label-name-input').value = '';
    document.getElementById('label-color-input').value = '#3b82f6';
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

function saveNewLabel(event) {
    event.preventDefault();
    const name = document.getElementById('label-name-input')?.value;
    const color = document.getElementById('label-color-input')?.value;

    fetch('/api/email/labels', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ name: name, color: color })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const modalEl = document.getElementById('createLabelModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            if (typeof showToast === 'function') showToast('success', 'Label created.');
            if (typeof loadPage === 'function') loadPage('/erp/email-inbox');
        } else {
            alert('Failed to create label: ' + (data.error || ''));
        }
    });
}

function deleteLabel(event, id) {
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }
    if (!confirm('Delete this label?')) return;

    fetch('/api/email/label/' + id, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (typeof showToast === 'function') showToast('success', 'Label deleted.');
            if (typeof loadPage === 'function') loadPage('/erp/email-inbox');
        } else {
            alert('Failed to delete label.');
        }
    });
}

function printActiveEmailThread() {
    const container = document.getElementById('preview-messages-container');
    if (!container) return;
    const win = window.open('', '_blank');
    win.document.write(`
        <html>
        <head><title>Print Email Thread - ${escapeHtml(activeEmail ? activeEmail.subject : '')}</title></head>
        <body>${container.innerHTML}</body>
        </html>
    `);
    win.document.close();
    win.print();
}

/**
 * Unread Badges Refresh
 */
function refreshFolderCounts() {
    fetch('/api/email/folder-counts')
        .then(r => r.json())
        .then(counts => {
            for (let k in counts) {
                const el = document.getElementById('count-' + k.toLowerCase());
                if (el) el.textContent = counts[k];
            }
        });
}

function updateFolderCountBadge(folder, delta) {
    const el = document.getElementById('count-' + folder.toLowerCase());
    if (el) {
        const curr = parseInt(el.textContent || '0') + delta;
        el.textContent = curr > 0 ? curr : 0;
    }
}

/**
 * Live Auto Sync (Thunderbird Style Incoming Drop Controlled by DB Column)
 */
function toggleLiveSync(event) {
    if (event) event.stopPropagation();
    
    fetch('/api/email/toggle-live-sync', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const indicator = document.getElementById('live-sync-indicator');
            const textEl = document.getElementById('live-sync-text');
            const isEnabled = res.is_live_sync_enabled;

            if (textEl) textEl.textContent = 'Live Sync: ' + (isEnabled ? 'ON' : 'OFF');

            if (indicator) {
                if (isEnabled) {
                    indicator.className = 'badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 ms-1 d-none d-md-inline-flex align-items-center';
                } else {
                    indicator.className = 'badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1 ms-1 d-none d-md-inline-flex align-items-center';
                }
            }

            if (typeof showToast === 'function') {
                showToast('info', res.message);
            }
        }
    });
}

function startLiveAutoSync() {
    window.emailAutoSyncInterval = setInterval(() => {
        fetch('/api/email/auto-sync')
            .then(r => r.json())
            .then(res => {
                const indicator = document.getElementById('live-sync-indicator');
                const textEl = document.getElementById('live-sync-text');
                const isEnabled = res.is_live_sync_enabled ?? true;

                if (textEl) textEl.textContent = 'Live Sync: ' + (isEnabled ? 'ON' : 'OFF');

                if (indicator) {
                    if (isEnabled) {
                        indicator.className = 'badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 ms-1 d-none d-md-inline-flex align-items-center';
                    } else {
                        indicator.className = 'badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1 ms-1 d-none d-md-inline-flex align-items-center';
                    }
                }

                if (isEnabled && res.new_emails_count && res.new_emails_count > 0) {
                    loadEmailsList();
                    refreshFolderCounts();
                }
            })
            .catch(() => {});
    }, 15000);
}

function refreshActiveAccountEmails(btn) {
    const icon = document.getElementById('sync-icon');
    if (icon) icon.classList.add('spin-animation');

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    fetch('/api/email/sync/' + activeEmailAccountId, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        }
    })
        .then(r => r.json())
        .then(res => {
            if (icon) icon.classList.remove('spin-animation');
            loadEmailsList();
            refreshFolderCounts();
            if (typeof showToast === 'function') showToast('success', res.message || 'Synced successfully.');
        })
        .catch(err => {
            if (icon) icon.classList.remove('spin-animation');
        });
}

/**
 * Utilities
 */
function getContactInitial(nameOrEmail) {
    if (!nameOrEmail) return '✉';
    const clean = nameOrEmail.trim().replace(/^["']|["']$/g, '');
    return clean.charAt(0).toUpperCase();
}

function getAvatarBgColor(str) {
    if (!str) return '#3b82f6';
    let hash = 0;
    for (let i = 0; i < str.length; i++) {
        hash = str.charCodeAt(i) + ((hash << 5) - hash);
    }
    const colors = ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#6366f1'];
    return colors[Math.abs(hash) % colors.length];
}

function escapeHtml(str) {
    return (str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function escapeAttr(str) {
    return (str || '').replace(/"/g, '&quot;');
}

function nl2br(str) {
    return (str || '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br>$2');
}

function populateThreadLabelDropdown(email) {
    fetch('/api/email/labels')
        .then(r => r.json())
        .then(labels => {
            const bulkMenu = document.getElementById('bulk-label-dropdown-menu');
            const threadMenu = document.getElementById('thread-label-dropdown-menu');

            let html = '';
            labels.forEach(lbl => {
                html += `
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between py-1" href="#" onclick="window.applyBulkAction('apply_label', ${lbl.id})">
                            <span><i class="bi bi-tag-fill me-2" style="color: ${lbl.color};"></i> ${escapeHtml(lbl.name)}</span>
                        </a>
                    </li>
                `;
            });
            if (bulkMenu) bulkMenu.innerHTML = html;
            if (threadMenu) threadMenu.innerHTML = html;
        });
}

// Global Window Function Exports (Solves closure / IIFE ReferenceErrors)
window.initEmailInboxApp = initEmailInboxApp;
window.formatLocalTimezoneDate = formatLocalTimezoneDate;
window.loadEmailsList = loadEmailsList;
window.renderEmailCardsFeed = renderEmailCardsFeed;
window.onEmailCardClick = onEmailCardClick;
window.renderSafeEmailBodyHtml = renderSafeEmailBodyHtml;
window.resizeEmailIframe = resizeEmailIframe;
window.onEmailCardDragStart = onEmailCardDragStart;
window.setupDragAndDropFolderTargets = setupDragAndDropFolderTargets;
window.setQuickFilter = setQuickFilter;
window.switchFolder = switchFolder;
window.switchLabel = switchLabel;
window.toggleSelectEmailRow = toggleSelectEmailRow;
window.toggleSelectAllRows = toggleSelectAllRows;
window.selectRowsByCondition = selectRowsByCondition;
window.updateBulkSelectionUI = updateBulkSelectionUI;
window.applyBulkAction = applyBulkAction;
window.applyBulkActionOnIds = applyBulkActionOnIds;
window.toggleStarRow = toggleStarRow;
window.toggleActiveStar = toggleActiveStar;
window.moveActiveEmail = moveActiveEmail;
window.formatQuickReplyText = formatQuickReplyText;
window.triggerQuickReplyAutoSave = triggerQuickReplyAutoSave;
window.saveQuickReplyDraft = saveQuickReplyDraft;
window.setupQuickReplyDropzone = setupQuickReplyDropzone;
window.handleQuickReplyFileSelect = handleQuickReplyFileSelect;
window.handleQuickReplyFiles = handleQuickReplyFiles;
window.sendQuickReply = sendQuickReply;
window.replySpecificMessage = replySpecificMessage;
window.replyCurrentThread = replyCurrentThread;
window.popoutToFullCompose = popoutToFullCompose;
window.previewAttachmentInline = previewAttachmentInline;
window.onEmailSearch = onEmailSearch;
window.openAdvancedSearchModal = openAdvancedSearchModal;
window.executeAdvancedSearch = executeAdvancedSearch;
window.resetAdvancedSearch = resetAdvancedSearch;
window.refreshFolderCounts = refreshFolderCounts;
window.updateFolderCountBadge = updateFolderCountBadge;
window.startLiveAutoSync = startLiveAutoSync;
window.toggleLiveSync = toggleLiveSync;
window.refreshActiveAccountEmails = refreshActiveAccountEmails;
window.switchEmailAccountLocal = switchEmailAccountLocal;
window.openEmailSettingsModal = openEmailSettingsModal;
window.saveEmailSettings = saveEmailSettings;
window.openCreateLabelModal = openCreateLabelModal;
window.saveNewLabel = saveNewLabel;
window.deleteLabel = deleteLabel;
window.populateThreadLabelDropdown = populateThreadLabelDropdown;
window.printActiveEmailThread = printActiveEmailThread;
