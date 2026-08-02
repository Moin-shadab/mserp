# 🚀 MS ERP - Zero-Config, Child-Simple & Enterprise-Secure ERP

An enterprise-grade, 100% dynamic ERP system designed with extreme simplicity, bank-grade security, zero-hardcoding, and a 1-command automated setup.

---

## ⚡ Super Easy 1-Command Setup

Get the entire application up and running in **seconds** with zero manual configuration:

```bash
php artisan erp:setup
```
*(or `composer run setup`)*

### What `php artisan erp:setup` does automatically:
1. 📝 Creates `.env` configuration automatically if missing.
2. 🗄️ Initializes the SQLite database file (`database/database.sqlite`).
3. 🔑 Generates application security keys.
4. ⚙️ Runs all migrations and seeds dynamic ERP metadata (pages, forms, modules, roles, permissions, realistic demo data).
5. 🔑 Displays instant login credentials directly in the terminal!

---

## 🔑 Default Sign-In Credentials

All demo accounts use the default password: **`password`**

| Role Title | Email Address | Description / Scope |
| :--- | :--- | :--- |
| **🛡️ CFO / Super Admin** | `admin@mserp.com` | Unrestricted full system control |
| **📈 North Sales Head** | `north.head@mserp.com` | Regional sales manager & supervisor |
| **💰 Finance Head** | `accounts.head@mserp.com` | Financial ledger & workflow signatures |
| **💼 Sales Representative** | `rep.north1@mserp.com` | Customer management & quotation draft |
| **📝 Accounts Assistant** | `accounts.member@mserp.com` | Billing, vendor receipts & audit entry |
| **👤 General Executive** | `user@mserp.com` | General workspace read-only |

---

## 🌟 Key Features

1. **Child-Simple UI**: High-contrast icons, 1-click role sign-in cards, self-explanatory workflows, and zero jargon.
2. **100% Dynamic Engine**: Navigation menus, CRUD forms, database tables, grid columns, role permissions, notification routing, email accounts, and reports are fully database-driven.
3. **Uncompromised Security**:
   - Password hashing with Bcrypt.
   - CSRF protection across all forms and endpoints.
   - Dynamic Role-Based Access Control (RBAC) and user permission matrices.
   - Hierarchical data isolation (supervisor vs. subordinate data scoping).
4. **Built-in Modules**:
   - Core ERP (Customers, Vendors, Invoices, Orders, Quotes, Inventory, Taxes, Cost Centers).
   - Organization Panel (Companies, Branches, Departments, Users, Permission Matrix).
   - Communication Hub (Multi-account Email Client & Internal Chat).
   - Analytics Console (Dynamic Custom Report Builder).

---

## 💻 Local Development Server

Run the development server:

```bash
php artisan serve
```
Open [http://127.0.0.1:8000](http://127.0.0.1:8000) in your browser.
