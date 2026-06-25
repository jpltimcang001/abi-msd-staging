cd c:\
cd %~dp0\..
cls
php artisan api:WithdrawStockSalesOrder --company="BII Live" --sales-office="750300"
php artisan api:WithdrawStockSalesOrder --company="BMI" --sales-office="780800" 
php artisan api:WithdrawStockSalesOrder --company="BMI" --sales-office="780900" 
TIMEOUT /T 10