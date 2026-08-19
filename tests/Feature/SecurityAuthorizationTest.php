<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Services\SqlSecurityAnalyzer;
use Illuminate\Support\Facades\DB;

class SecurityAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\ComprehensiveErpSeeder::class);

        $roleId = DB::table('roles')->value('id');
        if (!$roleId) {
            $roleId = DB::table('roles')->insertGetId([
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Super Administrator',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->adminUser = User::where('email', 'admin@mserp.com')->first() ?? User::factory()->create([
            'email' => 'admin@mserp.com',
            'role_id' => $roleId
        ]);
    }

    /**
     * Test SqlSecurityAnalyzer blocks destructive queries (DROP, DELETE, TRUNCATE, multi-statements).
     */
    public function test_sql_security_analyzer_rejects_destructive_queries(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        SqlSecurityAnalyzer::validateSelectQuery("SELECT * FROM users; DROP TABLE users;");
    }

    public function test_sql_security_analyzer_rejects_delete_queries(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        SqlSecurityAnalyzer::validateSelectQuery("DELETE FROM sales_invoices");
    }

    public function test_sql_security_analyzer_accepts_valid_select_queries(): void
    {
        $result = SqlSecurityAnalyzer::validateSelectQuery("SELECT id, name FROM customers WHERE is_active = 1");
        $this->assertTrue($result);
    }

    /**
     * Test live system health endpoint returns actual operational metrics.
     */
    public function test_system_health_endpoint_returns_live_diagnostics(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'timestamp',
            'execution_time_ms',
            'checks' => [
                'database' => ['status', 'connection', 'latency_ms'],
                'cache' => ['status', 'driver'],
                'storage' => ['status', 'free_space_gb'],
                'queue' => ['status', 'pending_jobs', 'failed_jobs']
            ]
        ]);
    }

    /**
     * Test generic CRUD blocks direct mutation on protected immutable tables (general_ledger).
     */
    public function test_generic_crud_blocks_direct_update_on_immutable_ledger(): void
    {
        $pageConfig = (object) [
            'db_table' => 'general_ledger',
            'primary_key' => 'id'
        ];

        $service = app(\App\Services\DynamicCrudService::class);

        $this->expectException(\InvalidArgumentException::class);
        $service->updateRecord($pageConfig, 1, ['debit' => 99999], '127.0.0.1', 'PHPUnit');
    }

    /**
     * Test generic CRUD blocks direct deletion on protected immutable tables (general_ledger).
     */
    public function test_generic_crud_blocks_direct_delete_on_immutable_ledger(): void
    {
        $pageConfig = (object) [
            'db_table' => 'general_ledger',
            'primary_key' => 'id'
        ];

        $service = app(\App\Services\DynamicCrudService::class);

        $this->expectException(\InvalidArgumentException::class);
        $service->deleteRecord($pageConfig, 1, '127.0.0.1', 'PHPUnit');
    }
}
