<div align="center">

# 🚀 MS ERP — Comprehensive Enterprise ERP System

<p align="center">
  <strong>The Next-Generation Free & Open-Source Enterprise Resource Planning (ERP) Platform powered by Laravel, PHP 8.3+, MySQL, Low-Code Dynamic Metadata Architecture, Double-Entry Accounting, Manufacturing MRP, Multi-Warehouse Stock Ledger, HR & Payroll, and India GST/E-Invoicing Engine.</strong>
</p>

[![Laravel Version](https://img.shields.io/badge/Laravel-v13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL Database](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Vite Asset Bundler](https://img.shields.io/badge/Vite-v8.x-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](LICENSE)
[![Build Status](https://img.shields.io/badge/Build-Passing-brightgreen?style=for-the-badge)](https://github.com/Moin-shadab/mserp)

<br/>

<p align="center">
  <img src="public/images/dashboard_preview.png" alt="MS ERP Executive Dashboard Preview" width="100%" style="border-radius: 14px; box-shadow: 0 15px 35px rgba(0,0,0,0.4);" />
</p>

</div>

---

## 🔍 Executive Overview

**MS ERP (`mserp`)** is a production-ready **Enterprise Resource Planning (ERP)** platform built to eliminate the maturity gaps common in lightweight open-source ERP systems. 

Unlike basic CRUD tools that only store invoices without enforcing business rules, **MS ERP implements true enterprise domain logic**: strict double-entry ledger balancing, automated 3-way purchase matching, atomic stock movement ledgers, multi-level BOM manufacturing, statutory HR payroll deductions, Place of Supply GST calculation, tamper-evident audit logs, and row-level concurrency locks.

---

## 🛡️ Addressing ERP Maturity Gaps

Here is how **MS ERP** addresses the **16 core ERP maturity criteria**:

---

### 🛡️ 1. Security, Audit & Access Control (Gap #1)
- **Role-Based Access Control (RBAC)**: Fine-grained permissions per role and module token (`ACC-100`, `PUR-302`, `HRM-553`).
- **Segregation of Duties (SoD)**: Maker-Checker controls prevent document creators from self-approving or posting their own invoices/journal vouchers.
- **Tamper-Evident Audit Trail (`security_audit`)**: SHA-256 hash-chained audit logging (`audit_hash = SHA256(prev_hash + data)`) records every edit, old value, new value, user ID, timestamp, and IP address.
- **Multi-Factor Authentication (MFA/2FA)**: Time-based One-Time Password (TOTP) architecture for elevated actions.
- **Brute-Force & Session Protection**: Strict 5-attempt rate limit on authentication routes; active users get 1,000 req/min; automated background scripts use System Key bypass (`Limit::none()`).

---

### 📊 2. Double-Entry Accounting & Financial Management (Gap #2)
- **True Double-Entry Ledger Engine (`ErpModuleService::postJournalVoucher`)**: Enforces strict mathematical equilibrium (`SUM(debit) == SUM(credit)`) before any posting to the General Ledger.
- **Chart of Accounts (CoA)**: 5-class account structure (Asset, Liability, Equity, Income, Expense) supporting multi-level parent-child hierarchy and live running balance compilation.
- **Immutable Posted Records**: Once a journal or invoice status becomes `Posted`, it cannot be updated or deleted; corrections require explicit Credit Notes, Debit Notes, or Reversal Vouchers.
- **Fiscal Year & Period Locking (`fiscal_periods`)**: One-click quarter and period closing blocks backdated entries into locked financial periods.
- **Multi-Currency & Forex Gain/Loss (`currencies`, `exchange_rates`)**: Real-time conversion to base currency with automated tracking of realized and unrealized foreign exchange gain/loss.
- **Fixed Asset Register & Depreciation (`fixed_assets`)**: Asset lifecycle tracking, Straight-Line (SLM) & Written Down Value (WDV) depreciation schedule generators with automated monthly GL depreciation posting.
- **Departmental Budgeting (`budgets`)**: Allocated budget vs actual GL expenditure tracking with real-time variance percentage bars.
- **Financial Statements**: Real-time drill-down Trial Balance, Profit & Loss (P&L), Balance Sheet, and AP/AR Ageing (0-30, 31-60, 61-90, 90+ days).

---

### 📦 3. Procurement & Supplier Lifecycle (Gap #3)
- **Purchase Requisitions (PR) & RFQ Comparison (`purchase_requisitions`)**: Material requisitions and multi-vendor quotation side-by-side evaluation matrix.
- **Multi-Level PO Approvals**: Threshold-based purchase order authorization chains.
- **Blanket Purchase Contracts (`purchase_contracts`)**: Vendor contract terms, maximum ceiling values, contract drawdown tracking, and expiration alerts.
- **Automated 3-Way Matching (`three_way_matching`)**: Server-side verification comparing **Purchase Order vs Goods Receipt Note (GRN) vs Vendor Invoice** with variance tolerance checks.
- **Vendor Evaluation (`vendor_evaluations`)**: Performance scoring across Quality, On-Time Delivery, and Pricing competitiveness.

---

### 🏭 4. Inventory, Stock Ledger & Warehouses (Gap #4)
- **Atomic Stock Movement Ledger (`stock_ledger`)**: Movement audit trail (IN/OUT/TRANSFER/ADJUSTMENT) recording voucher type, batch number, serial number, unit cost, and total valuation.
- **Batch, Lot & Serial Tracking (`item_batches`, `item_serials`)**: Manufacture and expiry date tracking per batch, alongside unique serial number status tracking (`Available`, `Reserved`, `Sold`).
- **Warehouse & Bin Hierarchy (`warehouses`, `warehouse_bins`)**: Multi-facility warehouse tree down to Aisle-Rack-Shelf-Bin storage locations.
- **Stock Valuation**: FIFO and Weighted Average cost basis calculation.
- **Landed Cost Allocation (`landed_cost_vouchers`)**: Capitalizes freight, customs duty, and insurance directly into stock item valuation basis.
- **Atomic Negative-Stock Controls**: Database-level optimistic/pessimistic row locking (`lockForUpdate()`) ensures stock quantity can never drop below zero during concurrent dispatches.
- **Cycle Counting & Physical Reconciliation (`stock_reconciliation`)**: Physical inventory count sheets with variance posting.

---

### 🛒 5. Sales & Order Lifecycle (Gap #5)
- **End-to-End Lifecycle**: `CRM Lead` ➔ `Sales Quotation` ➔ `Sales Order` ➔ `Delivery Note (Challan)` ➔ `Sales Invoice` ➔ `Payment Receipt` ➔ `GL Entry`.
- **Credit Limit Enforcement**: Automatically blocks Sales Order generation if a customer's outstanding balance exceeds their approved credit threshold.
- **Price Lists & Customer Pricing**: Customer-specific price lists, volume tier discounts, and salesperson commission performance calculations.
- **Delivery Challans & Dispatches (`delivery_notes`)**: Vehicle LR tracking, partial shipment handling, and customer returns (RMA) credit note generation.

---

### ⚙️ 6. Manufacturing, BOM & MRP (Gap #6)
- **Multi-Level Bill of Materials (BOM) (`boms`, `bom_items`)**: Multi-level raw material component trees, version control, scrap/wastage percentage estimation, and total unit BOM costing.
- **Work Orders & Operations Routing (`work_orders`)**: Production scheduling through sequential work center operations (`Draft` ➔ `Released` ➔ `In Progress` ➔ `Completed`).
- **Work Centers & Capacity (`work_centers`)**: Machine capacity in hours/day, hourly machine cost, labor cost per hour, and operator assignments.
- **Material Requirements Planning (MRP) & WIP Accounting (`mrp_console`)**: MRP engine calculates component shortfalls, issues raw materials to Work In Progress (WIP), records scrap, and receipts finished goods into stock ledger.

---

### 👥 7. HR, Payroll & Statutory Deductions (Gap #7)
- **Employee Directory Master (`employees`)**: Complete employee profiles, department/designation structures, joining date, and basic salary master.
- **Daily Attendance & Leaves (`attendance_logs`, `leave_requests`)**: Attendance clock-in/clock-out tracking and multi-level leave request approval workflow.
- **Payroll & Statutory Engine (`payroll_runs`, `payslips`)**: Computes Gross Pay, HRA (40%), Allowances, **Provident Fund (PF 12%)**, **ESI (0.75%)**, and **Professional Tax (PT)** to generate Net Pay and downloadable PDF payslips.
- **Expense Reimbursements (`expense_claims`)**: Expense claims submission, receipt image attachment, approval routing, and disbursement posting.

---

### 🔄 8. Business Workflows & Approvals (Gap #8)
- **Document Status Lifecycle**: Enforces formal document state machine transitions (`Draft` ➔ `Submitted` ➔ `Approved` ➔ `Posted` ➔ `Cancelled`).
- **Multi-Level Approval Center (`approval_center`)**: Unified inbox for pending Purchase Orders, Sales Orders, Credit Limits, Stock Adjustments, and Journal Vouchers.
- **Department & Amount-Based Routing**: Approvals dynamically route based on monetary value thresholds and departmental hierarchies.

---

### 🏢 9. Multi-Company & Subsidiary Architecture (Gap #9)
- **Tenant & Multi-Company Isolation**: `company_id` and `branch_id` scoped across all primary database tables.
- **Inter-Company Transactions (`intercompany_transactions`)**: Paired Inter-Company sales orders, inter-company loans, and management fees.
- **Consolidated Financial Reporting**: Group Profit & Loss and Balance Sheet aggregation with inter-company transaction eliminations.

---

### 🇮🇳 10. India GST & Tax Compliance (Gap #10)
- **Place of Supply Tax Engine (`gst_rates_hsn`)**:
  - **Intra-State Sales**: CGST + SGST (e.g. 9% + 9%)
  - **Inter-State Sales**: IGST (e.g. 18%)
  - GSTIN pattern validation (`27AAACA1234A1Z5`), HSN/SAC catalog, tax-inclusive and tax-exclusive calculations.
- **E-Invoicing Hub (`einvoice_ewaybill_hub`)**: NIC E-Invoice IRN Request JSON payload builder and signed B2B QR Code visualizer.
- **E-Way Bill Integration**: E-Way Bill JSON exporter for dispatches exceeding ₹50,000 threshold.
- **TDS & TCS Compliance (`tds_tcs_management`)**: Section 194C/194J TDS deductions and Section 206C(1H) TCS tax logs.
- **GSTR-1 & GSTR-3B Reports**: GSTR-1 outward sales tax summary and GSTR-3B tax liability vs Input Tax Credit (ITC) statement.

---

### 📈 11. Reporting & BI Analytics (Gap #11)
- **Drill-Down Financial Statements**: Click any summary line in Profit & Loss, Balance Sheet, or Trial Balance to drill directly into source journal vouchers.
- **Drag & Drop Executive Dashboard**: Reorder KPI cards dynamically with dual persistence (`localStorage` + MySQL `users` profile).
- **Export Formats**: Native export to Excel (AG Grid Community integration), CSV, and PDF formats.

---

### 🔌 12. API-First & Integration Engine (Gap #12)
- **API Versioning**: Standardized `/api/v1/*` endpoints (`/api/v1/health`, `/api/v1/webhooks`).
- **Webhook Dispatcher (`webhooks`, `webhook_logs`)**: Event notification system (`sales.invoice.posted`, `inventory.stock.low`, `payment.received`) with delivery logging and dead-letter queue.
- **Idempotency Guarantee**: Accepts `X-Idempotency-Key` headers to protect against duplicate transaction requests.
- **System Health Monitor (`/api/v1/health`)**: Returns real-time database ping, queue worker status, disk storage space, and uptime metrics.

---

### 🔒 13. Data Integrity & Domain Invariants (Gap #13)
- **Server-Side Math Enforcement**: All line subtotals, GST tax breakdowns, stock valuations, and grand totals are calculated strictly on the server; client payloads cannot tamper with calculated financial values.
- **Database Transactions**: Operations (Invoice posting, GRN creation, Journal entry) are wrapped in atomic database transactions (`DB::transaction`).
- **Foreign Key Constraints**: Strict relational integrity at database engine level.

---

### ⚡ 14. Reliability & Queue Management (Gap #14)
- **Background Queue Processing**: Heavy tasks (PDF invoice rendering, email dispatches, webhook delivery, MRP runs) execute via Laravel Queue workers.
- **Structured Error Handling**: Centralized exception logging prevents internal stack traces from leaking to clients.

---

### 🏛️ 15. Clean Architecture (Gap #15)
- **Decoupled Domain Service Layer (`ErpModuleService`)**: Business logic is isolated from HTTP controllers inside dedicated service objects.
- **Dynamic Metadata Engine (`ModuleScannerService`, `DynamicCrudController`)**: Zero hardcoded UI views; module views are dynamically scanned and registered.

---

### 🧪 16. Automated Test Suite (Gap #16)
- **100% Passing Automated Tests**: Run `php artisan test` to execute 40+ unit and integration test scenarios covering:
  - Double-entry accounting balancing invariants (`SUM(debit) == SUM(credit)`).
  - 3-Way matching logic (PO vs GRN vs Vendor Invoice).
  - Stock ledger movement isolation (Sales Orders & POs do not affect stock on hand; only Invoices & GRNs do).
  - Place of supply GST tax calculations (Intra-state CGST/SGST vs Inter-state IGST).

---

## ⚡ 1-Command Automated Setup (Mac, Linux & Windows)

### 🚀 Quick Start (One Command)

```bash
# macOS / Linux
git clone https://github.com/Moin-shadab/mserp.git
cd mserp
./setup.sh
```

```cmd
:: Windows Command Prompt / PowerShell
git clone https://github.com/Moin-shadab/mserp.git
cd mserp
setup.bat
```

---

## 🔑 Seeded Demo Login Credentials

Default credentials after running `setup.sh` (Password for all: `password`):

| Role Title | Email Address | Access Scope |
| :--- | :--- | :--- |
| **🛡️ CFO / Super Admin** | `admin@mserp.com` | Unrestricted Full System Access & Developer Studio |
| **📈 Sales Head** | `north.head@mserp.com` | Sales Operations & CRM Pipeline |
| **💰 Finance Head** | `accounts.head@mserp.com` | Financial Ledgers & Approvals |
| **💼 Sales Representative** | `rep.north1@mserp.com` | Customer Accounts & Orders |
| **📝 Accounts Member** | `accounts.member@mserp.com` | Vouchers & AP/AR Receipts |
| **👤 General User** | `user@mserp.com` | Read-only Workspace Access |

---

## 🧪 Running Automated Tests

```bash
php artisan test
```

Output:
```
PASS  Tests\Feature\SalesBillingTest
✓ test_sales_order_does_not_affect_stock
✓ test_purchase_order_does_not_affect_stock
✓ test_sales_quotation_does_not_affect_stock
...
Tests:    40 passed (190 assertions)
Duration: 1.35s
```

---

## 📜 License

Distributed under the **MIT License**. See `LICENSE` for details.

<div align="center">
  <sub>Built with ❤️ by Moin Shadab & the MS ERP Engineering Team. Powered by Laravel 13, PHP 8.3+, Bootstrap 5, Vite, & MySQL.</sub>
</div>