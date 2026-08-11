<?php

namespace App\Http\Controllers;

use App\Services\DeveloperModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeveloperModuleController extends Controller
{
    protected $devService;

    public function __construct(DeveloperModuleService $devService)
    {
        $this->devService = $devService;
    }

    /**
     * Render the Developer Studio Interface.
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) {
            return redirect('/');
        }

        $modules = DB::table('modules')->orderBy('sequence')->get();
        $pages = DB::table('pages')->get();
        $tables = DB::select('SHOW TABLES');

        return view('modules.loader', array_merge(
            compact('modules', 'pages', 'tables'),
            ['pageDir' => 'modules/developer']
        ));
    }

    /**
     * API to analyze SQL query and extract metadata.
     */
    public function analyzeQuery(Request $request)
    {
        $request->validate([
            'sql' => 'required|string'
        ]);

        try {
            $analysis = $this->devService->analyzeQuery($request->input('sql'));
            return response()->json([
                'success' => true,
                'data' => $analysis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * API to generate page (metadata or isolated code mode).
     */
    public function generatePage(Request $request)
    {
        $request->validate([
            'module_name' => 'required|string',
            'page_name' => 'required|string',
            'sql_query' => 'required|string',
            'primary_key' => 'required|string',
            'grid_schema' => 'required|array',
            'form_schema' => 'required|array'
        ]);

        try {
            $result = $this->devService->generatePage($request->all());
            return response()->json($result);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Generate Page Error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
