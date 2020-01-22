CREATE DATABASE test_framework CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'test_framework'@'%' IDENTIFIED BY 'test_framework';
GRANT ALL PRIVILEGES ON test_framework . * TO 'test_framework'@'%';
