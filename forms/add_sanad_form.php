<!-- فورم إضافة سند قيد (سحب) -->
<div id="addSanadForm" class="modal-overlay hidden">
    <div class="form-card">
        <form class="operation-form" id="add-sanad-form" method="POST">
            <span class="close-modal close-modal-form" data-close="addSanadForm">&rarr;</span>
            <div class="form-header sanad-header">
                <i class="fas fa-minus-circle"></i>
                <h3>إضافة سند قيد (سحب)</h3>
            </div>

            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="idempotency_key" value="<?php echo bin2hex(random_bytes(16)); ?>">

            <div class="form-body">
                <div class="form-row">
                    <div class="form-field">
                        <label for="sanad-ammount"><i class="fas fa-calculator"></i> المبلغ</label>
                        <input type="number" step="0.000000001" name="ammount" id="sanad-ammount" placeholder="المبلغ" required>
                    </div>
                    <div class="form-field">
                        <label for="sanad-currency"><i class="fas fa-money-bill"></i> العملة</label>
                        <select name="currency" id="sanad-currency" required>
                            <option value="" disabled selected>اختر العملة</option>
                            <option value="new">قعيطي</option>
                            <option value="old">قديم</option>
                            <option value="sa">سعودي</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label for="sanad-date"><i class="fas fa-calendar-alt"></i> التاريخ</label>
                        <input type="date" name="tra-date" id="sanad-date">
                    </div>
                    <div class="form-field">
                        <label for="sanad-atm"><i class="fas fa-university"></i> الصراف</label>
                        <input type="text" name="atm" id="sanad-atm" placeholder="الصراف" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field full-width">
                        <label for="sanad-note"><i class="fas fa-sticky-note"></i> ملاحظة</label>
                        <input type="text" name="note" id="sanad-note" placeholder="ملاحظة">
                    </div>
                </div>

                <button class="btn form-submit-btn" type="submit" id="sanad-submit-btn">
                    <span class="btn-text" id="sanad-btn-text">حفظ</span>
                    <span class="btn-spinner hidden" id="sanad-spinner"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </form>
    </div>
</div>
