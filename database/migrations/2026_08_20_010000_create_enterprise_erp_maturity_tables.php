<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. HR & Payroll
        if (!Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('employee_code')->unique();
                $table->string('first_name');
                $table->string('last_name')->nullable();
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('department')->default('General');
                $table->string('designation')->default('Staff');
                $table->date('joining_date')->nullable();
                $table->decimal('basic_salary', 15, 2)->default(30000.00);
                $table->string('status')->default('Active'); // Active, On Leave, Terminated
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('attendance_logs')) {
            Schema::create('attendance_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->date('date');
                $table->string('status')->default('Present'); // Present, Absent, Half Day, Leave
                $table->time('check_in')->nullable();
                $table->time('check_out')->nullable();
                $table->decimal('total_hours', 5, 2)->default(8.00);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('leave_requests')) {
            Schema::create('leave_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->string('leave_type')->default('Casual'); // Casual, Sick, Earned, Unpaid
                $table->date('start_date');
                $table->date('end_date');
                $table->integer('total_days')->default(1);
                $table->text('reason')->nullable();
                $table->string('status')->default('Pending'); // Pending, Approved, Rejected
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('payroll_runs')) {
            Schema::create('payroll_runs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('payroll_period'); // e.g. 2026-08
                $table->integer('total_employees')->default(0);
                $table->decimal('gross_salary', 15, 2)->default(0.00);
                $table->decimal('pf_deductions', 15, 2)->default(0.00);
                $table->decimal('esi_deductions', 15, 2)->default(0.00);
                $table->decimal('pt_deductions', 15, 2)->default(0.00);
                $table->decimal('net_salary', 15, 2)->default(0.00);
                $table->string('status')->default('Draft'); // Draft, Processed, Approved, Paid
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('payslips')) {
            Schema::create('payslips', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payroll_run_id')->constrained('payroll_runs')->onDelete('cascade');
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->decimal('basic_salary', 15, 2)->default(0.00);
                $table->decimal('hra', 15, 2)->default(0.00);
                $table->decimal('allowances', 15, 2)->default(0.00);
                $table->decimal('pf', 15, 2)->default(0.00);
                $table->decimal('esi', 15, 2)->default(0.00);
                $table->decimal('pt', 15, 2)->default(0.00);
                $table->decimal('net_pay', 15, 2)->default(0.00);
                $table->string('status')->default('Draft');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('expense_claims')) {
            Schema::create('expense_claims', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->string('claim_no')->unique();
                $table->date('claim_date');
                $table->string('category')->default('Travel'); // Travel, Food, Office Supplies, Client Meeting
                $table->decimal('amount', 15, 2)->default(0.00);
                $table->text('description')->nullable();
                $table->string('status')->default('Submitted'); // Submitted, Approved, Paid, Rejected
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }

        // 2. Fixed Assets & Depreciation
        if (!Schema::hasTable('fixed_assets')) {
            Schema::create('fixed_assets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('asset_code')->unique();
                $table->string('name');
                $table->string('category')->default('Machinery'); // Machinery, Furniture, Vehicles, IT Equipment
                $table->date('purchase_date');
                $table->decimal('purchase_cost', 15, 2)->default(0.00);
                $table->decimal('salvage_value', 15, 2)->default(0.00);
                $table->integer('useful_life_years')->default(5);
                $table->string('depreciation_method')->default('Straight Line'); // Straight Line, WDV
                $table->decimal('accumulated_depreciation', 15, 2)->default(0.00);
                $table->decimal('current_book_value', 15, 2)->default(0.00);
                $table->string('status')->default('Active'); // Active, Retired, Sold
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('budgets')) {
            Schema::create('budgets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('fiscal_year')->default('2026-2027');
                $table->string('department')->default('General');
                $table->foreignId('account_id')->constrained('chart_of_accounts')->onDelete('cascade');
                $table->decimal('allocated_amount', 15, 2)->default(0.00);
                $table->decimal('spent_amount', 15, 2)->default(0.00);
                $table->decimal('remaining_amount', 15, 2)->default(0.00);
                $table->timestamps();
            });
        }

        // 3. Purchase Contracts & Vendor Evaluation
        if (!Schema::hasTable('purchase_contracts')) {
            Schema::create('purchase_contracts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('contract_no')->unique();
                $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
                $table->date('start_date');
                $table->date('end_date');
                $table->decimal('max_amount', 15, 2)->default(0.00);
                $table->decimal('remaining_amount', 15, 2)->default(0.00);
                $table->text('terms')->nullable();
                $table->string('status')->default('Active'); // Active, Expired, Terminated
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('vendor_evaluations')) {
            Schema::create('vendor_evaluations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
                $table->date('evaluation_date');
                $table->integer('quality_score')->default(85); // 0-100
                $table->integer('delivery_score')->default(90); // 0-100
                $table->integer('pricing_score')->default(80); // 0-100
                $table->decimal('overall_rating', 4, 2)->default(8.50);
                $table->text('feedback')->nullable();
                $table->foreignId('evaluated_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }

        // 4. Landed Cost Allocation
        if (!Schema::hasTable('landed_cost_vouchers')) {
            Schema::create('landed_cost_vouchers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('voucher_no')->unique();
                $table->unsignedBigInteger('purchase_invoice_id')->nullable();
                $table->decimal('freight_amount', 15, 2)->default(0.00);
                $table->decimal('customs_duty', 15, 2)->default(0.00);
                $table->decimal('insurance_amount', 15, 2)->default(0.00);
                $table->decimal('total_landed_cost', 15, 2)->default(0.00);
                $table->string('status')->default('Posted');
                $table->timestamps();
            });
        }

        // 5. Multi-Currency & Exchange Rates
        if (!Schema::hasTable('currencies')) {
            Schema::create('currencies', function (Blueprint $table) {
                $table->id();
                $table->string('code', 3)->unique(); // INR, USD, EUR, GBP, AED
                $table->string('name');
                $table->string('symbol', 10);
                $table->boolean('is_base')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('exchange_rates')) {
            Schema::create('exchange_rates', function (Blueprint $table) {
                $table->id();
                $table->string('from_currency', 3);
                $table->string('to_currency', 3)->default('INR');
                $table->decimal('rate', 15, 6)->default(1.000000);
                $table->date('effective_date');
                $table->timestamps();
            });
        }

        // 6. Inter-Company Transactions
        if (!Schema::hasTable('intercompany_transactions')) {
            Schema::create('intercompany_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('from_company_id')->constrained('companies')->onDelete('cascade');
                $table->foreignId('to_company_id')->constrained('companies')->onDelete('cascade');
                $table->string('voucher_type'); // Inter-Company Sales, Inter-Company Loan, Management Fee
                $table->string('reference_no');
                $table->decimal('amount', 15, 2)->default(0.00);
                $table->string('status')->default('Reconciled');
                $table->timestamps();
            });
        }

        // 7. Webhooks & Event Subscriptions
        if (!Schema::hasTable('webhooks')) {
            Schema::create('webhooks', function (Blueprint $table) {
                $table->id();
                $table->string('event_name'); // invoice.posted, order.created, stock.low, payment.received
                $table->string('payload_url');
                $table->string('secret')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('webhook_logs')) {
            Schema::create('webhook_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('webhook_id')->constrained('webhooks')->onDelete('cascade');
                $table->string('event_name');
                $table->text('payload');
                $table->integer('response_status')->default(200);
                $table->text('error_message')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
        Schema::dropIfExists('webhooks');
        Schema::dropIfExists('intercompany_transactions');
        Schema::dropIfExists('exchange_rates');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('landed_cost_vouchers');
        Schema::dropIfExists('vendor_evaluations');
        Schema::dropIfExists('purchase_contracts');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('fixed_assets');
        Schema::dropIfExists('expense_claims');
        Schema::dropIfExists('payslips');
        Schema::dropIfExists('payroll_runs');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('attendance_logs');
        Schema::dropIfExists('employees');
    }
};
