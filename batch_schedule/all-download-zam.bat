cd c:\
cd %~dp0\..
cls 
php artisan api:allDownload --company="BMI" --sales-office="780900" --so-short="ZAM"
TIMEOUT /T 5