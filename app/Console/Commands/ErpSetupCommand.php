<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ErpSetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'erp:install {--force : Force database migration and seed without prompts}';

    /**
     * Aliases for the command.
     *
     * @var array
     */
    protected $aliases = ['erp:setup'];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Complete 1-command ERP installer (DB creation, migration, dummy data seeding, and login credentials)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=================================================");
        $this->info("      🚀 MS ERP - ONE-COMMAND INSTALLER         ");
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
        $this->info("⚙ Running database migrations and seeding dynamic ERP metadata & dummy accounts...");
        $this->call('migrate:fresh', [
            '--force' => true,
            '--seed' => true
        ]);

        // Step 5: Render Setup Complete Dashboard & Login Credentials
        $this->newLine();
        $this->info("========================================================================");
        $this->info("🎉 ERP INSTALLED & READY TO USE - FULLY CONFIDENTIAL & DUMMY SEEDED 🎉");
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
        $this->line("<fg=cyan;options=bold>TEMPLATE EMAIL ACCOUNTS SEEDED (Update under Email > Settings):</>");
        $this->table(
            ['Type', 'Dummy Address', 'SMTP Host', 'IMAP Host'],
            [
                ['Gmail Account', 'demo.user@gmail.com', 'smtp.gmail.com:587 (TLS)', 'imap.gmail.com:993 (SSL)'],
                ['Custom Mail Server', 'user@mserp.in', 'mail.mserp.in:587 (TLS)', 'mail.mserp.in:993 (SSL)']
            ]
        );

        $this->newLine();
        $this->info("📌 TO START THE APP:");
        $this->line("   Run: <fg=yellow>php artisan serve</>");
        $this->line("   Open: <fg=green>http://127.0.0.1:8000</>");
        $this->info("========================================================================");

        return 0;
    }
}
