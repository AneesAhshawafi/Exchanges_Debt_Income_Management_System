<!-- فورم تعديل سند قيد (سحب) -->
<div id="editSanadModal" class="modal-overlay hidden">
    <div class="form-card">
        <form class="operation-form" id="edit-sanad-form" method="POST">
            <span class="close-modal close-modal-form" data-close="editSanadModal">&rarr;</span>
            <div class="form-header sanad-header">
                <i class="fas fa-minus-circle"></i>
                <h3>تعديل سند قيد (سحب)</h3>
            </div>

            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="exchange_id" id="edit-sanad-exchange-id">
            <input type="hidden" name="transfer-no" id="edit-sanad-transfer-no">

            <div class="form-body">
                <div class="form-row">
                    <div class="form-field">
                        <label for="edit-sanad-ammount"><i class="fas fa-calculator"></i> المبلغ</label>
                        <input type="number" step="0.000000001" name="ammount" id="edit-sanad-ammount" placeholder="المبلغ" required>
                    </div>
                    <div class="form-field">
                        <label for="edit-sanad-currency"><i class="fas fa-money-bill"></i> العملة</label>
                        <select name="currency" id="edit-sanad-currency" required>
                            <option value="" disabled selected>اختر العملة</option>
                            <option value="new">قعيطي</option>
                            <option value="old">قديم</option>
                            <option value="sa">سعودي</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label for="edit-sanad-date"><i class="fas fa-calendar-alt"></i> التاريخ</label>
                        <input type="date" name="date" id="edit-sanad-date">
                    </div>
                    <div class="form-field">
                        <label for="edit-sanad-atm"><i class="fas fa-university"></i> الصراف</label>
                        <input type="text" name="atm" id="edit-sanad-atm" placeholder="الصراف" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field full-width">
                        <label for="edit-sanad-note"><i class="fas fa-sticky-note"></i> ملاحظة</label>
                        <input type="text" name="note" id="edit-sanad-note" placeholder="ملاحظة">
                    </div>
                </div>

                <button class="btn form-submit-btn" type="submit">تحديث</button>
            </div>
        </form>
    </div>
</div>
