<!-- فورم إضافة إيداع -->
<div id="addDepositForm" class="modal-overlay hidden">
    <div class="form-card">
        <form class="operation-form" id="add-deposit-form" method="POST">
            <span class="close-modal close-modal-form" data-close="addDepositForm">&rarr;</span>
            <div class="form-header deposit-header">
                <i class="fas fa-wallet"></i>
                <h3>إضافة إيداع</h3>
            </div>

            <input type="hidden" name="type" value="إيداع">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="idempotency_key" value="<?php echo bin2hex(random_bytes(16)); ?>">

            <div class="form-body">
                <div class="form-row">
                    <div class="form-field">
                        <label for="deposit-currency"><i class="fas fa-money-bill"></i> العملة</label>
                        <select name="currency" id="deposit-currency" required>
                            <option value="" disabled selected>اختر العملة</option>
                            <option value="new">قعيطي</option>
                            <option value="old">قديم</option>
                            <option value="sa">سعودي</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="deposit-for-or-on"><i class="fas fa-balance-scale"></i> له / عليه</label>
                        <select name="for-or-on" id="deposit-for-or-on" required>
                            <option value="" disabled selected>له / عليه</option>
                            <option value="له">له</option>
                            <option value="عليه">عليه</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label for="deposit-ammount"><i class="fas fa-calculator"></i> المبلغ</label>
                        <input type="number" step="0.000000001" name="ammount" id="deposit-ammount" placeholder="المبلغ" required>
                    </div>
                    <div class="form-field">
                        <label for="deposit-transfer-no"><i class="fas fa-hashtag"></i> رقم السند</label>
                        <input type="text" name="transfer-no" id="deposit-transfer-no" placeholder="رقم السند">
                    </div>
                </div>

                <!-- المودع + رقمه — يظهر في حالة "له" -->
                <div class="form-row" id="deposit-sender-row">
                    <div class="form-field">
                        <label for="deposit-sender"><i class="fas fa-user"></i> اسم المودع</label>
                        <input type="text" name="sender-name" id="deposit-sender" placeholder="اسم المودع">
                    </div>
                    <div class="form-field">
                        <label for="deposit-sender-phone"><i class="fas fa-phone"></i> رقم المودع</label>
                        <input type="text" name="sender-phone" id="deposit-sender-phone" placeholder="رقم المودع">
                    </div>
                </div>

                <!-- المستلم + رقمه — يظهر في حالة "عليه" -->
                <div class="form-row hidden" id="deposit-receiver-row">
                    <div class="form-field">
                        <label for="deposit-receiver"><i class="fas fa-user-check"></i> اسم المستلم</label>
                        <input type="text" name="receiver-name" id="deposit-receiver" placeholder="اسم المستلم">
                    </div>
                    <div class="form-field">
                        <label for="deposit-receiver-phone"><i class="fas fa-phone"></i> رقم المستلم</label>
                        <input type="text" name="receiver-phone" id="deposit-receiver-phone" placeholder="رقم المستلم" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label for="deposit-date"><i class="fas fa-calendar-alt"></i> التاريخ</label>
                        <input type="date" name="tra-date" id="deposit-date">
                    </div>
                    <div class="form-field">
                        <label for="deposit-atm"><i class="fas fa-university"></i> الصراف</label>
                        <input type="text" name="atm" id="deposit-atm" placeholder="الصراف" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field full-width">
                        <label for="deposit-note"><i class="fas fa-sticky-note"></i> ملاحظة</label>
                        <input type="text" name="note" id="deposit-note" placeholder="ملاحظة">
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
