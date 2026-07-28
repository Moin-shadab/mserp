<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\Email\EmailSyncService;

class SyncEmailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:sync {--account= : Specific Email Account ID to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize active IMAP email accounts into the ERP database';

    /**
     * Execute the console command.
     */
    public function handle(EmailSyncService $syncService)
    {
        $specificAccountId = $this->option('account');

        if ($specificAccountId) {
            $accounts = DB::table('email_accounts')
                ->where('id', $specificAccountId)
                ->where('is_active', true)
                ->get();
        } else {
            $accounts = DB::table('email_accounts')
                ->where('is_active', true)
                ->get();
        }

        if ($accounts->isEmpty()) {
            $this->info('No active email accounts found to sync.');
            return 0;
        }

        $this->info("Starting email auto-sync for " . $accounts->count() . " active account(s)...");

        foreach ($accounts as $account) {
            $lockKey = "email_sync_account_{$account->id}";
            $lock = Cache::lock($lockKey, 60);

            if (!$lock->get()) {
                $this->warn("Account #{$account->id} ({$account->email}) is currently being synced by another process. Skipping.");
                continue;
            }

            try {
                $this->line("Syncing account #{$account->id} ({$account->email})...");
                $result = $syncService->syncAccount($account->id);

                if (!empty($result['success'])) {
                    $msg = $result['message'] ?? 'Sync completed.';
                    $this->info("  -> {$account->email}: {$msg}");
                } else {
                    $err = $result['message'] ?? 'Sync failed.';
                    $this->error("  -> {$account->email} Error: {$err}");
                }
            } catch (\Exception $e) {
                Log::error("Command email:sync error for account #{$account->id}: " . $e->getMessage());
                $this->error("  -> Exception for account #{$account->id}: " . $e->getMessage());
            } finally {
                $lock->release();
            }
        }

        $this->info("Email auto-sync completed.");
        return 0;
    }
}
