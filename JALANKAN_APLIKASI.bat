@echo off
title Menjalankan Web Telaga Rizqy 21 (Laravel + POS)
color 0b

echo ========================================================
echo    MENJALANKAN SISTEM INFORMASI TRANSAKSI KEUANGAN
echo                 TELAGA RIZQY 21 METRO
echo ========================================================
echo.

:: Tambahkan PHP Laragon ke PATH sementara
set "PATH=C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64;%PATH%"

cd /d "%~dp0laravel-app"

echo [1/3] Memeriksa file konfigurasi (.env)...
if not exist ".env" (
    echo Menyalin .env dari .env.example...
    copy .env.example .env
    php artisan key:generate
)

echo [2/3] Membuka browser ke http://localhost:8000/login...
start http://localhost:8000/login

echo [3/3] Server web sedang berjalan di http://127.0.0.1:8000
echo.
echo ========================================================
echo PENTING:
echo 1. Jangan tutup jendela terminal hitam ini selama memakai web.
echo 2. Pastikan Anda sudah klik "Start All" di Laragon agar
echo    database MySQL aktif.
echo ========================================================
echo.

php artisan serve --port=8000

pause

