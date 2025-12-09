#!/bin/bash
set -e

echo "Initializing MySQL users and permissions..."

mysql -uroot -p"${MYSQL_ROOT_PASSWORD}" <<-EOSQL
    -- Create user with access from any host in Docker network
    CREATE USER IF NOT EXISTS '${MYSQL_USER}'@'%' IDENTIFIED BY '${MYSQL_PASSWORD}';
    GRANT ALL PRIVILEGES ON ${MYSQL_DATABASE}.* TO '${MYSQL_USER}'@'%';
    
    -- Update root password and ensure root can connect from Docker network
    ALTER USER 'root'@'%' IDENTIFIED BY '${MYSQL_ROOT_PASSWORD}';
    GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;
    
    FLUSH PRIVILEGES;
    
    SELECT user, host FROM mysql.user WHERE user IN ('${MYSQL_USER}', 'root');
EOSQL

echo "MySQL initialization complete!"
