BASEDIR=$(dirname $0)
FNAME=`date +"%d_%m_%Y"`
FPASSWORD=
DBNAME=price_match_crawler
DBHOST=localhost
DBUSERNAME=root
DBPASSWORD=root
MYSQLPATH=mysql

echo Directory: $BASEDIR
echo FileName: $FNAME
echo DatabaseName: $DBNAME

echo create database $DBNAME if not exists
$MYSQLPATH -h $DBHOST -u $DBUSERNAME -p$DBPASSWORD -e "CREATE DATABASE IF NOT EXISTS $DBNAME DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;"

echo import sql files
$MYSQLPATH -h $DBHOST -u $DBUSERNAME -p$DBPASSWORD $DBNAME < $BASEDIR/../structure.sql
$MYSQLPATH -h $DBHOST -u $DBUSERNAME -p$DBPASSWORD $DBNAME < $BASEDIR/../useraccount.sql
$MYSQLPATH -h $DBHOST -u $DBUSERNAME -p$DBPASSWORD $DBNAME < $BASEDIR/../person.sql
$MYSQLPATH -h $DBHOST -u $DBUSERNAME -p$DBPASSWORD $DBNAME < $BASEDIR/../role.sql
$MYSQLPATH -h $DBHOST -u $DBUSERNAME -p$DBPASSWORD $DBNAME < $BASEDIR/../useraccountinfotype.sql
$MYSQLPATH -h $DBHOST -u $DBUSERNAME -p$DBPASSWORD $DBNAME < $BASEDIR/../useraccountinfo.sql
$MYSQLPATH -h $DBHOST -u $DBUSERNAME -p$DBPASSWORD $DBNAME < $BASEDIR/../systemsettings.sql
$MYSQLPATH -h $DBHOST -u $DBUSERNAME -p$DBPASSWORD $DBNAME < $BASEDIR/../product.sql
$MYSQLPATH -h $DBHOST -u $DBUSERNAME -p$DBPASSWORD $DBNAME < $BASEDIR/price_match_crawler.sql
echo done
