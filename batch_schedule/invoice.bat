cd c:\
cd %~dp0\..
cls
php artisan api:invoice --sales-office="710100" --company="Parallel" 
php artisan api:invoice --sales-office="750300" --company="Parallel" 
timeout /t 5 /nobreak