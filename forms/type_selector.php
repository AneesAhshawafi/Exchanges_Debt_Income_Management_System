<!-- مودال اختيار نوع العملية -->
<div id="typeSelectorModal" class="modal-overlay hidden">
    <div class="type-selector-card">
        <span class="close-modal close-modal-form" id="closeTypeSelectorBtn">&rarr;</span>
        <div class="type-selector-title">
            <h3>اختر نوع العملية</h3>
        </div>
        <div class="type-selector-grid">
            <button class="type-option" id="selectHawala" type="button">
                <i class="fas fa-exchange-alt"></i>
                <span>حوالة</span>
                <small>إرسال واستقبال حوالات</small>
            </button>
            <button class="type-option" id="selectDeposit" type="button">
                <i class="fas fa-wallet"></i>
                <span>إيداع</span>
                <small>إيداع مبالغ للعملاء</small>
            </button>
            <button class="type-option" id="selectTransfer" type="button">
                <i class="fas fa-coins"></i>
                <span>تحويل عملات</span>
                <small>تحويل بين الحسابات</small>
            </button>
        </div>
    </div>
</div>
