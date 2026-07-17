<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use App\Models\User;

class SalesBillingTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $companyId;
    protected $branchId;
    protected $mumbaiCustomer;
    protected $delhiCustomer;
    protected $vendorId;
    protected $keyboardItem;
    protected $mouseItem;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Seed base configuration
        $this->artisan('db:seed');

        // Resolve seeded references
        $this->adminUser = User::where('email', 'admin@mserp.com')->first();
        $this->companyId = $this->adminUser->company_id;
        $this->branchId = $this->adminUser->branch_id;

        // Create testing customers, vendor and items
        $this->mumbaiCustomer = DB::table('customers')->insertGetId([
            'company_id' => $this->companyId,
            'branch_id' => $this->branchId,
            'assigned_user_id' => $this->adminUser->id,
            'name' => 'Mumbai Retail Store',
            'email' => 'mumbai@retail.in',
            'state' => 'Maharashtra',
            'gstin' => '27AAAAA1111A1Z1',
            'city' => 'Mumbai',
            'status' => 'Active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->delhiCustomer = DB::table('customers')->insertGetId([
            'company_id' => $this->companyId,
            'branch_id' => $this->branchId,
            'assigned_user_id' => $this->adminUser->id,
            'name' => 'Delhi Wholesaler',
            'email' => 'delhi@wholesale.in',
            'state' => 'Delhi',
            'gstin' => '07BBBBB2222B1Z2',
            'city' => 'New Delhi',
            'status' => 'Active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->vendorId = DB::table('vendors')->first()->id;
        DB::table('vendors')->where('id', $this->vendorId)->update([
            'state' => 'Maharashtra'
        ]);

        $this->keyboardItem = DB::table('inventory_items')->insertGetId([
            'company_id' => $this->companyId,
            'branch_id' => $this->branchId,
            'item_code' => 'TEST-ITM001',
            'name' => 'Testing Mechanical Keyboard',
            'qty_on_hand' => 100,
            'unit_price' => 1000.00,
            'tax_rate' => 18.00,
            'hsn_sac' => '84713010',
            'reorder_level' => 10,
            'status' => 'In Stock',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->mouseItem = DB::table('inventory_items')->insertGetId([
            'company_id' => $this->companyId,
            'branch_id' => $this->branchId,
            'item_code' => 'TEST-ITM002',
            'name' => 'Testing Wireless Mouse',
            'qty_on_hand' => 50,
            'unit_price' => 500.00,
            'tax_rate' => 12.00,
            'hsn_sac' => '84716060',
            'reorder_level' => 5,
            'status' => 'In Stock',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Test schema tables and columns exist.
     */
    public function test_billing_tables_schema_columns_exist()
    {
        $this->assertTrue(Schema::hasTable('sales_invoice_items'));
        $this->assertTrue(Schema::hasTable('sales_orders'));
        $this->assertTrue(Schema::hasTable('purchase_orders'));
        $this->assertTrue(Schema::hasTable('purchase_invoices'));
        $this->assertTrue(Schema::hasTable('sales_quotations'));
        $this->assertTrue(Schema::hasColumn('companies', 'gstin'));
        $this->assertTrue(Schema::hasColumn('companies', 'state'));
        $this->assertTrue(Schema::hasColumn('inventory_items', 'hsn_sac'));
        $this->assertTrue(Schema::hasColumn('inventory_items', 'tax_rate'));
    }

    /**
     * Test Intra-state billing (Calculates CGST + SGST).
     */
    public function test_intra_state_billing_calculates_cgst_and_sgst()
    {
        $payload = [
            'contact_id' => $this->mumbaiCustomer,
            'document_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'billing_address' => '123 Nariman Point, Mumbai',
            'payment_terms' => 'Net 30',
            'items' => [
                [
                    'inventory_item_id' => $this->keyboardItem,
                    'qty' => 5,
                    'unit_price' => 1000.00
                ]
            ]
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/billing/sales-invoices/invoice/store', $payload);

        // Assert success
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'invoice_id', 'invoice_no']);

        $invoiceId = $response->json('invoice_id');

        // Verify database invoice record
        $invoice = DB::table('sales_invoices')->where('id', $invoiceId)->first();
        $this->assertNotNull($invoice);

        // Subtotal = 5 * 1000 = 5000
        // CGST = 5000 * 9% = 450
        // SGST = 5000 * 9% = 450
        // IGST = 0
        // Total = 5900
        $this->assertEquals(5000.00, floatval($invoice->amount));
        $this->assertEquals(450.00, floatval($invoice->cgst));
        $this->assertEquals(450.00, floatval($invoice->sgst));
        $this->assertEquals(0.00, floatval($invoice->igst));
        $this->assertEquals(5900.00, floatval($invoice->total_amount));

        // Verify line item database record
        $line = DB::table('sales_invoice_items')->where('sales_invoice_id', $invoiceId)->first();
        $this->assertNotNull($line);
        $this->assertEquals(9.00, floatval($line->cgst_rate));
        $this->assertEquals(450.00, floatval($line->cgst_amount));
        $this->assertEquals(9.00, floatval($line->sgst_rate));
        $this->assertEquals(450.00, floatval($line->sgst_amount));
        $this->assertEquals(0.00, floatval($line->igst_amount));

        // Verify inventory decrement (100 - 5 = 95)
        $item = DB::table('inventory_items')->where('id', $this->keyboardItem)->first();
        $this->assertEquals(95, $item->qty_on_hand);
    }

    /**
     * Test Inter-state billing (Calculates IGST).
     */
    public function test_inter_state_billing_calculates_igst()
    {
        $payload = [
            'contact_id' => $this->delhiCustomer,
            'document_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'billing_address' => 'Connaught Place, New Delhi',
            'payment_terms' => 'Net 30',
            'items' => [
                [
                    'inventory_item_id' => $this->mouseItem,
                    'qty' => 10,
                    'unit_price' => 500.00
                ]
            ]
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/billing/sales-invoices/invoice/store', $payload);

        // Assert success
        $response->assertStatus(200);

        $invoiceId = $response->json('invoice_id');

        // Verify database invoice record
        $invoice = DB::table('sales_invoices')->where('id', $invoiceId)->first();
        $this->assertNotNull($invoice);

        // Subtotal = 10 * 500 = 5000
        // IGST = 5000 * 12% = 600
        // CGST/SGST = 0
        // Total = 5600
        $this->assertEquals(5000.00, floatval($invoice->amount));
        $this->assertEquals(0.00, floatval($invoice->cgst));
        $this->assertEquals(0.00, floatval($invoice->sgst));
        $this->assertEquals(600.00, floatval($invoice->igst));
        $this->assertEquals(5600.00, floatval($invoice->total_amount));

        // Verify line item database record
        $line = DB::table('sales_invoice_items')->where('sales_invoice_id', $invoiceId)->first();
        $this->assertNotNull($line);
        $this->assertEquals(0.00, floatval($line->cgst_amount));
        $this->assertEquals(0.00, floatval($line->sgst_amount));
        $this->assertEquals(12.00, floatval($line->igst_rate));
        $this->assertEquals(600.00, floatval($line->igst_amount));

        // Verify inventory decrement (50 - 10 = 40)
        $item = DB::table('inventory_items')->where('id', $this->mouseItem)->first();
        $this->assertEquals(40, $item->qty_on_hand);
    }

    /**
     * Test Out of stock validation throws error and rolls back database.
     */
    public function test_out_of_stock_validation_fails_and_rolls_back()
    {
        $payload = [
            'contact_id' => $this->mumbaiCustomer,
            'document_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'billing_address' => '123 Nariman Point, Mumbai',
            'payment_terms' => 'Net 30',
            'items' => [
                [
                    'inventory_item_id' => $this->mouseItem,
                    'qty' => 60, // Exceeds available stock of 50
                    'unit_price' => 500.00
                ]
            ]
        ];

        // Act
        $initialCount = DB::table('sales_invoices')->count();
        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/billing/sales-invoices/invoice/store', $payload);

        // Assert validation fails on stock limit
        $response->assertStatus(422);
        $response->assertJsonFragment(['error' => 'Insufficient stock for item: Testing Wireless Mouse. Available stock: 50.']);

        // Verify no invoice record was created (DB transaction rolled back)
        $invoiceCount = DB::table('sales_invoices')->count();
        $this->assertEquals($initialCount, $invoiceCount);

        // Verify inventory was not decremented
        $item = DB::table('inventory_items')->where('id', $this->mouseItem)->first();
        $this->assertEquals(50, $item->qty_on_hand);
    }

    /**
     * Test cancelling invoice restores stock quantities.
     */
    public function test_cancelling_invoice_restores_stock_levels()
    {
        // 1. Create a successful invoice
        $payload = [
            'contact_id' => $this->mumbaiCustomer,
            'document_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'billing_address' => '123 Nariman Point, Mumbai',
            'payment_terms' => 'Net 30',
            'items' => [
                [
                    'inventory_item_id' => $this->mouseItem,
                    'qty' => 10,
                    'unit_price' => 500.00
                ]
            ]
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/billing/sales-invoices/invoice/store', $payload);

        $invoiceId = $response->json('invoice_id');

        // Check stock is decremented
        $itemBefore = DB::table('inventory_items')->where('id', $this->mouseItem)->first();
        $this->assertEquals(40, $itemBefore->qty_on_hand);

        // 2. Cancel the invoice via destroy endpoint
        $cancelResponse = $this->actingAs($this->adminUser)
            ->deleteJson("/api/billing/sales-invoices/invoice/destroy/{$invoiceId}");

        $cancelResponse->assertStatus(200);
        $cancelResponse->assertJson(['success' => true]);

        // Verify invoice and lines deleted from database
        $this->assertFalse(DB::table('sales_invoices')->where('id', $invoiceId)->exists());
        $this->assertFalse(DB::table('sales_invoice_items')->where('sales_invoice_id', $invoiceId)->exists());

        // Verify stock is restored
        $itemAfter = DB::table('inventory_items')->where('id', $this->mouseItem)->first();
        $this->assertEquals(50, $itemAfter->qty_on_hand);
    }

    /**
     * Test creating a Sales Order does not modify inventory stock.
     */
    public function test_sales_order_does_not_affect_stock()
    {
        $payload = [
            'contact_id' => $this->mumbaiCustomer,
            'document_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'billing_address' => '123 Nariman Point, Mumbai',
            'payment_terms' => 'Net 30',
            'items' => [
                [
                    'inventory_item_id' => $this->keyboardItem,
                    'qty' => 20,
                    'unit_price' => 1000.00
                ]
            ]
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/billing/sales-orders/invoice/store', $payload);

        $response->assertStatus(200);
        $this->assertTrue(DB::table('sales_orders')->where('id', $response->json('invoice_id'))->exists());

        // Stock should remain at 100 (unaffected by sales orders)
        $item = DB::table('inventory_items')->where('id', $this->keyboardItem)->first();
        $this->assertEquals(100, $item->qty_on_hand);
    }

    /**
     * Test creating a Purchase Order does not modify inventory stock.
     */
    public function test_purchase_order_does_not_affect_stock()
    {
        $payload = [
            'contact_id' => $this->vendorId,
            'document_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'billing_address' => 'Vendor Street, Pune',
            'payment_terms' => 'Net 30',
            'items' => [
                [
                    'inventory_item_id' => $this->keyboardItem,
                    'qty' => 30,
                    'unit_price' => 1000.00
                ]
            ]
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/billing/purchase-orders/invoice/store', $payload);

        $response->assertStatus(200);
        $this->assertTrue(DB::table('purchase_orders')->where('id', $response->json('invoice_id'))->exists());

        // Stock should remain at 100 (unaffected by purchase orders)
        $item = DB::table('inventory_items')->where('id', $this->keyboardItem)->first();
        $this->assertEquals(100, $item->qty_on_hand);
    }

    /**
     * Test recording a Vendor Bill increments stock, and cancelling it restores it.
     */
    public function test_vendor_bill_increments_stock_and_cancellation_restores_it()
    {
        $payload = [
            'contact_id' => $this->vendorId,
            'document_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'billing_address' => 'Vendor Street, Pune',
            'payment_terms' => 'Net 30',
            'items' => [
                [
                    'inventory_item_id' => $this->keyboardItem,
                    'qty' => 15,
                    'unit_price' => 1000.00
                ]
            ]
        ];

        // 1. Record vendor bill
        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/billing/purchase-invoices/invoice/store', $payload);

        $response->assertStatus(200);
        $billId = $response->json('invoice_id');
        $this->assertTrue(DB::table('purchase_invoices')->where('id', $billId)->exists());

        // Stock should increment: 100 + 15 = 115
        $itemBefore = DB::table('inventory_items')->where('id', $this->keyboardItem)->first();
        $this->assertEquals(115, $itemBefore->qty_on_hand);

        // 2. Cancel vendor bill
        $cancelResponse = $this->actingAs($this->adminUser)
            ->deleteJson("/api/billing/purchase-invoices/invoice/destroy/{$billId}");

        $cancelResponse->assertStatus(200);
        $cancelResponse->assertJson(['success' => true]);

        // Stock should decrement back to 100
        $itemAfter = DB::table('inventory_items')->where('id', $this->keyboardItem)->first();
        $this->assertEquals(100, $itemAfter->qty_on_hand);
    }

    /**
     * Test creating a Sales Quotation does not modify inventory stock.
     */
    public function test_sales_quotation_does_not_affect_stock()
    {
        $payload = [
            'contact_id' => $this->mumbaiCustomer,
            'document_date' => now()->toDateString(),
            'due_date' => now()->addDays(15)->toDateString(),
            'billing_address' => '123 Nariman Point, Mumbai',
            'payment_terms' => 'Net 15',
            'items' => [
                [
                    'inventory_item_id' => $this->keyboardItem,
                    'qty' => 5,
                    'unit_price' => 1000.00
                ]
            ]
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/billing/sales-quotations/invoice/store', $payload);

        $response->assertStatus(200);
        $this->assertTrue(DB::table('sales_quotations')->where('id', $response->json('invoice_id'))->exists());

        // Stock should remain at 100
        $item = DB::table('inventory_items')->where('id', $this->keyboardItem)->first();
        $this->assertEquals(100, $item->qty_on_hand);
    }
}
