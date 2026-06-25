cd c:\
cd %~dp0\..
cls 
php artisan api:allDownload --company="BMI" --sales-office="790500" --so-short="COT"
TIMEOUT /T 5