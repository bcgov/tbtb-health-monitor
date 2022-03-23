#!/bin/bash

echo "Start entrypoint file"

echo "APACHE_REMOTE_IP_HEADER: ${APACHE_REMOTE_IP_HEADER}"
echo "APACHE_REMOTE_IP_TRUSTED_PROXY: ${APACHE_REMOTE_IP_TRUSTED_PROXY}"
echo "APACHE_REMOTE_IP_INTERNAL_PROXY: ${APACHE_REMOTE_IP_INTERNAL_PROXY}"

echo "Setup TZ"
php -r "date_default_timezone_set('${TZ}');"

echo "symlink to screenshots"
ln -snf "/var/www/html/tests/Browser/screenshots" "/var/www/html/public/screenshots"


echo "Install composer"
composer install && composer dump-auto && php artisan key:generate

echo "Starting queue 1:"
php /var/www/html/artisan queue:listen --memory=1028 --sleep=5 --timeout=360 --tries=3 &

echo "Starting schedule list 1:"
php /var/www/html/artisan schedule:list &

echo "Starting schedule 2:"
php /var/www/html/artisan schedule:work &
echo "End schedule 2:"

echo "Starting apache:"
/usr/sbin/apache2ctl start

echo "ReStarting apache:"
/usr/sbin/apache2ctl restart

while true; do
    sleep 1;
done
