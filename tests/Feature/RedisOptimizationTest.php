<?php

namespace Tests\Feature;

use App\Services\ErpCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RedisOptimizationTest extends TestCase
{
    public function test_erp_cache_service_remembers_and_forgets_safe(): void
    {
        $key = 'test_safe_key';
        ErpCacheService::forgetSafe($key);

        $executed = 0;
        $val1 = ErpCacheService::rememberSafe($key, 60, function () use (&$executed) {
            $executed++;
            return 'computed_val';
        });

        $this->assertEquals('computed_val', $val1);
        $this->assertEquals(1, $executed);

        // Second call should hit cache
        $val2 = ErpCacheService::rememberSafe($key, 60, function () use (&$executed) {
            $executed++;
            return 'computed_val_again';
        });

        $this->assertEquals('computed_val', $val2);
        $this->assertEquals(1, $executed);

        ErpCacheService::forgetSafe($key);
    }

    public function test_permission_version_bumping_invalidates_context_keys(): void
    {
        $v1 = ErpCacheService::getPermissionVersion();
        $key1 = ErpCacheService::getUserContextKey(1);

        ErpCacheService::bumpPermissionVersion();

        $v2 = ErpCacheService::getPermissionVersion();
        $key2 = ErpCacheService::getUserContextKey(1);

        $this->assertGreaterThan($v1, $v2);
        $this->assertNotEquals($key1, $key2);
    }

    public function test_schema_column_and_page_config_keys(): void
    {
        $this->assertEquals('erp:schema_col:users:email', ErpCacheService::getSchemaColumnKey('users', 'email'));
        $this->assertEquals('erp:page_config:customers', ErpCacheService::getPageConfigKey('customers'));
        $this->assertEquals('erp:role_slug:1', ErpCacheService::getRoleSlugKey(1));
    }
}
