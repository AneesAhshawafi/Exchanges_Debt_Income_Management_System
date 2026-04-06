<!-- فورم تعديل تحويل عملات -->
<div id="editTransferModal" class="modal-overlay hidden">
    <div class="form-card">
        <form class="operation-form" id="edit-transfer-form" method="POST">
            <span class="close-modal close-modal-form" data-close="editTransferModal">&rarr;</span>
            <div class="form-header transfer-header">
                <i class="fas fa-coins"></i>
                <h3>تعديل تحويل عملات</h3>
            </div>

            <input type="hidden" name="type" value="تحويل">
            <input type="hidden" name="exchange_id" id="edit-transfer-id">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="form-body">
                <div class="form-row">
                    <div class="form-field">
                        <label><i class="fas fa-arrow-right"></i> من العملة</label>
                        <select name="select-from" id="edit-transfer-select-from" required>
                            <option value="" disabled selected>التحويل من</option>
                            <option value="new">القعيطي</option>
                            <option value="old">القديم</option>
                            <option value="sa">السعودي</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label><i class="fas fa-arrow-left"></i> إلى العملة</label>
                        <select name="select-to" id="edit-transfer-select-to" required>
                            <option value="" disabled selected>التحويل إلى</option>
                            <option value="new">القعيطي</option>
                            <option value="old">القديم</option>
                            <option value="sa">السعودي</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label><i class="fas fa-calculator"></i> المبلغ</label>
                        <input type="number" step="0.000000001" name="ammount" id="edit-transfer-ammount" placeholder="المبلغ" required>
                    </div>
                    <div class="form-field">
                        <label><i class="fas fa-tag"></i> السعر</label>
                        <input type="number" step="0.00001" name="price" id="edit-transfer-price" placeholder="السعر" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label><i class="fas fa-hashtag"></i> رقم التحويل</label>
                        <input type="text" name="transfer-no" id="edit-transfer-transfer-no" placeholder="رقم التحويل">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label><i class="fas fa-calendar-alt"></i> التاريخ</label>
                        <input type="date" name="date" id="edit-transfer-date">
                    </div>
                    <div class="form-field">
                        <label><i class="fas fa-university"></i> الصراف</label>
                        <input type="text" name="atm" id="edit-transfer-atm" placeholder="الصراف" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field full-width">
                        <label><i class="fas fa-sticky-note"></i> ملاحظة</label>
                        <input type="text" name="note" id="edit-transfer-note" placeholder="ملاحظة">
                    </div>
                </div>

                <button class="btn form-submit-btn" type="submit">تحديث</button>
            </div>
        </form>
    </div>
</div>
