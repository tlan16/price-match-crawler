@ECHO OFF

SET BASEDIR=%~dp0
SET FPASSWORD=
SET DBNAME=price_match_crawler
SET DBHOST=localhost
SET DBUSERNAME=root
SET DBPASSWORD=
SET MYSQLPATH=C:\wamp\bin\mysql\mysql5.6.17\bin\mysql.exe

echo Directory: %BASEDIR%
echo DatabaseName: %DBNAME%

echo create database %DBNAME% if not exists
%MYSQLPATH% -h %DBHOST% -u %DBUSERNAME% -p%DBPASSWORD% -e "CREATE DATABASE IF NOT EXISTS %DBNAME% DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;"

echo import sql files
%MYSQLPATH% -h%DBHOST% -u%DBUSERNAME% -p%DBPASSWORD% %DBNAME% < %BASEDIR%/../structure.sql
%MYSQLPATH% -h%DBHOST% -u%DBUSERNAME% -p%DBPASSWORD% %DBNAME% < %BASEDIR%/../useraccount.sql
%MYSQLPATH% -h%DBHOST% -u%DBUSERNAME% -p%DBPASSWORD% %DBNAME% < %BASEDIR%/../person.sql
%MYSQLPATH% -h%DBHOST% -u%DBUSERNAME% -p%DBPASSWORD% %DBNAME% < %BASEDIR%/../role.sql
%MYSQLPATH% -h%DBHOST% -u%DBUSERNAME% -p%DBPASSWORD% %DBNAME% < %BASEDIR%/../useraccountinfotype.sql
%MYSQLPATH% -h%DBHOST% -u%DBUSERNAME% -p%DBPASSWORD% %DBNAME% < %BASEDIR%/../useraccountinfo.sql
%MYSQLPATH% -h%DBHOST% -u%DBUSERNAME% -p%DBPASSWORD% %DBNAME% < %BASEDIR%/../systemsettings.sql
%MYSQLPATH% -h%DBHOST% -u%DBUSERNAME% -p%DBPASSWORD% %DBNAME% < %BASEDIR%/../product.sql
%MYSQLPATH% -h%DBHOST% -u%DBUSERNAME% -p%DBPASSWORD% %DBNAME% < %BASEDIR%/price_match_crawler.sql
echo done
