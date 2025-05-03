#!/bin/bash
# Create this file in ./docker-scripts/wait-for-mysql.sh

set -e

host="mysql"
port="3306"
user="$DB_USERNAME"
password="$DB_PASSWORD"

until mysql -h"$host" -P"$port" -u"$user" -p"$password" -e "SELECT 1;" > /dev/null 2>&1; do
  echo "Waiting for MySQL to be ready..."
  sleep 2
done

echo "MySQL is up and running!"
