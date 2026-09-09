<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * High-Performance Redis Caching Service for ERP
 * 
 * Provides targeted, selective caching for high-frequency bottlenecks
 * (User Context, Permissions, Schema Introspection, Page Configs, KPIs)
 * with atomic versioning and resilient fallback.
 */
class ErpCacheService
{
    /**
     * Safely fetch from or store in Cache with automatic fallback to DB on failure.
     */
    public static function rememberSafe(string $key, int $ttlSeconds, Closure $callback)
    {
        try {
            return Cache::remember($key, $ttlSeconds, $callback);
        } catch (Throwable $e) {
            Log::warning("ErpCacheService: Cache lookup failed for key '{$key}': " . $e->getMessage());
            return $callback();
        }
    }

    /**
     * Safely forget a cache key.
     */
    public static function forgetSafe(string $key): void
    {
        try {
            Cache::forget($key);
        } catch (Throwable $e) {
            Log::warning("ErpCacheService: Cache forget failed for key '{$key}': " . $e->getMessage());
        }
    }

    // ==========================================
    // 1. User Context & Navigation Caching
    // ==========================================

    public static function getUserContextKey(int $userId): string
    {
        $permVersion = self::getPermissionVersion();
        return "erp:user_context:v{$permVersion}:{$userId}";
    }

    public static function clearUserContext(?int $userId = null): void
    {
        if ($userId) {
            $permVersion = self::getPermissionVersion();
            self::forgetSafe("erp:user_context:v{$permVersion}:{$userId}");
        } else {
            // Invalidate all user contexts at once by bumping permission/context version
            self::bumpPermissionVersion();
        }
    }

    // ==========================================
    // 2. Atomic Permission Matrix Versioning
    // ==========================================

    /**
     * Get the current global permission version.
     * When bumped, all user contexts and permission caches instantly invalidate in O(1) time.
     */
    public static function getPermissionVersion(): int
    {
        try {
            return (int) Cache::get('erp:perm_version', 1);
        } catch (Throwable $e) {
            return 1;
        }
    }

    public static function bumpPermissionVersion(): void
    {
        try {
            if (Cache::has('erp:perm_version')) {
                Cache::increment('erp:perm_version');
            } else {
                Cache::put('erp:perm_version', 2, 86400 * 30);
            }
        } catch (Throwable $e) {
            Log::warning("ErpCacheService: Failed to bump permission version: " . $e->getMessage());
        }
    }

    public static function getUserPermissionKey(int $userId, int $pageId): string
    {
        $v = self::getPermissionVersion();
        return "erp:perm:v{$v}:u{$userId}:p{$pageId}";
    }

    // ==========================================
    // 3. Dynamic Page Configurations
    // ==========================================

    public static function getPageConfigKey(string $slug): string
    {
        return "erp:page_config:{$slug}";
    }

    public static function clearPageConfig(?string $slug = null): void
    {
        if ($slug) {
            self::forgetSafe(self::getPageConfigKey($slug));
        }
        // Invalidate navigation context as page config or route might have changed
        self::clearUserContext();
    }

    // ==========================================
    // 4. Schema Column Introspection Caching
    // ==========================================

    public static function getSchemaColumnKey(string $table, string $column): string
    {
        return "erp:schema_col:{$table}:{$column}";
    }

    public static function clearSchemaColumnCache(string $table, string $column): void
    {
        self::forgetSafe(self::getSchemaColumnKey($table, $column));
    }

    // ==========================================
    // 5. Dashboard Core KPIs Caching
    // ==========================================

    public static function getDashboardKpiKey(?int $companyId, ?int $branchId): string
    {
        $comp = $companyId ?? 'all';
        $br = $branchId ?? 'all';
        return "erp:dashboard_kpis:c{$comp}:b{$br}";
    }

    public static function clearDashboardKpis(): void
    {
        try {
            Cache::forget('erp:dashboard_kpis:call:ball');
        } catch (Throwable $e) {}
    }

    // ==========================================
    // 6. Role Slug Caching
    // ==========================================

    public static function getRoleSlugKey(int $roleId): string
    {
        return "erp:role_slug:{$roleId}";
    }
}
