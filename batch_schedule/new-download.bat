cd c:\
cd %~dp0\..
cls 
php artisan api:allDownload --company="BMI" --sales-office="780500" --so-short="ILI"
php artisan api:allDownload --company="BMI" --sales-office="780800" --so-short="GEN"
php artisan api:allDownload --company="BMI" --sales-office="780900" --so-short="ZAM"
php artisan api:allDownload --company="BMI" --sales-office="790500" --so-short="COT"
php artisan api:allDownload --company="BMI" --sales-office="800100" --so-short="KID"
TIMEOUT /T 99