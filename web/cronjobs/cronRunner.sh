#!/bin/bash

## product import  ########################################
if ps ax | grep -v grep | grep "cronjobs/sync/product.php" > /dev/null; then
echo -n "product import is Already Running....... :: "
date
echo -n " "
else
/usr/bin/php /var/www/price-match-crawler/web/cronjobs/sync/product.php >> /tmp/log/productImport_`date +"%d_%b_%y"`.log
fi

## crawler  ########################################
if ps ax | grep -v grep | grep "cronjobs/pricematch/crawlerRunner.php" > /dev/null; then
echo -n "crawlerRunner is Already Running....... :: "
date
echo -n " "
else
/usr/bin/php /var/www/price-match-crawler/web/cronjobs/pricematch/crawlerRunner.php >> /tmp/log/crawler_`date +"%Y_%m_%d_%H_%M_%S"`.log
fi


