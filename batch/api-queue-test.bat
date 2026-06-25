cd c:\
cd %~dp0\..
cls

php artisan queue:work --queue=api-queue-bmi,api-queue-bii,api-queue-uat