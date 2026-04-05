-- =====================================================
-- إضافة عمود RECEIVED_AT إلى جدول الحوالات العامة
-- Add RECEIVED_AT column to public_exchange table
-- يستخدم لتسجيل تاريخ تأكيد استلام الحوالة
-- =====================================================

ALTER TABLE `public_exchange`
ADD COLUMN `RECEIVED_AT` DATETIME DEFAULT NULL COMMENT 'تاريخ تأكيد الاستلام'
AFTER `STATUS`;
