<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DynamicCrudController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    if (Auth::check()) {
        return redirect('/');
    }
    return view('login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // Check if user exists and is inactive
    $user = DB::table('users')->where('email', $credentials['email'])->first();
    if ($user && !$user->is_active) {
        return back()->withErrors([
            'email' => 'Your account has been deactivated. Please contact your system administrator.',
        ])->onlyInput('email');
    }

    // Force is_active to be 1 (true) for attempt
    $credentials['is_active'] = 1;

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        
        $accountId = DB::table('email_account_users')->where('user_id', Auth::id())->value('email_account_id');
        if ($accountId) {
            session(['active_email_account_id' => $accountId]);
        }

        return redirect()->intended('/');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
});

Route::get('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
});

/*
|--------------------------------------------------------------------------
| Authenticated ERP System Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Main workspace
    Route::get('/', [DashboardController::class, 'index']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    // User context endpoints
    Route::get('/api/user/context', [DashboardController::class, 'getUserContext']);
    Route::post('/api/user/switch-context', [DashboardController::class, 'switchContext']);

    // Dynamic metadata-driven CRUD core routes
    Route::get('/erp/{slug}', [DynamicCrudController::class, 'index']);
    Route::get('/erp/api/{slug}/data', [DynamicCrudController::class, 'getData']);
    Route::post('/erp/api/{slug}/store', [DynamicCrudController::class, 'store']);
    Route::post('/erp/api/{slug}/update/{id}', [DynamicCrudController::class, 'update']);
    Route::delete('/erp/api/{slug}/destroy/{id}', [DynamicCrudController::class, 'destroy']);
    Route::get('/erp/api/{slug}/export', [DynamicCrudController::class, 'export']);
    Route::post('/erp/api/{slug}/import', [DynamicCrudController::class, 'import']);

    // Global search route
    Route::get('/erp/search', function (Request $request) {
        $q = $request->query('q');
        // Search across customer and invoice tables
        $customers = DB::table('customers')
            ->where('name', 'like', "%{$q}%")
            ->orWhere('email', 'like', "%{$q}%")
            ->limit(5)->get();

        $invoices = DB::table('sales_invoices')
            ->where('invoice_no', 'like', "%{$q}%")
            ->limit(5)->get();

        $inventory = DB::table('inventory_items')
            ->where('name', 'like', "%{$q}%")
            ->orWhere('item_code', 'like', "%{$q}%")
            ->limit(5)->get();

        return view('search_results', compact('q', 'customers', 'invoices', 'inventory'));
    });

    // Workflows action endpoints
    Route::post('/api/workflow/approve/{instance_id}', [DynamicCrudController::class, 'approveWorkflow']);
    Route::post('/api/workflow/reject/{instance_id}', [DynamicCrudController::class, 'rejectWorkflow']);

    // Notifications AJAX endpoints
    Route::get('/api/notifications', function () {
        $user = Auth::user();
        $items = DB::table('notifications')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $item->time_ago = \Carbon\Carbon::parse($item->created_at)->diffForHumans(null, true) . ' ago';
                return $item;
            });
        $unreadCount = DB::table('notifications')
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        // Get active unread broadcast
        $pendingBroadcast = \App\Services\NotificationService::getPendingBroadcast($user);

        return response()->json([
            'items' => $items, 
            'unread_count' => $unreadCount,
            'pending_broadcast' => $pendingBroadcast
        ]);
    });

    Route::post('/api/notifications/read/{id}', function ($id) {
        DB::table('notifications')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->update(['is_read' => true]);
        return response()->json(['success' => true]);
    });

    Route::post('/api/notifications/read-all', function () {
        DB::table('notifications')
            ->where('user_id', Auth::id())
            ->update(['is_read' => true]);
        return response()->json(['success' => true]);
    });

    Route::post('/api/notifications/test-send', function (Request $request) {
        $request->validate([
            'sender_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);
        
        $receivers = DB::table('notification_routes')
            ->join('users', 'notification_routes.receiver_id', '=', 'users.id')
            ->where('notification_routes.sender_id', $request->sender_id)
            ->where('notification_routes.is_active', true)
            ->select('users.id', 'users.name')
            ->get();
            
        foreach ($receivers as $receiver) {
            \App\Services\NotificationService::sendDirect(
                $receiver->id,
                $request->title,
                $request->message,
                'SYSTEM'
            );
        }
        
        return response()->json([
            'success' => true,
            'receivers' => $receivers->pluck('name')->toArray()
        ]);
    });

    Route::post('/api/notifications/broadcast/acknowledge/{id}', function ($id) {
        DB::table('broadcast_read_receipts')->insert([
            'broadcast_id' => $id,
            'user_id' => Auth::id(),
            'created_at' => now(),
        ]);
        return response()->json(['success' => true]);
    });

    Route::post('/api/profile/change-password', function (Request $request) {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:4|confirmed',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Current password matches incorrectly.'], 422);
        }

        DB::table('users')->where('id', $user->id)->update([
            'password' => Hash::make($request->new_password),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Password credentials updated successfully.']);
    });

    // Developer Module Studio Endpoints
    Route::middleware(['admin'])->group(function () {
        Route::get('/developer-module', [\App\Http\Controllers\DeveloperModuleController::class, 'index']);
        Route::post('/api/developer/analyze-query', [\App\Http\Controllers\DeveloperModuleController::class, 'analyzeQuery']);
        Route::post('/api/developer/generate-page', [\App\Http\Controllers\DeveloperModuleController::class, 'generatePage']);
    });

    // Admin-only Notification Routing Configuration APIs
    Route::middleware(['admin'])->group(function () {
        Route::get('/api/notification-routes/{userId}', function ($userId) {
            $outgoing = DB::table('notification_routes')
                ->where('sender_id', $userId)
                ->pluck('receiver_id');
            $incoming = DB::table('notification_routes')
                ->where('receiver_id', $userId)
                ->pluck('sender_id');
            return response()->json([
                'outgoing' => $outgoing,
                'incoming' => $incoming
            ]);
        });
        Route::post('/api/notification-routes/update', function (Request $request) {
            $request->validate([
                'user_id' => 'required|integer',
                'target_user_id' => 'required|integer',
                'direction' => 'required|string', // incoming, outgoing
                'value' => 'required|boolean'
            ]);
            $userId = $request->user_id;
            $targetId = $request->target_user_id;
            $direction = $request->direction;
            $val = $request->value;
            $senderId = ($direction === 'outgoing') ? $userId : $targetId;
            $receiverId = ($direction === 'outgoing') ? $targetId : $userId;
            if ($val) {
                DB::table('notification_routes')->updateOrInsert(
                    ['sender_id' => $senderId, 'receiver_id' => $receiverId],
                    ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]
                );
            } else {
                DB::table('notification_routes')
                    ->where('sender_id', $senderId)
                    ->where('receiver_id', $receiverId)
                    ->delete();
            }
            return response()->json(['success' => true]);
        });
        Route::post('/api/notification-routes/copy', function (Request $request) {
            $request->validate([
                'source_user_id' => 'required|integer',
                'target_user_id' => 'required|integer',
            ]);
            $sourceId = $request->source_user_id;
            $targetId = $request->target_user_id;
            if ($sourceId === $targetId) {
                return response()->json(['error' => 'Source and target users must be different.'], 400);
            }
            // Copy outgoing rules (where sender is source)
            $outgoingRules = DB::table('notification_routes')
                ->where('sender_id', $sourceId)
                ->get();

            foreach ($outgoingRules as $rule) {
                if ($rule->receiver_id == $targetId) continue;
                DB::table('notification_routes')->updateOrInsert(
                    ['sender_id' => $targetId, 'receiver_id' => $rule->receiver_id],
                    ['is_active' => $rule->is_active, 'created_at' => now(), 'updated_at' => now()]
                );
            }

            // Copy incoming rules (where receiver is source)
            $incomingRules = DB::table('notification_routes')
                ->where('receiver_id', $sourceId)
                ->get();

            foreach ($incomingRules as $rule) {
                if ($rule->sender_id == $targetId) continue;
                DB::table('notification_routes')->updateOrInsert(
                    ['sender_id' => $rule->sender_id, 'receiver_id' => $targetId],
                    ['is_active' => $rule->is_active, 'created_at' => now(), 'updated_at' => now()]
                );
            }

            return response()->json(['success' => true]);
        });
    });

    // Email client module routes
    Route::get('/email/inbox', [EmailController::class, 'inbox']);
    Route::get('/email/contacts', [EmailController::class, 'contacts']);
    Route::get('/email/compose', [EmailController::class, 'compose']);
    
    Route::get('/api/email/list', [EmailController::class, 'getEmailList']);
    Route::get('/api/email/thread/{thread_id}', [EmailController::class, 'getThread']);
    Route::post('/api/email/send', [EmailController::class, 'send']);
    Route::post('/api/email/save-draft', [EmailController::class, 'saveDraft']);
    Route::post('/api/email/toggle-star/{id}', [EmailController::class, 'toggleStar']);
    Route::post('/api/email/move-folder/{id}', [EmailController::class, 'moveFolder']);
    Route::post('/api/email/sync/{id}', [EmailController::class, 'sync']);
    Route::match(['get', 'post'], '/api/email/auto-sync', [EmailController::class, 'autoSync']);
    Route::post('/api/email/toggle-live-sync', [EmailController::class, 'toggleLiveSync']);
    Route::post('/api/email/switch-account', [EmailController::class, 'switchAccount']);
    Route::post('/api/email/contacts/store', [EmailController::class, 'storeContact']);
    Route::post('/api/email/contacts/delete/{id}', [EmailController::class, 'deleteContact']);
    Route::get('/api/email/folder-counts', [EmailController::class, 'getFolderCounts']);
    Route::post('/api/email/bulk-action', [EmailController::class, 'bulkAction']);
    Route::get('/api/email/labels', [EmailController::class, 'getLabels']);
    Route::post('/api/email/labels', [EmailController::class, 'storeLabel']);
    Route::post('/api/email/apply-label', [EmailController::class, 'applyLabel']);
    Route::delete('/api/email/label/{id}', [EmailController::class, 'deleteLabel']);
    Route::get('/api/email/attachment/{id}', [EmailController::class, 'getAttachment']);
    Route::get('/api/email/settings', [EmailController::class, 'getSettings']);
    Route::post('/api/email/settings', [EmailController::class, 'updateSettings']);

    // Email Accounts Administrative endpoints
    Route::get('/api/email-accounts', [EmailController::class, 'getEmailAccounts']);
    Route::post('/api/email-accounts/store', [EmailController::class, 'storeEmailAccount']);
    Route::delete('/api/email-accounts/delete/{id}', [EmailController::class, 'deleteEmailAccount']);
    Route::get('/api/email-accounts/lookup-config', [EmailController::class, 'lookupServerConfig']);
    Route::post('/api/email-accounts/test-connection', [EmailController::class, 'testAccountConnection']);

    // Dynamic Report Builder routes
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/api/reports/columns/{table}', [ReportController::class, 'getColumns']);
    Route::post('/api/reports/generate', [ReportController::class, 'generate']);
    Route::post('/api/reports/save', [ReportController::class, 'save']);
    Route::post('/api/reports/delete/{id}', [ReportController::class, 'destroy']);

    // Internal Chat system routes
    Route::get('/api/chat/context', [ChatController::class, 'getChannelsAndContacts']);
    Route::get('/api/chat/users/search', [ChatController::class, 'searchUsers']);
    Route::get('/api/chat/messages', [ChatController::class, 'getMessages']);
    Route::post('/api/chat/send', [ChatController::class, 'sendMessage']);
    Route::delete('/api/chat/delete/{id}', [ChatController::class, 'deleteMessage']);
    Route::post('/api/chat/forward', [ChatController::class, 'forwardMessage']);
    Route::get('/api/chat/thread/{id}', [ChatController::class, 'getThreadReplies']);

    // Admin-only Staff and Permission Matrix Management APIs
    Route::middleware(['admin'])->group(function () {
        // Dynamic Permission matrix update route
        Route::post('/api/permissions/update', function (Request $request) {
            $roleId = $request->input('role_id');
            $pageId = $request->input('page_id');
            $action = $request->input('action'); // can_view, can_create, can_edit, etc.
            $val = (bool) $request->input('value');

            // Check if permission mapping already exists
            $exists = DB::table('role_permissions')
                ->where('role_id', $roleId)
                ->where('page_id', $pageId)
                ->exists();

            if ($exists) {
                DB::table('role_permissions')
                    ->where('role_id', $roleId)
                    ->where('page_id', $pageId)
                    ->update([
                        $action => $val,
                        'updated_at' => now()
                    ]);
            } else {
                DB::table('role_permissions')->insert([
                    'role_id' => $roleId,
                    'page_id' => $pageId,
                    $action => $val,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return response()->json(['success' => true]);
        });

        // Page Page-Token Update Route
        Route::post('/api/pages/update-token', [DynamicCrudController::class, 'updatePageToken']);

        // Custom User Management API Routes
        Route::post('/api/users/store', function (Request $request) {
            $data = $request->validate([
                'name' => 'required|string|max:255|unique:users,name',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:4',
                'role_id' => 'required|integer',
                'reports_to_id' => 'nullable|integer',
                'is_active' => 'required|boolean',
                'can_send_to_anyone' => 'nullable|boolean',
            ]);

            $data['can_send_to_anyone'] = $request->boolean('can_send_to_anyone') ? 1 : 0;
            $admin = Auth::user();
            $data['password'] = Hash::make($data['password']);
            $data['company_id'] = $admin->company_id;
            $data['branch_id'] = $admin->branch_id;
            $data['department_id'] = $admin->department_id;
            $data['created_at'] = now();
            $data['updated_at'] = now();

            $userId = DB::table('users')->insertGetId($data);

            // Optionally copy permissions from another user if source_user_id is provided
            $sourceUserId = $request->input('source_user_id');
            if ($sourceUserId) {
                $sourcePerms = DB::table('user_permissions')->where('user_id', $sourceUserId)->get();
                if ($sourcePerms->isNotEmpty()) {
                    foreach ($sourcePerms as $sp) {
                        DB::table('user_permissions')->insert([
                            'user_id' => $userId,
                            'page_id' => $sp->page_id,
                            'can_view' => $sp->can_view,
                            'can_create' => $sp->can_create,
                            'can_edit' => $sp->can_edit,
                            'can_delete' => $sp->can_delete,
                            'can_export' => $sp->can_export,
                            'can_print' => $sp->can_print,
                            'can_approve' => $sp->can_approve,
                            'can_reject' => $sp->can_reject,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } else {
                    // Fall back to role permissions
                    $rolePerms = DB::table('role_permissions')->where('role_id', $data['role_id'])->get();
                    foreach ($rolePerms as $rp) {
                        DB::table('user_permissions')->insert([
                            'user_id' => $userId,
                            'page_id' => $rp->page_id,
                            'can_view' => $rp->can_view,
                            'can_create' => $rp->can_create,
                            'can_edit' => $rp->can_edit,
                            'can_delete' => $rp->can_delete,
                            'can_export' => $rp->can_export,
                            'can_print' => $rp->can_print,
                            'can_approve' => $rp->can_approve,
                            'can_reject' => $rp->can_reject,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            } else {
                // Populate default user permissions from role permissions
                $rolePerms = DB::table('role_permissions')->where('role_id', $data['role_id'])->get();
                foreach ($rolePerms as $rp) {
                    DB::table('user_permissions')->insert([
                        'user_id' => $userId,
                        'page_id' => $rp->page_id,
                        'can_view' => $rp->can_view,
                        'can_create' => $rp->can_create,
                        'can_edit' => $rp->can_edit,
                        'can_delete' => $rp->can_delete,
                        'can_export' => $rp->can_export,
                        'can_print' => $rp->can_print,
                        'can_approve' => $rp->can_approve,
                        'can_reject' => $rp->can_reject,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            return response()->json(['success' => true, 'user_id' => $userId]);
        });

        Route::post('/api/users/update/{id}', function (Request $request, $id) {
            $data = $request->validate([
                'name' => 'required|string|max:255|unique:users,name,' . $id,
                'email' => 'required|email|unique:users,email,' . $id,
                'role_id' => 'required|integer',
                'reports_to_id' => 'nullable|integer',
                'is_active' => 'required|boolean',
                'can_send_to_anyone' => 'nullable|boolean',
            ]);

            $data['can_send_to_anyone'] = $request->boolean('can_send_to_anyone') ? 1 : 0;
            $data['updated_at'] = now();

            DB::table('users')->where('id', $id)->update($data);

            return response()->json(['success' => true]);
        });

        Route::post('/api/users/change-password/{id}', function (Request $request, $id) {
            $data = $request->validate([
                'password' => 'required|string|min:4',
            ]);

            DB::table('users')->where('id', $id)->update([
                'password' => Hash::make($data['password']),
                'updated_at' => now()
            ]);

            return response()->json(['success' => true]);
        });

        Route::get('/api/users/{id}/permissions', function ($id) {
            $user = DB::table('users')->where('id', $id)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $userPerms = DB::table('user_permissions')->where('user_id', $id)->get()->keyBy('page_id');

            if ($userPerms->isEmpty()) {
                // Populate defaults from role
                $rolePerms = DB::table('role_permissions')->where('role_id', $user->role_id)->get();
                foreach ($rolePerms as $rp) {
                    DB::table('user_permissions')->insert([
                        'user_id' => $id,
                        'page_id' => $rp->page_id,
                        'can_view' => $rp->can_view,
                        'can_create' => $rp->can_create,
                        'can_edit' => $rp->can_edit,
                        'can_delete' => $rp->can_delete,
                        'can_export' => $rp->can_export,
                        'can_print' => $rp->can_print,
                        'can_approve' => $rp->can_approve,
                        'can_reject' => $rp->can_reject,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                $userPerms = DB::table('user_permissions')->where('user_id', $id)->get()->keyBy('page_id');
            }

            return response()->json(['permissions' => $userPerms]);
        });

        Route::post('/api/users/permissions/update', function (Request $request) {
            $userId = $request->input('user_id');
            $pageId = $request->input('page_id');
            $action = $request->input('action'); // can_view, can_create, can_edit, etc.
            $val = (bool) $request->input('value');

            // Check if permission mapping already exists
            $exists = DB::table('user_permissions')
                ->where('user_id', $userId)
                ->where('page_id', $pageId)
                ->exists();

            if ($exists) {
                DB::table('user_permissions')
                    ->where('user_id', $userId)
                    ->where('page_id', $pageId)
                    ->update([
                        $action => $val,
                        'updated_at' => now()
                    ]);
            } else {
                DB::table('user_permissions')->insert([
                    'user_id' => $userId,
                    'page_id' => $pageId,
                    $action => $val,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return response()->json(['success' => true]);
        });

        Route::post('/api/users/permissions/clone', function (Request $request) {
            $targetUserId = $request->input('target_user_id');
            $sourceUserId = $request->input('source_user_id');

            if (!$targetUserId || !$sourceUserId) {
                return response()->json(['error' => 'Invalid source or target user ID'], 400);
            }

            // Delete existing user permissions for target
            DB::table('user_permissions')->where('user_id', $targetUserId)->delete();

            // Copy from source
            $sourcePerms = DB::table('user_permissions')->where('user_id', $sourceUserId)->get();
            foreach ($sourcePerms as $sp) {
                DB::table('user_permissions')->insert([
                    'user_id' => $targetUserId,
                    'page_id' => $sp->page_id,
                    'can_view' => $sp->can_view,
                    'can_create' => $sp->can_create,
                    'can_edit' => $sp->can_edit,
                    'can_delete' => $sp->can_delete,
                    'can_export' => $sp->can_export,
                    'can_print' => $sp->can_print,
                    'can_approve' => $sp->can_approve,
                    'can_reject' => $sp->can_reject,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json(['success' => true]);
        });

        Route::get('/api/my-sales-reps', function () {
            $user = Auth::user();
            if (!$user) return response()->json([], 401);
            
            $role = DB::table('roles')->where('id', $user->role_id)->first();
            $isSuperOrAdmin = $role && in_array($role->slug, ['super-admin', 'admin']);
            
            $query = DB::table('users')
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.slug', 'sales-rep')
                ->where('users.is_active', true);
                
            if (!$isSuperOrAdmin) {
                // For a Sales Head, only list Sales Reps reporting directly or indirectly to them
                $subordinateIds = app(App\Repositories\DynamicCrudRepository::class)->getSubordinateUserIds($user->id);
                $query->whereIn('users.id', $subordinateIds);
            }
            
            $reps = $query->select('users.id', 'users.name', 'users.email')->get();
            return response()->json($reps);
        });

        Route::get('/api/customers/{id}/shares', function ($id) {
            $shares = DB::table('customer_shares')
                ->where('customer_id', $id)
                ->pluck('user_id');
            $customer = DB::table('customers')->where('id', $id)->first();
            return response()->json([
                'shared_user_ids' => $shares,
                'share_with_everyone' => (bool)($customer->share_with_everyone ?? false)
            ]);
        });

        // Chat admin config routes
        Route::get('/api/chat/admin/directory', [ChatController::class, 'getAdminDirectory']);
        Route::post('/api/chat/admin/groups', [ChatController::class, 'createChannel']);
        Route::post('/api/chat/admin/rules', [ChatController::class, 'addChatRule']);
        Route::delete('/api/chat/admin/rules/{id}', [ChatController::class, 'removeChatRule']);
        Route::post('/api/chat/admin/delete-permission', [ChatController::class, 'toggleDeletePermission']);
    });

    /*
    |--------------------------------------------------------------------------
    | GST Invoicing & Billing Module Routes
    |--------------------------------------------------------------------------
    */
    /*
    |--------------------------------------------------------------------------
    | GST Invoicing & Billing Module Routes (PO, SO, Bills & Invoices)
    |--------------------------------------------------------------------------
    */
    Route::get('/api/billing/items', function () {
        $user = Auth::user();
        $items = DB::table('inventory_items')
            ->where('company_id', $user->company_id)
            ->where('status', '!=', 'Out of Stock')
            ->get();
        return response()->json($items);
    });

    Route::get('/api/billing/contacts/{docType}', function ($docType) {
        $user = Auth::user();
        $isPurchase = in_array($docType, ['purchase-orders', 'purchase-invoices']);
        if ($isPurchase) {
            $vendors = DB::table('vendors')
                ->where('company_id', $user->company_id)
                ->where('status', 'Active')
                ->get();
            return response()->json($vendors);
        } else {
            $customers = DB::table('customers')
                ->where('company_id', $user->company_id)
                ->where('status', 'Active')
                ->get();
            return response()->json($customers);
        }
    });

    Route::get('/api/billing/{docType}/data', function ($docType) {
        $user = Auth::user();
        
        $config = [
            'sales-invoices' => ['table' => 'sales_invoices', 'join' => 'customers', 'join_key' => 'customer_id', 'no' => 'invoice_no'],
            'sales-orders' => ['table' => 'sales_orders', 'join' => 'customers', 'join_key' => 'customer_id', 'no' => 'order_no'],
            'purchase-orders' => ['table' => 'purchase_orders', 'join' => 'vendors', 'join_key' => 'vendor_id', 'no' => 'po_no'],
            'purchase-invoices' => ['table' => 'purchase_invoices', 'join' => 'vendors', 'join_key' => 'vendor_id', 'no' => 'bill_no'],
            'sales-quotations' => ['table' => 'sales_quotations', 'join' => 'customers', 'join_key' => 'customer_id', 'no' => 'quote_no'],
        ];

        if (!isset($config[$docType])) {
            return response()->json(['error' => 'Invalid document type'], 404);
        }

        $cfg = $config[$docType];
        $dateCol = ($docType === 'purchase-invoices') ? 'bill_date' : (($docType === 'sales-orders') ? 'order_date' : (($docType === 'purchase-orders') ? 'po_date' : (($docType === 'sales-quotations') ? 'quote_date' : 'invoice_date')));
        
        $records = DB::table($cfg['table'])
            ->leftJoin($cfg['join'], "{$cfg['table']}.{$cfg['join_key']}", '=', "{$cfg['join']}.id")
            ->where("{$cfg['table']}.company_id", $user->company_id)
            ->select(
                "{$cfg['table']}.*", 
                "{$cfg['join']}.name as contact_name",
                "{$cfg['join']}.state as contact_state",
                "{$cfg['join']}.gstin as contact_gstin",
                "{$cfg['table']}.{$cfg['no']} as document_no",
                "{$cfg['table']}.{$dateCol} as document_date"
            )
            ->orderBy("{$cfg['table']}.created_at", 'desc')
            ->get();

        return response()->json(['data' => $records]);
    });

    Route::get('/api/billing/{docType}/invoice/{id}', function ($docType, $id) {
        $user = Auth::user();
        
        $config = [
            'sales-invoices' => ['table' => 'sales_invoices', 'items_table' => 'sales_invoice_items', 'fk' => 'sales_invoice_id', 'join' => 'customers', 'join_key' => 'customer_id'],
            'sales-orders' => ['table' => 'sales_orders', 'items_table' => 'sales_order_items', 'fk' => 'sales_order_id', 'join' => 'customers', 'join_key' => 'customer_id'],
            'purchase-orders' => ['table' => 'purchase_orders', 'items_table' => 'purchase_order_items', 'fk' => 'purchase_order_id', 'join' => 'vendors', 'join_key' => 'vendor_id'],
            'purchase-invoices' => ['table' => 'purchase_invoices', 'items_table' => 'purchase_invoice_items', 'fk' => 'purchase_invoice_id', 'join' => 'vendors', 'join_key' => 'vendor_id'],
            'sales-quotations' => ['table' => 'sales_quotations', 'items_table' => 'sales_quotation_items', 'fk' => 'sales_quotation_id', 'join' => 'customers', 'join_key' => 'customer_id'],
        ];

        if (!isset($config[$docType])) {
            return response()->json(['error' => 'Invalid document type'], 404);
        }

        $cfg = $config[$docType];
        $invoice = DB::table($cfg['table'])
            ->where('id', $id)
            ->where('company_id', $user->company_id)
            ->first();
        
        if (!$invoice) {
            return response()->json(['error' => 'Document not found'], 404);
        }

        $contact = DB::table($cfg['join'])->where('id', $invoice->{$cfg['join_key']})->first();
        
        $items = DB::table($cfg['items_table'])
            ->join('inventory_items', "{$cfg['items_table']}.inventory_item_id", '=', 'inventory_items.id')
            ->where("{$cfg['items_table']}.{$cfg['fk']}", $id)
            ->select("{$cfg['items_table']}.*", 'inventory_items.name as item_name')
            ->get();

        return response()->json([
            'invoice' => $invoice,
            'contact' => $contact,
            'items' => $items
        ]);
    });

    Route::post('/api/billing/{docType}/invoice/store', function (Request $request, $docType) {
        $user = Auth::user();
        $data = $request->validate([
            'contact_id' => 'required|integer',
            'document_date' => 'required|date',
            'due_date' => 'required|date',
            'billing_address' => 'required|string',
            'payment_terms' => 'nullable|string',
            'items' => 'required|array',
            'items.*.inventory_item_id' => 'required|integer',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $config = [
            'sales-invoices' => ['table' => 'sales_invoices', 'items_table' => 'sales_invoice_items', 'fk' => 'sales_invoice_id', 'join' => 'customers', 'join_key' => 'customer_id', 'no' => 'invoice_no', 'prefix' => 'INV'],
            'sales-orders' => ['table' => 'sales_orders', 'items_table' => 'sales_order_items', 'fk' => 'sales_order_id', 'join' => 'customers', 'join_key' => 'customer_id', 'no' => 'order_no', 'prefix' => 'SO'],
            'purchase-orders' => ['table' => 'purchase_orders', 'items_table' => 'purchase_order_items', 'fk' => 'purchase_order_id', 'join' => 'vendors', 'join_key' => 'vendor_id', 'no' => 'po_no', 'prefix' => 'PO'],
            'purchase-invoices' => ['table' => 'purchase_invoices', 'items_table' => 'purchase_invoice_items', 'fk' => 'purchase_invoice_id', 'join' => 'vendors', 'join_key' => 'vendor_id', 'no' => 'bill_no', 'prefix' => 'BILL'],
            'sales-quotations' => ['table' => 'sales_quotations', 'items_table' => 'sales_quotation_items', 'fk' => 'sales_quotation_id', 'join' => 'customers', 'join_key' => 'customer_id', 'no' => 'quote_no', 'prefix' => 'QTN'],
        ];

        if (!isset($config[$docType])) {
            return response()->json(['error' => 'Invalid document type'], 404);
        }

        $cfg = $config[$docType];

        // Generate document number
        $year = date('Y', strtotime($data['document_date']));
        $count = DB::table($cfg['table'])->whereYear('created_at', $year)->count();
        $docNo = $cfg['prefix'] . '/' . $year . '/' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        // Retrieve company and contact
        $company = DB::table('companies')->where('id', $user->company_id)->first();
        $contact = DB::table($cfg['join'])->where('id', $data['contact_id'])->first();

        if (!$company || !$contact) {
            return response()->json(['error' => 'Company or Contact not found'], 422);
        }

        // Determine transaction tax (Intra-state vs Inter-state)
        $companyState = trim(strtolower($company->state ?? 'maharashtra'));
        $contactState = trim(strtolower($contact->state ?? 'maharashtra'));
        $isIntraState = ($companyState === $contactState);

        $subtotalAmt = 0;
        $cgstAmtTotal = 0;
        $sgstAmtTotal = 0;
        $igstAmtTotal = 0;
        $grandTotalAmt = 0;

        $lineItemsToSave = [];

        try {
            return DB::transaction(function () use ($user, $data, $docNo, $docType, $isIntraState, $cfg, &$lineItemsToSave, &$subtotalAmt, &$cgstAmtTotal, &$sgstAmtTotal, &$igstAmtTotal, &$grandTotalAmt) {
                foreach ($data['items'] as $itemInput) {
                    $invItem = DB::table('inventory_items')
                        ->where('id', $itemInput['inventory_item_id'])
                        ->first();

                    if (!$invItem) {
                        throw new \Exception('Product item not found.');
                    }

                    // Stock operations check
                    if ($docType === 'sales-invoices') {
                        if ($invItem->qty_on_hand < $itemInput['qty']) {
                            throw new \Exception("Insufficient stock for item: {$invItem->name}. Available stock: {$invItem->qty_on_hand}.");
                        }
                    }

                    $lineSubtotal = $itemInput['qty'] * $itemInput['unit_price'];
                    $taxRate = $invItem->tax_rate ?? 18.00;

                    $cgstRate = 0.00;
                    $cgstAmt = 0.00;
                    $sgstRate = 0.00;
                    $sgstAmt = 0.00;
                    $igstRate = 0.00;
                    $igstAmt = 0.00;

                    if ($isIntraState) {
                       $cgstRate = $taxRate / 2;
                       $cgstAmt = round(($lineSubtotal * $cgstRate) / 100, 2);
                       $sgstRate = $taxRate / 2;
                       $sgstAmt = round(($lineSubtotal * $sgstRate) / 100, 2);
                    } else {
                       $igstRate = $taxRate;
                       $igstAmt = round(($lineSubtotal * $igstRate) / 100, 2);
                    }

                    $lineTotal = $lineSubtotal + $cgstAmt + $sgstAmt + $igstAmt;

                    // Stock adjustments
                    if ($docType === 'sales-invoices') {
                        // Decrement stock
                        $newQty = $invItem->qty_on_hand - $itemInput['qty'];
                        $status = 'In Stock';
                        if ($newQty <= 0) {
                            $status = 'Out of Stock';
                        } else if ($newQty <= $invItem->reorder_level) {
                            $status = 'Low Stock';
                        }
                        DB::table('inventory_items')->where('id', $invItem->id)->update([
                            'qty_on_hand' => $newQty,
                            'status' => $status,
                            'updated_at' => now()
                        ]);
                    } else if ($docType === 'purchase-invoices') {
                        // Increment stock
                        $newQty = $invItem->qty_on_hand + $itemInput['qty'];
                        $status = 'In Stock';
                        if ($newQty <= $invItem->reorder_level) {
                            $status = 'Low Stock';
                        }
                        DB::table('inventory_items')->where('id', $invItem->id)->update([
                            'qty_on_hand' => $newQty,
                            'status' => $status,
                            'updated_at' => now()
                        ]);
                    }

                    $lineItemsToSave[] = [
                        'inventory_item_id' => $invItem->id,
                        'hsn_sac' => $invItem->hsn_sac,
                        'qty' => $itemInput['qty'],
                        'unit_price' => $itemInput['unit_price'],
                        'subtotal' => $lineSubtotal,
                        'cgst_rate' => $cgstRate,
                        'cgst_amount' => $cgstAmt,
                        'sgst_rate' => $sgstRate,
                        'sgst_amount' => $sgstAmt,
                        'igst_rate' => $igstRate,
                        'igst_amount' => $igstAmt,
                        'total_amount' => $lineTotal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $subtotalAmt += $lineSubtotal;
                    $cgstAmtTotal += $cgstAmt;
                    $sgstAmtTotal += $sgstAmt;
                    $igstAmtTotal += $igstAmt;
                    $grandTotalAmt += $lineTotal;
                }

                $dateCol = ($docType === 'purchase-invoices') ? 'bill_date' : (($docType === 'sales-orders') ? 'order_date' : (($docType === 'purchase-orders') ? 'po_date' : (($docType === 'sales-quotations') ? 'quote_date' : 'invoice_date')));

                $headerData = [
                    'company_id' => $user->company_id,
                    'branch_id' => $user->branch_id,
                    $cfg['no'] => $docNo,
                    $cfg['join_key'] => $data['contact_id'],
                    $dateCol => $data['document_date'],
                    'amount' => $subtotalAmt,
                    'tax' => $cgstAmtTotal + $sgstAmtTotal + $igstAmtTotal,
                    'cgst' => $cgstAmtTotal,
                    'sgst' => $sgstAmtTotal,
                    'igst' => $igstAmtTotal,
                    'discount' => 0.00,
                    'total_amount' => $grandTotalAmt,
                    'billing_address' => $data['billing_address'],
                    'payment_terms' => $data['payment_terms'],
                    'status' => 'Approved',
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                if ($docType === 'sales-orders' || $docType === 'purchase-orders') {
                    $headerData['delivery_date'] = $data['due_date'];
                    $headerData['shipping_address'] = $data['billing_address'];
                    $headerData['status'] = 'Sent';
                } else if ($docType === 'sales-quotations') {
                    $headerData['valid_until'] = $data['due_date'];
                    $headerData['status'] = 'Sent';
                } else {
                    $headerData['due_date'] = $data['due_date'];
                }

                // Insert header record
                $docId = DB::table($cfg['table'])->insertGetId($headerData);

                // Insert line items
                foreach ($lineItemsToSave as &$line) {
                    $line[$cfg['fk']] = $docId;
                    DB::table($cfg['items_table'])->insert($line);
                }

                return response()->json(['success' => true, 'invoice_id' => $docId, 'invoice_no' => $docNo]);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    });

    Route::delete('/api/billing/{docType}/invoice/destroy/{id}', function ($docType, $id) {
        $user = Auth::user();
        
        $config = [
            'sales-invoices' => ['table' => 'sales_invoices', 'items_table' => 'sales_invoice_items', 'fk' => 'sales_invoice_id'],
            'sales-orders' => ['table' => 'sales_orders', 'items_table' => 'sales_order_items', 'fk' => 'sales_order_id'],
            'purchase-orders' => ['table' => 'purchase_orders', 'items_table' => 'purchase_order_items', 'fk' => 'purchase_order_id'],
            'purchase-invoices' => ['table' => 'purchase_invoices', 'items_table' => 'purchase_invoice_items', 'fk' => 'purchase_invoice_id'],
            'sales-quotations' => ['table' => 'sales_quotations', 'items_table' => 'sales_quotation_items', 'fk' => 'sales_quotation_id'],
        ];

        if (!isset($config[$docType])) {
            return response()->json(['error' => 'Invalid document type'], 404);
        }

        $cfg = $config[$docType];
        $invoice = DB::table($cfg['table'])
            ->where('id', $id)
            ->where('company_id', $user->company_id)
            ->first();

        if (!$invoice) {
            return response()->json(['error' => 'Document not found'], 404);
        }

        try {
            return DB::transaction(function () use ($id, $docType, $cfg, $invoice) {
                // Restore stock levels
                $items = DB::table($cfg['items_table'])
                    ->where($cfg['fk'], $id)
                    ->get();

                foreach ($items as $item) {
                    $invItem = DB::table('inventory_items')->where('id', $item->inventory_item_id)->first();
                    if ($invItem) {
                        $newQty = $invItem->qty_on_hand;
                        if ($docType === 'sales-invoices') {
                            $newQty = $invItem->qty_on_hand + $item->qty; // restore subtracted
                        } else if ($docType === 'purchase-invoices') {
                            $newQty = $invItem->qty_on_hand - $item->qty; // reverse added
                        }

                        $status = 'In Stock';
                        if ($newQty <= 0) {
                            $status = 'Out of Stock';
                        } else if ($newQty <= $invItem->reorder_level) {
                            $status = 'Low Stock';
                        }

                        DB::table('inventory_items')
                            ->where('id', $invItem->id)
                            ->update([
                                'qty_on_hand' => $newQty,
                                'status' => $status,
                                'updated_at' => now()
                            ]);
                    }
                }

                // Delete child items and parent header
                DB::table($cfg['items_table'])->where($cfg['fk'], $id)->delete();
                DB::table($cfg['table'])->where('id', $id)->delete();

                return response()->json(['success' => true]);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    });

    Route::post('/api/billing/{docType}/invoice/pay/{id}', function ($docType, $id) {
        $user = Auth::user();
        $table = str_replace('-', '_', $docType);
        if ($docType === 'purchase-invoices') {
            $table = 'purchase_invoices';
        }
        
        $status = in_array($docType, ['sales-orders', 'purchase-orders']) ? 'Closed' : (($docType === 'sales-quotations') ? 'Accepted' : 'Paid');

        DB::table($table)
            ->where('id', $id)
            ->where('company_id', $user->company_id)
            ->update(['status' => $status, 'updated_at' => now()]);

        return response()->json(['success' => true]);
    });

    Route::get('/erp/{docType}/print/{id}', function ($docType, $id) {
        $user = Auth::user();
        
        $config = [
            'sales-invoices' => ['table' => 'sales_invoices', 'items_table' => 'sales_invoice_items', 'fk' => 'sales_invoice_id', 'join' => 'customers', 'join_key' => 'customer_id', 'no' => 'invoice_no', 'title' => 'Tax Invoice'],
            'sales-orders' => ['table' => 'sales_orders', 'items_table' => 'sales_order_items', 'fk' => 'sales_order_id', 'join' => 'customers', 'join_key' => 'customer_id', 'no' => 'order_no', 'title' => 'Sales Order Confirmation'],
            'purchase-orders' => ['table' => 'purchase_orders', 'items_table' => 'purchase_order_items', 'fk' => 'purchase_order_id', 'join' => 'vendors', 'join_key' => 'vendor_id', 'no' => 'po_no', 'title' => 'Purchase Order'],
            'purchase-invoices' => ['table' => 'purchase_invoices', 'items_table' => 'purchase_invoice_items', 'fk' => 'purchase_invoice_id', 'join' => 'vendors', 'join_key' => 'vendor_id', 'no' => 'bill_no', 'title' => 'Vendor Bill'],
            'sales-quotations' => ['table' => 'sales_quotations', 'items_table' => 'sales_quotation_items', 'fk' => 'sales_quotation_id', 'join' => 'customers', 'join_key' => 'customer_id', 'no' => 'quote_no', 'title' => 'Sales Quotation'],
        ];

        if (!isset($config[$docType])) {
            abort(404, 'Invalid document type.');
        }

        $cfg = $config[$docType];
        $invoice = DB::table($cfg['table'])
            ->where('id', $id)
            ->where('company_id', $user->company_id)
            ->first();

        if (!$invoice) {
            abort(404, 'Document not found.');
        }

        $company = DB::table('companies')->where('id', $invoice->company_id)->first();
        $contact = DB::table($cfg['join'])->where('id', $invoice->{$cfg['join_key']})->first();
        
        $items = DB::table($cfg['items_table'])
            ->join('inventory_items', "{$cfg['items_table']}.inventory_item_id", '=', 'inventory_items.id')
            ->where("{$cfg['items_table']}.{$cfg['fk']}", $id)
            ->select("{$cfg['items_table']}.*", 'inventory_items.name as item_name')
            ->get();

        // Convert total amount to words helper function
        $numberToWords = function ($number) {
            $decimal = round($number - ($no = floor($number)), 2) * 100;
            $hundred = null;
            $digits_length = strlen($no);
            $i = 0;
            $str = array();
            $words = array(
                0 => '', 1 => 'One', 2 => 'Two',
                3 => 'Three', 4 => 'Four', 5 => 'Five',
                6 => 'Six', 7 => 'Seven', 8 => 'Eight',
                9 => 'Nine', 10 => 'Ten', 11 => 'Eleven',
                12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
                15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen',
                18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty',
                30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty',
                60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty',
                90 => 'Ninety'
            );
            $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
            while( $i < $digits_length ) {
                $divider = ($i == 2) ? 10 : 100;
                $number = floor($no % $divider);
                $no = floor($no / $divider);
                $i += $divider == 10 ? 1 : 2;
                if ($number) {
                    $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                    $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                    $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter].$plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
                } else $str[] = null;
            }
            $Rupees = implode('', array_reverse($str));
            $Paise = ($decimal > 0) ? "." . ($words[$decimal] ?? $words[floor($decimal / 10) * 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
            return ($Rupees ? $Rupees . 'Rupees ' : '') . $Paise . ' Only';
        };

        $totalInWords = $numberToWords($invoice->total_amount);
        $docTitle = $cfg['title'];
        $docNoKey = $cfg['no'];

        return view('modules.sales_billing.print', compact('invoice', 'company', 'contact', 'items', 'totalInWords', 'docTitle', 'docNoKey', 'docType'));
    });
});

if (file_exists(__DIR__ . '/generated_modules.php')) {
    require __DIR__ . '/generated_modules.php';
}

