cd c:\
cd %~dp0\..
cls
php artisan api:SalesOrderBatch --company="UAT" --sales-office="750300" 
timeout /t 5 /nobreak