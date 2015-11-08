BASEDIR=$(dirname $0)
FNAME=`date +"%d_%m_%Y"`
FPASSWORD=
DBNAME=price_match_crawler
DBHOST=localhost
DBUSERNAME=root
DBPASSWORD=root
MYSQLPATH=mysql

$MYSQLPATH -h $DBHOST -u $DBUSERNAME -p$DBPASSWORD -e '
SELECT table_schema                                        "DB Name", 
   Round(Sum(data_length + index_length) / 1024 / 1024, 1) "DB Size in MB" 
FROM   information_schema.tables 
GROUP  BY table_schema; 
'