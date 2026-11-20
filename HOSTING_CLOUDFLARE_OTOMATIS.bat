@echo off
title HOSTING CLOUDFLARE ONLINE - TELAGA RIZQY 21
cd /d "%~dp0"

echo ==============================================================================
echo        HOSTING OTOMATIS CLOUDFLARE - TELAGA RIZQY 21
echo ==============================================================================
echo.

set "PHP_EXE=C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe"
if not exist "%PHP_EXE%" (
    where php >nul 2>nul
    if %ERRORLEVEL% EQU 0 (
        set "PHP_EXE=php"
    ) else (
        echo [ERROR] PHP tidak ditemukan. Pastikan Laragon sudah terpasang.
        echo.
        echo Tekan tombol ENTER untuk menutup...
        pause >nul
        exit /b 1
    )
)

echo [1/3] Memastikan server lokal (Laravel) aktif...
start /b "" "%PHP_EXE%" "laravel-app\artisan" serve --host=127.0.0.1 --port=8000 >nul 2>&1
timeout /t 2 /nobreak >nul

if not exist "cloudflared.exe" (
    echo [ERROR] File cloudflared.exe tidak ditemukan di folder ini!
    echo Tekan tombol ENTER untuk keluar...
    pause >nul
    exit /b 1
)

echo [2/3] Mengaktifkan Terowongan Publik Cloudflare (HTTPS Global)...
if exist "tunnel.log" del /f /q "tunnel.log" >nul 2>&1

:: Jalankan Cloudflare di background dan rekam log ke tunnel.log
start /b "" "cloudflared.exe" tunnel --url http://127.0.0.1:8000 > tunnel.log 2>&1

echo [3/3] Sedang mengambil link web online publik Anda (tunggu 5 detik)...
powershell -NoProfile -Command ^
    "$found = $false; ^
    for ($i = 0; $i -lt 15; $i++) { ^
        Start-Sleep -Seconds 1; ^
        if (Test-Path 'tunnel.log') { ^
            $match = Select-String -Path 'tunnel.log' -Pattern 'https://[a-zA-Z0-9.-]+\.trycloudflare\.com' | Select-Object -First 1; ^
            if ($match) { ^
                $url = $match.Matches.Value; ^
                Write-Host ''; ^
                Write-Host '==============================================================================' -ForegroundColor Green; ^
                Write-Host '   SELAMAT! WEBSITE TELAGA RIZQY 21 SUDAH LIVE / ONLINE DI INTERNET:' -ForegroundColor Green; ^
                Write-Host '' ; ^
                Write-Host \"   >> $url <<\" -ForegroundColor Yellow; ^
                Write-Host ''; ^
                Write-Host '   Link di atas BISA DIAKSES SIAPA SAJA lewat HP, Komputer, atau Internet!' -ForegroundColor Cyan; ^
                Write-Host '==============================================================================' -ForegroundColor Green; ^
                Write-Host ''; ^
                Write-Host '[INFO] Membuka website secara otomatis di browser Anda...'; ^
                Start-Process $url; ^
                $found = $true; ^
                break; ^
            } ^
        } ^
    } ^
    if (-not $found) { ^
        Write-Host '[INFO] Cloudflare aktif! Silakan buka tautan yang muncul di konsol tunnel.log.'; ^
    }"

echo.
echo ==============================================================================
echo  CATATAN PENTING:
echo  1. BIARKAN JENDELA HITAM INI TETAP TERBUKA agar website tetap bisa diakses online.
echo  2. Pastikan MySQL di Laragon tetap AKTIF (Start All).
echo  3. Jika ingin mematikan hosting / server online, cukup klik tanda silang (X).
echo ==============================================================================
echo.
echo Server sedang berjalan... (Jendela ini aktif melayani pengunjung web)
powershell -NoProfile -Command "while($true) { Start-Sleep -Seconds 60 }"
