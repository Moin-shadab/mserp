<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use PDO;

class ErpSetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'erp:install 
        {--force : Force database migration and seed without interactive prompts}
        {--db= : Database driver (mysql|sqlite)}
        {--host= : Database host}
        {--port= : Database port}
        {--database= : Database name}
        {--username= : Database username}
        {--password= : Database password}';

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
    protected $description = 'Interactive 1-command ERP installer (Auto DB creation, .env configuration, migration, and seeder)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=================================================");
        $this->info("   🚀 MS ERP - AUTOMATED 1-COMMAND INSTALLER     ");
        $this->info("=================================================");

        // Step 1: Environment file setup (.env)
        $envPath = base_path('.env');
        $envExamplePath = base_path('.env.example');

        if (!File::exists($envPath)) {
            if (File::exists($envExamplePath)) {
                File::copy($envExamplePath, $envPath);
                $this->info("✔ Created .env file from .env.example");
            } else {
                $this->warn("⚠ .env.example not found.");
            }
        } else {
            $this->info("✔ Found existing .env file");
        }

        $force = $this->option('force');
        $dbDriver = $this->option('db') ?: 'mysql';
        $dbHost = $this->option('host') ?: '127.0.0.1';
        $dbPort = $this->option('port') ?: '3306';
        $dbName = $this->option('database') ?: 'mserp';
        $dbUser = $this->option('username') ?: 'root';
        $dbPass = $this->option('password') ?: '';

        // Step 2: Interactive Database Configuration (If not forced)
        if (!$force && $this->input->isInteractive()) {
            $dbDriver = $this->choice('Select Database Engine', ['mysql' => 'MySQL / MariaDB (Recommended)', 'sqlite' => 'SQLite'], 'mysql');

            if ($dbDriver === 'mysql') {
                $dbHost = $this->ask('MySQL Host', '127.0.0.1');
                $dbPort = $this->ask('MySQL Port', '3306');
                $dbName = $this->ask('Database Name', 'mserp');
                $dbUser = $this->ask('MySQL Username', 'root');
                $dbPass = $this->secret('MySQL Password (press Enter if empty)', '') ?? '';
            }
        }

        // Step 3: Update .env file dynamically
        $envUpdates = [
            'DB_CONNECTION' => $dbDriver,
        ];

        if ($dbDriver === 'mysql') {
            $envUpdates['DB_HOST'] = $dbHost;
            $envUpdates['DB_PORT'] = $dbPort;
            $envUpdates['DB_DATABASE'] = $dbName;
            $envUpdates['DB_USERNAME'] = $dbUser;
            $envUpdates['DB_PASSWORD'] = $dbPass;

            // Auto-create MySQL Database if it does not exist
            $this->info("⚙ Connecting to MySQL server and creating database '{$dbName}' if missing...");
            try {
                $pdo = new PDO("mysql:host={$dbHost};port={$dbPort}", $dbUser, $dbPass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
                $this->info("✔ MySQL Database '{$dbName}' is ready.");
            } catch (\Exception $e) {
                $this->error("❌ Could not connect to MySQL server or create database: " . $e->getMessage());
                $this->line("Please check your MySQL host, port, username, and password in .env");
                return 1;
            }

            // Rebind runtime config
            config([
                'database.default' => 'mysql',
                'database.connections.mysql.host' => $dbHost,
                'database.connections.mysql.port' => $dbPort,
                'database.connections.mysql.database' => $dbName,
                'database.connections.mysql.username' => $dbUser,
                'database.connections.mysql.password' => $dbPass,
            ]);
            DB::purge('mysql');
            DB::reconnect('mysql');
        } else {
            $sqlitePath = database_path('database.sqlite');
            if (!File::exists($sqlitePath)) {
                File::put($sqlitePath, '');
                $this->info("✔ Created database/database.sqlite file.");
            }
            $envUpdates['DB_DATABASE'] = $sqlitePath;
            config([
                'database.default' => 'sqlite',
                'database.connections.sqlite.database' => $sqlitePath,
            ]);
            DB::purge('sqlite');
            DB::reconnect('sqlite');
        }

        $this->updateEnvValues($envUpdates);

        // Step 4: Generate Application Key
        if (empty(config('app.key')) || config('app.key') === 'base64:') {
            $this->info("⚙ Generating application security key...");
            $this->call('key:generate', ['--force' => true]);
        }

        // Step 5: Run Migrations and Seeders
        $this->info("⚙ Running database migrations & seeding dynamic ERP metadata and demo records...");
        $this->call('migrate:fresh', [
            '--force' => true,
            '--seed' => true,
        ]);

        // Step 6: Render Setup Complete Dashboard & Login Credentials
        $this->newLine();
        $this->info("========================================================================");
        $this->info("🎉 ERP INSTALLED & READY TO USE - FULLY PROVISIONED & SEEDED 🎉");
        $this->info("========================================================================");
        $this->newLine();

        $this->line("<fg=cyan;options=bold>DEMO ACCOUNTS SEEDED (Password for all: password):</>");
        $this->table(
            ['Role Title', 'Email Address', 'Security Scope / Access'],
            [
                ['🛡️ CFO / Super Admin', 'admin@mserp.com', 'Unrestricted System Access'],
                ['📈 North Sales Head', 'north.head@mserp.com', 'Department Supervisor'],
                ['💰 Finance Head', 'accounts.head@mserp.com', 'Accounts Ledger & Approvals'],
                ['💼 Sales Representative', 'rep.north1@mserp.com', 'Quotations & Invoices'],
                ['📝 Accounts Member', 'accounts.member@mserp.com', 'Billing & Receipt Approvals'],
                ['👤 General User', 'user@mserp.com', 'Workspace Read-only']
            ]
        );

        $this->newLine();
        $this->info("📌 TO START THE ERP SYSTEM:");
        $this->line("   Run: <fg=yellow>php artisan serve</>");
        $this->line("   Open: <fg=green>http://127.0.0.1:8000</>");
        $this->info("========================================================================");

        return 0;
    }

    /**
     * Helper to update values in .env file dynamically.
     */
    protected function updateEnvValues(array $values): void
    {
        $envPath = base_path('.env');
        if (!File::exists($envPath)) {
            return;
        }

        $content = File::get($envPath);

        foreach ($values as $key => $value) {
            $formattedValue = (str_contains($value, ' ') && !str_starts_with($value, '"')) ? '"' . $value . '"' : $value;
            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$formattedValue}", $content);
            } else {
                $content .= "\n{$key}={$formattedValue}";
            }
        }

        File::put($envPath, $content);
    }
}
