<!-- فورم تعديل إيداع -->
<div id="editDepositModal" class="modal-overlay hidden">
    <div class="form-card">
        <form class="operation-form" id="edit-deposit-form" method="POST">
            <span class="close-modal close-modal-form" data-close="editDepositModal">&rarr;</span>
            <div class="form-header deposit-header">
                <i class="fas fa-wallet"></i>
                <h3>تعديل إيداع</h3>
            </div>

            <input type="hidden" name="type" value="إيداع">
            <input type="hidden" name="exchange_id" id="edit-deposit-id">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="form-body">
                <div class="form-row">
                    <div class="form-field">
                        <label><i class="fas fa-money-bill"></i> العملة</label>
                        <select name="currency" id="edit-deposit-currency" required>
                            <option value="" disabled selected>اختر العملة</option>
                            <option value="new">قعيطي</option>
                            <option value="old">قديم</option>
                            <option value="sa">سعودي</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label><i class="fas fa-balance-scale"></i> له / عليه</label>
                        <select name="for-or-on" id="edit-deposit-for-or-on" required>
                            <option value="" disabled selected>له / عليه</option>
                            <option value="له">له</option>
                            <option value="عليه">عليه</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label><i class="fas fa-calculator"></i> المبلغ</label>
                        <input type="number" step="0.000000001" name="ammount" id="edit-deposit-ammount" placeholder="المبلغ" required>
                    </div>
                    <div class="form-field">
                        <label><i class="fas fa-hashtag"></i> رقم السند</label>
                        <input type="text" name="transfer-no" id="edit-deposit-transfer-no" placeholder="رقم السند">
                    </div>
                </div>

                <!-- المودع + رقمه -->
                <div class="form-row" id="edit-deposit-sender-row">
                    <div class="form-field">
                        <label><i class="fas fa-user"></i> اسم المودع</label>
                        <input type="text" name="edit-sender-name" id="edit-deposit-sender" placeholder="اسم المودع">
                    </div>
                    <div class="form-field">
                        <label><i class="fas fa-phone"></i> رقم المودع</label>
                        <input type="text" name="edit-sender-phone" id="edit-deposit-sender-phone" placeholder="رقم المودع">
                    </div>
                </div>

                <!-- المستلم + رقمه -->
                <div class="form-row hidden" id="edit-deposit-receiver-row">
                    <div class="form-field">
                        <label><i class="fas fa-user-check"></i> اسم المستلم</label>
                        <input type="text" name="edit-receiver-name" id="edit-deposit-receiver" placeholder="اسم المستلم">
                    </div>
                    <div class="form-field">
                        <label><i class="fas fa-phone"></i> رقم المستلم</label>
                        <input type="text" name="edit-receiver-phone" id="edit-deposit-receiver-phone" placeholder="رقم المستلم">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label><i class="fas fa-calendar-alt"></i> التاريخ</label>
                        <input type="date" name="date" id="edit-deposit-date">
                    </div>
                    <div class="form-field">
                        <label><i class="fas fa-university"></i> الصراف</label>
                        <input type="text" name="atm" id="edit-deposit-atm" placeholder="الصراف" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field full-width">
                        <label><i class="fas fa-sticky-note"></i> ملاحظة</label>
                        <input type="text" name="note" id="edit-deposit-note" placeholder="ملاحظة">
                    </div>
                </div>

                <button class="btn form-submit-btn" type="submit">تحديث</button>
            </div>
        </form>
    </div>
</div>
