<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ComprehensiveErpSeeder extends Seeder
{
    public function run(): void
    {
        $acmeCompany = DB::table('companies')->where('code', 'ACME')->first();
        $companyId = $acmeCompany ? $acmeCompany->id : 1;
        $branch = DB::table('branches')->where('company_id', $companyId)->first();
        $branchId = $branch ? $branch->id : null;

        // 1. Chart of Accounts Seeding
        $accounts = [
            ['code' => '1010', 'name' => 'Cash in Hand', 'type' => 'Asset', 'parent_id' => null, 'opening_balance' => 150000.00, 'current_balance' => 150000.00],
            ['code' => '1020', 'name' => 'HDFC Bank Operating Account', 'type' => 'Asset', 'parent_id' => null, 'opening_balance' => 2450000.00, 'current_balance' => 2450000.00],
            ['code' => '1030', 'name' => 'Accounts Receivable (Trade Debtors)', 'type' => 'Asset', 'parent_id' => null, 'opening_balance' => 850000.00, 'current_balance' => 850000.00],
            ['code' => '1040', 'name' => 'Finished Goods Inventory Valuation', 'type' => 'Asset', 'parent_id' => null, 'opening_balance' => 1200000.00, 'current_balance' => 1200000.00],
            ['code' => '2010', 'name' => 'Accounts Payable (Trade Creditors)', 'type' => 'Liability', 'parent_id' => null, 'opening_balance' => 620000.00, 'current_balance' => 620000.00],
            ['code' => '2020', 'name' => 'Output CGST Payable', 'type' => 'Liability', 'parent_id' => null, 'opening_balance' => 45000.00, 'current_balance' => 45000.00],
            ['code' => '2021', 'name' => 'Output SGST Payable', 'type' => 'Liability', 'parent_id' => null, 'opening_balance' => 45000.00, 'current_balance' => 45000.00],
            ['code' => '2022', 'name' => 'Output IGST Payable', 'type' => 'Liability', 'parent_id' => null, 'opening_balance' => 90000.00, 'current_balance' => 90000.00],
            ['code' => '3010', 'name' => 'Share Capital', 'type' => 'Equity', 'parent_id' => null, 'opening_balance' => 3000000.00, 'current_balance' => 3000000.00],
            ['code' => '3020', 'name' => 'Retained Earnings', 'type' => 'Equity', 'parent_id' => null, 'opening_balance' => 1030000.00, 'current_balance' => 1030000.00],
            ['code' => '4010', 'name' => 'Domestic Product Sales Revenue', 'type' => 'Income', 'parent_id' => null, 'opening_balance' => 0.00, 'current_balance' => 3400000.00],
            ['code' => '4020', 'name' => 'Service & Maintenance Income', 'type' => 'Income', 'parent_id' => null, 'opening_balance' => 0.00, 'current_balance' => 450000.00],
            ['code' => '5010', 'name' => 'Cost of Goods Sold (COGS)', 'type' => 'Expense', 'parent_id' => null, 'opening_balance' => 0.00, 'current_balance' => 1800000.00],
            ['code' => '5020', 'name' => 'Salaries & Staff Expenses', 'type' => 'Expense', 'parent_id' => null, 'opening_balance' => 0.00, 'current_balance' => 650000.00],
            ['code' => '5030', 'name' => 'Office Rent & Utilities', 'type' => 'Expense', 'parent_id' => null, 'opening_balance' => 0.00, 'current_balance' => 180000.00]
        ];

        foreach ($accounts as $acc) {
            DB::table('chart_of_accounts')->updateOrInsert(
                ['code' => $acc['code']],
                array_merge($acc, ['company_id' => $companyId, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        // 2. Fiscal Periods
        $periods = [
            ['code' => 'FP-2026-Q1', 'name' => 'FY 2026-27 Q1 (Apr-Jun)', 'start_date' => '2026-04-01', 'end_date' => '2026-06-30', 'is_closed' => true],
            ['code' => 'FP-2026-Q2', 'name' => 'FY 2026-27 Q2 (Jul-Sep)', 'start_date' => '2026-07-01', 'end_date' => '2026-09-30', 'is_closed' => false],
            ['code' => 'FP-2026-Q3', 'name' => 'FY 2026-27 Q3 (Oct-Dec)', 'start_date' => '2026-10-01', 'end_date' => '2026-12-31', 'is_closed' => false],
            ['code' => 'FP-2026-Q4', 'name' => 'FY 2026-27 Q4 (Jan-Mar)', 'start_date' => '2027-01-01', 'end_date' => '2027-03-31', 'is_closed' => false]
        ];

        foreach ($periods as $p) {
            DB::table('fiscal_periods')->updateOrInsert(
                ['code' => $p['code']],
                array_merge($p, ['company_id' => $companyId, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        // 3. Warehouses & Bins
        $whMainId = DB::table('warehouses')->where('code', 'WH-MUM-MAIN')->value('id');
        if (!$whMainId) {
            $whMainId = DB::table('warehouses')->insertGetId([
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'name' => 'Mumbai Central Warehouse',
                'code' => 'WH-MUM-MAIN',
                'address' => 'Bhiwandi Logistics Park, Sector 4, Thane, MH',
                'manager_name' => 'Ramesh Patil',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $whBlrId = DB::table('warehouses')->where('code', 'WH-BLR-TECH')->value('id');
        if (!$whBlrId) {
            $whBlrId = DB::table('warehouses')->insertGetId([
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'name' => 'Bengaluru Distribution Center',
                'code' => 'WH-BLR-TECH',
                'address' => 'Peenya Industrial Area, Bengaluru, KA',
                'manager_name' => 'Srinivas Gowda',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Bins
        $bins = [
            ['warehouse_id' => $whMainId, 'bin_code' => 'BIN-A1-01', 'aisle' => 'Aisle 1', 'rack' => 'Rack A', 'shelf' => 'Shelf 1'],
            ['warehouse_id' => $whMainId, 'bin_code' => 'BIN-A1-02', 'aisle' => 'Aisle 1', 'rack' => 'Rack A', 'shelf' => 'Shelf 2'],
            ['warehouse_id' => $whBlrId, 'bin_code' => 'BIN-B2-01', 'aisle' => 'Aisle 2', 'rack' => 'Rack B', 'shelf' => 'Shelf 1']
        ];
        foreach ($bins as $b) {
            DB::table('warehouse_bins')->updateOrInsert(
                ['warehouse_id' => $b['warehouse_id'], 'bin_code' => $b['bin_code']],
                array_merge($b, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // 4. HSN / SAC Codes
        $hsns = [
            ['code' => '84713010', 'description' => 'Personal Computer Laptops & Workstations', 'type' => 'HSN', 'cgst_rate' => 9.00, 'sgst_rate' => 9.00, 'igst_rate' => 18.00],
            ['code' => '85176290', 'description' => 'Networking Routers & Enterprise Switches', 'type' => 'HSN', 'cgst_rate' => 9.00, 'sgst_rate' => 9.00, 'igst_rate' => 18.00],
            ['code' => '998313', 'description' => 'IT Infrastructure Support & Software Consulting Services', 'type' => 'SAC', 'cgst_rate' => 9.00, 'sgst_rate' => 9.00, 'igst_rate' => 18.00]
        ];
        foreach ($hsns as $h) {
            DB::table('hsn_sac_codes')->updateOrInsert(
                ['code' => $h['code']],
                array_merge($h, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        // 5. Work Centers & BOMs
        $wcId = DB::table('work_centers')->where('code', 'WC-ASSY-01')->value('id');
        if (!$wcId) {
            $wcId = DB::table('work_centers')->insertGetId([
                'company_id' => $companyId,
                'name' => 'Main Electronics Assembly Line',
                'code' => 'WC-ASSY-01',
                'capacity_per_day' => 16,
                'hourly_cost' => 750.00,
                'labor_cost' => 350.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Items check
        $item1 = DB::table('inventory_items')->first();
        if ($item1) {
            $bomId = DB::table('boms')->where('bom_no', 'BOM-SERVER-001')->value('id');
            if (!$bomId) {
                $bomId = DB::table('boms')->insertGetId([
                    'company_id' => $companyId,
                    'bom_no' => 'BOM-SERVER-001',
                    'item_id' => $item1->id,
                    'qty' => 1,
                    'total_cost' => 45000.00,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Work Order
            DB::table('work_orders')->updateOrInsert(
                ['work_order_no' => 'WO-2026-0001'],
                [
                    'company_id' => $companyId,
                    'bom_id' => $bomId,
                    'item_id' => $item1->id,
                    'qty' => 10,
                    'start_date' => '2026-08-01',
                    'completion_date' => '2026-08-25',
                    'status' => 'In Progress',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        // 6. Numbering Series
        $series = [
            ['module' => 'journal_entry', 'prefix' => 'JV/2026/', 'current_number' => 1001, 'padding' => 4],
            ['module' => 'sales_order', 'prefix' => 'SO/2026/', 'current_number' => 2001, 'padding' => 4],
            ['module' => 'purchase_order', 'prefix' => 'PO/2026/', 'current_number' => 3001, 'padding' => 4],
            ['module' => 'delivery_note', 'prefix' => 'DN/2026/', 'current_number' => 4001, 'padding' => 4],
            ['module' => 'grn', 'prefix' => 'GRN/2026/', 'current_number' => 5001, 'padding' => 4]
        ];
        foreach ($series as $s) {
            DB::table('numbering_series')->updateOrInsert(
                ['module' => $s['module']],
                array_merge($s, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // 7. Register Navigation Modules and Pages
        $moduleDefinitions = [
            [
                'name' => 'Accounting & Finance',
                'icon' => 'bi-calculator',
                'sequence' => 3,
                'pages' => [
                    ['name' => 'Chart of Accounts', 'slug' => 'chart-of-accounts', 'token' => 'ACC-100', 'view' => 'modules/accounting/chart_of_accounts', 'icon' => 'bi-diagram-3'],
                    ['name' => 'Journal Entries', 'slug' => 'journal-entries', 'token' => 'ACC-101', 'view' => 'modules/accounting/journal_entries', 'icon' => 'bi-journal-bookmark'],
                    ['name' => 'General Ledger', 'slug' => 'general-ledger', 'token' => 'ACC-102', 'view' => 'modules/accounting/general_ledger', 'icon' => 'bi-book-half'],
                    ['name' => 'Trial Balance', 'slug' => 'trial-balance', 'token' => 'ACC-103', 'view' => 'modules/accounting/trial_balance', 'icon' => 'bi-scale'],
                    ['name' => 'Financial Statements', 'slug' => 'financial-statements', 'token' => 'ACC-104', 'view' => 'modules/accounting/financial_statements', 'icon' => 'bi-graph-up-arrow'],
                    ['name' => 'AP & AR Ageing', 'slug' => 'ap-ar-management', 'token' => 'ACC-105', 'view' => 'modules/accounting/ap_ar', 'icon' => 'bi-cash-stack'],
                    ['name' => 'Credit & Debit Notes', 'slug' => 'credit-debit-notes', 'token' => 'ACC-106', 'view' => 'modules/accounting/credit_debit_notes', 'icon' => 'bi-card-checklist'],
                    ['name' => 'Fiscal Periods', 'slug' => 'fiscal-periods', 'token' => 'ACC-107', 'view' => 'modules/accounting/fiscal_periods', 'icon' => 'bi-calendar-range'],
                    ['name' => 'Bank Reconciliation', 'slug' => 'bank-reconciliation', 'token' => 'ACC-108', 'view' => 'modules/accounting/reconciliation', 'icon' => 'bi-bank']
                ]
            ],
            [
                'name' => 'Sales & CRM',
                'icon' => 'bi-cart-check',
                'sequence' => 4,
                'pages' => [
                    ['name' => 'CRM Leads', 'slug' => 'crm-leads', 'token' => 'SLS-201', 'view' => 'modules/sales/leads', 'icon' => 'bi-person-badge'],
                    ['name' => 'Sales Orders & Quotes', 'slug' => 'sales-orders-management', 'token' => 'SLS-202', 'view' => 'modules/sales/sales_orders', 'icon' => 'bi-cart4'],
                    ['name' => 'Deliveries & Dispatches', 'slug' => 'deliveries-management', 'token' => 'SLS-203', 'view' => 'modules/sales/deliveries', 'icon' => 'bi-truck'],
                    ['name' => 'Sales Commission & Pricing', 'slug' => 'sales-commission', 'token' => 'SLS-204', 'view' => 'modules/sales/sales_commission', 'icon' => 'bi-percent']
                ]
            ],
            [
                'name' => 'Purchase & Procurement',
                'icon' => 'bi-bag-check',
                'sequence' => 5,
                'pages' => [
                    ['name' => 'Purchase Requisitions & RFQs', 'slug' => 'purchase-requisitions', 'token' => 'PUR-301', 'view' => 'modules/purchase/purchase_requisitions', 'icon' => 'bi-file-earmark-text'],
                    ['name' => 'Purchase Orders & GRNs', 'slug' => 'purchase-orders-management', 'token' => 'PUR-302', 'view' => 'modules/purchase/purchase_orders', 'icon' => 'bi-bag-dash'],
                    ['name' => '3-Way Matching', 'slug' => 'three-way-matching', 'token' => 'PUR-303', 'view' => 'modules/purchase/three_way_matching', 'icon' => 'bi-check2-all'],
                    ['name' => 'Purchase Returns & Payments', 'slug' => 'purchase-returns', 'token' => 'PUR-304', 'view' => 'modules/purchase/purchase_returns', 'icon' => 'bi-arrow-counterclockwise']
                ]
            ],
            [
                'name' => 'Inventory & Warehouses',
                'icon' => 'bi-boxes',
                'sequence' => 6,
                'pages' => [
                    ['name' => 'Warehouses & Bins', 'slug' => 'warehouses-management', 'token' => 'INV-401', 'view' => 'modules/inventory/warehouses', 'icon' => 'bi-building-gear'],
                    ['name' => 'Stock Ledger & Valuation', 'slug' => 'stock-ledger', 'token' => 'INV-402', 'view' => 'modules/inventory/stock_ledger', 'icon' => 'bi-journal-text'],
                    ['name' => 'Batch & Serial Tracking', 'slug' => 'batch-serial-tracking', 'token' => 'INV-403', 'view' => 'modules/inventory/batch_serial', 'icon' => 'bi-upc-scan'],
                    ['name' => 'Stock Transfers & Reservations', 'slug' => 'stock-transfers', 'token' => 'INV-404', 'view' => 'modules/inventory/stock_transfers', 'icon' => 'bi-arrow-left-right'],
                    ['name' => 'Physical Stock Reconciliation', 'slug' => 'stock-reconciliation', 'token' => 'INV-405', 'view' => 'modules/inventory/stock_reconciliation', 'icon' => 'bi-clipboard-check']
                ]
            ],
            [
                'name' => 'Manufacturing & MRP',
                'icon' => 'bi-gear-wide-connected',
                'sequence' => 7,
                'pages' => [
                    ['name' => 'Bill of Materials (BOM)', 'slug' => 'bom-management', 'token' => 'MFG-501', 'view' => 'modules/manufacturing/bom', 'icon' => 'bi-diagram-2'],
                    ['name' => 'Work Orders & Operations', 'slug' => 'work-orders', 'token' => 'MFG-502', 'view' => 'modules/manufacturing/work_orders', 'icon' => 'bi-cpu'],
                    ['name' => 'Work Centers & Routing', 'slug' => 'work-centers', 'token' => 'MFG-503', 'view' => 'modules/manufacturing/work_centers', 'icon' => 'bi-tools'],
                    ['name' => 'MRP & Production Costing', 'slug' => 'mrp-console', 'token' => 'MFG-504', 'view' => 'modules/manufacturing/mrp', 'icon' => 'bi-cpu-fill']
                ]
            ],
            [
                'name' => 'India Tax & GST Compliance',
                'icon' => 'bi-receipt',
                'sequence' => 8,
                'pages' => [
                    ['name' => 'HSN/SAC & GST Rates', 'slug' => 'gst-rates-hsn', 'token' => 'TAX-601', 'view' => 'modules/tax_compliance/gst_rates', 'icon' => 'bi-hash'],
                    ['name' => 'E-Invoicing & E-Way Bill Hub', 'slug' => 'einvoice-ewaybill-hub', 'token' => 'TAX-602', 'view' => 'modules/tax_compliance/einvoice_ewaybill', 'icon' => 'bi-qr-code-scan'],
                    ['name' => 'TDS & TCS Management', 'slug' => 'tds-tcs-management', 'token' => 'TAX-603', 'view' => 'modules/tax_compliance/tds_tcs', 'icon' => 'bi-scissors'],
                    ['name' => 'GSTR-1 & GSTR-3B Reports', 'slug' => 'gstr-reports', 'token' => 'TAX-604', 'view' => 'modules/tax_compliance/gstr_reports', 'icon' => 'bi-file-earmark-bar-graph']
                ]
            ],
            [
                'name' => 'Master Data Architecture',
                'icon' => 'bi-sliders',
                'sequence' => 9,
                'pages' => [
                    ['name' => 'Master Data Catalog', 'slug' => 'master-catalog', 'token' => 'MST-701', 'view' => 'modules/master_data/master_catalog', 'icon' => 'bi-folder2-open'],
                    ['name' => 'Security & Audit Controls', 'slug' => 'security-audit-controls', 'token' => 'MST-702', 'view' => 'modules/master_data/security_audit', 'icon' => 'bi-shield-lock']
                ]
            ],
            [
                'name' => 'Workflows & Concurrency',
                'icon' => 'bi-diagram-3',
                'sequence' => 10,
                'pages' => [
                    ['name' => 'Multi-Level Approval Center', 'slug' => 'approval-center', 'token' => 'WFK-801', 'view' => 'modules/workflows_approvals/approval_center', 'icon' => 'bi-check-circle'],
                    ['name' => 'Concurrency & Posting Controls', 'slug' => 'concurrency-posting-controls', 'token' => 'WFK-802', 'view' => 'modules/workflows_approvals/concurrency_controls', 'icon' => 'bi-lock-fill']
                ]
            ],
            [
                'name' => 'Reporting & Analytics Hub',
                'icon' => 'bi-bar-chart-line',
                'sequence' => 11,
                'pages' => [
                    ['name' => 'Executive Analytics Hub', 'slug' => 'executive-analytics-hub', 'token' => 'RPT-901', 'view' => 'modules/reporting_hub/analytics_hub', 'icon' => 'bi-speedometer2']
                ]
            ]
        ];

        foreach ($moduleDefinitions as $mDef) {
            $modId = DB::table('modules')->where('name', $mDef['name'])->value('id');
            if (!$modId) {
                $modId = DB::table('modules')->insertGetId([
                    'name' => $mDef['name'],
                    'icon' => $mDef['icon'],
                    'sequence' => $mDef['sequence'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                DB::table('modules')->where('id', $modId)->update([
                    'icon' => $mDef['icon'],
                    'sequence' => $mDef['sequence'],
                    'updated_at' => now()
                ]);
            }

            foreach ($mDef['pages'] as $pDef) {
                DB::table('pages')->updateOrInsert(
                    ['slug' => $pDef['slug']],
                    [
                        'module_id' => $modId,
                        'name' => $pDef['name'],
                        'token' => $pDef['token'],
                        'title' => $pDef['name'],
                        'is_custom' => true,
                        'custom_view' => $pDef['view'],
                        'icon' => $pDef['icon'],
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        }
    }
}
