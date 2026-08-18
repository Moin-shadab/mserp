@echo off
rem MS ERP - 1-Command Automated Installer for Windows

echo =================================================
echo    🚀 MS ERP - AUTOMATED ONE-TOUCH INSTALLER    
echo =================================================

if not exist "vendor" (
    echo ⚙ Installing PHP dependencies via Composer...
    call composer install --prefer-dist --no-progress
)

call php artisan erp:setup

if not exist "node_modules" (
    echo ⚙ Installing Node.js packages...
    call npm install
)

echo ⚙ Building production CSS and JavaScript assets...
call npm run build

echo.
echo =================================================
echo 🎉 INSTALLATION COMPLETE!
echo Run: php artisan serve
echo Open: http://127.0.0.1:8000
echo =================================================
pause
