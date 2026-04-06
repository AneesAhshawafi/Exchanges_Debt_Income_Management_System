<!-- فورم إضافة تحويل عملات -->
<div id="addTransferForm" class="modal-overlay hidden">
    <div class="form-card">
        <form class="operation-form" id="add-transfer-form" method="POST">
            <span class="close-modal close-modal-form" data-close="addTransferForm">&rarr;</span>
            <div class="form-header transfer-header">
                <i class="fas fa-coins"></i>
                <h3>تحويل بين الحسابات</h3>
            </div>

            <input type="hidden" name="type" value="تحويل">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="idempotency_key" value="<?php echo bin2hex(random_bytes(16)); ?>">

            <div class="form-body">
                <div class="form-row">
                    <div class="form-field">
                        <label for="transfer-select-from"><i class="fas fa-arrow-right"></i> من العملة</label>
                        <select name="select-from" id="transfer-select-from" required>
                            <option value="" disabled selected>التحويل من</option>
                            <option value="new">القعيطي</option>
                            <option value="old">القديم</option>
                            <option value="sa">السعودي</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="transfer-select-to"><i class="fas fa-arrow-left"></i> إلى العملة</label>
                        <select name="select-to" id="transfer-select-to" required>
                            <option value="" disabled selected>التحويل إلى</option>
                            <option value="new">القعيطي</option>
                            <option value="old">القديم</option>
                            <option value="sa">السعودي</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label for="transfer-ammount"><i class="fas fa-calculator"></i> المبلغ</label>
                        <input type="number" step="0.000000001" name="ammount" id="transfer-ammount" placeholder="المبلغ" required>
                    </div>
                    <div class="form-field">
                        <label for="transfer-price"><i class="fas fa-tag"></i> السعر</label>
                        <input type="number" step="0.00001" name="price" id="transfer-price" placeholder="السعر" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label for="transfer-no-input"><i class="fas fa-hashtag"></i> رقم التحويل</label>
                        <input type="text" name="transfer-no" id="transfer-no-input" placeholder="رقم التحويل">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label for="transfer-date"><i class="fas fa-calendar-alt"></i> التاريخ</label>
                        <input type="date" name="tra-date" id="transfer-date">
                    </div>
                    <div class="form-field">
                        <label for="transfer-atm"><i class="fas fa-university"></i> الصراف</label>
                        <input type="text" name="atm" id="transfer-atm" placeholder="الصراف" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field full-width">
                        <label for="transfer-note"><i class="fas fa-sticky-note"></i> ملاحظة</label>
                        <input type="text" name="note" id="transfer-note" placeholder="ملاحظة">
                    </div>
                </div>

                <button class="btn form-submit-btn" type="submit">
                    <span class="btn-text">حفظ</span>
                    <span class="btn-spinner hidden"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </form>
    </div>
</div>
