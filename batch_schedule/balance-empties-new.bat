cd c:\
cd %~dp0\..
cls
php artisan api:balanceRequest --company="Parallel" --sales-office="750300"
TIMEOUT /T 10