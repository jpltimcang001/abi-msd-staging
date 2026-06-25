cd c:\
cd %~dp0\..
cls
php artisan api:SalesOrderBatch --company="BII" --sales-office="780800" 
timeout /t 5 /nobreak