@echo off
title Push Skripsi Telaga Rizqy 21 ke GitHub
cd /d "%~dp0"

echo ===================================================================
echo   SISTEM INFORMASI TRANSAKSI KEUANGAN TELAGA RIZQY 21
echo   Mengunggah Repository ke GitHub: muhfarell93-tech/skripsi-telagarizqy21
echo ===================================================================
echo.
echo Menghubungkan ke GitHub...
echo Jika muncul jendela browser / Git Credential, silakan klik 'Sign in with your browser'.
echo.

"C:\laragon\bin\git\bin\git.exe" push -u origin main

echo.
if %ERRORLEVEL% EQU 0 (
    echo ===================================================================
    echo   BERHASIL! Seluruh berkas skripsi dan aplikasi sudah ada di GitHub:
    echo   https://github.com/muhfarell93-tech/skripsi-telagarizqy21
    echo ===================================================================
) else (
    echo ===================================================================
    echo   Gagal melakukan push. Pastikan koneksi internet aktif dan
    echo   Anda telah login ke akun GitHub muhfarell93-tech.
    echo ===================================================================
)

echo.
pause
