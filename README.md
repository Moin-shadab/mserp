<div align="center">

# 🚀 MS ERP — Production-Hardened Enterprise Platform

<p align="center">
  <strong>The Next-Generation Free & Open-Source Enterprise Resource Planning (ERP) Platform powered by Laravel, PHP 8.3+, MySQL, Low-Code Metadata Architecture, Double-Entry Accounting, Manufacturing MRP, Multi-Warehouse Stock Ledger, HR & Payroll, and India GST Engine.</strong>
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

## 🏛️ Production Hardening & Security Architecture

To ensure enterprise-grade reliability and security before processing financial data, **MS ERP** enforces strict architectural boundaries:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                      PRODUCTION SECURITY & GOVERNANCE                       │
├─────────────────┬───────────────────┬───────────────────┬───────────────────┤
│ SQL SECURITY    │ PROTECTED TABLE   │ MAKER-CHECKER     │ LIVE HEALTH &     │
│ ANALYZER        │ CLASSIFICATION    │ SOD CONTROLS      │ DIAGNOSTICS       │
├─────────────────┼───────────────────┼───────────────────┼───────────────────┤
│ SELECT-Only     │ 🔴 Immutable GL   │ Creator cannot    │ Real DB latency,  │
│ Query Parser    │ 🟠 Controlled Inv │ approve/post own  │ Cache R/W, Disk & │
│ No DDL/DML      │ 🟢 Master Data    │ vouchers          │ Queue metrics     │
└─────────────────┴───────────────────┴───────────────────┴───────────────────┘
```

---

## 🔐 1. Protected Table Classification System

Generic CRUD controllers and API endpoints are strictly restricted based on table classification:

- 🔴 **SYSTEM / IMMUTABLE TABLES** (`general_ledger`, `journal_entries`, `journal_entry_lines`, `stock_ledger`, `audit_logs`, `security_audit`):
  - Generic CRUD `UPDATE` or `DELETE` is **STRICTLY BLOCKED** at the domain service level.
  - Financial records can only be mutated through valid business transactions (Credit/Debit Notes, Reversal Vouchers).
- 🟠 **CONTROLLED TABLES** (`sales_invoices`, `purchase_invoices`, `payments`):
  - Direct deletion is **STRICTLY BLOCKED**; status must transition through formal workflow states (`Void`, `Cancelled`).
- 🟢 **MASTER DATA TABLES** (`customers`, `vendors`, `inventory_items`, `warehouses`, `employees`):
  - Managed via strict RBAC authorization (`view`, `create`, `edit`, `delete` page tokens).
- 🔵 **CUSTOM TABLES**:
  - Developer-defined module tables with server-side column whitelisting and input validation.

---

## 🛡️ 2. Defense-in-Depth & SQL Security

- **SQL Security Analyzer (`App\Services\SqlSecurityAnalyzer`)**:
  - Validates developer SQL queries to strictly enforce `SELECT`-only execution.
  - Automatically rejects multi-statement SQL (`SELECT...; DROP...`), destructive DDL (`DROP`, `ALTER`, `TRUNCATE`), DML modifications (`INSERT`, `UPDATE`, `DELETE`), and privilege escalation (`GRANT`, `REVOKE`).
- **Server-Side Field Whitelisting**:
  - Request payloads are filtered against approved form schemas and database column listings; arbitrary or un-whitelisted client fields are automatically discarded.
- **No Runtime DDL Executions**:
  - Dynamic controllers do not create or alter database tables at request time (`Schema::create`). All database schemas are managed through version-controlled migrations (`php artisan migrate`).

---

## ⚡ 3. Live System Health Diagnostics (`/api/v1/health`)

The health endpoint performs real-time live operational diagnostic tests:

```json
{
    "status": "ok",
    "timestamp": "2026-08-19T22:59:43+05:30",
    "execution_time_ms": 1.24,
    "checks": {
        "database": {
            "status": "ok",
            "connection": "mysql",
            "latency_ms": 0.85
        },
        "cache": {
            "status": "ok",
            "driver": "redis"
        },
        "storage": {
            "status": "ok",
            "free_space_gb": 45.2
        },
        "queue": {
            "status": "ok",
            "pending_jobs": 0,
            "failed_jobs": 0
        }
    }
}
```

---

## ⚙️ 4. Dynamic Statutory & Payroll Rules Engine (`statutory_rules`)

Statutory rules (PF 12%, ESI 0.75%, HRA 40%, Professional Tax, TDS 194C/194J) are stored dynamically in the `statutory_rules` database table rather than hardcoded in application logic:

| Rule Key | Rule Name | Percentage / Amount | Description |
| :--- | :--- | :--- | :--- |
| `PF_EMPLOYEE_RATE` | Provident Fund (Employee) | `12.00%` | Employee PF Deduction Rate |
| `ESI_EMPLOYEE_RATE` | Employee State Insurance | `0.75%` | ESI Rate (Threshold ₹21,000) |
| `HRA_RATE` | House Rent Allowance | `40.00%` | HRA Allowance Rate |
| `PT_MONTHLY_FLAT` | Professional Tax | `₹200.00` | Flat Monthly Professional Tax |
| `TDS_194C` | TDS Section 194C | `1.00%` | TDS Rate for Contractors |
| `TDS_194J` | TDS Section 194J | `10.00%` | TDS Rate for Professional Fees |

---

## 📚 Complete ERP Module Matrix

### 📊 Accounting & Finance Module
- **Double-Entry Accounting**: Enforces `SUM(debit) == SUM(credit)` before posting.
- **Chart of Accounts (CoA)**: 5-class account hierarchy (Asset, Liability, Equity, Income, Expense).
- **General Ledger (`/erp/general-ledger`)**: Account-wise transaction statement with running balances.
- **Trial Balance & Financial Statements**: Real-time Trial Balance, Profit & Loss (P&L), and Balance Sheet.
- **Fixed Assets & Depreciation (`/erp/fixed-assets`)**: Asset register with Straight-Line (SLM) and WDV depreciation schedule generator.
- **Departmental Budgeting (`/erp/budgeting`)**: Departmental budgets vs YTD actual GL expenditures tracking.

### 🛒 Sales & CRM Module
- **Sales Lifecycle**: CRM Leads ➔ Sales Quotation ➔ Sales Order ➔ Delivery Challan ➔ Invoice ➔ Payment ➔ GL.
- **Credit Limit Enforcement**: Blocks Sales Order generation if customer balance exceeds approved credit ceiling.

### 📦 Purchase & Procurement Module
- **Requisitions & RFQs**: Requisition requests and supplier quotation comparison matrix.
- **Automated 3-Way Matching**: Server-side match check comparing PO vs GRN vs Vendor Invoice.
- **Vendor Contracts & Evaluation (`/erp/contracts-evaluations`)**: Blanket contracts and supplier performance rating matrix.

### 🏭 Inventory & Warehouse Management
- **Stock Ledger & Valuation**: Movement audit ledger (IN/OUT/TRANSFER), FIFO & Weighted Average costing.
- **Landed Cost Allocation**: Absorbs freight, customs, and insurance directly into stock valuation basis.
- **Batch, Lot & Serial Tracking**: Expiry date management and unique serial number tracking.
- **Negative-Stock Control**: Pessimistic/optimistic row locking (`lockForUpdate()`) ensures stock quantity never drops below zero.

### ⚙️ Manufacturing & MRP Engine
- Multi-level BOM trees, Work Center machine/labor hourly rates, Work Order routing, scrap accounting, WIP tracking, and Material Requirements Planning (MRP).

### 👥 HR, Payroll & Expenses
- Employee Directory Master, Attendance & Leave approval workflow, Payroll engine (Gross, PF, ESI, PT, Net Pay, PDF payslips), and Expense Claim reimbursements.

### 🇮🇳 India GST Compliance Engine
- Place of Supply tax engine (CGST+SGST vs IGST), HSN/SAC catalog, E-Invoicing IRN payload generator, signed B2B QR Code visualizer, E-Way Bill JSON exporter, TDS & TCS tax logs, and GSTR-1/GSTR-3B summaries.

---

## ⚡ Quick Start Automated Setup

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

## 🧪 Testing & Verification

Execute the automated test suite covering security, accounting invariants, protected tables, and health diagnostics:

```bash
php artisan test
```

Output:
```
PASS  Tests\Feature\SalesBillingTest
✓ test_sales_order_does_not_affect_stock
✓ test_purchase_order_does_not_affect_stock
✓ test_sales_quotation_does_not_affect_stock

PASS  Tests\Feature\SecurityAuthorizationTest
✓ test_sql_security_analyzer_rejects_destructive_queries
✓ test_sql_security_analyzer_rejects_delete_queries
✓ test_sql_security_analyzer_accepts_valid_select_queries
✓ test_system_health_endpoint_returns_live_diagnostics
✓ test_generic_crud_blocks_direct_update_on_immutable_ledger
✓ test_generic_crud_blocks_direct_delete_on_immutable_ledger

Tests:    46 passed (214 assertions)
Duration: 1.36s
```

---

## 📜 License

Distributed under the **MIT License**. See `LICENSE` for details.

<div align="center">
  <sub>Built with ❤️ by Moin Shadab & the MS ERP Engineering Team. Powered by Laravel 13, PHP 8.3+, Bootstrap 5, Vite, & MySQL.</sub>
</div>
