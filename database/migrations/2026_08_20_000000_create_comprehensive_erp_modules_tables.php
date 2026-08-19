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
        // 1. Chart of Accounts
        if (!Schema::hasTable('chart_of_accounts')) {
            Schema::create('chart_of_accounts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('code')->unique();
                $table->string('name');
                $table->enum('type', ['Asset', 'Liability', 'Equity', 'Income', 'Expense']);
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->decimal('opening_balance', 15, 2)->default(0.00);
                $table->decimal('current_balance', 15, 2)->default(0.00);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Journal Entries & Lines
        if (!Schema::hasTable('journal_entries')) {
            Schema::create('journal_entries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('set null');
                $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
                $table->string('voucher_no')->unique();
                $table->date('entry_date');
                $table->text('narration')->nullable();
                $table->decimal('total_debit', 15, 2)->default(0.00);
                $table->decimal('total_credit', 15, 2)->default(0.00);
                $table->enum('status', ['Draft', 'Posted', 'Cancelled'])->default('Draft');
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('posted_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('journal_entry_lines')) {
            Schema::create('journal_entry_lines', function (Blueprint $table) {
                $table->id();
                $table->foreignId('journal_entry_id')->constrained('journal_entries')->onDelete('cascade');
                $table->foreignId('account_id')->constrained('chart_of_accounts')->onDelete('cascade');
                $table->decimal('debit', 15, 2)->default(0.00);
                $table->decimal('credit', 15, 2)->default(0.00);
                $table->string('line_narration')->nullable();
                $table->timestamps();
            });
        }

        // 3. General Ledger
        if (!Schema::hasTable('general_ledger')) {
            Schema::create('general_ledger', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('account_id')->constrained('chart_of_accounts')->onDelete('cascade');
                $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->onDelete('cascade');
                $table->date('entry_date');
                $table->decimal('debit', 15, 2)->default(0.00);
                $table->decimal('credit', 15, 2)->default(0.00);
                $table->decimal('running_balance', 15, 2)->default(0.00);
                $table->string('source_document_type')->nullable();
                $table->unsignedBigInteger('source_document_id')->nullable();
                $table->timestamps();
            });
        }

        // 4. Fiscal Periods
        if (!Schema::hasTable('fiscal_periods')) {
            Schema::create('fiscal_periods', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('name');
                $table->string('code')->unique();
                $table->date('start_date');
                $table->date('end_date');
                $table->boolean('is_closed')->default(false);
                $table->timestamp('closed_at')->nullable();
                $table->foreignId('closed_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }

        // 5. Credit & Debit Notes
        if (!Schema::hasTable('credit_debit_notes')) {
            Schema::create('credit_debit_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
                $table->string('note_no')->unique();
                $table->enum('note_type', ['Credit Note', 'Debit Note']);
                $table->enum('party_type', ['Customer', 'Vendor']);
                $table->unsignedBigInteger('party_id');
                $table->string('reference_invoice_no')->nullable();
                $table->decimal('amount', 15, 2)->default(0.00);
                $table->decimal('gst_amount', 15, 2)->default(0.00);
                $table->decimal('total_amount', 15, 2)->default(0.00);
                $table->text('reason')->nullable();
                $table->string('status')->default('Draft');
                $table->timestamps();
            });
        }

        // 6. CRM & Leads
        if (!Schema::hasTable('leads')) {
            Schema::create('leads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('assigned_user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('title');
                $table->string('company_name')->nullable();
                $table->string('contact_name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->enum('stage', ['New', 'Contacted', 'Qualified', 'Lost', 'Converted'])->default('New');
                $table->decimal('estimated_value', 15, 2)->default(0.00);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 7. Sales Quotations & Orders
        if (!Schema::hasTable('sales_quotations')) {
            Schema::create('sales_quotations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
                $table->string('quotation_no')->nullable();
                $table->string('quote_no')->nullable();
                $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
                $table->date('quotation_date')->nullable();
                $table->date('quote_date')->nullable();
                $table->date('valid_until')->nullable();
                $table->decimal('total_amount', 15, 2)->default(0.00);
                $table->string('status')->default('Draft');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sales_orders')) {
            Schema::create('sales_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
                $table->string('order_no')->unique();
                $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
                $table->date('order_date');
                $table->date('delivery_date')->nullable();
                $table->decimal('subtotal', 15, 2)->default(0.00);
                $table->decimal('tax_amount', 15, 2)->default(0.00);
                $table->decimal('total_amount', 15, 2)->default(0.00);
                $table->string('status')->default('Draft');
                $table->boolean('credit_limit_approved')->default(true);
                $table->foreignId('salesperson_id')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }

        // 8. Delivery Notes (Challan)
        if (!Schema::hasTable('delivery_notes')) {
            Schema::create('delivery_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('delivery_no')->unique();
                $table->foreignId('sales_order_id')->nullable()->constrained('sales_orders')->onDelete('set null');
                $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
                $table->date('delivery_date');
                $table->string('vehicle_no')->nullable();
                $table->string('lr_no')->nullable();
                $table->string('status')->default('Draft');
                $table->timestamps();
            });
        }

        // 9. Purchase Requisitions & POs
        if (!Schema::hasTable('purchase_requisitions')) {
            Schema::create('purchase_requisitions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('requisition_no')->unique();
                $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
                $table->date('required_date')->nullable();
                $table->string('status')->default('Draft');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
                $table->string('po_no')->unique();
                $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
                $table->date('po_date');
                $table->date('delivery_date')->nullable();
                $table->date('expected_delivery_date')->nullable();
                $table->decimal('subtotal', 15, 2)->default(0.00);
                $table->decimal('tax_amount', 15, 2)->default(0.00);
                $table->decimal('total_amount', 15, 2)->default(0.00);
                $table->string('status')->default('Draft');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('goods_receipt_notes')) {
            Schema::create('goods_receipt_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('grn_no')->unique();
                $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->onDelete('set null');
                $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
                $table->date('received_date');
                $table->string('status')->default('Draft');
                $table->timestamps();
            });
        }

        // 10. Warehouses & Bins
        if (!Schema::hasTable('warehouses')) {
            Schema::create('warehouses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
                $table->string('name');
                $table->string('code')->unique();
                $table->string('address')->nullable();
                $table->string('manager_name')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('warehouse_bins')) {
            Schema::create('warehouse_bins', function (Blueprint $table) {
                $table->id();
                $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
                $table->string('bin_code');
                $table->string('aisle')->nullable();
                $table->string('rack')->nullable();
                $table->string('shelf')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 11. Stock Ledger & Batches/Serials
        if (!Schema::hasTable('stock_ledger')) {
            Schema::create('stock_ledger', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
                $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
                $table->string('voucher_type');
                $table->string('voucher_no');
                $table->integer('qty_in')->default(0);
                $table->integer('qty_out')->default(0);
                $table->integer('balance_qty')->default(0);
                $table->decimal('unit_cost', 15, 2)->default(0.00);
                $table->decimal('total_valuation', 15, 2)->default(0.00);
                $table->string('batch_no')->nullable();
                $table->string('serial_no')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('item_batches')) {
            Schema::create('item_batches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
                $table->string('batch_no');
                $table->date('manufacture_date')->nullable();
                $table->date('expiry_date')->nullable();
                $table->integer('qty_on_hand')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('item_serials')) {
            Schema::create('item_serials', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
                $table->string('serial_no')->unique();
                $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null');
                $table->string('status')->default('Available');
                $table->timestamps();
            });
        }

        // 12. Manufacturing BOM & Work Orders
        if (!Schema::hasTable('boms')) {
            Schema::create('boms', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('bom_no')->unique();
                $table->foreignId('item_id')->constrained('inventory_items')->onDelete('cascade');
                $table->integer('qty')->default(1);
                $table->decimal('total_cost', 15, 2)->default(0.00);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('bom_items')) {
            Schema::create('bom_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bom_id')->constrained('boms')->onDelete('cascade');
                $table->foreignId('component_item_id')->constrained('inventory_items')->onDelete('cascade');
                $table->decimal('qty', 10, 4)->default(1.0000);
                $table->decimal('unit_cost', 15, 2)->default(0.00);
                $table->decimal('scrap_percent', 5, 2)->default(0.00);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('work_centers')) {
            Schema::create('work_centers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('name');
                $table->string('code')->unique();
                $table->integer('capacity_per_day')->default(8); // hours
                $table->decimal('hourly_cost', 15, 2)->default(500.00);
                $table->decimal('labor_cost', 15, 2)->default(250.00);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('work_orders')) {
            Schema::create('work_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('work_order_no')->unique();
                $table->foreignId('bom_id')->constrained('boms')->onDelete('cascade');
                $table->foreignId('item_id')->constrained('inventory_items')->onDelete('cascade');
                $table->integer('qty')->default(1);
                $table->date('start_date')->nullable();
                $table->date('completion_date')->nullable();
                $table->string('status')->default('Draft');
                $table->timestamps();
            });
        }

        // 13. India GST & Tax Masters
        if (!Schema::hasTable('hsn_sac_codes')) {
            Schema::create('hsn_sac_codes', function (Blueprint $table) {
                $table->id();
                $table->string('code', 10)->unique();
                $table->string('description');
                $table->enum('type', ['HSN', 'SAC'])->default('HSN');
                $table->decimal('cgst_rate', 5, 2)->default(9.00);
                $table->decimal('sgst_rate', 5, 2)->default(9.00);
                $table->decimal('igst_rate', 5, 2)->default(18.00);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('einvoice_logs')) {
            Schema::create('einvoice_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sales_invoice_id')->constrained('sales_invoices')->onDelete('cascade');
                $table->string('irn', 100)->nullable();
                $table->string('ack_no')->nullable();
                $table->timestamp('ack_date')->nullable();
                $table->text('qr_code_payload')->nullable();
                $table->enum('status', ['PENDING', 'GENERATED', 'CANCELLED', 'FAILED'])->default('PENDING');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ewaybill_logs')) {
            Schema::create('ewaybill_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('delivery_note_id')->constrained('delivery_notes')->onDelete('cascade');
                $table->string('ewaybill_no', 20)->nullable();
                $table->timestamp('generated_date')->nullable();
                $table->timestamp('valid_until')->nullable();
                $table->integer('distance_km')->default(100);
                $table->enum('status', ['GENERATED', 'CANCELLED'])->default('GENERATED');
                $table->timestamps();
            });
        }

        // 14. Master Data: UOM Conversions & Price Lists & Numbering Series
        if (!Schema::hasTable('uom_conversions')) {
            Schema::create('uom_conversions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('from_uom_id')->constrained('uoms')->onDelete('cascade');
                $table->foreignId('to_uom_id')->constrained('uoms')->onDelete('cascade');
                $table->decimal('conversion_factor', 12, 4)->default(1.0000);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('numbering_series')) {
            Schema::create('numbering_series', function (Blueprint $table) {
                $table->id();
                $table->string('module')->unique();
                $table->string('prefix');
                $table->integer('current_number')->default(1000);
                $table->integer('padding')->default(5);
                $table->string('suffix')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('numbering_series');
        Schema::dropIfExists('uom_conversions');
        Schema::dropIfExists('ewaybill_logs');
        Schema::dropIfExists('einvoice_logs');
        Schema::dropIfExists('hsn_sac_codes');
        Schema::dropIfExists('work_orders');
        Schema::dropIfExists('work_centers');
        Schema::dropIfExists('bom_items');
        Schema::dropIfExists('boms');
        Schema::dropIfExists('item_serials');
        Schema::dropIfExists('item_batches');
        Schema::dropIfExists('stock_ledger');
        Schema::dropIfExists('warehouse_bins');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('goods_receipt_notes');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('purchase_requisitions');
        Schema::dropIfExists('delivery_notes');
        Schema::dropIfExists('sales_orders');
        Schema::dropIfExists('sales_quotations');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('credit_debit_notes');
        Schema::dropIfExists('fiscal_periods');
        Schema::dropIfExists('general_ledger');
        Schema::dropIfExists('journal_entry_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('chart_of_accounts');
    }
};
