cd c:\
cd %~dp0\..
cls 
php artisan api:allDownload --company="BII Live" --sales-office="750300" --so-short="UNS" --salesman="UNS-204"
TIMEOUT /T 5
php artisan api:allDownload --company="BII Live" --sales-office="750300" --so-short="UNS" --salesman="UNS-230"
TIMEOUT /T 5
php artisan api:allDownload --company="BII Live" --sales-office="750300" --so-short="UNS" --salesman="UNS-501"
TIMEOUT /T 5