cd c:\
cd %~dp0\..
cls
php artisan api:balanceRequest --company="BII Live" --sales-office="750300" --salesman-code="UNS-223"
TIMEOUT /T 10