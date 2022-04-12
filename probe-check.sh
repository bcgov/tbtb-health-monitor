#!/bin/sh

if [ $(php /var/www/html/artisan dusk:chrome-driver --detect | grep -v grep | grep -c Exception) -ge 1 ]; then
  exit 1
else
  exit 0
fi
