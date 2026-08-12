<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Services\DeveloperModuleService;

class DeveloperModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--force' => true]);
    }

    public function test_developer_module_page_loads_successfully()
    {
        $user = DB::table('users')->where('email', 'admin@mserp.com')->first();
        $this->actingAs(\App\Models\User::find($user->id));

        $response = $this->get('/erp/developer-module', ['X-Requested-With' => 'XMLHttpRequest']);
        $response->assertStatus(200);
        $response->assertSee('SQL to AG Grid');
    }

    public function test_analyze_sql_query()
    {
        $devService = new DeveloperModuleService();
        $analysis = $devService->analyzeQuery("SELECT id, name, email, is_active, created_at FROM users");

        $this->assertEquals('users', $analysis['table_name']);
        $this->assertEquals('id', $analysis['primary_key']);
        $this->assertCount(5, $analysis['columns']);
    }

    public function test_generate_metadata_crud_page()
    {
        $user = DB::table('users')->where('email', 'admin@mserp.com')->first();
        $this->actingAs(\App\Models\User::find($user->id));

        $payload = [
            'module_name' => 'Task Management',
            'module_slug' => 'task-management',
            'page_name' => 'Todos',
            'page_slug' => 'todos-test',
            'sql_query' => 'SELECT * FROM users',
            'db_table' => 'users',
            'primary_key' => 'id',
            'generation_mode' => 'metadata',
            'grid_schema' => [
                ['field' => 'id', 'headerName' => 'ID'],
                ['field' => 'name', 'headerName' => 'Name']
            ],
            'form_schema' => [
                ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true]
            ]
        ];

        $response = $this->postJson('/api/developer/generate-page', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('pages', [
            'slug' => 'todos-test',
            'is_custom' => false
        ]);
    }

    public function test_generate_isolated_code_crud_page()
    {
        $user = DB::table('users')->where('email', 'admin@mserp.com')->first();
        $this->actingAs(\App\Models\User::find($user->id));

        $payload = [
            'module_name' => 'Task Management',
            'module_slug' => 'task-management',
            'page_name' => 'Custom Todos',
            'page_slug' => 'custom-todos-test',
            'sql_query' => 'SELECT * FROM users',
            'db_table' => 'users',
            'primary_key' => 'id',
            'generation_mode' => 'isolated_code',
            'grid_schema' => [
                ['field' => 'id', 'headerName' => 'ID'],
                ['field' => 'name', 'headerName' => 'Name']
            ],
            'form_schema' => [
                ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true]
            ]
        ];

        $response = $this->postJson('/api/developer/generate-page', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('pages', [
            'slug' => 'custom-todos-test',
            'is_custom' => true
        ]);

        $responseView = $this->get('/erp/custom/task-management/custom-todos-test', ['X-Requested-With' => 'XMLHttpRequest']);
        $responseView->assertStatus(200);

        $responseSlugView = $this->get('/erp/custom-todos-test', ['X-Requested-With' => 'XMLHttpRequest']);
        $responseSlugView->assertStatus(200);

        $responseData = $this->getJson('/api/custom/task-management/custom-todos-test/data');
        $responseData->assertStatus(200);
        $responseData->assertJsonStructure(['data']);

        $responseStore = $this->postJson('/api/custom/task-management/custom-todos-test/store', [
            'name' => 'New Test User',
            'email' => 'testuser@mserp.com',
            'password' => bcrypt('secret'),
            'role_id' => 1
        ]);
        $responseStore->assertStatus(200);
        $responseStore->assertJson(['success' => true]);
    }
}
