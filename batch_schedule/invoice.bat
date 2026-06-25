cd c:\
cd %~dp0\..
cls
REM php artisan api:invoice --sales-office="710100" --company="Parallel" 
REM php artisan api:invoice --sales-office="750300" --company="Parallel" 
php artisan api:invoice --company="BMI" --sales-office="780800"
php artisan api:invoice --company="BMI" --sales-office="780900"
php artisan api:invoice --company="BII Live" --sales-office="750300"
timeout /t 5 /nobreak