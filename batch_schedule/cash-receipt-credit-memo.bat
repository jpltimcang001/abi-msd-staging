cd c:\
cd %~dp0\..
cls
php artisan api:SalesOrderBatch --company="BII Live" --sales-office="750300"
php artisan api:SalesOrderBatch --company="BII Live" --sales-office="710200"
php artisan api:SalesOrderBatch --company="BII Live" --sales-office="750200"
php artisan api:SalesOrderBatch --company="BII Live" --sales-office="710100"
timeout 1000