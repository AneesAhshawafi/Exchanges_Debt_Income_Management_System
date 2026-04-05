<?php
/**
 * نموذج تعديل حوالة عامة (ملف منفصل)
 * Edit General Transfer Form (Separate File)
 * يتم تضمينه في public_exchanges_page.php
 * النوع ثابت: حوالة فقط
 */
?>

<!-- Start Edit Public Exchange Form -->
<div id="editPeFormOverlay" class="pe-modal-overlay hidden">
    <div class="pe-form-container">
        <form class="pe-form" id="pe-edit-form" method="POST">
            <span class="pe-close-btn" id="closeEditPeBtn">&rarr;</span>

            <div class="pe-form-title">
                <h3>تعديل بيانات الحوالة العامة</h3>
            </div>

            <!-- رموز الأمان -->
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="pe_id" id="edit-pe-id">

            <div class="pe-edit-form-body">
                <!-- العملة -->
                <div class="pe-input-group">
                    <label for="edit-pe-currency">العملة</label>
                    <select name="currency" id="edit-pe-currency">
                        <option value="" disabled selected>اختر العملة</option>
                        <option value="new">قعيطي</option>
                        <option value="old">قديم</option>
                        <option value="sa">سعودي</option>
                    </select>
                </div>

                <!-- حالة الحوالة -->
                <div class="pe-input-group">
                    <label for="edit-pe-status">حالة الحوالة</label>
                    <select name="status" id="edit-pe-status">
                        <option value="" disabled selected>حالة الحوالة</option>
                        <option value="استلمت">استلمت</option>
                        <option value="لم تستلم">لم تستلم</option>
                    </select>
                </div>

                <!-- المبلغ -->
                <div class="pe-input-group">
                    <label for="edit-pe-ammount">المبلغ</label>
                    <input type="number" step="0.000001" name="ammount" id="edit-pe-ammount" placeholder="المبلغ">
                </div>

                <!-- اسم المرسل / المودع -->
                <div class="pe-input-group">
                    <label for="edit-pe-sender-name">المرسل / المودع</label>
                    <input type="text" name="sender-name" id="edit-pe-sender-name" placeholder="اسم المرسل">
                </div>

                <!-- رقم المرسل -->
                <div class="pe-input-group">
                    <label for="edit-pe-sender-phone">رقم المرسل</label>
                    <input type="text" name="sender-phone" id="edit-pe-sender-phone" placeholder="رقم المرسل">
                </div>

                <!-- اسم المستلم -->
                <div class="pe-input-group">
                    <label for="edit-pe-receiver-name">المستلم</label>
                    <input type="text" name="receiver-name" id="edit-pe-receiver-name" placeholder="اسم المستلم">
                </div>

                <!-- رقم المستلم -->
                <div class="pe-input-group">
                    <label for="edit-pe-receiver-phone">رقم المستلم</label>
                    <input type="text" name="receiver-phone" id="edit-pe-receiver-phone" placeholder="رقم المستلم">
                </div>

                <!-- رقم الحوالة -->
                <div class="pe-input-group">
                    <label for="edit-pe-transfer-no">رقم الحوالة</label>
                    <input type="text" name="transfer-no" id="edit-pe-transfer-no" placeholder="رقم الحوالة">
                </div>

                <!-- الرسوم -->
                <div class="pe-input-group">
                    <label for="edit-pe-fees">الرسوم</label>
                    <input type="number" step="0.00001" name="fees" id="edit-pe-fees" placeholder="الرسوم">
                </div>

                <!-- ربح الرسوم -->
                <div class="pe-input-group">
                    <label for="edit-pe-fees-income">ربح الرسوم</label>
                    <input type="number" step="0.00001" name="fees-income" id="edit-pe-fees-income" placeholder="ربح الرسوم">
                </div>

                <!-- التاريخ -->
                <div class="pe-input-group">
                    <label for="edit-pe-date">التاريخ</label>
                    <input type="date" name="tra-date" id="edit-pe-date" placeholder="التاريخ">
                </div>

                <!-- الصراف -->
                <div class="pe-input-group">
                    <label for="edit-pe-atm">الصراف</label>
                    <input type="text" name="atm" id="edit-pe-atm" placeholder="الصراف" required>
                </div>

                <!-- ملاحظة -->
                <div class="pe-input-group">
                    <label for="edit-pe-note">ملاحظة</label>
                    <input type="text" name="note" id="edit-pe-note" placeholder="ملاحظة">
                </div>

                <!-- DYNAMIC_FIELD_PLACEHOLDER: أضف حقول جديدة هنا عند الحاجة -->
            </div>

            <!-- زر التحديث -->
            <button class="pe-submit-btn" type="submit" id="pe-edit-submit-btn">
                <span id="pe-edit-btn-text">تحديث</span>
                <span id="pe-edit-spinner" class="pe-spinner hidden"></span>
            </button>
        </form>
    </div>
</div>
<!-- End Edit Public Exchange Form -->
