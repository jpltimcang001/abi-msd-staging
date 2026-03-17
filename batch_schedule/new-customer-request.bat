cd c:\
cd %~dp0\..
cls
php artisan api:NewCustomerRequest --company="Parallel" --sales-office="750300"
php artisan api:NewCustomerRequest --company="Parallel" --sales-office="710200"
php artisan api:NewCustomerRequest --company="Parallel" --sales-office="750200"
php artisan api:NewCustomerRequest --company="Parallel" --sales-office="710100"