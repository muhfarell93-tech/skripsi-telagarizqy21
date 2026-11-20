@echo off
title Telaga Rizqy 21 - Standalone POS Launcher
cd /d "%~dp0"

echo ===================================================================
echo   TELAGA RIZQY 21 - SISTEM INFORMASI TRANSAKSI KEUANGAN & POS
echo   Menjalankan Aplikasi secara Mandiri (Tanpa Laragon & Antigravity)
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

echo [1/3] Menyiapkan server lokal port 8000...
start /b "" "%PHP_EXE%" "laravel-app\artisan" serve --host=127.0.0.1 --port=8000 >nul 2>&1

echo [2/3] Menunggu server aktif...
timeout /t 2 /nobreak >nul

echo [3/3] Membuka aplikasi di browser (Kasir POS)...
start http://localhost:8000/pos

echo.
echo ===================================================================
echo   APLIKASI SEDANG BERJALAN PADA: http://localhost:8000
echo   Untuk menutup aplikasi, cukup tutup jendela konsol ini.
echo ===================================================================
echo.
pause
