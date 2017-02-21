CREATE DATABASE iiSpeed;
CREATE USER 'iiSpeed'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON iiSpeed.* TO 'iiSpeed'@'localhost';
CREATE USER 'iiSpeed'@'localhost' IDENTIFIED BY 'password';
GRANT SELECT ON iiSpeed.* TO 'iiSpeed'@'localhost';
FLUSH PRIVILEGES;
USE iiSpeed;
CREATE TABLE data ( Date varchar(255), Ping varchar(255), DownSpeed varchar(255), UpSpeed varchar(255) );
QUIT;
