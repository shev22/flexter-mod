-- Create database if not exists
CREATE DATABASE IF NOT EXISTS db;

-- Wait for MySQL to be ready before trying to alter users
-- This is executed after MySQL creates the initial users
ALTER USER IF EXISTS 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'user_root_password';
ALTER USER IF EXISTS 'db_user'@'%' IDENTIFIED WITH mysql_native_password BY 'db_password';

-- Grant privileges
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%';
GRANT ALL PRIVILEGES ON db.* TO 'db_user'@'%';

FLUSH PRIVILEGES;
