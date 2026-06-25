cd c:\
cd %~dp0\..
cls
php artisan api:balanceRequest --company="BII Live" --sales-office="710100"
php artisan api:balanceRequest --company="BII Live" --sales-office="710200"
php artisan api:balanceRequest --company="BII Live" --sales-office="750300"
php artisan api:balanceRequest --company="BII Live" --sales-office="750200"
TIMEOUT /T 10