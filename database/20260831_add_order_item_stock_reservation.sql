-- Apply this migration to existing installations before deploying the order-cancellation changes.
ALTER TABLE `order_item`
    ADD COLUMN `qty` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `type`,
    ADD COLUMN `stock_reserved` TINYINT NOT NULL DEFAULT 0 AFTER `qty`;

ALTER TABLE `admin`
    ADD COLUMN `session_version` BIGINT UNSIGNED NOT NULL DEFAULT 1 AFTER `status`;

ALTER TABLE `client`
    ADD COLUMN `session_version` BIGINT UNSIGNED NOT NULL DEFAULT 1 AFTER `status`;

-- Existing unpaid orders were created before stock-reservation metadata existed.
-- They are not marked as reserved to avoid incorrectly restoring stock during cancellation.
