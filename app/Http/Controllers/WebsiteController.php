<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebsiteController extends Controller
{
    /**
     * Display the official ERP system landing page.
     */
    public function index()
    {
        $stats = [
            'total_modules' => DB::table('modules')->count(),
            'total_pages' => DB::table('pages')->count(),
            'active_users' => DB::table('users')->where('is_active', true)->count(),
            'total_invoices' => DB::table('sales_invoices')->count(),
        ];

        $modules = DB::table('modules')->orderBy('sequence')->limit(8)->get();

        return view('website.index', compact('stats', 'modules'));
    }

    /**
     * Display subpages if needed in the future.
     */
    public function show($page)
    {
        if (view()->exists("website.{$page}")) {
            return view("website.{$page}");
        }
        return redirect('/website');
    }
}
