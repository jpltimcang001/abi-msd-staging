cd c:\
cd %~dp0\..
cls
php artisan api:SalesOrderBatch --company="UAT" --sales-office="710100"
timeout /t 5 /nobreak