cd c:\
cd %~dp0\..
cls
php artisan api:SalesOrderBatch --company="UAT" --sales-office="750200" 
timeout /t 5 /nobreak