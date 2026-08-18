#!/usr/bin/env bash
# MS ERP - 1-Command Automated Installer for macOS & Linux

set -e

echo "================================================="
echo "   🚀 MS ERP - AUTOMATED ONE-TOUCH INSTALLER     "
echo "================================================="

# Step 1: Install Composer Dependencies
if [ ! -d "vendor" ]; then
    echo "⚙ Installing PHP dependencies via Composer..."
    composer install --prefer-dist --no-progress
fi

# Step 2: Run Interactive ERP Setup Wizard
php artisan erp:setup

# Step 3: Install NPM Dependencies & Build Assets
if [ ! -d "node_modules" ]; then
    echo "⚙ Installing Node.js packages..."
    npm install
fi

echo "⚙ Building production CSS and JavaScript assets..."
npm run build

echo ""
echo "================================================="
echo "🎉 INSTALLATION COMPLETE!"
echo "Run: php artisan serve"
echo "Open: http://127.0.0.1:8000"
echo "================================================="
