cd c:\
cd %~dp0\..
cls
php artisan api:ReturnRequest --company="BII Live" --sales-office="750300"
php artisan api:ReturnRequest --company="BMI" --sales-office="780800"
php artisan api:ReturnRequest --company="BMI" --sales-office="780900"