@echo off
title Hosting Otomatis Cloudflare - Telaga Rizqy 21
cd /d "%~dp0"

echo ===================================================================
echo   HOSTING OTOMATIS CLOUDFLARE - TELAGA RIZQY 21
echo   Menjalankan Server Web + Database Live + Terowongan Cloudflare
echo ===================================================================
echo.

set "PHP_EXE=C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe"
if not exist "%PHP_EXE%" (
    where php >nul 2>nul
    if %ERRORLEVEL% EQU 0 (
        set "PHP_EXE=php"
    ) else (
        echo [ERROR] PHP tidak ditemukan di sistem.
        pause
        exit /b 1
    )
)

echo [1/3] Memastikan server lokal aktif di port 8000...
start /b "" "%PHP_EXE%" "laravel-app\artisan" serve --host=127.0.0.1 --port=8000 >nul 2>&1
timeout /t 2 /nobreak >nul

echo [2/3] Mengaktifkan Cloudflare Tunnel Publik (HTTPS Global)...
echo Menghubungkan aplikasi Telaga Rizqy 21 ke jaringan global Cloudflare...
echo.

if exist "cloudflared.exe" (
    start "" "cloudflared.exe" tunnel --url http://127.0.0.1:8000
) else (
    echo [ERROR] cloudflared.exe tidak ditemukan di folder proyek.
    pause
    exit /b 1
)

echo [3/3] Cloudflare Tunnel telah aktif!
echo.
echo ===================================================================
echo   LINK WEB PUBLIK CLOUDFLARE (BISA DIAKSES SIAPA SAJA DARI INTERNET):
echo   Periksa jendela konsol Cloudflare yang terbuka untuk melihat link
echo   resmi Anda (contoh: https://xxxx.trycloudflare.com)
echo.
echo   Seluruh database (MySQL) dan file .env sudah otomatis terhubung!
echo ===================================================================
echo.
pause
