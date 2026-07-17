{{-- Internal Chat Custom CSS --}}
<style>
    :root {
        --slack-sidebar-gradient: #ffffff;
        --slack-sidebar-hover-bg: #f0f2f5;
        --slack-sidebar-text: #111b21;
        --slack-sidebar-active-bg: #eae6df;
        --slack-sidebar-active-text: #111b21;
        --slack-border: #e9edef;
        --slack-active-green: #25d366;
        
        /* WhatsApp Theme Palette */
        --chat-bg-main: #efeae2; /* Classic WhatsApp Tiled Background */
        --chat-bg-bubble-in: #ffffff;
        --chat-bg-bubble-out: #d9fdd3;
        --chat-text-main: #111b21;
        --chat-text-muted: #667781;
        --chat-text-light: #8696a0;
    }

    .chat-container {
        display: flex;
        height: calc(100vh - 130px);
        background-color: var(--chat-bg-main) !important;
        color: var(--chat-text-main) !important;
        border: 1px solid var(--slack-border) !important;
        border-radius: 12px;
        overflow: hidden;
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    /* Left Sidebar - WhatsApp Style Chat List */
    .chat-sidebar {
        width: 340px;
        background: #ffffff !important;
        color: var(--chat-text-main) !important;
        display: flex;
        flex-direction: column;
        border-right: 1px solid var(--slack-border) !important;
        flex-shrink: 0;
    }

    .chat-sidebar-header {
        padding: 16px 20px;
        background-color: #f0f2f5;
        border-bottom: 1px solid var(--slack-border) !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .chat-sidebar-header h5 {
        font-size: 1.15rem;
        font-weight: 700;
        margin: 0;
        color: var(--chat-text-main) !important;
        letter-spacing: -0.3px;
    }

    .chat-sidebar-content {
        flex: 1;
        overflow-y: auto;
        padding: 0;
    }

    .sidebar-section {
        margin-bottom: 15px;
    }

    .sidebar-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px 6px;
        font-size: 0.78rem;
        font-weight: 700;
        color: #008069 !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .sidebar-section-header .add-btn {
        background: none;
        border: none;
        color: #008069 !important;
        cursor: pointer;
        padding: 0;
        font-size: 0.95rem;
        transition: color 0.15s;
    }

    .sidebar-section-header .add-btn:hover {
        color: #015c4b !important;
    }

    .sidebar-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    /* WhatsApp Chat List Item (Card style) */
    .sidebar-item-link {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: var(--chat-text-main) !important;
        text-decoration: none;
        transition: background 0.15s;
        cursor: pointer;
        border-radius: 0;
        border-bottom: 1px solid #f0f2f5;
    }

    .sidebar-item-link:hover {
        background-color: var(--slack-sidebar-hover-bg) !important;
    }

    .sidebar-item-link.active {
        background-color: var(--slack-sidebar-active-bg) !important;
    }

    /* WhatsApp Custom Elements inside sidebar link */
    .chat-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
        margin-right: 12px;
        color: #ffffff;
        flex-shrink: 0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .chat-details {
        flex-grow: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .chat-name-time {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 3px;
    }
    
    .chat-name {
        font-weight: 600;
        font-size: 0.92rem;
        color: var(--chat-text-main);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .chat-time {
        font-size: 0.72rem;
        color: var(--chat-text-muted);
        flex-shrink: 0;
    }
    
    .chat-msg-preview-unread {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .chat-msg-preview {
        font-size: 0.8rem;
        color: var(--chat-text-muted);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        flex-grow: 1;
        margin-right: 8px;
    }
    
    .chat-unread-badge {
        background-color: #25d366;
        color: #fff;
        font-size: 0.72rem;
        font-weight: 700;
        min-width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
        flex-shrink: 0;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        background-color: transparent;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 50%;
        margin-right: 8px;
        display: inline-block;
    }

    .status-dot.online {
        background-color: var(--slack-active-green) !important;
        border-color: var(--slack-active-green) !important;
    }

    /* Main Chat Stream */
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background-color: var(--chat-bg-main) !important;
        min-width: 0;
        position: relative;
    }

    .chat-header {
        height: 60px;
        background-color: #f0f2f5 !important;
        border-bottom: 1px solid var(--slack-border) !important;
        padding: 10px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03) !important;
    }

    .chat-header-info h6 {
        margin: 0;
        font-size: 0.98rem;
        font-weight: 700;
        color: var(--chat-text-main) !important;
    }

    .chat-header-info span {
        font-size: 0.75rem;
        color: var(--chat-text-muted) !important;
    }

    .chat-messages-scroll {
        flex: 1;
        overflow-y: auto;
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        background-color: var(--chat-bg-main) !important;
        /* Subtle Tiled Pattern Wallpaper */
        background-image: radial-gradient(rgba(0, 0, 0, 0.04) 1px, transparent 0), radial-gradient(rgba(0, 0, 0, 0.04) 1px, transparent 0);
        background-size: 24px 24px;
        background-position: 0 0, 12px 12px;
    }

    /* WhatsApp Styled Message Rows */
    .message-wrapper {
        display: flex;
        width: 100%;
        margin-bottom: 2px;
    }

    .message-wrapper.message-in {
        justify-content: flex-start;
    }

    .message-wrapper.message-out {
        justify-content: flex-end;
    }

    .message-bubble {
        position: relative;
        max-width: 65%;
        padding: 8px 12px 28px; /* space at bottom for timestamp status bar */
        border-radius: 8px;
        box-shadow: 0 1px 1px rgba(0,0,0,0.08) !important;
        font-size: 0.92rem;
        line-height: 1.45;
        word-break: break-word;
    }

    .message-wrapper.message-in .message-bubble {
        background-color: var(--chat-bg-bubble-in) !important;
        color: var(--chat-text-main) !important;
        border-top-left-radius: 0;
    }

    .message-wrapper.message-out .message-bubble {
        background-color: var(--chat-bg-bubble-out) !important;
        color: var(--chat-text-main) !important;
        border-top-right-radius: 0;
    }

    .message-text {
        color: inherit !important;
        white-space: pre-wrap;
    }

    .message-status-bar {
        position: absolute;
        bottom: 4px;
        right: 8px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .message-time {
        font-size: 0.68rem;
        color: #667781 !important;
    }

    .message-status {
        display: flex;
        align-items: center;
    }

    /* Message Actions Hover Overlay */
    .message-actions {
        position: absolute;
        right: 8px;
        top: 4px;
        background-color: #ffffff !important;
        border: 1px solid var(--slack-border) !important;
        border-radius: 6px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1) !important;
        display: none;
        padding: 2px;
        z-index: 10;
        gap: 2px;
    }

    .message-bubble:hover .message-actions {
        display: flex;
    }

    .action-btn {
        background: none;
        border: none;
        color: var(--chat-text-muted) !important;
        cursor: pointer;
        padding: 2px 6px;
        font-size: 0.75rem;
        border-radius: 4px;
        transition: background 0.15s;
        display: flex;
        align-items: center;
        gap: 3px;
    }

    .action-btn:hover {
        background-color: #f1f5f9 !important;
        color: var(--chat-text-main) !important;
    }

    .action-btn.delete-btn:hover {
        background-color: #fee2e2 !important;
        color: #ef4444 !important;
    }

    /* Thread reply trigger indicator */
    .thread-reply-indicator {
        display: inline-flex;
        align-items: center;
        margin-top: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #008069 !important;
        cursor: pointer;
        border-radius: 4px;
        padding: 2px 4px;
        transition: background-color 0.15s;
    }

    .thread-reply-indicator:hover {
        background-color: rgba(0, 128, 105, 0.08) !important;
        text-decoration: underline;
    }

    .thread-reply-indicator i {
        margin-right: 3px;
    }

    /* Chat Input Box */
    .chat-input-container {
        padding: 16px 24px;
        background-color: #ffffff !important;
        border-top: 1px solid var(--slack-border) !important;
        flex-shrink: 0;
    }

    .chat-input-box {
        border: 1px solid var(--slack-border) !important;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: border-color 0.15s;
        background-color: #ffffff !important;
    }

    .chat-input-box:focus-within {
        border-color: var(--slack-sidebar-active) !important;
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.15) !important;
    }

    .chat-textarea {
        border: none !important;
        resize: none;
        padding: 12px;
        font-size: 0.9rem;
        color: var(--chat-text-main) !important;
        background-color: #ffffff !important;
        outline: none;
        max-height: 120px;
        min-height: 44px;
    }

    .chat-input-toolbar {
        background-color: #f8fafc !important;
        border-top: 1px solid #e2e8f0 !important;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .toolbar-left {
        display: flex;
        gap: 6px;
    }

    .toolbar-btn {
        background: none;
        border: none;
        color: var(--chat-text-muted) !important;
        cursor: pointer;
        padding: 6px;
        font-size: 1.05rem;
        border-radius: 4px;
        transition: background 0.15s, color 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .toolbar-btn:hover {
        background-color: #cbd5e1 !important;
        color: var(--chat-text-main) !important;
    }

    .send-msg-btn {
        background-color: #dcfce7 !important; /* Tailwind green-100 */
        color: #15803d !important; /* Tailwind green-700 */
        border: 1px solid #bbf7d0 !important; /* Tailwind green-200 */
        border-radius: 4px;
        padding: 6px 12px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .send-msg-btn:hover {
        background-color: #bbf7d0 !important; /* Tailwind green-200 */
        color: #166534 !important; /* Tailwind green-800 */
        border-color: #86efac !important; /* Tailwind green-300 */
    }

    .send-msg-btn:disabled {
        background-color: #f1f5f9 !important;
        color: #94a3b8 !important;
        border-color: #e2e8f0 !important;
        cursor: not-allowed;
    }

    /* Attachments Preview Area */
    .attachments-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 8px 12px;
        background-color: #ffffff !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }

    .preview-chip {
        display: flex;
        align-items: center;
        background-color: #f1f5f9 !important;
        border: 1px solid var(--slack-border) !important;
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 0.78rem;
        color: var(--chat-text-muted) !important;
        max-width: 200px;
    }

    .preview-chip-name {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-right: 6px;
    }

    .preview-chip-remove {
        cursor: pointer;
        color: #94a3b8 !important;
        font-size: 0.9rem;
    }

    .preview-chip-remove:hover {
        color: #ef4444 !important;
    }

    /* Inline attachments render */
    .message-attachments {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-top: 8px;
    }

    .attachment-card {
        display: flex;
        align-items: center;
        background-color: #f8fafc !important;
        border: 1px solid var(--slack-border) !important;
        border-radius: 6px;
        padding: 8px 12px;
        max-width: 320px;
        text-decoration: none;
        transition: background-color 0.15s;
    }

    .attachment-card:hover {
        background-color: #f1f5f9 !important;
    }

    .attachment-icon {
        font-size: 1.4rem;
        color: #3b82f6 !important;
        margin-right: 12px;
        display: flex;
        align-items: center;
    }

    .attachment-info {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .attachment-name {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--chat-text-main) !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .attachment-size {
        font-size: 0.72rem;
        color: var(--chat-text-light) !important;
    }

    .attachment-image-preview {
        max-width: 280px;
        max-height: 200px;
        border-radius: 6px;
        border: 1px solid var(--slack-border) !important;
        overflow: hidden;
        margin-top: 6px;
        cursor: pointer;
    }

    .attachment-image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Thread Sidebar Pane */
    .chat-thread-sidebar {
        width: 340px;
        background-color: #ffffff !important;
        border-left: 1px solid var(--slack-border) !important;
        display: none;
        flex-direction: column;
        flex-shrink: 0;
    }

    .thread-header {
        height: 60px;
        background-color: #ffffff !important;
        border-bottom: 1px solid var(--slack-border) !important;
        padding: 10px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }

    .thread-header h6 {
        margin: 0;
        font-weight: 700;
        color: var(--chat-text-main) !important;
    }

    .thread-close-btn {
        background: none;
        border: none;
        color: var(--chat-text-muted) !important;
        cursor: pointer;
        font-size: 1.1rem;
        padding: 4px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .thread-close-btn:hover {
        background-color: #f1f5f9 !important;
        color: var(--chat-text-main) !important;
    }

    .thread-content-scroll {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        background-color: #ffffff !important;
    }

    /* Parent Message Highlight inside Thread Sidebar */
    .thread-parent-message {
        border-bottom: 1px solid var(--slack-border) !important;
        padding-bottom: 16px;
        margin-bottom: 4px;
    }

    .replies-divider {
        display: flex;
        align-items: center;
        font-size: 0.72rem;
        color: var(--chat-text-light) !important;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 8px 0;
    }

    .replies-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background-color: #e2e8f0;
        margin-left: 10px;
    }

    .thread-input-container {
        padding: 12px 20px 16px 20px;
        border-top: 1px solid var(--slack-border) !important;
        background-color: #ffffff !important;
    }

    /* Admin Settings Dashboard View */
    .admin-dashboard {
        flex: 1;
        display: none;
        flex-direction: column;
        background-color: #f8fafc !important;
        min-width: 0;
        overflow-y: auto;
        color: #1e293b !important;
    }

    .admin-dashboard,
    .admin-dashboard * {
        color: #1e293b;
    }

    .admin-header {
        height: 60px;
        background-color: #ffffff !important;
        border-bottom: 1px solid var(--slack-border) !important;
        padding: 10px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }

    .admin-header h6 {
        margin: 0;
        font-weight: 700;
        color: #0f172a !important;
    }

    .admin-content {
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .admin-card {
        background-color: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
    }

    .admin-card-title {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a !important;
        border-bottom: 2px solid #6366f1;
        padding-bottom: 8px;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .admin-card-title span {
        color: #0f172a !important;
    }

    .admin-dashboard select, 
    .admin-dashboard input {
        color: #0f172a !important;
        background-color: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
    }

    .admin-dashboard select option {
        color: #0f172a !important;
        background-color: #ffffff !important;
    }

    .admin-dashboard table th {
        color: #334155 !important;
        background-color: #f1f5f9 !important;
        font-weight: 700;
    }

    .admin-dashboard table td {
        color: #1e293b !important;
    }

    .admin-dashboard .text-muted {
        color: #64748b !important;
    }

    /* Loading placeholder skeleton */
    .chat-loading-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex: 1;
        color: var(--chat-text-muted) !important;
        padding: 40px;
        text-align: center;
        background-color: var(--chat-bg-main) !important;
    }

    .chat-loading-placeholder i {
        font-size: 2.5rem;
        margin-bottom: 16px;
        color: #cbd5e1 !important;
    }

    .chat-loading-placeholder h5 {
        color: var(--chat-text-main) !important;
        font-weight: 700;
    }

    /* Modal customizations */
    .slack-modal .modal-content {
        border-radius: 8px;
        border: none;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        background-color: #ffffff !important;
        color: var(--chat-text-main) !important;
    }

    .slack-modal .modal-header {
        border-bottom: 1px solid var(--slack-border) !important;
        padding: 16px 20px;
        color: var(--chat-text-main) !important;
    }

    .slack-modal .modal-body {
        padding: 20px;
    }

    .slack-modal .modal-footer {
        border-top: 1px solid var(--slack-border) !important;
        padding: 12px 20px;
    }

    /* Custom form elements */
    .slack-form-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--chat-text-muted) !important;
        margin-bottom: 6px;
    }

    .slack-form-control {
        border: 1px solid var(--slack-border) !important;
        border-radius: 6px;
        font-size: 0.88rem;
        padding: 8px 12px;
        color: var(--chat-text-main) !important;
        background-color: #ffffff !important;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .slack-form-control:focus {
        border-color: var(--slack-sidebar-active) !important;
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.15) !important;
        outline: none;
    }

    /* User item badges */
    .user-chip {
        display: inline-flex;
        align-items: center;
        background-color: #f1f5f9 !important;
        border-radius: 4px;
        padding: 2px 8px;
        font-size: 0.75rem;
        color: var(--chat-text-muted) !important;
        font-weight: 500;
        gap: 4px;
    }

    .user-chip-remove {
        cursor: pointer;
        color: #94a3b8 !important;
    }

    .user-chip-remove:hover {
        color: #ef4444 !important;
    }

    /* Clean up any potential white text in button elements */
    .admin-dashboard .btn-primary,
    .slack-modal .btn-primary {
        background-color: #e0e7ff !important;
        color: #3730a3 !important;
        border: 1px solid #c7d2fe !important;
        font-weight: 600 !important;
    }
    .admin-dashboard .btn-primary:hover,
    .slack-modal .btn-primary:hover {
        background-color: #c7d2fe !important;
        color: #1e1b4b !important;
        border-color: #a5b4fc !important;
    }
    .admin-dashboard .btn-outline-secondary,
    .slack-modal .btn-light {
        color: #475569 !important;
        border-color: #cbd5e1 !important;
        background-color: #f8fafc !important;
    }
    .admin-dashboard .btn-outline-secondary:hover,
    .slack-modal .btn-light:hover {
        background-color: #e2e8f0 !important;
        color: #0f172a !important;
    }
    .admin-dashboard .btn-danger {
        background-color: #fee2e2 !important;
        color: #ef4444 !important;
        border: 1px solid #fca5a5 !important;
    }
    .admin-dashboard .btn-danger:hover {
        background-color: #fca5a5 !important;
        color: #991b1b !important;
    }
</style>
