#!/bin/bash
service mysql start
a2enmod rewrite
service apache2 start

mysql -u root --execute="CREATE USER 'wbsrvcs' IDENTIFIED BY 'wbsrvcs';"
mysql -u root --execute="GRANT ALL PRIVILEGES ON * . * TO 'wbsrvcs';"

while true; do sleep 1000; done

