@echo off
title Push Laporan & Aplikasi Telaga Rizqy 21 ke GitHub
cd /d "%~dp0"

echo ===================================================================
echo   SISTEM INFORMASI TRANSAKSI KEUANGAN TELAGA RIZQY 21
echo   Mengunggah Repository ke GitHub: muhfarell93-tech/skripsi-telagarizqy21
echo ===================================================================
echo.
echo Menambahkan berkas ke Git...
git add .
git commit -m "docs: perbarui README.md laporan akhir Telaga Rizqy 21 (M. Fahrul Rajab Firdaus - 24430011)"
echo.
echo Mengunggah ke GitHub...
git push -u origin main

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
