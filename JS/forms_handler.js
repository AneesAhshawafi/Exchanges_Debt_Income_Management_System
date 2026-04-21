/**
 * forms_handler.js — التحكم في الفورمات المنفصلة (بديل switch_withdraw_exchange.js)
 */

// ===== إغلاق المودالات بزر data-close =====
document.addEventListener('click', function (e) {
    if (e.target.matches('[data-close]')) {
        const modalId = e.target.getAttribute('data-close');
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.add('hidden');
    }
});

// ===== Type Selector =====
const addExchangeBtn = document.getElementById('addExchangeBtn');
const typeSelectorModal = document.getElementById('typeSelectorModal');
const closeTypeSelectorBtn = document.getElementById('closeTypeSelectorBtn');

addExchangeBtn.addEventListener('click', () => {
    typeSelectorModal.classList.remove('hidden');
});

closeTypeSelectorBtn.addEventListener('click', () => {
    typeSelectorModal.classList.add('hidden');
});

// اختيار نوع العملية
document.getElementById('selectHawala').addEventListener('click', () => {
    typeSelectorModal.classList.add('hidden');
    document.getElementById('addHawalaForm').classList.remove('hidden');
});

document.getElementById('selectDeposit').addEventListener('click', () => {
    typeSelectorModal.classList.add('hidden');
    document.getElementById('addDepositForm').classList.remove('hidden');
});

document.getElementById('selectTransfer').addEventListener('click', () => {
    typeSelectorModal.classList.add('hidden');
    document.getElementById('addTransferForm').classList.remove('hidden');
});

// ===== سند القيد - زر فتح =====
document.getElementById('addSanadBtn').addEventListener('click', () => {
    document.getElementById('addSanadForm').classList.remove('hidden');
});

// ===== دالة عامة: Toggle حقول المرسل/المستلم + الرسوم (للحوالة) =====
function setupHawalaToggle(forOrOnId, senderRowId, receiverRowId, feesRowId) {
    const forOrOnSelect = document.getElementById(forOrOnId);
    if (!forOrOnSelect) return;

    function update() {
        const senderRow = document.getElementById(senderRowId);
        const receiverRow = document.getElementById(receiverRowId);
        const feesRow = document.getElementById(feesRowId);
        if (!senderRow || !receiverRow || !feesRow) return;

        const val = forOrOnSelect.value;
        if (val === 'له') {
            senderRow.classList.remove('hidden');
            receiverRow.classList.add('hidden');
            feesRow.classList.add('hidden');
            senderRow.querySelectorAll('input').forEach(inp => inp.required = true);
            receiverRow.querySelectorAll('input').forEach(inp => inp.required = false);
            feesRow.querySelectorAll('input').forEach(inp => inp.required = false);
        } else if (val === 'عليه') {
            senderRow.classList.add('hidden');
            receiverRow.classList.remove('hidden');
            feesRow.classList.remove('hidden');
            senderRow.querySelectorAll('input').forEach(inp => inp.required = false);
            receiverRow.querySelectorAll('input').forEach(inp => inp.required = true);
            feesRow.querySelectorAll('input').forEach(inp => inp.required = true);
        }
    }

    forOrOnSelect.addEventListener('change', update);
    update();
}

// ===== دالة عامة: Toggle حقول المودع/المستلم مع الأرقام (للإيداع) =====
function setupDepositToggle(forOrOnId, senderRowId, receiverRowId) {
    const forOrOnSelect = document.getElementById(forOrOnId);
    if (!forOrOnSelect) return;

    function update() {
        const senderRow = document.getElementById(senderRowId);
        const receiverRow = document.getElementById(receiverRowId);
        if (!senderRow || !receiverRow) return;

        const val = forOrOnSelect.value;
        if (val === 'له') {
            senderRow.classList.remove('hidden');
            receiverRow.classList.add('hidden');
            senderRow.querySelectorAll('input').forEach(inp => inp.required = true);
            receiverRow.querySelectorAll('input').forEach(inp => inp.required = false);
        } else if (val === 'عليه') {
            senderRow.classList.add('hidden');
            receiverRow.classList.remove('hidden');
            senderRow.querySelectorAll('input').forEach(inp => inp.required = false);
            receiverRow.querySelectorAll('input').forEach(inp => inp.required = true);
            // رقم المستلم مطلوب في حالة "عليه"
            const receiverPhone = receiverRow.querySelector('input[name*="receiver-phone"]');
            if (receiverPhone) receiverPhone.required = true;
        }
    }

    forOrOnSelect.addEventListener('change', update);
    update();
}

// ===== إعداد toggles لفورمات الإضافة =====
setupHawalaToggle('hawala-for-or-on', 'hawala-sender-row', 'hawala-receiver-row', 'hawala-fees-row');
setupDepositToggle('deposit-for-or-on', 'deposit-sender-row', 'deposit-receiver-row');
