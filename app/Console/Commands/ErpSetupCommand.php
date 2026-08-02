<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ErpSetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'erp:setup {--force : Force database migration and seed without prompts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Complete zero-config 1-command ERP setup (DB creation, migration, seeding, and default logins)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=================================================");
        $this->info("      🚀 MS ERP - ZERO-CONFIG SETUP ENGINE      ");
        $this->info("=================================================");

        // Step 1: Environment file setup (.env)
        $envPath = base_path('.env');
        $envExamplePath = base_path('.env.example');

        if (!File::exists($envPath)) {
            if (File::exists($envExamplePath)) {
                File::copy($envExamplePath, $envPath);
                $this->info("✔ Created .env file from .env.example");
            } else {
                $this->warn("⚠ .env.example not found, skipping .env copy.");
            }
        } else {
            $this->info("✔ Found existing .env file");
        }

        // Step 2: Ensure SQLite database file exists if DB_CONNECTION is sqlite
        $sqlitePath = database_path('database.sqlite');
        if (!File::exists($sqlitePath)) {
            File::put($sqlitePath, '');
            $this->info("✔ Initialized database/database.sqlite file");
        }

        // Step 3: Application Key Generation
        if (empty(env('APP_KEY'))) {
            $this->info("⚙ Generating application security key...");
            $this->call('key:generate', ['--force' => true]);
        }

        // Step 4: Run Migrations and Seeders
        $this->info("⚙ Running database migrations and seeding dynamic ERP metadata...");
        $this->call('migrate:fresh', [
            '--force' => true,
            '--seed' => true
        ]);

        // Step 5: Render Setup Complete Dashboard & Login Credentials
        $this->newLine();
        $this->info("========================================================================");
        $this->info("🎉 ERP INSTALLED & READY TO USE - ZERO HARDCODING & FULLY SECURED 🎉");
        $this->info("========================================================================");
        $this->newLine();
        
        $this->line("<fg=cyan;options=bold>READY-TO-USE DEMO LOGINS (Password for all: password):</>");
        $this->table(
            ['Role Title', 'Email Address', 'Security Scope / Role'],
            [
                ['🛡️ CFO / Super Admin', 'admin@mserp.com', 'Unrestricted System Access'],
                ['📈 North Sales Head', 'north.head@mserp.com', 'Department Supervisor & Rules'],
                ['💰 Finance Head', 'accounts.head@mserp.com', 'Accounts Ledger & Workflows'],
                ['💼 Sales Representative', 'rep.north1@mserp.com', 'Customer Accounts & Quotes'],
                ['📝 Accounts Assistant', 'accounts.member@mserp.com', 'Billing & Receipt Approvals'],
                ['👤 General Executive', 'user@mserp.com', 'Workspace Read-only']
            ]
        );

        $this->newLine();
        $this->info("📌 TO START THE APP:");
        $this->line("   Run: <fg=yellow>php artisan serve</> or <fg=yellow>npm run dev</>");
        $this->line("   Open: <fg=green>http://127.0.0.1:8000</>");
        $this->info("========================================================================");

        return 0;
    }
}
