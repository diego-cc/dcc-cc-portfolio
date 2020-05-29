DROP USER IF EXISTS `cc_store_user`@`localhost`;
DROP DATABASE IF EXISTS `cc_store`;

CREATE DATABASE IF NOT EXISTS `cc_store` /*!40100 COLLATE 'utf8mb4_general_ci' */;

CREATE USER IF NOT EXISTS `cc_store_user`@`localhost` IDENTIFIED BY 'Secret1';
GRANT USAGE ON *.* TO 'cc_store_user'@localhost IDENTIFIED BY 'Secret1';
GRANT ALL privileges ON `cc_store`.* TO 'cc_store_user'@localhost;

USE `cc_store`;
