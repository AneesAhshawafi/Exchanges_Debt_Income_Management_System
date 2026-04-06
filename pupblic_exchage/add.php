<?php
/**
 * نموذج إضافة حوالة عامة (ملف منفصل)
 * Add General Transfer Form (Separate File)
 * يتم تضمينه في public_exchanges_page.php
 * النوع ثابت: حوالة فقط
 */

// توليد رمز فريد لكل مرة تفتح فيها الصفحة (مفتاح عدم التكرار)
$idempotency_token = bin2hex(random_bytes(16));
?>

<!-- Start Add Public Exchange Form -->
<div id="addPeFormOverlay" class="pe-modal-overlay hidden">
    <div class="pe-form-container">
        <form class="pe-form" id="pe-add-form" method="POST">
            <span class="pe-close-btn" id="closeAddPeBtn">&rarr;</span>

            <div class="pe-form-title">
                <h3>إضافة حوالة عامة</h3>
            </div>

            <!-- رموز الأمان -->
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="idempotency_key" value="<?php echo $idempotency_token; ?>">

            <!-- العملة -->
            <div class="pe-input-group">
                <label for="pe-add-currency">العملة</label>
                <select name="currency" id="pe-add-currency" required>
                    <option value="" disabled selected>اختر العملة</option>
                    <option value="new">قعيطي</option>
                    <option value="old">قديم</option>
                    <option value="sa">سعودي</option>
                </select>
            </div>

            <!-- حالة الحوالة -->
            <div class="pe-input-group">
                <label for="pe-add-status">حالة الحوالة</label>
                <select name="status" id="pe-add-status">
                    <option value="" disabled selected>حالة الحوالة</option>
                    <option value="استلمت">استلمت</option>
                    <option value="لم تستلم">لم تستلم</option>
                </select>
            </div>

            <!-- المبلغ -->
            <div class="pe-input-group">
                <label for="pe-add-ammount">المبلغ</label>
                <input type="number" step="0.000001" name="ammount" id="pe-add-ammount" placeholder="المبلغ" required>
            </div>

            <!-- اسم المرسل / المودع -->
            <div class="pe-input-group">
                <label for="pe-add-sender-name">المرسل / المودع</label>
                <input type="text" name="sender-name" id="pe-add-sender-name" placeholder="اسم المرسل أو المودع">
            </div>

            <!-- رقم المرسل -->
            <div class="pe-input-group">
                <label for="pe-add-sender-phone">رقم المرسل</label>
                <input type="text" name="sender-phone" id="pe-add-sender-phone" placeholder="رقم المرسل">
            </div>

            <!-- اسم المستلم -->
            <div class="pe-input-group">
                <label for="pe-add-receiver-name">المستلم</label>
                <input type="text" name="receiver-name" id="pe-add-receiver-name" placeholder="اسم المستلم">
            </div>

            <!-- رقم المستلم -->
            <div class="pe-input-group">
                <label for="pe-add-receiver-phone">رقم المستلم</label>
                <input type="text" name="receiver-phone" id="pe-add-receiver-phone" placeholder="رقم المستلم">
            </div>

            <!-- رقم الحوالة -->
            <div class="pe-input-group">
                <label for="pe-add-transfer-no">رقم الحوالة</label>
                <input type="text" name="transfer-no" id="pe-add-transfer-no" placeholder="رقم الحوالة">
            </div>

            <!-- الرسوم -->
            <div class="pe-input-group">
                <label for="pe-add-fees">الرسوم</label>
                <input type="number" step="0.00001" name="fees" id="pe-add-fees" placeholder="الرسوم">
            </div>

            <!-- ربح الرسوم -->
            <div class="pe-input-group">
                <label for="pe-add-fees-income">ربح الرسوم</label>
                <input type="number" step="0.00001" name="fees-income" id="pe-add-fees-income" placeholder="ربح الرسوم">
            </div>

            <!-- التاريخ -->
            <div class="pe-input-group">
                <label for="pe-add-date">التاريخ</label>
                <input type="date" name="tra-date" id="pe-add-date" placeholder="التاريخ">
            </div>

            <!-- الصراف -->
            <div class="pe-input-group">
                <label for="pe-add-atm">الصراف</label>
                <input type="text" name="atm" id="pe-add-atm" placeholder="الصراف / الجهة المنفذة" required>
            </div>

            <!-- ملاحظة -->
            <div class="pe-input-group">
                <label for="pe-add-note">ملاحظة</label>
                <input type="text" name="note" id="pe-add-note" placeholder="ملاحظة">
            </div>

            <!-- DYNAMIC_FIELD_PLACEHOLDER: أضف حقول جديدة هنا عند الحاجة -->

            <!-- زر الحفظ -->
            <button class="pe-submit-btn" type="submit" id="pe-add-submit-btn" name="submit-pe">
                <span id="pe-add-btn-text">حفظ</span>
                <span id="pe-add-spinner" class="pe-spinner hidden"></span>
            </button>
        </form>
    </div>
</div>
<!-- End Add Public Exchange Form -->
