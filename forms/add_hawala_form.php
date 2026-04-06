<!-- فورم إضافة حوالة -->
<div id="addHawalaForm" class="modal-overlay hidden">
    <div class="form-card">
        <form class="operation-form" id="add-hawala-form" method="POST">
            <span class="close-modal close-modal-form" data-close="addHawalaForm">&rarr;</span>
            <div class="form-header hawala-header">
                <i class="fas fa-exchange-alt"></i>
                <h3>إضافة حوالة</h3>
            </div>

            <input type="hidden" name="type" value="حوالة">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="idempotency_key" value="<?php echo bin2hex(random_bytes(16)); ?>">

            <div class="form-body">
                <div class="form-row">
                    <div class="form-field">
                        <label for="hawala-currency"><i class="fas fa-money-bill"></i> العملة</label>
                        <select name="currency" id="hawala-currency" required>
                            <option value="" disabled selected>اختر العملة</option>
                            <option value="new">قعيطي</option>
                            <option value="old">قديم</option>
                            <option value="sa">سعودي</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="hawala-for-or-on"><i class="fas fa-balance-scale"></i> له / عليه</label>
                        <select name="for-or-on" id="hawala-for-or-on" required>
                            <option value="" disabled selected>له / عليه</option>
                            <option value="له">له</option>
                            <option value="عليه">عليه</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label for="hawala-status"><i class="fas fa-info-circle"></i> حالة الحوالة</label>
                        <select name="status" id="hawala-status" required>
                            <option value="" disabled selected>حالة الحوالة</option>
                            <option value="استلمت">استلمت</option>
                            <option value="لم تستلم">لم تستلم</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="hawala-transfer-no"><i class="fas fa-hashtag"></i> رقم الحوالة</label>
                        <input type="text" name="transfer-no" id="hawala-transfer-no" placeholder="رقم الحوالة">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label for="hawala-ammount"><i class="fas fa-calculator"></i> المبلغ</label>
                        <input type="number" step="0.000000001" name="ammount" id="hawala-ammount" placeholder="المبلغ" required>
                    </div>
                </div>

                <!-- اسم المرسل — يظهر عند "له" -->
                <div class="form-row" id="hawala-sender-row">
                    <div class="form-field">
                        <label for="hawala-sender"><i class="fas fa-user"></i> اسم المرسل</label>
                        <input type="text" name="sender-name" id="hawala-sender" placeholder="اسم المرسل">
                    </div>
                </div>

                <!-- اسم المستلم — يظهر عند "عليه" -->
                <div class="form-row hidden" id="hawala-receiver-row">
                    <div class="form-field">
                        <label for="hawala-receiver"><i class="fas fa-user-check"></i> اسم المستلم</label>
                        <input type="text" name="receiver-name" id="hawala-receiver" placeholder="اسم المستلم">
                    </div>
                </div>

                <!-- حقول الرسوم — تظهر فقط عند "عليه" -->
                <div class="form-row fees-row hidden" id="hawala-fees-row">
                    <div class="form-field">
                        <label for="hawala-fees"><i class="fas fa-receipt"></i> الرسوم</label>
                        <input type="number" step="0.00001" name="fees" id="hawala-fees" placeholder="الرسوم">
                    </div>
                    <div class="form-field">
                        <label for="hawala-fees-income"><i class="fas fa-hand-holding-usd"></i> الرسوم لك</label>
                        <input type="number" step="0.00001" name="fees-income" id="hawala-fees-income" placeholder="الرسوم لك">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label for="hawala-date"><i class="fas fa-calendar-alt"></i> التاريخ</label>
                        <input type="date" name="tra-date" id="hawala-date">
                    </div>
                    <div class="form-field">
                        <label for="hawala-atm"><i class="fas fa-university"></i> الصراف</label>
                        <input type="text" name="atm" id="hawala-atm" placeholder="الصراف" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field full-width">
                        <label for="hawala-note"><i class="fas fa-sticky-note"></i> ملاحظة</label>
                        <input type="text" name="note" id="hawala-note" placeholder="ملاحظة">
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
