#!/bin/bash

echo "Start entrypoint file"

echo "APACHE_REMOTE_IP_HEADER: ${APACHE_REMOTE_IP_HEADER}"
echo "APACHE_REMOTE_IP_TRUSTED_PROXY: ${APACHE_REMOTE_IP_TRUSTED_PROXY}"
echo "APACHE_REMOTE_IP_INTERNAL_PROXY: ${APACHE_REMOTE_IP_INTERNAL_PROXY}"

echo "Setup TZ"
php -r "date_default_timezone_set('${TZ}');"
php -r "echo date_default_timezone_get();"

echo "symlink to screenshots"
ln -snf "/var/www/html/tests/Browser/screenshots" "/var/www/html/public/screenshots"


echo "Install composer"
composer install && composer dump-auto && php artisan key:generate && php artisan cache:clear && php artisan config:clear
chmod 764 artisan

echo "Starting queue 1:"
php /var/www/html/artisan queue:listen --memory=1028 --sleep=5 --timeout=400 --tries=3 &

echo "Starting schedule list 1:"
php /var/www/html/artisan schedule:list &

echo "Starting schedule 2:"
php /var/www/html/artisan schedule:work &
echo "End schedule 2:"

chmod 766 /var/www/html/probe-check.sh

echo "Run NPM:"
npm run --prefix /var/www/html/ dev

echo "Starting apache:"
/usr/sbin/apache2ctl start

echo "ReStarting apache:"
/usr/sbin/apache2ctl restart

while :; do
    sleep 300;
done
