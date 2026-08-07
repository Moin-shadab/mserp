<div class="container-fluid p-0">
    <div class="row g-0 email-app-container">
        <!-- Left Sidebar Pane: Accounts, Folders & Labels -->
        <div class="col-md-3 col-lg-2 email-sidebar-pane d-flex flex-column h-100 p-3">
            <!-- Email Account Selector -->
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.5px;">Active Mail Account</label>
                <div class="dropdown">
                    <button class="btn btn-light btn-sm w-100 border text-start d-flex justify-content-between align-items-center py-2 px-2 shadow-sm" type="button" id="emailAccountDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 8px;">
                        <span class="text-truncate fw-semibold text-dark" style="font-size: 0.78rem;" id="active-account-display-email">
                            {{ $account->email ?? 'No Account Configured' }}
                        </span>
                        <i class="bi bi-chevron-down text-muted small ms-1"></i>
                    </button>
                    <ul class="dropdown-menu w-100 shadow border-0" aria-labelledby="emailAccountDropdown" id="email-accounts-list" style="border-radius: 8px;">
                        @foreach($allAcc as $acc)
                            <li>
                                <a class="dropdown-item py-2 d-flex justify-content-between align-items-center @if($account && $account->id == $acc->id) active @endif" href="#" onclick="switchEmailAccountLocal(event, {{ $acc->id }}, '{{ $acc->email }}')">
                                    <span class="text-truncate small fw-medium @if($account && $account->id == $acc->id) text-white @else text-dark @endif">{{ $acc->email }}</span>
                                    @if($account && $account->id == $acc->id)
                                        <i class="bi bi-check-lg small"></i>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                        @if($allAcc->isEmpty())
                            <li class="px-3 py-2 text-muted small text-center">No accounts found.</li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Primary Compose Button -->
            <button class="btn btn-primary btn-sm mb-3 w-100 py-2 d-flex align-items-center justify-content-center shadow-sm" onclick="loadEmailApp(null, 'compose')" style="border-radius: 8px; font-weight: 600;">
                <i class="bi bi-pencil-square me-2 fs-6"></i> Compose Mail
            </button>

            <!-- Navigation Folders List (Drop targets for Drag & Drop) -->
            <div class="list-group list-group-flush mb-3 custom-scrollbar overflow-y-auto" id="folder-list">
                <a href="#" class="email-folder-item active" data-folder="INBOX" onclick="switchFolder(this, 'INBOX')">
                    <span><i class="bi bi-inbox me-2"></i> Inbox</span>
                    <span class="badge rounded-pill bg-primary" id="count-inbox">{{ $counts['INBOX'] ?? 0 }}</span>
                </a>
                <a href="#" class="email-folder-item" data-folder="STARRED" onclick="switchFolder(this, 'STARRED')">
                    <span><i class="bi bi-star me-2 text-warning"></i> Starred</span>
                    <span class="badge rounded-pill bg-light text-dark border" id="count-starred">{{ $counts['STARRED'] ?? 0 }}</span>
                </a>
                <a href="#" class="email-folder-item" data-folder="SENT" onclick="switchFolder(this, 'SENT')">
                    <span><i class="bi bi-send me-2"></i> Sent</span>
                    <span class="badge rounded-pill bg-light text-muted" id="count-sent">{{ $counts['SENT'] ?? 0 }}</span>
                </a>
                <a href="#" class="email-folder-item" data-folder="DRAFTS" onclick="switchFolder(this, 'DRAFTS')">
                    <span><i class="bi bi-file-earmark-text me-2"></i> Drafts</span>
                    <span class="badge rounded-pill bg-light text-muted" id="count-drafts">{{ $counts['DRAFTS'] ?? 0 }}</span>
                </a>
                <a href="#" class="email-folder-item" data-folder="SPAM" onclick="switchFolder(this, 'SPAM')">
                    <span><i class="bi bi-exclamation-triangle me-2"></i> Spam</span>
                    <span class="badge rounded-pill bg-light text-muted" id="count-spam">{{ $counts['SPAM'] ?? 0 }}</span>
                </a>
                <a href="#" class="email-folder-item" data-folder="TRASH" onclick="switchFolder(this, 'TRASH')">
                    <span><i class="bi bi-trash me-2"></i> Trash</span>
                    <span class="badge rounded-pill bg-light text-muted" id="count-trash">{{ $counts['TRASH'] ?? 0 }}</span>
                </a>
                <a href="#" class="email-folder-item" data-folder="ARCHIVE" onclick="switchFolder(this, 'ARCHIVE')">
                    <span><i class="bi bi-archive me-2"></i> Archive</span>
                    <span class="badge rounded-pill bg-light text-muted" id="count-archive">{{ $counts['ARCHIVE'] ?? 0 }}</span>
                </a>
            </div>

            <!-- Labels Section -->
            <div class="mt-2 mb-2 d-flex justify-content-between align-items-center px-1">
                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.7rem;">Labels</span>
                <button class="btn btn-link p-0 text-primary d-flex align-items-center" onclick="openCreateLabelModal()" title="New Label" style="text-decoration: none;">
                    <i class="bi bi-plus-lg fs-6"></i>
                </button>
            </div>
            <div class="list-group list-group-flush custom-scrollbar overflow-y-auto mb-3" id="label-list" style="max-height: 160px;">
                @foreach($labels as $lbl)
                    <a href="#" class="email-folder-item" data-label-id="{{ $lbl->id }}" data-label-name="{{ $lbl->name }}" onclick="switchLabel(this, '{{ $lbl->name }}')">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <span class="text-truncate"><i class="bi bi-tag-fill me-2" style="color: {{ $lbl->color }};"></i> {{ $lbl->name }}</span>
                            <button class="btn btn-link p-0 text-muted label-delete-btn ms-1" onclick="deleteLabel(event, {{ $lbl->id }})" style="visibility: hidden;">
                                <i class="bi bi-x fs-6"></i>
                            </button>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Settings Footer Links -->
            <div class="mt-auto pt-2 border-top d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm flex-fill py-1 px-2 d-flex align-items-center justify-content-center" onclick="openEmailSettingsModal()" style="font-size: 0.75rem;">
                    <i class="bi bi-gear me-1"></i> Mail Config
                </button>
                <button class="btn btn-outline-secondary btn-sm flex-fill py-1 px-2 d-flex align-items-center justify-content-center" onclick="loadEmailApp(null, 'contacts')" style="font-size: 0.75rem;">
                    <i class="bi bi-person-lines-fill me-1"></i> Contacts
                </button>
            </div>
        </div>

        <!-- Middle Pane: Email Cards Feed & Multi-Select Bar -->
        <div class="col-md-4 col-lg-4 email-list-pane d-flex flex-column h-100 p-0">
            <!-- Header Bar with Search & Sync -->
            <div class="p-3 border-bottom bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="fw-bold mb-0 text-dark" id="current-folder-title">Inbox</h6>
                        <button class="btn btn-xs btn-outline-secondary d-flex align-items-center gap-1 py-1 px-2" onclick="refreshActiveAccountEmails(this)" title="Manual Sync Emails">
                            <i class="bi bi-arrow-clockwise" id="sync-icon"></i> <span class="d-none d-lg-inline">Sync</span>
                        </button>
                        <span class="badge @if($account && ($account->is_live_sync_enabled ?? true)) bg-success-subtle text-success border border-success-subtle @else bg-secondary-subtle text-secondary border border-secondary-subtle @endif rounded-pill px-2 py-1 ms-1 d-none d-md-inline-flex align-items-center" id="live-sync-indicator" style="font-size: 0.68rem; cursor: pointer;" onclick="window.toggleLiveSync(event)" title="Click to Toggle DB Live Refresh Auto-Sync">
                            <i class="bi bi-broadcast me-1"></i> <span id="live-sync-text">Live Sync: {{ ($account && ($account->is_live_sync_enabled ?? true)) ? 'ON' : 'OFF' }}</span>
                        </span>
                    </div>
                </div>
                
                <!-- Omnibox Search Input -->
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0 border-end-0 bg-light" id="email-search" placeholder="Search emails (from:, subject:, is:unread)..." oninput="onEmailSearch()">
                    <button class="btn btn-light border-start-0 text-muted" type="button" onclick="openAdvancedSearchModal()" title="Advanced Filters">
                        <i class="bi bi-sliders"></i>
                    </button>
                </div>

                <!-- Quick Filter Pills -->
                <div class="d-flex gap-1 mt-2 overflow-x-auto custom-scrollbar pb-1" id="quick-filters-bar">
                    <button class="filter-pill active" onclick="setQuickFilter('all', this)">All</button>
                    <button class="filter-pill" onclick="setQuickFilter('unread', this)">Unread</button>
                    <button class="filter-pill" onclick="setQuickFilter('starred', this)">Starred</button>
                    <button class="filter-pill" onclick="setQuickFilter('attachments', this)">Has Attachment</button>
                </div>
            </div>

            <!-- Multi-Select & Bulk Actions Bar -->
            <div class="px-3 py-2 bg-light border-bottom d-flex align-items-center justify-content-between" id="bulk-selection-bar">
                <div class="d-flex align-items-center gap-2">
                    <div class="dropdown">
                        <input type="checkbox" class="form-check-input me-1" id="select-all-checkbox" onchange="toggleSelectAllRows(this.checked)" style="cursor: pointer;">
                        <button class="btn btn-xs btn-light border-0 dropdown-toggle p-0" type="button" data-bs-toggle="dropdown"></button>
                        <ul class="dropdown-menu shadow border-0" style="font-size: 0.8rem;">
                            <li><a class="dropdown-item" href="#" onclick="selectRowsByCondition('all')">Select All</a></li>
                            <li><a class="dropdown-item" href="#" onclick="selectRowsByCondition('unread')">Select Unread</a></li>
                            <li><a class="dropdown-item" href="#" onclick="selectRowsByCondition('starred')">Select Starred</a></li>
                            <li><a class="dropdown-item" href="#" onclick="selectRowsByCondition('none')">Select None</a></li>
                        </ul>
                    </div>
                    <span class="small text-muted" id="selected-count-label">0 emails selected</span>
                </div>

                <!-- Bulk Toolbar Action Buttons (Hidden when 0 selected) -->
                <div class="d-none gap-1" id="bulk-actions-group">
                    <button class="btn btn-xs btn-white border shadow-sm" onclick="applyBulkAction('read')" title="Mark Read"><i class="bi bi-envelope-open text-primary"></i></button>
                    <button class="btn btn-xs btn-white border shadow-sm" onclick="applyBulkAction('unread')" title="Mark Unread"><i class="bi bi-envelope text-secondary"></i></button>
                    <button class="btn btn-xs btn-white border shadow-sm" onclick="applyBulkAction('star')" title="Star"><i class="bi bi-star-fill text-warning"></i></button>
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-xs btn-white border shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" title="Move to Folder">
                            <i class="bi bi-folder-symlink text-dark"></i>
                        </button>
                        <ul class="dropdown-menu shadow border-0" style="font-size: 0.8rem;">
                            <li><a class="dropdown-item" href="#" onclick="applyBulkAction('inbox')"><i class="bi bi-inbox me-2"></i> Inbox</a></li>
                            <li><a class="dropdown-item" href="#" onclick="applyBulkAction('archive')"><i class="bi bi-archive me-2"></i> Archive</a></li>
                            <li><a class="dropdown-item" href="#" onclick="applyBulkAction('spam')"><i class="bi bi-exclamation-triangle me-2"></i> Spam</a></li>
                            <li><a class="dropdown-item" href="#" onclick="applyBulkAction('trash')"><i class="bi bi-trash me-2"></i> Trash</a></li>
                        </ul>
                    </div>
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-xs btn-white border shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" title="Apply Label">
                            <i class="bi bi-tag text-info"></i>
                        </button>
                        <ul class="dropdown-menu shadow border-0" id="bulk-label-dropdown-menu" style="font-size: 0.8rem;">
                            <!-- Populated dynamically -->
                        </ul>
                    </div>
                    <button class="btn btn-xs btn-white border shadow-sm text-danger" onclick="applyBulkAction('trash')" title="Delete"><i class="bi bi-trash"></i></button>
                </div>
            </div>

            <!-- Email Card Feed (Drag & Drop Sources) -->
            <div class="flex-grow-1 overflow-y-auto custom-scrollbar" id="email-list-feed" style="min-height: 0;">
                <!-- Loaded dynamically via JS -->
            </div>
        </div>

        <!-- Right Pane: Reading & Conversation Thread View -->
        <div class="col-md-5 col-lg-6 email-reader-pane d-flex flex-column h-100 p-0 overflow-hidden" style="min-height: 0;">
            <!-- Empty State Placeholder -->
            <div id="preview-placeholder" class="d-flex flex-column align-items-center justify-content-center h-100 text-muted p-4">
                <div class="p-4 rounded-circle bg-light mb-3">
                    <i class="bi bi-envelope-open fs-1 text-primary"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">No Email Selected</h6>
                <p class="small text-muted text-center" style="max-width: 320px;">Choose a message from the email list to view the full conversation thread, timezone conversion, and document attachments.</p>
            </div>

            <!-- Active Thread Reading View -->
            <div id="preview-pane" class="d-none d-flex h-100 flex-column overflow-hidden" style="min-height: 0;">
                <!-- Thread Header Toolbar -->
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-white">
                    <div class="d-inline-flex gap-1 flex-wrap">
                        <button class="btn btn-sm btn-primary border" onclick="replyCurrentThread('reply')" title="Reply to sender"><i class="bi bi-reply-fill me-1"></i> Reply</button>
                        <button class="btn btn-sm btn-light border" onclick="replyCurrentThread('reply_all')" title="Reply All"><i class="bi bi-reply-all-fill me-1"></i> Reply All</button>
                        <button class="btn btn-sm btn-light border me-1" onclick="replyCurrentThread('forward')" title="Forward Message"><i class="bi bi-forward-fill me-1"></i> Forward</button>

                        <button class="btn btn-sm btn-light border" onclick="moveActiveEmail('ARCHIVE')" title="Archive Mail"><i class="bi bi-archive"></i> Archive</button>
                        <button class="btn btn-sm btn-light border" onclick="moveActiveEmail('TRASH')" title="Delete Mail"><i class="bi bi-trash"></i> Delete</button>
                        <button class="btn btn-sm btn-light border" onclick="moveActiveEmail('SPAM')" title="Report Spam"><i class="bi bi-exclamation-triangle"></i> Spam</button>
                        
                        <div class="dropdown d-inline-block ms-1">
                            <button class="btn btn-sm btn-light border dropdown-toggle d-flex align-items-center gap-1" type="button" id="labelDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Assign Labels">
                                <i class="bi bi-tag"></i> Label
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="labelDropdown" id="thread-label-dropdown-menu" style="min-width: 180px; border-radius: 8px;">
                                <!-- Populate dynamically -->
                            </ul>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-1">
                        <button class="btn btn-sm btn-light border" onclick="toggleActiveStar()" id="preview-star-btn" title="Toggle Star"><i class="bi bi-star"></i></button>
                        <button class="btn btn-sm btn-light border" onclick="printActiveEmailThread()" title="Print Thread"><i class="bi bi-printer"></i></button>
                    </div>
                </div>

                <!-- Subject & Labels Bar -->
                <div class="px-4 pt-3 pb-2 bg-white">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <h5 class="fw-bold text-dark mb-1" id="preview-subject">Subject</h5>
                            <div id="preview-labels-container" class="d-flex flex-wrap gap-1 mt-1"></div>
                        </div>
                    </div>
                </div>

                <!-- Messages Stream Scroll Container -->
                <div class="flex-grow-1 overflow-y-auto custom-scrollbar px-4 py-2" id="preview-messages-container" style="min-height: 0; background-color: #f8fafc;">
                    <!-- Loaded dynamically -->
                </div>

                <!-- Embedded Quick Reply Box at bottom -->
                <div class="quick-reply-box p-3 bg-white border-top">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-bold text-dark"><i class="bi bi-reply-fill text-primary me-1"></i> Quick Reply</span>
                        <span class="small text-muted" id="quick-reply-save-status" style="font-size: 0.72rem;"></span>
                    </div>

                    <div class="border rounded-3 p-2 bg-white shadow-sm" id="quick-reply-dropzone">
                        <!-- WYSIWYG Format Toolbar -->
                        <div class="d-flex flex-wrap gap-1 border-bottom pb-1 mb-2">
                            <button type="button" class="wysiwyg-toolbar-btn" onclick="formatQuickReplyText('bold')" title="Bold"><i class="bi bi-type-bold"></i></button>
                            <button type="button" class="wysiwyg-toolbar-btn" onclick="formatQuickReplyText('italic')" title="Italic"><i class="bi bi-type-italic"></i></button>
                            <button type="button" class="wysiwyg-toolbar-btn" onclick="formatQuickReplyText('underline')" title="Underline"><i class="bi bi-type-underline"></i></button>
                            <button type="button" class="wysiwyg-toolbar-btn" onclick="formatQuickReplyText('insertUnorderedList')" title="Bullet List"><i class="bi bi-list-ul"></i></button>
                            <button type="button" class="wysiwyg-toolbar-btn" onclick="formatQuickReplyText('insertOrderedList')" title="Numbered List"><i class="bi bi-list-ol"></i></button>
                            <button type="button" class="wysiwyg-toolbar-btn" onclick="formatQuickReplyText('formatBlock', 'blockquote')" title="Quote"><i class="bi bi-quote"></i></button>
                            <button type="button" class="wysiwyg-toolbar-btn" onclick="formatQuickReplyText('removeFormat')" title="Clear Formatting"><i class="bi bi-trash3"></i></button>
                        </div>

                        <!-- Editable Content Area -->
                        <div class="form-control border-0 p-1" id="quick-reply-editor" contenteditable="true" style="min-height: 80px; max-height: 160px; overflow-y: auto; outline: none; font-size: 0.88rem; line-height: 1.5;" placeholder="Write your reply here..." oninput="triggerQuickReplyAutoSave()"></div>
                        
                        <!-- Attachments Upload List for Quick Reply -->
                        <div id="quick-reply-attachments-preview" class="d-flex flex-wrap gap-2 mt-2"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div class="d-flex align-items-center gap-2">
                            <label class="btn btn-xs btn-light border text-dark" title="Attach Files">
                                <i class="bi bi-paperclip me-1"></i> Attach
                                <input type="file" id="quick-reply-file-input" multiple onchange="handleQuickReplyFileSelect(event)" style="display: none;">
                            </label>
                            <button type="button" class="btn btn-xs btn-light border text-dark" onclick="popoutToFullCompose()" title="Pop out to Full Compose Window">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Pop-out Editor
                            </button>
                        </div>

                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-primary px-3 shadow-sm" onclick="sendQuickReply()" id="quick-reply-send-btn" style="border-radius: 6px; font-weight: 600;">
                                <i class="bi bi-send-fill me-1"></i> Send Reply
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mail Settings Modal -->
<div class="modal fade" id="emailSettingsModal" tabindex="-1" aria-labelledby="emailSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden; background: #ffffff;">
            <div class="modal-header border-0 px-4 pt-4 pb-2 d-flex justify-content-between align-items-center">
                <h5 class="modal-title fw-bold text-dark" id="emailSettingsModalLabel">Configure Email Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="emailSettingsForm" onsubmit="saveEmailSettings(event)">
                <div class="modal-body px-4 pb-4">
                    <p class="text-muted small mb-4">Set up your credentials below. For Gmail accounts, please use an App Password generated from your Google Account settings.</p>
                    
                    <div class="mb-3">
                        <label for="settings-email" class="form-label small fw-bold text-muted">Email Address</label>
                        <input type="email" class="form-control form-control-sm py-2 text-dark" id="settings-email" placeholder="e.g. name@gmail.com" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="settings-display-name" class="form-label small fw-bold text-muted">Display Name</label>
                        <input type="text" class="form-control form-control-sm py-2 text-dark" id="settings-display-name" placeholder="e.g. John Doe (Sales)" required>
                    </div>
                </div>
                
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-sm btn-light border px-3 text-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-3" id="save-settings-btn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Label Modal -->
<div class="modal fade" id="createLabelModal" tabindex="-1" aria-labelledby="createLabelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; background: #ffffff;">
            <div class="modal-header border-0 px-3 pt-3 pb-0 d-flex justify-content-between align-items-center">
                <h6 class="modal-title fw-bold text-dark" id="createLabelModalLabel">Create New Label</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createLabelForm" onsubmit="saveNewLabel(event)">
                <div class="modal-body px-3 pb-3">
                    <div class="mb-3">
                        <label for="label-name-input" class="form-label small fw-bold text-muted">Label Name</label>
                        <input type="text" class="form-control form-control-sm" id="label-name-input" placeholder="e.g. Work, Bills" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted d-block">Label Color</label>
                        <input type="color" class="form-control form-control-color border-0 p-0" id="label-color-input" value="#3b82f6" title="Choose color" style="width: 100%; height: 38px; border-radius: 6px; cursor: pointer;">
                    </div>
                </div>
                <div class="modal-footer border-0 px-3 pb-3 pt-0">
                    <button type="button" class="btn btn-xs btn-light border text-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-xs btn-primary px-3" id="save-label-btn">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Advanced Search Filter Modal -->
<div class="modal fade" id="advancedSearchModal" tabindex="-1" aria-labelledby="advancedSearchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; background: #ffffff;">
            <div class="modal-header border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <h6 class="modal-title fw-bold text-dark" id="advancedSearchModalLabel"><i class="bi bi-sliders me-2 text-primary"></i> Advanced Email Search</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="advancedSearchForm" onsubmit="executeAdvancedSearch(event)">
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">From Sender</label>
                            <input type="text" class="form-control form-control-sm" id="adv-search-from" placeholder="e.g. john@example.com">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">To Recipient</label>
                            <input type="text" class="form-control form-control-sm" id="adv-search-to" placeholder="e.g. sales@erp.local">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Subject Line</label>
                            <input type="text" class="form-control form-control-sm" id="adv-search-subject" placeholder="e.g. Quarterly Invoice">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Date From</label>
                            <input type="date" class="form-control form-control-sm" id="adv-search-date-from">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Date To</label>
                            <input type="date" class="form-control form-control-sm" id="adv-search-date-to">
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="adv-search-has-attachment">
                                <label class="form-check-label small text-dark fw-medium" for="adv-search-has-attachment">Only emails with attachments</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-2">
                    <button type="button" class="btn btn-sm btn-light border" onclick="resetAdvancedSearch()">Reset</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4">Search Emails</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Inline Attachment Preview Modal -->
<div class="modal fade" id="attachmentPreviewModal" tabindex="-1" aria-labelledby="attachmentPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; background: #ffffff;">
            <div class="modal-header border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="attachmentPreviewModalLabel">
                    <i class="bi bi-file-earmark-text text-primary"></i> <span id="attachment-preview-filename">Document</span>
                </h6>
                <div class="d-flex align-items-center gap-2">
                    <a href="#" id="attachment-download-btn" class="btn btn-sm btn-outline-primary" target="_blank"><i class="bi bi-download me-1"></i> Download</a>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 d-flex justify-content-center align-items-center bg-dark" style="min-height: 500px;" id="attachment-preview-body">
                <!-- Loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Connection / Sync Logs Modal -->
<div class="modal fade" id="syncLogsModal" tabindex="-1" aria-labelledby="syncLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden; background: #ffffff;">
            <div class="modal-header border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="syncLogsModalLabel">
                    <i class="bi bi-exclamation-triangle-fill text-danger"></i> Connection / Sync Failed
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="alert alert-danger d-flex align-items-center gap-2 py-2 px-3 mb-3 small" role="alert">
                    <i class="bi bi-x-circle-fill"></i>
                    <span id="sync-error-message">Failed to sync email account.</span>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold text-muted mb-1">IMAP Session Connection Logs</label>
                    <div class="bg-dark text-light p-3 rounded font-monospace small overflow-auto" id="sync-logs-container" style="max-height: 300px; white-space: pre-wrap; font-family: SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace; font-size: 0.8rem; line-height: 1.4;">
                        <!-- Logs populated dynamically -->
                    </div>
                </div>
                <div class="text-muted small" style="font-size: 0.75rem;">
                    <i class="bi bi-info-circle me-1"></i> For Google/Gmail accounts, verify that 2-Step Verification is enabled and you are using a 16-character <strong>App Password</strong> instead of your personal password.
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-2">
                <button type="button" class="btn btn-sm btn-secondary px-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>