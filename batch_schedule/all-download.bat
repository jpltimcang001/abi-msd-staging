cd c:\
cd %~dp0\..
cls
php artisan api:allDownload --company="BII Live" --sales-office="750300" --so-short="UNS"
TIMEOUT /T 5
php artisan api:allDownload --company="BII Live" --sales-office="710100" --so-short="PAS"
php artisan api:allDownload --company="BII Live" --sales-office="710200" --so-short="BAE"
php artisan api:allDownload --company="BII Live" --sales-office="750200" --so-short="SUC"
TIMEOUT /T 5
php artisan api:allDownload --company="BMI" --sales-office="780500" --so-short="ILI"
php artisan api:allDownload --company="BMI" --sales-office="780800" --so-short="GEN"
php artisan api:allDownload --company="BMI" --sales-office="780900" --so-short="ZAM"
php artisan api:allDownload --company="BMI" --sales-office="790500" --so-short="COT"
php artisan api:allDownload --company="BMI" --sales-office="800100" --so-short="KID"
TIMEOUT /T 5