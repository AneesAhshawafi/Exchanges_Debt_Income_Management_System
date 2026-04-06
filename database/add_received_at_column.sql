-- إضافة عمود تاريخ الاستلام إلى جدول المعاملات
ALTER TABLE `transaction` ADD COLUMN `RECEIVED_AT` DATETIME DEFAULT NULL AFTER `STATUS`;

-- إضافة فهرس فريد على رقم الحوالة لتحسين أداء البحث
CREATE UNIQUE INDEX `idx_transfer_no` ON `transaction` (`TRANSFER_NO`);
