#!/bin/sh

if [ $(php /var/www/html/artisan dusk:chrome-driver --detect | grep -v grep | grep Exception | wc -l) -lt 2 ]; then
  exit 1
else
  exit 0
fi
