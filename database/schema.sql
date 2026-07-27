-- Cloud Manager System Database Schema
-- MySQL 5.7+ / 8.0+ with InnoDB engine, utf8mb4 charset

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table: admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `email` VARCHAR(255) NOT NULL DEFAULT '',
  `phone` VARCHAR(255) NOT NULL DEFAULT '',
  `password` VARCHAR(255) NOT NULL DEFAULT '',
  `operate_password` VARCHAR(255) NOT NULL DEFAULT '',
  `status` TINYINT NOT NULL DEFAULT 1,
  `last_login_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `last_login_ip` VARCHAR(255) NOT NULL DEFAULT '',
  `last_action_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `update_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`),
  UNIQUE KEY `uk_email` (`email`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: admin_login
-- ----------------------------
DROP TABLE IF EXISTS `admin_login`;
CREATE TABLE `admin_login` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `ip` VARCHAR(255) NOT NULL DEFAULT '',
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: admin_role
-- ----------------------------
DROP TABLE IF EXISTS `admin_role`;
CREATE TABLE `admin_role` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `description` TEXT,
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `update_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: admin_role_link
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_link`;
CREATE TABLE `admin_role_link` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `role_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: admin_rule
-- ----------------------------
DROP TABLE IF EXISTS `admin_rule`;
CREATE TABLE `admin_rule` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: admin_widget
-- ----------------------------
DROP TABLE IF EXISTS `admin_widget`;
CREATE TABLE `admin_widget` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `widget` VARCHAR(255) NOT NULL DEFAULT '',
  `order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: client
-- ----------------------------
DROP TABLE IF EXISTS `client`;
CREATE TABLE `client` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL DEFAULT '',
  `email` VARCHAR(255) NOT NULL DEFAULT '',
  `phone_code` VARCHAR(255) NOT NULL DEFAULT '',
  `phone` VARCHAR(255) NOT NULL DEFAULT '',
  `password` VARCHAR(255) NOT NULL DEFAULT '',
  `operate_password` VARCHAR(255) NOT NULL DEFAULT '',
  `status` TINYINT NOT NULL DEFAULT 1,
  `credit` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `company` VARCHAR(255) NOT NULL DEFAULT '',
  `address` TEXT,
  `language` VARCHAR(255) NOT NULL DEFAULT '',
  `country_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `notes` TEXT,
  `last_login_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `last_login_ip` VARCHAR(255) NOT NULL DEFAULT '',
  `last_action_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `update_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`),
  UNIQUE KEY `uk_email` (`email`),
  KEY `idx_status` (`status`),
  KEY `idx_country_id` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: client_login
-- ----------------------------
DROP TABLE IF EXISTS `client_login`;
CREATE TABLE `client_login` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `ip` VARCHAR(255) NOT NULL DEFAULT '',
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: client_credit
-- ----------------------------
DROP TABLE IF EXISTS `client_credit`;
CREATE TABLE `client_credit` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `type` VARCHAR(255) NOT NULL DEFAULT '',
  `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `balance` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `notes` TEXT,
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: client_record
-- ----------------------------
DROP TABLE IF EXISTS `client_record`;
CREATE TABLE `client_record` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `admin_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `content` TEXT,
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: order
-- ----------------------------
DROP TABLE IF EXISTS `order`;
CREATE TABLE `order` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `host_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `type` VARCHAR(255) NOT NULL DEFAULT '',
  `status` VARCHAR(255) NOT NULL DEFAULT '',
  `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `credit_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `gateway` VARCHAR(255) NOT NULL DEFAULT '',
  `pay_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `update_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_host_id` (`host_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: order_item
-- ----------------------------
DROP TABLE IF EXISTS `order_item`;
CREATE TABLE `order_item` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `product_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `host_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `type` VARCHAR(255) NOT NULL DEFAULT '',
  `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `description` VARCHAR(255) NOT NULL DEFAULT '',
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_host_id` (`host_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: order_record
-- ----------------------------
DROP TABLE IF EXISTS `order_record`;
CREATE TABLE `order_record` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `admin_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `content` TEXT,
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: host
-- ----------------------------
DROP TABLE IF EXISTS `host`;
CREATE TABLE `host` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `product_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `server_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `total` BIGINT NOT NULL DEFAULT 0 COMMENT '主机总数/相关额度',
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `status` VARCHAR(255) NOT NULL DEFAULT '',
  `billing_cycle` VARCHAR(255) NOT NULL DEFAULT '',
  `first_payment_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `renew_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `notes` TEXT,
  `suspend_reason` TEXT,
  `suspend_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `terminate_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `active_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `due_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `update_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_server_id` (`server_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: host_ip
-- ----------------------------
DROP TABLE IF EXISTS `host_ip`;
CREATE TABLE `host_ip` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `host_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `ip` VARCHAR(255) NOT NULL DEFAULT '',
  `subnet_mask` VARCHAR(255) NOT NULL DEFAULT '',
  `gateway` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_host_id` (`host_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: product
-- ----------------------------
DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `description` TEXT,
  `type` VARCHAR(255) NOT NULL DEFAULT '',
  `product_group_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `server_group_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `billing_cycle` VARCHAR(255) NOT NULL DEFAULT '',
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `stock` INT NOT NULL DEFAULT -1,
  `hidden` TINYINT NOT NULL DEFAULT 0,
  `order` INT NOT NULL DEFAULT 0,
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `update_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_product_group_id` (`product_group_id`),
  KEY `idx_server_group_id` (`server_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: product_group
-- ----------------------------
DROP TABLE IF EXISTS `product_group`;
CREATE TABLE `product_group` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `parent_id` INT NOT NULL DEFAULT 0,
  `description` TEXT,
  `order` INT NOT NULL DEFAULT 0,
  `hidden` TINYINT NOT NULL DEFAULT 0,
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `update_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: cart
-- ----------------------------
DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `product_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `qty` INT NOT NULL DEFAULT 1,
  `config_options` TEXT,
  `billing_cycle` VARCHAR(255) NOT NULL DEFAULT '',
  `position` INT NOT NULL DEFAULT 0,
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: transaction
-- ----------------------------
DROP TABLE IF EXISTS `transaction`;
CREATE TABLE `transaction` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `order_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `gateway` VARCHAR(255) NOT NULL DEFAULT '',
  `transaction_id` VARCHAR(255) NOT NULL DEFAULT '',
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: task
-- ----------------------------
DROP TABLE IF EXISTS `task`;
CREATE TABLE `task` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(255) NOT NULL DEFAULT '',
  `description` TEXT,
  `host_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `client_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `status` VARCHAR(255) NOT NULL DEFAULT 'Wait',
  `error` TEXT,
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `update_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_host_id` (`host_id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: task_notice_wait
-- ----------------------------
DROP TABLE IF EXISTS `task_notice_wait`;
CREATE TABLE `task_notice_wait` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `action` VARCHAR(255) NOT NULL DEFAULT '',
  `client_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `host_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `order_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `template_param` TEXT,
  `status` VARCHAR(255) NOT NULL DEFAULT 'Wait',
  `error` TEXT,
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `update_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_host_id` (`host_id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: configuration
-- ----------------------------
DROP TABLE IF EXISTS `configuration`;
CREATE TABLE `configuration` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting` VARCHAR(255) NOT NULL DEFAULT '',
  `value` TEXT,
  `group` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_setting` (`setting`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: country
-- ----------------------------
DROP TABLE IF EXISTS `country`;
CREATE TABLE `country` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name_zh` VARCHAR(255) NOT NULL DEFAULT '',
  `name_en` VARCHAR(255) NOT NULL DEFAULT '',
  `phone_code` VARCHAR(255) NOT NULL DEFAULT '',
  `iso` VARCHAR(255) NOT NULL DEFAULT '',
  `iso3` VARCHAR(255) NOT NULL DEFAULT '',
  `order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: custom_host_name
-- ----------------------------
DROP TABLE IF EXISTS `custom_host_name`;
CREATE TABLE `custom_host_name` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: email_template
-- ----------------------------
DROP TABLE IF EXISTS `email_template`;
CREATE TABLE `email_template` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `subject` VARCHAR(255) NOT NULL DEFAULT '',
  `content` TEXT,
  `status` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: feedback
-- ----------------------------
DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `content` TEXT,
  `type_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `client_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `status` VARCHAR(255) NOT NULL DEFAULT 'pending',
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_type_id` (`type_id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: feedback_type
-- ----------------------------
DROP TABLE IF EXISTS `feedback_type`;
CREATE TABLE `feedback_type` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: file_log
-- ----------------------------
DROP TABLE IF EXISTS `file_log`;
CREATE TABLE `file_log` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `path` VARCHAR(255) NOT NULL DEFAULT '',
  `filename` VARCHAR(255) NOT NULL DEFAULT '',
  `ext` VARCHAR(255) NOT NULL DEFAULT '',
  `size` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: friendly_link
-- ----------------------------
DROP TABLE IF EXISTS `friendly_link`;
CREATE TABLE `friendly_link` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `url` VARCHAR(255) NOT NULL DEFAULT '',
  `logo` VARCHAR(255) NOT NULL DEFAULT '',
  `order` INT NOT NULL DEFAULT 0,
  `status` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: honor
-- ----------------------------
DROP TABLE IF EXISTS `honor`;
CREATE TABLE `honor` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `img` VARCHAR(255) NOT NULL DEFAULT '',
  `order` INT NOT NULL DEFAULT 0,
  `status` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: index_banner
-- ----------------------------
DROP TABLE IF EXISTS `index_banner`;
CREATE TABLE `index_banner` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `img` VARCHAR(255) NOT NULL DEFAULT '',
  `url` VARCHAR(255) NOT NULL DEFAULT '',
  `start_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `end_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `show` TINYINT NOT NULL DEFAULT 1,
  `notes` VARCHAR(255) NOT NULL DEFAULT '',
  `order` INT NOT NULL DEFAULT 0,
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `update_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: cloud_server_banner
-- ----------------------------
DROP TABLE IF EXISTS `cloud_server_banner`;
CREATE TABLE `cloud_server_banner` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `img` VARCHAR(255) NOT NULL DEFAULT '',
  `url` VARCHAR(255) NOT NULL DEFAULT '',
  `start_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `end_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `show` TINYINT NOT NULL DEFAULT 1,
  `notes` VARCHAR(255) NOT NULL DEFAULT '',
  `order` INT NOT NULL DEFAULT 0,
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `update_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: menu
-- ----------------------------
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(255) NOT NULL DEFAULT '',
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `icon` VARCHAR(255) NOT NULL DEFAULT '',
  `url` VARCHAR(255) NOT NULL DEFAULT '',
  `order` INT NOT NULL DEFAULT 0,
  `parent_id` INT NOT NULL DEFAULT 0,
  `status` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: notice_setting
-- ----------------------------
DROP TABLE IF EXISTS `notice_setting`;
CREATE TABLE `notice_setting` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `sms_global` TINYINT NOT NULL DEFAULT 0,
  `sms_account` TINYINT NOT NULL DEFAULT 0,
  `email_global` TINYINT NOT NULL DEFAULT 0,
  `email_account` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: partner
-- ----------------------------
DROP TABLE IF EXISTS `partner`;
CREATE TABLE `partner` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `url` VARCHAR(255) NOT NULL DEFAULT '',
  `logo` VARCHAR(255) NOT NULL DEFAULT '',
  `order` INT NOT NULL DEFAULT 0,
  `status` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: plugin
-- ----------------------------
DROP TABLE IF EXISTS `plugin`;
CREATE TABLE `plugin` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(255) NOT NULL DEFAULT '',
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `version` VARCHAR(255) NOT NULL DEFAULT '',
  `author` VARCHAR(255) NOT NULL DEFAULT '',
  `description` VARCHAR(255) NOT NULL DEFAULT '',
  `status` TINYINT NOT NULL DEFAULT 1,
  `config` TEXT,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: plugin_hook
-- ----------------------------
DROP TABLE IF EXISTS `plugin_hook`;
CREATE TABLE `plugin_hook` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `plugin_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `hook` VARCHAR(255) NOT NULL DEFAULT '',
  `class` VARCHAR(255) NOT NULL DEFAULT '',
  `priority` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_plugin_id` (`plugin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: refund_record
-- ----------------------------
DROP TABLE IF EXISTS `refund_record`;
CREATE TABLE `refund_record` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `client_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `admin_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `reason` TEXT,
  `status` VARCHAR(255) NOT NULL DEFAULT 'pending',
  `reject_reason` TEXT,
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `update_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: self_defined_field
-- ----------------------------
DROP TABLE IF EXISTS `self_defined_field`;
CREATE TABLE `self_defined_field` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `field_type` VARCHAR(255) NOT NULL DEFAULT 'text',
  `type` VARCHAR(255) NOT NULL DEFAULT 'host',
  `product_id` INT NOT NULL DEFAULT 0,
  `required` TINYINT NOT NULL DEFAULT 0,
  `options` TEXT,
  `description` VARCHAR(255) NOT NULL DEFAULT '',
  `order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: self_defined_field_value
-- ----------------------------
DROP TABLE IF EXISTS `self_defined_field_value`;
CREATE TABLE `self_defined_field_value` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `field_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `type` VARCHAR(255) NOT NULL DEFAULT '',
  `rel_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `value` TEXT,
  PRIMARY KEY (`id`),
  KEY `idx_field_id` (`field_id`),
  KEY `idx_rel_id` (`rel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: seo
-- ----------------------------
DROP TABLE IF EXISTS `seo`;
CREATE TABLE `seo` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `page` VARCHAR(255) NOT NULL DEFAULT '',
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `keywords` VARCHAR(255) NOT NULL DEFAULT '',
  `description` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: server
-- ----------------------------
DROP TABLE IF EXISTS `server`;
CREATE TABLE `server` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `hostname` VARCHAR(255) NOT NULL DEFAULT '',
  `ip` VARCHAR(255) NOT NULL DEFAULT '',
  `port` INT NOT NULL DEFAULT 22,
  `username` VARCHAR(255) NOT NULL DEFAULT '',
  `password` VARCHAR(255) NOT NULL DEFAULT '',
  `server_group_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `module` VARCHAR(255) NOT NULL DEFAULT '',
  `status` TINYINT NOT NULL DEFAULT 1,
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `update_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_server_group_id` (`server_group_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: server_group
-- ----------------------------
DROP TABLE IF EXISTS `server_group`;
CREATE TABLE `server_group` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: side_floating_window
-- ----------------------------
DROP TABLE IF EXISTS `side_floating_window`;
CREATE TABLE `side_floating_window` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `url` VARCHAR(255) NOT NULL DEFAULT '',
  `icon` VARCHAR(255) NOT NULL DEFAULT '',
  `image` VARCHAR(255) NOT NULL DEFAULT '',
  `content` TEXT,
  `order` INT NOT NULL DEFAULT 0,
  `status` TINYINT NOT NULL DEFAULT 1,
  `position` VARCHAR(255) NOT NULL DEFAULT 'right',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: sms_template
-- ----------------------------
DROP TABLE IF EXISTS `sms_template`;
CREATE TABLE `sms_template` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `content` TEXT,
  `status` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: supplier
-- ----------------------------
DROP TABLE IF EXISTS `supplier`;
CREATE TABLE `supplier` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `url` VARCHAR(255) NOT NULL DEFAULT '',
  `username` VARCHAR(255) NOT NULL DEFAULT '',
  `token` VARCHAR(255) NOT NULL DEFAULT '',
  `credit` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status` TINYINT NOT NULL DEFAULT 1,
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `update_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: system_log
-- ----------------------------
DROP TABLE IF EXISTS `system_log`;
CREATE TABLE `system_log` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `description` TEXT,
  `type` VARCHAR(255) NOT NULL DEFAULT '',
  `rel_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `admin_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `client_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `ip` VARCHAR(255) NOT NULL DEFAULT '',
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_rel_id` (`rel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: web_nav
-- ----------------------------
DROP TABLE IF EXISTS `web_nav`;
CREATE TABLE `web_nav` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `url` VARCHAR(255) NOT NULL DEFAULT '',
  `target` VARCHAR(255) NOT NULL DEFAULT '_self',
  `parent_id` INT NOT NULL DEFAULT 0,
  `order` INT NOT NULL DEFAULT 0,
  `status` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: upstream_host
-- ----------------------------
DROP TABLE IF EXISTS `upstream_host`;
CREATE TABLE `upstream_host` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `supplier_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `host_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_supplier_id` (`supplier_id`),
  KEY `idx_host_id` (`host_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: upstream_order
-- ----------------------------
DROP TABLE IF EXISTS `upstream_order`;
CREATE TABLE `upstream_order` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `supplier_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `order_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_supplier_id` (`supplier_id`),
  KEY `idx_order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: upstream_product
-- ----------------------------
DROP TABLE IF EXISTS `upstream_product`;
CREATE TABLE `upstream_product` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `supplier_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `product_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `update_time` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_supplier_id` (`supplier_id`),
  KEY `idx_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: bottom_bar_group
-- ----------------------------
DROP TABLE IF EXISTS `bottom_bar_group`;
CREATE TABLE `bottom_bar_group` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: bottom_bar_nav
-- ----------------------------
DROP TABLE IF EXISTS `bottom_bar_nav`;
CREATE TABLE `bottom_bar_nav` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `url` VARCHAR(255) NOT NULL DEFAULT '',
  `order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: config_option
-- ----------------------------
DROP TABLE IF EXISTS `config_option`;
CREATE TABLE `config_option` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `type` VARCHAR(255) NOT NULL DEFAULT '',
  `order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table: config_option_sub
-- ----------------------------
DROP TABLE IF EXISTS `config_option_sub`;
CREATE TABLE `config_option_sub` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `config_option_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_config_option_id` (`config_option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;
