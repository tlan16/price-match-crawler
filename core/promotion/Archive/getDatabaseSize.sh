BASEDIR=$(dirname $0)
FNAME=`date +"%d_%m_%Y"`
FPASSWORD=
DBNAME=price_match_crawler
DBHOST=localhost
DBUSERNAME=root
DBPASSWORD=root
MYSQLPATH=mysql

$MYSQLPATH -h $DBHOST -u $DBUSERNAME -p$DBPASSWORD -e "SELECT price_match_crawler Round(Sum(data_length + index_length) / 1024 / 1024, 1) FROM information_schema.tables GROUP  BY price_match_crawler;" 