<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DeveloperModuleService
{
    /**
     * Analyze a user-pasted SQL query to detect columns, data types, primary key, and physical table.
     */
    public function analyzeQuery(string $sql): array
    {
        $sql = trim($sql);
        SqlSecurityAnalyzer::validateSelectQuery($sql);

        // Detect base table name using basic SQL regex
        $tableName = null;
        if (preg_match('/from\s+[`"]?([a-zA-Z0-9_]+)[`"]?/i', $sql, $matches)) {
            $tableName = $matches[1];
        }

        // Execute dummy SELECT query using PDO to get exact column metadata
        $pdo = DB::connection()->getPdo();
        
        // Wrap query if it does not contain limit
        $wrappedSql = preg_match('/limit\s+\d+/i', $sql) ? $sql : "SELECT * FROM ({$sql}) AS parse_subquery LIMIT 1";
        
        $stmt = $pdo->prepare($wrappedSql);
        $stmt->execute();
        
        $columnCount = $stmt->columnCount();
        $columns = [];
        $pkCandidates = [];

        for ($i = 0; $i < $columnCount; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $colName = is_array($meta) && isset($meta['name']) ? $meta['name'] : 'column_' . ($i + 1);
            $nativeType = strtolower(is_array($meta) && isset($meta['native_type']) ? $meta['native_type'] : 'string');

            // Determine normalized data type
            $mappedType = $this->mapNativeTypeToFormType($colName, $nativeType);

            // Check if column is a primary key candidate
            $isPk = ($colName === 'id' || str_ends_with($colName, '_id') || (is_array($meta) && isset($meta['flags']) && in_array('primary_key', $meta['flags'])));
            if ($isPk) {
                $pkCandidates[] = $colName;
            }

            $columns[] = [
                'field' => $colName,
                'headerName' => Str::title(str_replace('_', ' ', $colName)),
                'type' => $mappedType,
                'native_type' => $nativeType,
                'sortable' => true,
                'filter' => true,
                'required' => ($colName === 'id' || str_ends_with($colName, '_id')) ? false : true,
                'grid_width' => 6,
                'wrapText' => false,
                'autoHeight' => false,
                'formatter' => $mappedType === 'badge' ? 'badge' : ($mappedType === 'decimal' ? 'currency' : null)
            ];
        }

        // Default primary key
        $primaryKey = !empty($pkCandidates) ? $pkCandidates[0] : ($columns[0]['field'] ?? 'id');

        return [
            'sql' => $sql,
            'table_name' => $tableName,
            'primary_key' => $primaryKey,
            'pk_candidates' => array_unique($pkCandidates),
            'columns' => $columns
        ];
    }

    /**
     * Map native SQL type / column name to field component type.
     */
    protected function mapNativeTypeToFormType(string $colName, string $nativeType): string
    {
        $colNameLower = strtolower($colName);

        if ($colNameLower === 'status' || $colNameLower === 'state' || $colNameLower === 'type') {
            return 'badge';
        }
        if (str_contains($nativeType, 'int') || str_contains($nativeType, 'long') || str_contains($nativeType, 'short')) {
            if ($colNameLower === 'id' || str_ends_with($colNameLower, '_id')) {
                return 'number';
            }
            if (str_contains($colNameLower, 'is_') || str_contains($colNameLower, 'has_') || str_contains($colNameLower, 'active')) {
                return 'checkbox';
            }
            return 'number';
        }
        if (str_contains($nativeType, 'decimal') || str_contains($nativeType, 'float') || str_contains($nativeType, 'double') || str_contains($colNameLower, 'amount') || str_contains($colNameLower, 'price') || str_contains($colNameLower, 'total')) {
            return 'decimal';
        }
        if (str_contains($nativeType, 'date') || str_contains($nativeType, 'time') || str_contains($nativeType, 'timestamp')) {
            if (str_contains($nativeType, 'time') || str_contains($colNameLower, 'at')) {
                return 'datetime';
            }
            return 'date';
        }
        if (str_contains($nativeType, 'blob') || str_contains($nativeType, 'text')) {
            return 'textarea';
        }
        return 'text';
    }

    /**
     * Generate Module & Page (Low-Code Metadata or Isolated Code).
     */
    public function generatePage(array $input): array
    {
        $moduleName = trim($input['module_name'] ?? '');
        $moduleSlug = Str::slug($input['module_slug'] ?? $moduleName);
        $pageName = trim($input['page_name'] ?? '');
        $pageSlug = Str::slug($input['page_slug'] ?? $pageName);
        $sqlQuery = trim($input['sql_query'] ?? '');
        $primaryKey = trim($input['primary_key'] ?? 'id');
        $dbTable = trim($input['db_table'] ?? '') ?: ($this->extractTableFromQuery($sqlQuery) ?? 'users');
        $generationMode = $input['generation_mode'] ?? 'metadata'; // 'metadata' or 'isolated_code'

        $gridSchema = $input['grid_schema'] ?? [];
        $formSchema = $input['form_schema'] ?? [];

        if (empty($moduleSlug) || empty($pageSlug) || empty($sqlQuery)) {
            throw new \Exception('Module slug, page slug, and SQL query are required.');
        }

        // 1. Ensure Module exists or create it
        $module = DB::table('modules')->where('name', $moduleName)->first();
        if (!$module) {
            $maxSeq = DB::table('modules')->max('sequence') ?? 0;
            $moduleId = DB::table('modules')->insertGetId([
                'name' => $moduleName,
                'icon' => $input['module_icon'] ?? 'bi-box-seam',
                'sequence' => $maxSeq + 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $moduleId = $module->id;
        }

        // 2. Handle Generation Modes
        if ($generationMode === 'metadata') {
            return $this->generateMetadataPage($moduleId, $moduleSlug, $pageName, $pageSlug, $dbTable, $primaryKey, $sqlQuery, $gridSchema, $formSchema, $input);
        } else {
            return $this->generateIsolatedCodePage($moduleId, $moduleSlug, $pageName, $pageSlug, $dbTable, $primaryKey, $sqlQuery, $gridSchema, $formSchema, $input);
        }
    }

    /**
     * Mode 1: Generate Low-Code Metadata Page inside 'pages' table.
     */
    protected function generateMetadataPage($moduleId, $moduleSlug, $pageName, $pageSlug, $dbTable, $primaryKey, $sqlQuery, $gridSchema, $formSchema, $input): array
    {
        $existingPage = DB::table('pages')->where('slug', $pageSlug)->first();

        $pageData = [
            'module_id' => $moduleId,
            'name' => $pageName,
            'slug' => $pageSlug,
            'title' => $input['page_title'] ?? $pageName,
            'db_table' => $dbTable,
            'primary_key' => $primaryKey,
            'sql_query' => $sqlQuery,
            'grid_schema' => json_encode($gridSchema),
            'form_schema' => json_encode($formSchema),
            'is_custom' => false,
            'custom_view' => null,
            'icon' => $input['page_icon'] ?? 'bi-table',
            'is_active' => true,
            'updated_at' => now()
        ];

        if ($existingPage) {
            DB::table('pages')->where('id', $existingPage->id)->update($pageData);
            $pageId = $existingPage->id;
        } else {
            $pageData['created_at'] = now();
            $pageData['token'] = Str::upper(Str::random(12));
            $pageId = DB::table('pages')->insertGetId($pageData);
        }

        $this->assignDefaultPermissions($pageId);

        return [
            'success' => true,
            'mode' => 'metadata',
            'page_id' => $pageId,
            'url' => "/erp/{$pageSlug}",
            'message' => "Page '{$pageName}' generated successfully in Low-Code Metadata engine."
        ];
    }

    /**
     * Mode 2: Generate Standalone Isolated Code Files (Blade, JS, CSS, Controller, Route).
     */
    protected function generateIsolatedCodePage($moduleId, $moduleSlug, $pageName, $pageSlug, $dbTable, $primaryKey, $sqlQuery, $gridSchema, $formSchema, $input): array
    {
        $controllerClassName = Str::studly(str_replace('-', '_', $moduleSlug)) . Str::studly(str_replace('-', '_', $pageSlug)) . 'Controller';
        $viewPathDir = resource_path("views/modules/{$moduleSlug}/{$pageSlug}");
        
        if (!File::exists($viewPathDir)) {
            File::makeDirectory($viewPathDir, 0755, true);
        }

        // Render modular Blade, CSS, and JS files
        $mainBladeFile = "{$viewPathDir}/main.blade.php";
        $cssBladeFile = "{$viewPathDir}/css.blade.php";
        $jsBladeFile = "{$viewPathDir}/js.blade.php";

        $mainContent = $this->buildBladeMainContent($pageName, $pageSlug, $moduleSlug, $primaryKey);
        $cssContent = $this->buildBladeCssContent($pageSlug);
        $jsContent = $this->buildBladeJsContent($pageName, $pageSlug, $moduleSlug, $gridSchema, $formSchema, $primaryKey);

        File::put($mainBladeFile, $mainContent);
        File::put($cssBladeFile, $cssContent);
        File::put($jsBladeFile, $jsContent);

        // Generate Isolated Controller
        $controllerDir = app_path("Http/Controllers/Generated");
        if (!File::exists($controllerDir)) {
            File::makeDirectory($controllerDir, 0755, true);
        }
        $controllerFile = "{$controllerDir}/{$controllerClassName}.php";
        $controllerContent = $this->buildControllerContent($controllerClassName, $dbTable, $primaryKey, $sqlQuery, $moduleSlug, $pageSlug);
        File::put($controllerFile, $controllerContent);

        // Append to routes/generated_modules.php safely
        $routesFile = base_path('routes/generated_modules.php');
        if (!File::exists($routesFile)) {
            File::put($routesFile, "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\n// Developer Generated Module Routes\n");
        }

        $routeSnippet = "\nRoute::middleware(['auth'])->group(function () {\n";
        $routeSnippet .= "    Route::get('/erp/{$pageSlug}', [\\App\\Http\\Controllers\\Generated\\{$controllerClassName}::class, 'index']);\n";
        $routeSnippet .= "    Route::get('/erp/custom/{$moduleSlug}/{$pageSlug}', [\\App\\Http\\Controllers\\Generated\\{$controllerClassName}::class, 'index']);\n";
        $routeSnippet .= "    Route::get('/api/custom/{$moduleSlug}/{$pageSlug}/data', [\\App\\Http\\Controllers\\Generated\\{$controllerClassName}::class, 'getData']);\n";
        $routeSnippet .= "    Route::post('/api/custom/{$moduleSlug}/{$pageSlug}/store', [\\App\\Http\\Controllers\\Generated\\{$controllerClassName}::class, 'store']);\n";
        $routeSnippet .= "    Route::post('/api/custom/{$moduleSlug}/{$pageSlug}/update/{id}', [\\App\\Http\\Controllers\\Generated\\{$controllerClassName}::class, 'update']);\n";
        $routeSnippet .= "    Route::delete('/api/custom/{$moduleSlug}/{$pageSlug}/destroy/{id}', [\\App\\Http\\Controllers\\Generated\\{$controllerClassName}::class, 'destroy']);\n";
        $routeSnippet .= "});\n";

        // Prevent duplicate route appending
        $existingRoutesContent = File::get($routesFile);
        if (!str_contains($existingRoutesContent, "/erp/custom/{$moduleSlug}/{$pageSlug}")) {
            File::append($routesFile, $routeSnippet);
        }

        // Create Custom Page reference in ERP database
        $customViewName = "modules/{$moduleSlug}/{$pageSlug}";
        $existingPage = DB::table('pages')->where('slug', $pageSlug)->first();

        $pageData = [
            'module_id' => $moduleId,
            'name' => $pageName,
            'slug' => $pageSlug,
            'title' => $input['page_title'] ?? $pageName,
            'db_table' => $dbTable,
            'primary_key' => $primaryKey,
            'sql_query' => $sqlQuery,
            'grid_schema' => json_encode($gridSchema),
            'form_schema' => json_encode($formSchema),
            'is_custom' => true,
            'custom_view' => $customViewName,
            'icon' => $input['page_icon'] ?? 'bi-code-square',
            'is_active' => true,
            'updated_at' => now()
        ];

        if ($existingPage) {
            DB::table('pages')->where('id', $existingPage->id)->update($pageData);
            $pageId = $existingPage->id;
        } else {
            $pageData['created_at'] = now();
            $pageData['token'] = Str::upper(Str::random(12));
            $pageId = DB::table('pages')->insertGetId($pageData);
        }

        $this->assignDefaultPermissions($pageId);

        return [
            'success' => true,
            'mode' => 'isolated_code',
            'page_id' => $pageId,
            'url' => "/erp/{$pageSlug}",
            'blade_file' => $mainBladeFile,
            'controller_file' => $controllerFile,
            'message' => "Isolated page '{$pageName}' created with standalone main.blade.php, js.blade.php, css.blade.php, and Controller."
        ];
    }

    /**
     * Build Main Blade view file content.
     */
    protected function buildBladeMainContent(string $pageName, string $pageSlug, string $moduleSlug, string $primaryKey): string
    {
        $jsSlug = str_replace('-', '_', $pageSlug);
        return <<<BLADE
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">{$pageName}</h4>
            <p class="text-muted small mb-0">Developer Generated Module Page</p>
        </div>
        <div class="d-flex gap-2">
            <input type="text" id="custom-grid-search-{$pageSlug}" class="form-control form-control-sm" style="width: 220px;" placeholder="Search records...">
            <button type="button" class="btn btn-sm btn-primary" onclick="openCustomCreateModal_{$jsSlug}()"><i class="bi bi-plus-lg"></i> Add Record</button>
        </div>
    </div>

    <!-- AG Grid Container -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div id="grid-{$pageSlug}" style="height: 520px; width: 100%;"></div>
        </div>
    </div>

    <!-- CRUD Form Modal -->
    <div class="modal fade" id="modal-{$pageSlug}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modal-title-{$pageSlug}">Add Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-{$pageSlug}" onsubmit="saveCustomRecord_{$jsSlug}(event)">
                    <div class="modal-body p-4" id="form-body-{$pageSlug}"></div>
                    <div class="modal-footer bg-light px-4">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
BLADE;
    }

    /**
     * Build CSS content.
     */
    protected function buildBladeCssContent(string $pageSlug): string
    {
        return <<<CSS
/* Custom CSS for {$pageSlug} */
CSS;
    }

    /**
     * Build JS content for loader execution.
     */
    protected function buildBladeJsContent(string $pageName, string $pageSlug, string $moduleSlug, array $gridSchema, array $formSchema, string $primaryKey): string
    {
        $columnsJson = json_encode($gridSchema, JSON_PRETTY_PRINT);
        $formFieldsJson = json_encode($formSchema, JSON_PRETTY_PRINT);
        $jsSlug = str_replace('-', '_', $pageSlug);

        return <<<JS
const pageSlug = "{$pageSlug}";
const moduleSlug = "{$moduleSlug}";
const primaryKey = "{$primaryKey}";
const gridColumns = {$columnsJson};
const formFields = {$formFieldsJson};

let gridInstance = null;
const modalEl = document.getElementById(`modal-\${pageSlug}`);
const bsModal = new bootstrap.Modal(modalEl);
let activeRecordId = null;

// Render form fields
const formBody = document.getElementById(`form-body-\${pageSlug}`);
if (formBody) {
    formBody.innerHTML = ErpForms.renderFieldsHtml(formFields);
    ErpForms.bindInteractiveFields(formBody);
}

// Initialize ErpGrid
gridInstance = ErpGrid.createGrid({
    id: `grid-\${pageSlug}`,
    primaryKey: primaryKey,
    dataUrl: `/api/custom/\${moduleSlug}/\${pageSlug}/data`,
    columns: gridColumns,
    wrapText: true,
    autoRowHeight: true,
    onEdit: function(rowData) {
        openCustomEditModal_{$jsSlug}(rowData);
    },
    onDelete: function(id) {
        deleteCustomRecord_{$jsSlug}(id);
    }
});

// Quick Search Listener
const searchInput = document.getElementById(`custom-grid-search-\${pageSlug}`);
if (searchInput) {
    searchInput.addEventListener('input', function(e) {
        if (gridInstance) gridInstance.quickSearch(e.target.value);
    });
}

window.openCustomCreateModal_{$jsSlug} = function() {
    activeRecordId = null;
    document.getElementById(`modal-title-\${pageSlug}`).textContent = 'Add Record';
    document.getElementById(`form-\${pageSlug}`).reset();
    bsModal.show();
};

window.openCustomEditModal_{$jsSlug} = function(rowData) {
    activeRecordId = rowData[primaryKey];
    document.getElementById(`modal-title-\${pageSlug}`).textContent = 'Edit Record';
    formBody.innerHTML = ErpForms.renderFieldsHtml(formFields, rowData);
    ErpForms.bindInteractiveFields(formBody);
    bsModal.show();
};

window.saveCustomRecord_{$jsSlug} = function(e) {
    e.preventDefault();
    const form = document.getElementById(`form-\${pageSlug}`);
    if (!ErpForms.validateForm(form)) return;

    const formData = new FormData(form);
    const payload = Object.fromEntries(formData.entries());

    const url = activeRecordId 
        ? `/api/custom/\${moduleSlug}/\${pageSlug}/update/\${activeRecordId}`
        : `/api/custom/\${moduleSlug}/\${pageSlug}/store`;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) throw new Error(data.error);
        bsModal.hide();
        gridInstance.refresh();
        showToast('success', 'Record saved successfully.');
    })
    .catch(err => {
        showToast('danger', err.message || 'Error saving record.');
    });
};

window.deleteCustomRecord_{$jsSlug} = function(id) {
    if (!confirm('Are you sure you want to delete this record?')) return;
    fetch(`/api/custom/\${moduleSlug}/\${pageSlug}/destroy/\${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        gridInstance.refresh();
        showToast('success', 'Record deleted.');
    })
    .catch(err => showToast('danger', 'Failed to delete record.'));
};
JS;
    }

    /**
     * Build Controller content for standalone mode.
     */
    protected function buildControllerContent(string $className, string $table, string $primaryKey, string $sqlQuery, string $moduleSlug, string $pageSlug): string
    {
        $escapedSql = addslashes($sqlQuery);
        $customView = "modules/{$moduleSlug}/{$pageSlug}";
        return <<<PHP
<?php

namespace App\Http\Controllers\Generated;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class {$className} extends Controller
{
    protected \$table = '{$table}';
    protected \$primaryKey = '{$primaryKey}';
    protected \$sqlQuery = "{$escapedSql}";

    public function index()
    {
        if (!request()->ajax()) {
            return redirect('/');
        }

        return view('modules.loader', [
            'pageDir' => '{$customView}'
        ]);
    }

    public function getData()
    {
        \$data = DB::select(\$this->sqlQuery);
        return response()->json(['data' => \$data]);
    }

    public function store(Request \$request)
    {
        \$payload = \$request->except(['_token']);
        \$payload['created_at'] = now();
        \$payload['updated_at'] = now();

        \$id = DB::table(\$this->table)->insertGetId(\$payload);
        return response()->json(['success' => true, 'id' => \$id]);
    }

    public function update(Request \$request, \$id)
    {
        \$payload = \$request->except(['_token']);
        \$payload['updated_at'] = now();

        DB::table(\$this->table)->where(\$this->primaryKey, \$id)->update(\$payload);
        return response()->json(['success' => true]);
    }

    public function destroy(\$id)
    {
        DB::table(\$this->table)->where(\$this->primaryKey, \$id)->delete();
        return response()->json(['success' => true]);
    }
}
PHP;
    }

    /**
     * Helper to extract primary table name from SQL.
     */
    protected function extractTableFromQuery(string $sql): ?string
    {
        if (preg_match('/from\s+[`"]?([a-zA-Z0-9_]+)[`"]?/i', $sql, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Assign permissions to Super Admin and Admin roles for newly generated pages.
     */
    protected function assignDefaultPermissions(int $pageId): void
    {
        $roles = DB::table('roles')->whereIn('slug', ['super-admin', 'admin'])->get();
        foreach ($roles as $role) {
            DB::table('role_permissions')->updateOrInsert(
                ['role_id' => $role->id, 'page_id' => $pageId],
                [
                    'can_view' => true,
                    'can_create' => true,
                    'can_edit' => true,
                    'can_delete' => true,
                    'can_export' => true,
                    'can_print' => true,
                    'can_approve' => true,
                    'can_reject' => true,
                    'updated_at' => now()
                ]
            );
        }

        if (auth()->check()) {
            $user = auth()->user();
            DB::table('user_permissions')->updateOrInsert(
                ['user_id' => $user->id, 'page_id' => $pageId],
                [
                    'can_view' => true,
                    'can_create' => true,
                    'can_edit' => true,
                    'can_delete' => true,
                    'can_export' => true,
                    'can_print' => true,
                    'can_approve' => true,
                    'can_reject' => true,
                    'updated_at' => now()
                ]
            );
        }

        ModuleScannerService::clearCache();
    }
}
