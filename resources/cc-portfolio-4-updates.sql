USE `cc_store`;

ALTER TABLE `products`
	ADD COLUMN `image` VARCHAR(255) NULL DEFAULT '' AFTER `price`;

ALTER TABLE `products`
	CHANGE COLUMN `category_id` `category_id` BIGINT(20) NOT NULL DEFAULT '0' AFTER `image`;

ALTER TABLE `products`
	ADD COLUMN `deleted_at`  datetime     NULL;

ALTER TABLE `categories`
	ADD COLUMN `deleted_at`  datetime     NULL;

ALTER TABLE `categories`
    CHANGE COLUMN `updated_at`  `updated_at` datetime DEFAULT NOW() ON UPDATE NOW();

ALTER TABLE `categories`
    ADD COLUMN `icon` varchar(255) DEFAULT 'Unavailable' AFTER `code`;