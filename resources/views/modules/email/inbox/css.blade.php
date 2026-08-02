{{-- Email Inbox Modern Apple Mail / Thunderbird Client CSS --}}
<style>
    :root {
        --email-sidebar-bg: #f8fafc;
        --email-border-color: #e2e8f0;
        --email-active-bg: #eff6ff;
        --email-active-text: #2563eb;
        --email-unread-bg: #ffffff;
        --email-read-bg: #f8fafc;
        --email-hover-bg: #f1f5f9;
        --email-accent: #3b82f6;
        --email-star-color: #eab308;
    }

    /* Layout & Pane Rules */
    .email-app-container {
        height: calc(100vh - 120px);
        min-height: 550px;
        overflow: hidden;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
    }

    .email-sidebar-pane {
        background-color: var(--email-sidebar-bg);
        border-right: 1px solid var(--email-border-color);
        user-select: none;
    }

    .email-list-pane {
        border-right: 1px solid var(--email-border-color);
        background-color: #ffffff;
    }

    .email-reader-pane {
        background-color: #ffffff;
    }

    /* Custom Thin Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Navigation Folder Items */
    .email-folder-item {
        color: #475569;
        border: none;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.15s ease-in-out;
        margin-bottom: 2px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-decoration: none;
    }
    .email-folder-item:hover {
        background-color: #e2e8f0;
        color: #0f172a;
    }
    .email-folder-item.active {
        background-color: var(--email-active-bg);
        color: var(--email-active-text);
        font-weight: 600;
    }
    .email-folder-item .badge {
        font-size: 0.72rem;
        font-weight: 600;
        padding: 3px 8px;
    }

    /* Drag & Drop Hover Target Highlight */
    .drag-over-folder {
        background-color: #dbeafe !important;
        border: 2px dashed #3b82f6 !important;
        transform: scale(1.02);
    }
    .drag-over-dropzone {
        background-color: #f0f9ff !important;
        border-color: #0284c7 !important;
        box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.15);
    }

    /* Quick Filter Pills */
    .filter-pill {
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        padding: 4px 12px;
        border: 1px solid var(--email-border-color);
        background: #ffffff;
        color: #64748b;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
        white-space: nowrap;
    }
    .filter-pill:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
    .filter-pill.active {
        background: #1e293b;
        color: #ffffff;
        border-color: #1e293b;
    }

    /* Email List Items Cards (Apple Mail Style) */
    .email-card-item {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: background-color 0.12s ease-in-out, transform 0.1s ease;
        position: relative;
        user-select: none;
    }
    .email-card-item:hover {
        background-color: var(--email-hover-bg);
    }
    .email-card-item.selected {
        background-color: #eff6ff !important;
        border-left: 3px solid #3b82f6;
    }
    .email-card-item.unread {
        background-color: #ffffff;
        font-weight: 600;
    }
    .email-card-item.unread .email-subject {
        color: #0f172a;
        font-weight: 700;
    }
    .email-card-item.read {
        background-color: #f8fafc;
    }
    .email-card-item.read .email-subject {
        color: #334155;
        font-weight: 500;
    }

    /* Unread Accent Dot */
    .unread-indicator-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #3b82f6;
        display: inline-block;
        margin-right: 6px;
    }

    /* Avatar Initial Badge */
    .avatar-initial {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 700;
        color: #ffffff;
        text-transform: uppercase;
        flex-shrink: 0;
        box-shadow: 0 2px 5px rgba(0,0,0,0.08);
    }

    /* Star Toggle */
    .star-toggle-btn {
        color: #cbd5e1;
        cursor: pointer;
        font-size: 1rem;
        transition: color 0.15s ease, transform 0.15s ease;
    }
    .star-toggle-btn:hover {
        transform: scale(1.15);
        color: var(--email-star-color);
    }
    .star-toggle-btn.starred {
        color: var(--email-star-color);
    }

    /* Floating Bulk Actions Bar */
    .bulk-actions-toolbar {
        background: #1e293b;
        color: #ffffff;
        border-radius: 10px;
        padding: 6px 14px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
        animation: slideInUp 0.2s ease-out forwards;
    }

    @keyframes slideInUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Label Pills */
    .email-label-badge {
        font-size: 0.68rem;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        letter-spacing: 0.2px;
    }

    /* HTML Reader Frame */
    .email-iframe-container {
        width: 100%;
        border: none;
        min-height: 250px;
        background: #ffffff;
    }

    /* Embedded Quick Reply Box */
    .quick-reply-box {
        border-top: 1px solid var(--email-border-color);
        background: #ffffff;
    }
    .wysiwyg-toolbar-btn {
        padding: 3px 8px;
        font-size: 0.8rem;
        border: none;
        background: transparent;
        color: #475569;
        border-radius: 4px;
    }
    .wysiwyg-toolbar-btn:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    /* Labels Sidebar Delete Hover */
    #label-list .list-group-item:hover .label-delete-btn {
        visibility: visible !important;
    }
    .label-delete-btn:hover {
        color: #ef4444 !important;
    }
</style>
