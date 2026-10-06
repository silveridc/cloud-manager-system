-- Apply after database/20260831_add_order_item_stock_reservation.sql.
-- That base security migration adds admin.session_version and client.session_version.
-- Existing roles remain non-delegable until super administrator ID 1 explicitly configures them.
ALTER TABLE `admin_role`
    ADD COLUMN `level` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `description`,
    ADD COLUMN `delegable` TINYINT NOT NULL DEFAULT 0 AFTER `level`;
