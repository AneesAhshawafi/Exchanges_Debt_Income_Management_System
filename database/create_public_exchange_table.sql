-- =====================================================
-- جدول الحوالات العامة (غير مرتبطة بعميل محدد)
-- Public Exchange Table (General Transfers)
-- النوع ثابت: حوالة فقط — بدون TYPE و FOR_OR_ON
-- =====================================================

CREATE TABLE IF NOT EXISTS `public_exchange` (
    `PE_ID`           INT              NOT NULL AUTO_INCREMENT,
    `USER_ID`         INT              NOT NULL,

    -- بيانات العملية
    `CURRENCY`        VARCHAR(20)      NOT NULL DEFAULT '' COMMENT 'العملة',
    `STATUS`          VARCHAR(50)      NOT NULL DEFAULT '' COMMENT 'حالة الحوالة',
    `AMMOUNT`         DECIMAL(18,6)    NOT NULL DEFAULT 0  COMMENT 'المبلغ',
    `TRA_DATE`        DATE             NOT NULL             COMMENT 'تاريخ العملية',
    `NOTE`            TEXT                                  COMMENT 'ملاحظة',

    -- بيانات الأطراف
    `SENDER_NAME`     VARCHAR(255)     NOT NULL DEFAULT '' COMMENT 'اسم المرسل / المودع',
    `SENDER_PHONE`    VARCHAR(30)      NOT NULL DEFAULT '' COMMENT 'رقم المرسل',
    `RECEIVER_NAME`   VARCHAR(255)     NOT NULL DEFAULT '' COMMENT 'اسم المستلم',
    `RECEIVER_PHONE`  VARCHAR(30)      NOT NULL DEFAULT '' COMMENT 'رقم المستلم',
    `TRANSFER_NO`     VARCHAR(50)      NOT NULL DEFAULT '' COMMENT 'رقم الحوالة',

    -- الرسوم والجهة المنفذة
    `TRA_FEES`        DECIMAL(18,6)    NOT NULL DEFAULT 0  COMMENT 'الرسوم الإجمالية',
    `FEES_INCOME`     DECIMAL(18,6)    NOT NULL DEFAULT 0  COMMENT 'ربح الرسوم',
    `ATM`             VARCHAR(100)     NOT NULL DEFAULT '' COMMENT 'الصراف / الجهة المنفذة',

    -- بيانات النظام
    `created_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`PE_ID`),
    INDEX `idx_user_id` (`USER_ID`),
    INDEX `idx_transfer_no` (`TRANSFER_NO`),
    INDEX `idx_tra_date` (`TRA_DATE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
