cd c:\
cd %~dp0\..
cls 
php artisan api:allDownload --company="BII Live" --sales-office="710100" --so-short="PAS"
TIMEOUT /T 5