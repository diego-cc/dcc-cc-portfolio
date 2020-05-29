USE `cc_store`;

CREATE TABLE IF NOT EXISTS `categories`
(
    `id`          bigint       NOT NULL AUTO_INCREMENT,
    `code`        char(4)      NOT NULL DEFAULT 'UNKN' UNIQUE,
    `name`        varchar(32)  NOT NULL DEFAULT 'ERROR: Unknown',
    `description` varchar(255) NOT NULL DEFAULT 'ERROR: Unknown',
    `image`       varchar(255) NOT NULL DEFAULT 'ERROR: Unknown',
    `created_at`  datetime     NOT NULL DEFAULT NOW(),
    `updated_at`  datetime     NULL ON UPDATE NOW(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 30;

CREATE TABLE IF NOT EXISTS `products`
(
    `id`          bigint         NOT NULL AUTO_INCREMENT,
    `name`        varchar(128)   NOT NULL,
    `description` text           NOT NULL,
    `price`       decimal(10, 2) NOT NULL,
    `category_id` bigint         NOT NULL,
    `created_at`  datetime       NOT NULL,
    `updated_at`  datetime       NULL ON UPDATE NOW(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 70;
