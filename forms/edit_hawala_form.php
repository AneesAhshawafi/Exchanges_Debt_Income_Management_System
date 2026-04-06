<!-- فورم تعديل حوالة -->
<div id="editHawalaModal" class="modal-overlay hidden">
    <div class="form-card">
        <form class="operation-form" id="edit-hawala-form" method="POST">
            <span class="close-modal close-modal-form" data-close="editHawalaModal">&rarr;</span>
            <div class="form-header hawala-header">
                <i class="fas fa-exchange-alt"></i>
                <h3>تعديل حوالة</h3>
            </div>

            <input type="hidden" name="type" value="حوالة">
            <input type="hidden" name="exchange_id" id="edit-hawala-id">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="form-body">
                <div class="form-row">
                    <div class="form-field">
                        <label><i class="fas fa-money-bill"></i> العملة</label>
                        <select name="currency" id="edit-hawala-currency" required>
                            <option value="" disabled selected>اختر العملة</option>
                            <option value="new">قعيطي</option>
                            <option value="old">قديم</option>
                            <option value="sa">سعودي</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label><i class="fas fa-balance-scale"></i> له / عليه</label>
                        <select name="for-or-on" id="edit-hawala-for-or-on" required>
                            <option value="" disabled selected>له / عليه</option>
                            <option value="له">له</option>
                            <option value="عليه">عليه</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label><i class="fas fa-info-circle"></i> حالة الحوالة</label>
                        <select name="status" id="edit-hawala-status" required>
                            <option value="" disabled selected>حالة الحوالة</option>
                            <option value="استلمت">استلمت</option>
                            <option value="لم تستلم">لم تستلم</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label><i class="fas fa-hashtag"></i> رقم الحوالة</label>
                        <input type="text" name="transfer-no" id="edit-hawala-transfer-no" placeholder="رقم الحوالة">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label><i class="fas fa-calculator"></i> المبلغ</label>
                        <input type="number" step="0.000000001" name="ammount" id="edit-hawala-ammount" placeholder="المبلغ" required>
                    </div>
                </div>

                <!-- اسم المرسل — يظهر عند "له" -->
                <div class="form-row" id="edit-hawala-sender-row">
                    <div class="form-field">
                        <label><i class="fas fa-user"></i> اسم المرسل</label>
                        <input type="text" name="edit-sender-name" id="edit-hawala-sender" placeholder="اسم المرسل">
                    </div>
                </div>

                <!-- اسم المستلم — يظهر عند "عليه" -->
                <div class="form-row hidden" id="edit-hawala-receiver-row">
                    <div class="form-field">
                        <label><i class="fas fa-user-check"></i> اسم المستلم</label>
                        <input type="text" name="edit-receiver-name" id="edit-hawala-receiver" placeholder="اسم المستلم">
                    </div>
                </div>

                <!-- الرسوم — تظهر فقط عند "عليه" -->
                <div class="form-row fees-row hidden" id="edit-hawala-fees-row">
                    <div class="form-field">
                        <label><i class="fas fa-receipt"></i> الرسوم</label>
                        <input type="number" step="0.00001" name="fees" id="edit-hawala-fees" placeholder="الرسوم">
                    </div>
                    <div class="form-field">
                        <label><i class="fas fa-hand-holding-usd"></i> الرسوم لك</label>
                        <input type="number" step="0.00001" name="fees-income" id="edit-hawala-fees-income" placeholder="الرسوم لك">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label><i class="fas fa-calendar-alt"></i> التاريخ</label>
                        <input type="date" name="date" id="edit-hawala-date">
                    </div>
                    <div class="form-field">
                        <label><i class="fas fa-university"></i> الصراف</label>
                        <input type="text" name="atm" id="edit-hawala-atm" placeholder="الصراف" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field full-width">
                        <label><i class="fas fa-sticky-note"></i> ملاحظة</label>
                        <input type="text" name="note" id="edit-hawala-note" placeholder="ملاحظة">
                    </div>
                </div>

                <button class="btn form-submit-btn" type="submit">تحديث</button>
            </div>
        </form>
    </div>
</div>
