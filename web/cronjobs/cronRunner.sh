#!/bin/bash

## run all the product import  ########################################
if ps ax | grep -v grep | grep "crawlerRunner.php" > /dev/null; then
echo -n "crawlerRunner is Already Running....... :: "
date
echo -n " "
else
/usr/bin/php /var/www/price-match-crawler/web/cronjobs/pricematch/crawlerRunner.php >> /tmp/crawler.log
fi