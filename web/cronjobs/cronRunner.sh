#!/bin/bash

## crawler  ########################################
if ps ax | grep -v grep | grep "cronjobs/pricematch/crawlerRunner.php" > /dev/null; then
echo -n "crawlerRunner is Already Running....... :: "
date
echo -n " "
else
/usr/bin/php /var/www/price-match-crawler/web/cronjobs/pricematch/crawlerRunner.php >> /tmp/crawler.log
fi

## product import  ########################################
if ps ax | grep -v grep | grep "cronjobs/sync/product.php" > /dev/null; then
echo -n "product import is Already Running....... :: "
date
echo -n " "
else
/usr/bin/php /var/www/price-match-crawler/web/cronjobs/sync/product.php >> /tmp/productImport.log
fi