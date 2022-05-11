#!/bin/sh

if [ $(php /var/www/html/artisan dusk:chrome-driver --detect | grep -v grep | grep -c Exception) -ge 1 ]; then
  exit 1
else
    ### Check if queue died ###
    if [ $(ps -aux | grep queue | grep -v grep | grep -c queue:listen) -eq 0 ]; then
      exit 1
    fi

    ### Check if vendor directory does not exist ###
    ### composer did not install ###
    if [ ! -d "/var/www/html/vendor" ]; then
        exit 1
    fi
  exit 0
fi
