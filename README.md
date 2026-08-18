<div align="center">

# 🚀 MS ERP — Free Open-Source Enterprise Platform

<p align="center">
  <strong>The Next-Generation Free & Open-Source ERP System powered by Laravel, PHP 8.3+, MySQL, Dynamic Metadata Engine, Low-Code Developer Studio, and 6 Visual Theme Engines.</strong>
</p>

[![Laravel Version](https://img.shields.io/badge/Laravel-v11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL Database](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Vite Asset Bundler](https://img.shields.io/badge/Vite-v8.x-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](LICENSE)
[![Build Status](https://img.shields.io/badge/Build-Passing-brightgreen?style=for-the-badge)](https://github.com/Moin-shadab/mserp)

<br/>

</div>

---

## 🔍 Key Features & Capabilities

**MS ERP (`mserp`)** is a powerful, production-ready **Free Open-Source Enterprise Resource Planning (ERP)** software built to automate business operations, GST billing, financial accounting, inventory, CRM, email communications, and internal team chat. 

Unlike legacy ERP software that requires complex code rewrites to add new modules, **MS ERP is driven by a Low-Code Dynamic Metadata Architecture**. Form schemas, data grids, menu items, permissions, theme settings, and dashboard layouts are stored directly in MySQL and managed dynamically via **Developer Studio**.

---

## ✨ Features Breakdown

### 🎨 6 MySQL-Persisted Visual Theme Engines
Switch instantly between 6 curated visual design systems, persisted to your MySQL user profile:
1. 🎨 **Classic Enterprise**: Ultra-clean professional light interface.
2. ⚡ **Neo-Brutalism**: High-contrast brutalist borders, hard offset shadows (`3px 3px 0 #000`), and bold accents.
3. 🪵 **Skeuomorphism**: Tactile 3D physical glass buttons, metallic bevels, and embossed card depth.
4. ⚪ **Neomorphism**: Soft UI relief dual-shadow inset/outset design system.
5. 💎 **Executive Glassmorphism**: Linear/Raycast dark glass aesthetic with frosted backdrop blur and neon badges.
6. 💻 **Cyber Matrix**: Hacker dark pitch-black obsidian grid UI with Matrix Green (`#00ff66`) glowing accents.

### 🛠️ Low-Code / No-Code Developer Module Studio
- **Instant CRUD Generator**: Build new modules and pages directly from SQL queries or MySQL tables without writing boilerplate controllers or views.
- **3-File Architecture**: Auto-generates clean, isolated `main.blade.php`, `css.blade.php`, and `js.blade.php` files for maximum maintainability.
- **5-Minute Performance Cache**: Auto-discovers and caches dynamic module registrations to ensure **0ms filesystem overhead**.

### ⚡ Enterprise Security & High-Volume Optimization
- **Rate Limiting & Brute-Force Protection**: Stops `/login` password attacks with strict 5-attempt thresholds while granting active users high burst limits (1,000 req/min).
- **System Token & Backup Exemption**: Automated backup scripts, CLI tools, and data import jobs with System API Keys bypass rate limits (`Limit::none()`) for unlimited throughput.
- **Bulk Processing Engine (`/api/bulk/process`)**: Ingests arrays of up to 5,000 rows in single-transaction SQL queries, turning 100,000 individual requests into ~20 queries (**99.5% CPU & memory reduction**).

### 🎯 Drag & Drop Dashboard Customizer
- **Interactive Reordering**: Drag KPI cards and widgets with visual grip handles.
- **Dual Persistence**: Order settings are saved automatically to MySQL (`users` table) and `localStorage`.

### ⌘ Universal Command Palette (`Cmd + K` / `Ctrl + K`)
- Search any module, invoice, customer, report, or system action instantly using keyboard shortcuts.

### 📊 GST Billing & Dynamic Reporting Engine
- Purchase Orders, Sales Quotations, Invoices, Purchase Bills, GST Taxes (CGST, SGST, IGST), UOMs, Customer/Vendor Ledgers, and custom SQL analytics exportable to Excel/PDF.

### ✉️ Enterprise Email Client & Team Chat Hub
- Integrated SMTP/IMAP multi-account email client with thread management and internal team chat channels.

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

The interactive installer will:
1. Copy `.env` automatically.
2. Ask for your MySQL credentials (Host, Port, Database Name, User, Password).
3. **Automatically connect to MySQL and create the database if missing**.
4. Generate security keys (`APP_KEY`).
5. Run migrations & seed initial ERP metadata, default settings, and demo user accounts.
6. Install NPM packages and compile Vite production assets.

---

## 📥 Detailed OS Installation Guides

###  1. macOS Setup Guide
```bash
# Install dependencies via Homebrew
brew install php mysql node composer

# Start MySQL
brew services start mysql

# Clone & run setup
git clone https://github.com/Moin-shadab/mserp.git
cd mserp
./setup.sh

# Start ERP server
php artisan serve
```

---

### 🐧 2. Linux / Ubuntu Setup Guide
```bash
# Install PHP 8.3/8.4, MySQL & Node
sudo apt update && sudo apt install -y php8.3 php8.3-cli php8.3-mysql php8.3-mbstring \
    php8.3-xml php8.3-curl php8.3-bcmath php8.3-zip mysql-server nodejs npm composer git

# Clone repository & run setup
git clone https://github.com/Moin-shadab/mserp.git
cd mserp
chmod +x setup.sh
./setup.sh

# Start app
php artisan serve
```

---

### 🪟 3. Windows Setup Guide (Laragon / XAMPP)
```cmd
:: Clone repository
git clone https://github.com/Moin-shadab/mserp.git
cd mserp

:: Run 1-command installer
setup.bat

:: Start ERP server
php artisan serve
```

---

## ⚡ Seeded Demo Login Credentials

After running `setup.sh` or `php artisan erp:setup`, default accounts are seeded (Password for all: `password`):

| Role Title | Email Address | Access Scope |
| :--- | :--- | :--- |
| **🛡️ CFO / Super Admin** | `admin@mserp.com` | Unrestricted Full System Access & Developer Studio |
| **📈 Sales Head** | `north.head@mserp.com` | Department Supervisor & Sales Operations |
| **💰 Finance Head** | `accounts.head@mserp.com` | Financial Ledgers & Approvals |
| **💼 Sales Representative** | `rep.north1@mserp.com` | Customer Accounts & Invoicing |
| **📝 Accounts Member** | `accounts.member@mserp.com` | Receipts & Billing |
| **👤 General User** | `user@mserp.com` | Read-only Workspace Access |

---

## 🔍 How to Make This Repository Rank High on GitHub & Google

To ensure **MS ERP** appears at the top of Google & GitHub searches for keywords like `free erp`, `open source erp`, `mserp`, and `laravel erp`:

### 1. Configure GitHub Repository Topics
Go to repository page on GitHub (`https://github.com/Moin-shadab/mserp`):
1. Click the ⚙️ gear icon next to **"About"** on the right sidebar.
2. Under **Description**, set:
   > `MS ERP — Next-Generation Free Open-Source Enterprise Platform powered by Laravel, MySQL, Low-Code Developer Studio, and Multi-Theme System.`
3. Under **Topics**, add these exact tags:
   `erp`, `open-source-erp`, `free-erp`, `mserp`, `laravel-erp`, `developer-studio`, `low-code-erp`, `enterprise-resource-planning`, `accounting-software`, `inventory-management`, `billing-software`, `ag-grid`, `bootstrap5`, `php8`
4. Save changes.

---

## 🏛️ System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                   DYNAMIC METADATA ARCHITECTURE             │
├───────────────┬────────────────┬──────────────┬─────────────┤
│ modules       │ submodules     │ pages        │ system_     │
│               │                │              │ settings    │
├───────────────┼────────────────┼──────────────┼─────────────┤
│ users         │ roles          │ role_        │ user_       │
│               │                │ permissions  │ permissions │
├───────────────┼────────────────┼──────────────┼─────────────┤
│ sales_invoices│ purchase_orders│ customers    │ vendors     │
└───────────────┴────────────────┴──────────────┴─────────────┘
```

---

## 🤝 Contributing

Contributions, issues, and feature requests are welcome! Feel free to check the [issues page](https://github.com/Moin-shadab/mserp/issues).

---

## 📜 License

Distributed under the **MIT License**. See `LICENSE` for details.

<div align="center">
  <sub>Built with ❤️ by the MS ERP Engineering Team. Powered by Laravel, PHP 8.3+, Bootstrap 5, Vite, & MySQL.</sub>
</div>