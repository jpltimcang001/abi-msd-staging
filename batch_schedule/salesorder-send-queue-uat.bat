cd c:\
cd %~dp0\..
cls
php artisan api:SalesOrderBatch --company="Parallel" --sales-office="750300"  --date-from="2024-11-10"
timeout /t 5 /nobreak