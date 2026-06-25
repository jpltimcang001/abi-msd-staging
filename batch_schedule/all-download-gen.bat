cd c:\
cd %~dp0\..
cls 
php artisan api:allDownload --company="BMI" --sales-office="780800" --so-short="GEN"
TIMEOUT /T 5