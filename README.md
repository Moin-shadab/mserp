<div align="center">

# 🚀 MS ERP — Next-Generation Open-Source Enterprise Platform

<p align="center">
  <strong>A Modular, Database-Driven Enterprise Resource Planning (ERP) Engine with Dual-Theme System (Classic Light & Neo-Brutalism), Drag & Drop Dashboard Customizer, Universal Command Palette, and Developer Productivity Tools.</strong>
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

## 🌟 Overview & Key Differentiators

**MS ERP** is a modern enterprise management platform built on PHP 8.4+ and Laravel. Unlike legacy ERP systems that require heavy code rewrites to add new forms, menus, or workflows, MS ERP is powered by a **Dynamic Metadata Engine**. 

Everything from navigation menus, form fields, grid columns, role permissions, notification routing, to layout density and theme settings is stored as configurable data in MySQL.

---

## ✨ Features Highlight

### 🎨 Dual-Engine Design System (Classic & Neo-Brutalism)
- **MySQL-Persisted Theme Switcher**: Toggle instantly between **Classic Light Enterprise** and **Neo-Brutalism High Contrast**.
- **Brutalist Logo Styling**: Pitch-black `2px` borders, hard offset shadows (`3px 3px 0px #000`), white badge contrast frames, and tactile hover animations.
- **Zero GUI Breakage**: Scoped theme rules ensure 100% layout preservation across both themes.

### 🎯 Drag & Drop Dashboard Customizer
- **Interactive Reordering**: Every KPI card and widget container features drag handles (`<i class="bi bi-grip-vertical"></i>`).
- **MySQL & Browser Persistence**: Custom widget orders are automatically saved to MySQL database (`users` table / `system_settings`) and `localStorage`.
- **One-Click Reset**: Restore factory dashboard arrangement anytime.

### ⌘ Universal Command Palette (`Cmd + K` / `Ctrl + K`)
- **Keyboard-Driven Overlay**: Press `⌘K` or `Ctrl+K` to search anything in milliseconds.
- **Instant Search & Quick Actions**: Jump to any module (Invoices, Purchase Orders, Chat, Email, Users, Developer Studio) or trigger system actions (Theme switch, Density toggle, Fullscreen, Scratchpad).

### ⚡ Developer & Power User Floating Dock
- **Speed-Dial Action Bar**: Fixed bottom-right action button providing instant access to:
  - ⚡ 1-Click Theme Switcher (Classic ↔ Brutalism)
  - 📏 Layout Density Mode (Comfortable ↔ Compact)
  - 🔍 Command Palette (`⌘K`)
  - 📝 ERP Quick Scratchpad (Auto-saved developer notes & memos)
  - 🖥️ Full Screen Toggle Mode

### 🏢 Hierarchical Access Control & Multi-Context Switching
- **Granular Permissions Matrix**: Access control down to Module, Page, Operation (View, Create, Edit, Delete, Export, Print, Approve, Reject), Department, Branch, and Company level.
- **Context Switchers**: Switch active Company, Branch, Department, or Email Account from the topbar with real-time data filtering.

### ✉️ Built-in Enterprise Mail & Internal Team Chat Hub
- **Multi-Account Mail Client**: Integrated SMTP/IMAP engine with thread views, starred messages, attachments, contacts, labels, and auto-sync.
- **Real-Time Team Messaging**: Channels, direct messages, user search, broadcast announcements, and read receipts.

### 📊 Dynamic Report Builder & GST Billing Engine
- **Full Billing Lifecycle**: Purchase Orders, Sales Orders, Quotations, Sales Invoices, and Purchase Bills.
- **Tax Masters & UOMs**: Built-in GST handling (CGST, SGST, IGST) with customizable tax rates.
- **Custom SQL Report Builder**: Execute custom SQL analytics and export results to Excel or PDF.

### 🛠️ Low-Code / No-Code Developer Module Studio
- **Instant Page Generator**: Generate full CRUD pages with dynamic form schemas and AG Grid tables directly from SQL queries or table names.

---

## ⌨️ Keyboard Shortcuts Reference

| Shortcut | Action | Description |
| :--- | :--- | :--- |
| <kbd>⌘</kbd> + <kbd>K</kbd> / <kbd>Ctrl</kbd> + <kbd>K</kbd> | **Command Palette** | Open universal command palette overlay |
| <kbd>Esc</kbd> | **Close Overlay** | Close modals, search overlays, or dropdowns |
| <kbd>↑</kbd> / <kbd>↓</kbd> | **Command Navigation** | Navigate items in Command Palette |
| <kbd>Enter</kbd> | **Execute Command** | Run selected command or open page |

---

## 🛠️ System Requirements

| Requirement | Minimum | Recommended |
| :--- | :--- | :--- |
| **PHP Version** | `PHP 8.2+` | `PHP 8.4+` |
| **Database** | `MySQL 8.0+` or `MariaDB 10.5+` | `MySQL 8.0+` |
| **Web Server** | Nginx / Apache / Caddy | Nginx with FPM |
| **Node.js** | `Node v18.0+` | `Node v20.x LTS` |
| **Composer** | `Composer v2.5+` | `Composer v2.7+` |

---

## 📥 Installation & Setup Guide (All Operating Systems)

###  1. macOS Setup Guide

#### Step 1: Install Dependencies via Homebrew
```bash
brew update
brew install php mysql node composer
```

#### Step 2: Start MySQL Service & Create Database
```bash
brew services start mysql
mysql -u root -e "CREATE DATABASE IF NOT EXISTS mserp;"
```

#### Step 3: Clone Repository & Install Dependencies
```bash
git clone https://github.com/Moin-shadab/mserp.git
cd mserp
composer install
npm install
```

#### Step 4: Run ERP Automated Setup
```bash
php artisan erp:setup
```

#### Step 5: Build Assets & Start Development Server
```bash
npm run build
php artisan serve
```
Visit `http://127.0.0.1:8000` in your browser.

---

### 🐧 2. Linux / Ubuntu Setup Guide

#### Step 1: Install Required System Packages
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y php8.4 php8.4-cli php8.4-fpm php8.4-mysql php8.4-mbstring \
    php8.4-xml php8.4-curl php8.4-bcmath php8.4-zip mysql-server nginx nodejs npm composer git
```

#### Step 2: Configure MySQL Database
```bash
sudo mysql -e "CREATE DATABASE mserp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER 'mserp_user'@'localhost' IDENTIFIED BY 'Password123!';"
sudo mysql -e "GRANT ALL PRIVILEGES ON mserp.* TO 'mserp_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"
```

#### Step 3: Clone Codebase & Install Packages
```bash
cd /var/www
sudo git clone https://github.com/Moin-shadab/mserp.git
cd mserp
sudo chown -R $USER:www-data .
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

#### Step 4: Run One-Touch ERP Provisioner
```bash
php artisan erp:setup
```

#### Step 5: Configure Nginx Virtual Host
Create `/etc/nginx/sites-available/mserp`:
```nginx
server {
    listen 80;
    server_name erp.yourdomain.com;
    root /var/www/mserp/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```
Enable site & restart Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/mserp /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

---

### 🪟 3. Windows Setup Guide (Laragon / XAMPP / WSL2)

#### Option A: Using Laragon (Recommended)
1. Download & Install [Laragon Full](https://laragon.org/download/).
2. Open Laragon terminal (`Menu -> Terminal`) and navigate to `www`:
   ```bash
   cd C:\laragon\www
   git clone https://github.com/Moin-shadab/mserp.git
   cd mserp
   composer install
   npm install
   npm run build
   php artisan erp:setup
   ```
3. Laragon creates auto-virtualhost: `http://mserp.test`.

#### Option B: Using XAMPP
1. Open XAMPP Control Panel and start **Apache** and **MySQL**.
2. Open command prompt in `C:\xampp\htdocs`:
   ```cmd
   git clone https://github.com/Moin-shadab/mserp.git
   cd mserp
   composer install
   npm install
   npm run build
   php artisan erp:setup
   php artisan serve
   ```
3. Visit `http://127.0.0.1:8000`.

---

## ⚡ Default Demo Credentials

After running `php artisan erp:setup`, default users are seeded:

| Role | Email | Default Password | Permissions |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@mserp.com` | `admin123` | Full System Control, Theme Management & Developer Studio |
| **CFO / Manager** | `cfo@mserp.com` | `password` | Financial Approvals, GST Billing & Reports |
| **Sales Rep** | `sales@mserp.com` | `password` | Quotations, Invoices & Customer Management |

---

## 🏛️ Database Architecture & Schema

Key tables managed by the Dynamic Metadata Engine:

```
┌─────────────────────────────────────────────────────────────┐
│                    DYNAMIC METADATA ENGINE                  │
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

- **`system_settings`**: Stores global defaults such as `default_theme` (`classic` or `brutalism`) and `dashboard_layout_user_{id}`.
- **`users`**: Contains account profiles, active context IDs, and `theme` preference string.
- **`modules` & `pages`**: Metadata tables defining menu items, icons, dynamic SQL tables, grid JSON schemas, and form fields.

---

## 🤝 Contributing

Contributions, bug reports, and feature suggestions are welcome!

1. Fork the Project Repository.
2. Create your Feature Branch (`git checkout -b feature/CoolFeature`).
3. Commit your Changes (`git commit -m 'Add CoolFeature'`).
4. Push to the Branch (`git push origin feature/CoolFeature`).
5. Open a Pull Request.

---

## 📜 License

Distributed under the **MIT License**. See `LICENSE` for more information.

---

<div align="center">
  <sub>Built with ❤️ by the MS ERP Engineering Team. Powered by Laravel, PHP 8.4, Bootstrap 5, Vite, & MySQL.</sub>
</div>
