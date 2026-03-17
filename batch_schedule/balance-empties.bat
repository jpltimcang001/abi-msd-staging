cd c:\
cd %~dp0\..
cls
php artisan api:balanceRequest --company="BMI" --sales-office="780800"
php artisan api:balanceRequest --company="BMI" --sales-office="780900"
TIMEOUT /T 10