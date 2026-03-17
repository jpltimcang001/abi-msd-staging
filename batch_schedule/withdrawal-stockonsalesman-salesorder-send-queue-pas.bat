cd c:\
cd %~dp0\..
cls
php artisan api:WithdrawStockSalesOrder --company="BII Live" --sales-office="710100"
TIMEOUT 10