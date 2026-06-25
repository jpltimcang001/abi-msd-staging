cd c:\
cd %~dp0\..
cls 
php artisan api:allDownload --company="BMI" --sales-office="800100" --so-short="KID"
TIMEOUT /T 5