# 🚀 MS ERP - Enterprise-Grade, 100% Offline-First ERP Platform

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%20%7C%208.3-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Vite](https://img.shields.io/badge/Vite-6.x-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev)
[![Quill Editor](https://img.shields.io/badge/Quill-2.0-008080?style=for-the-badge&logo=quill&logoColor=white)](https://quilljs.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)
[![PRs Welcome](https://img.shields.io/badge/PRs-Welcome-brightgreen.svg?style=for-the-badge)](CONTRIBUTING.md)

> **MS ERP** is a modern, modular, database-driven Enterprise Resource Planning (ERP) platform built on **Laravel 11**, **PHP 8.3**, and **Vite**. Engineered for businesses that demand high customization without code clutter, MS ERP stores schemas, navigation, forms, permissions, and workflows as configurable data.

---

## ⭐ Key Highlights & Features

### 📶 100% Offline-First Architecture
- **Zero External CDN Dependencies**: No reliance on `fonts.googleapis.com`, `cdn.jsdelivr.net`, or third-party CDNs.
- **Local NPM Bundling**: Self-contained fonts (Plus Jakarta Sans, Instrument Sans), Bootstrap 5 icons, AG Grid Community, and Quill 2.0 bundled locally via Vite.
- **Air-Gapped Ready**: Operates seamlessly in intranet environments, isolated cloud VPCs, or offline local servers.

---

### ✉️ Low-Level Socket Custom Email Engine
- **Multi-Protocol Support**: Custom socket-level implementation for **SMTP**, **IMAP**, and **POP3** with dynamic **STARTTLS** (ports `587`, `143`, `110`, `25`) and **SSL/TLS** (ports `465`, `993`, `995`).
- **Universal Provider Compatibility**: Works out of the box with `@gmail.com`, `@mserp.in`, cPanel, Plesk, Office365, Zoho Mail, and custom corporate email servers.
- **⚡ Live Socket Diagnostic Tester**: Test connection health in real time with interactive socket log inspection under **Email > Settings**.
- **Full Email Client Suite**: Unified Inbox, Sent items, Starred threads, Draft auto-save, HTML templates, corporate signatures, and attachment drag-and-drop.

---

### ✍️ Universal 1-Line Rich Text Editor (`Quill 2.0`)
- **1-Line Global Initializer**: Instantiate a feature-complete WYSIWYG editor anywhere in your application:
  ```javascript
  const editor = window.initErpEditor('#my-editor-id');
  ```
- **📊 Native Table Module**: Create tables, insert/delete rows & columns, select cells directly inside Quill 2.0.
- **🖼️ Interactive Image Resizer & Containment**: Click any image inside the editor to display overlay drag handles, size presets (`25%`, `50%`, `100%`), and alignment buttons (`Left`, `Center`, `Right`).
- **🛡️ Overflow Protection**: Built-in CSS containment guarantees images and tables never overflow into attachment dropzones or page layout cards.

---

### 🛡️ Enterprise Security & Granular Access Control (RBAC)
- **Hierarchical Visibility**: Multi-company, multi-branch, and department-level data isolation.
- **Supervisory Approval Workflows**: Built-in multi-stage approvals for quotes, invoices, and expense vouchers (e.g., Sales Rep ➔ Sales Head ➔ Finance Head).
- **Row-Level Security**: Automatic data filtering based on user role and manager-subordinate relationships.

---

## ⚡ 1-Command Automated Installation

Run the zero-config installer command:

```bash
php artisan erp:install
```

What `php artisan erp:install` does automatically:
1. Creates `.env` from `.env.example` if not present.
2. Generates security key (`php artisan key:generate`).
3. Prepares SQLite/MySQL database structure.
4. Executes migrations (`migrate:fresh`).
5. Seeds initial roles, workflow rules, dummy email accounts, and sample ERP data.

---

## 🔑 Default Demo Login Credentials

| Role Title | Email Address | Password | Security Scope / Role |
| :--- | :--- | :--- | :--- |
| **🛡️ CFO / Super Admin** | `admin@mserp.com` | `password` | Unrestricted System Access |
| **📈 North Sales Head** | `north.head@mserp.com` | `password` | Department Supervisor & Approval Rules |
| **💰 Finance Head** | `accounts.head@mserp.com` | `password` | Accounts Ledger & Billing Workflows |
| **💼 Sales Representative** | `rep.north1@mserp.com` | `password` | Customer Accounts & Quotes |
| **📝 Accounts Assistant** | `accounts.member@mserp.com` | `password` | Billing & Receipt Approvals |
| **👤 General Executive** | `user@mserp.com` | `password` | Workspace Read-Only |

---

## 📧 Seeded Template Email Accounts

> 🔒 **Confidentiality Note**: Personal email credentials have been scrubbed. The system includes two clean template accounts out-of-the-box. Simply update your address & password under **Email > Settings**:

| Provider Type | Seeded Dummy Address | SMTP Configuration | IMAP Configuration |
| :--- | :--- | :--- | :--- |
| **Gmail Account** | `demo.user@gmail.com` | `smtp.gmail.com:587` (TLS) | `imap.gmail.com:993` (SSL) |
| **Custom Mail Server** | `user@mserp.in` | `mail.mserp.in:587` (TLS) | `mail.mserp.in:993` (SSL) |

---

## 💻 OS-Specific Installation Guide

### Prerequisites
- **PHP** >= 8.2 (Required extensions: `pdo`, `sqlite3`, `openssl`, `mbstring`, `curl`, `sockets`, `fileinfo`)
- **Composer** >= 2.x
- **Node.js** >= 18.x & **NPM** >= 9.x

---

### 🍏 macOS Installation (Homebrew)

```bash
# 1. Install prerequisites via Homebrew
brew install php composer node

# 2. Clone the repository
git clone https://github.com/Moin-shadab/mserp.in.git
cd erp

# 3. Install PHP & Node dependencies
composer install
npm install

# 4. Run 1-command installer
php artisan erp:install

# 5. Build frontend assets & start dev server
npm run build
php artisan serve
```

---

### 🐧 Linux Installation (Ubuntu / Debian)

```bash
# 1. Update package list & install PHP 8.3 & Node.js
sudo apt update
sudo apt install -y php8.3 php8.3-cli php8.3-curl php8.3-mbstring php8.3-xml php8.3-sqlite3 php8.3-sockets composer nodejs npm

# 2. Clone repository & change directory
git clone https://github.com/Moin-shadab/mserp.in.git
cd erp

# 3. Install dependencies
composer install
npm install

# 4. Execute installer & build assets
php artisan erp:install
npm run build

# 5. Run local development server
php artisan serve --host=0.0.0.0 --port=8000
```

---

### 🪟 Windows Installation (XAMPP / Laragon / WSL2)

#### Option A: Using Laragon / XAMPP (Native Windows)
1. Ensure **PHP 8.2+** and **Composer** are installed and added to system `PATH`.
2. Open PowerShell or Command Prompt as Administrator:
   ```powershell
   git clone https://github.com/Moin-shadab/mserp.in.git
   cd erp
   composer install
   npm install
   php artisan erp:install
   npm run build
   php artisan serve
   ```

#### Option B: Using WSL2 (Ubuntu on Windows)
```bash
wsl
git clone https://github.com/Moin-shadab/mserp.in.git
cd erp
composer install && npm install
php artisan erp:install
npm run build
php artisan serve
```

---

## 🛠️ Developer Usage & 1-Line Editor API

### Initializing Rich Text Editors
Any blade view or JavaScript module can initialize a Quill 2.0 WYSIWYG editor with full Table, Image Resizer, and Video capabilities in **1 single line**:

```javascript
// 1-line initializer
const editor = window.initErpEditor('#my-textarea-id', {
    placeholder: 'Type your content here...'
});

// Get HTML content
const html = editor.getHTML();

// Set HTML content
editor.setHTML('<p>Hello World!</p>');

// Clear editor
editor.clear();
```

---

## 🧪 Automated Testing & Verification

MS ERP comes with automated unit and feature test suites covering custom socket email clients, STARTTLS upgrades, server lookup handlers, and draft management.

Run the test suite:

```bash
php artisan test --filter=EmailServiceTest
```

Expected output:
```text
PASS  Tests\Feature\EmailServiceTest
✓ sync handles basic email fetch and store
✓ safe decrypt returns plain text on invalid payload
✓ sync skips disabled live sync accounts
✓ sync handles imap connection failure gracefully
✓ get email list returns formatted metadata
✓ delete contact removes email contact record
✓ get folder counts returns accurate counts per folder
✓ bulk action updates folder for multiple emails
✓ delete label removes label and unlinks emails
✓ save draft with attachments saves attachments
✓ thread id fallback groups emails by subject
✓ lookup server config for custom domain
✓ store custom email account
✓ test account connection endpoint

Tests: 14 passed (58 assertions)
Duration: 0.35s
```

---

## 🗺️ Project Directory Map

```text
erp/
├── app/
│   ├── Console/Commands/       # Artisan commands (erp:install, email:sync)
│   ├── Http/Controllers/       # Modular Controllers (EmailController, SalesController)
│   └── Services/Email/         # Custom Low-Level Sockets (SocketClient, SmtpSocketClient, ImapSocketClient)
├── database/
│   ├── migrations/             # Schema definitions
│   └── seeders/DatabaseSeeder.php # Zero-hardcoding data & dummy email accounts
├── resources/
│   ├── css/app.css             # Offline design tokens & Quill containment CSS
│   ├── js/app.js               # Global 1-line editor initializer (window.initErpEditor)
│   └── views/                  # Blade templates & module view components
├── routes/web.php              # ERP routes & API endpoints
└── tests/Feature/              # Automated PHPUnit / Pest test suites
```

---

## 🤝 Contributing

Contributions are welcome! If you'd like to report a bug, suggest an enhancement, or contribute a new module:

1. Fork the Repository.
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`).
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`).
4. Push to the Branch (`git push origin feature/AmazingFeature`).
5. Open a Pull Request.

---

## 📄 License

Distributed under the **MIT License**. See `LICENSE` for more information.

---

<p align="center">
  Developed with ❤️ for enterprise efficiency and total data privacy.
</p>
