cd c:\
cd %~dp0\..
cls
php artisan api:SalesOrderBatch --company="Parallel" --sales-office="710200" 
timeout /t 5 /nobreak