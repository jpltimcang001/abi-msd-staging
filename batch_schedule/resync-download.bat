cd c:\
cd %~dp0\..
cls 
REM php artisan api:allDownload --company="BII Live" --sales-office="750300" --so-short="UNS" --salesman="UNS-501"
REM TIMEOUT /T 5
REM php artisan api:allDownload --company="BII Live" --sales-office="750300" --so-short="UNS" --salesman="UNS-204"
REM TIMEOUT /T 5
php artisan api:allDownload --company="BII Live" --sales-office="750300" --so-short="UNS" --salesman="UNS-230"
TIMEOUT /T 5