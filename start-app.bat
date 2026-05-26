@echo off
title SalesApp Offline System

cd /d C:\xampp\htdocs\SalesApp

:: Start Laravel server only if not running
tasklist | find /i "php.exe" >nul
if errorlevel 1 (
    start "Laravel Server" cmd /k "C:\xampp\php\php.exe artisan serve"
)

:: Wait until Laravel is actually ready on port 8000
echo Waiting for Laravel server to start...

:check
timeout /t 2 >nul
curl -s http://127.0.0.1:8000 >nul 2>&1

if errorlevel 1 (
    goto check
)

:: When server is ready, open browser
start http://127.0.0.1:8000/login

exit