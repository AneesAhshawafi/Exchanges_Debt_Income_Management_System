const operSelectTypeInput = document.getElementById("oper-type-input");
const senderNameInput = document.getElementById("sender");
const receiverInput = document.getElementById('reciver-input');
const transferNoInput = document.getElementById("transfer-no");
const status = document.getElementById('status');
const transferOperDiv = document.getElementById('transfer-input-group');
const senderInputGroup = document.getElementById('sender-input-group');
const receiverInputGroup = document.getElementById('receiver-input-group');
const currencyIn = document.getElementById('currency');
const forOrOn = document.getElementById('for-or-on');
const selectFrom = document.getElementById('select-from');
const selectTo = document.getElementById('select-to');
const price = document.getElementById('price');
const feesInp = document.getElementById('fees');

operSelectTypeInput.addEventListener("change", () => {
    if (operSelectTypeInput.value == "حوالة") {
        senderInputGroup.classList.remove('hidden');
        receiverInputGroup.classList.remove('hidden');
        currencyIn.classList.remove('hidden');
        forOrOn.classList.remove('hidden');
        status.classList.remove('hidden');
        transferOperDiv.classList.add('hidden');
        feesInp.classList.remove('hidden');
        senderNameInput.placeholder = 'اسم المرسل';
        senderNameInput.required = true;
        receiverInput.required = true;
        currencyIn.required = true;
        forOrOn.required = true;
        status.required = true;
        selectFrom.required = false;
        selectTo.required = false;
        price.required = false;
        transferNoInput.placeholder = 'رقم الحوالة';
    } else if (operSelectTypeInput.value == 'إيداع') {
        senderInputGroup.classList.remove('hidden');
        receiverInputGroup.classList.remove('hidden');
        currencyIn.classList.remove('hidden');
        forOrOn.classList.remove('hidden');
        status.classList.add('hidden');
        transferOperDiv.classList.add('hidden');
        feesInp.classList.add('hidden');
        senderNameInput.placeholder = 'اسم المودع';
        transferNoInput.placeholder = 'رقم السند';
        senderNameInput.required = true;
        receiverInput.required = true;
        currencyIn.required = true;
        forOrOn.required = true;
        status.required = false;
        selectFrom.required = false;
        selectTo.required = false;
        price.required = false;
    } else {
        transferNoInput.placeholder = 'رقم التحويل';
        senderInputGroup.classList.add('hidden');
        receiverInputGroup.classList.add('hidden');
        transferOperDiv.classList.remove('hidden');
        status.classList.add('hidden');
        currencyIn.classList.add('hidden');
        forOrOn.classList.add('hidden');
        feesInp.classList.add('hidden');
        senderNameInput.required = false;
        receiverInput.required = false;
        currencyIn.required = false;
        forOrOn.required = false;
        status.required = false;
        selectFrom.required = true;
        selectTo.required = true;
        price.required = true;
    }
});

const editOperSelectTypeInput = document.getElementById("edit-type");
const editSenderNameInput = document.getElementById("edit-sender");
const labelEditSender=document.getElementById('label-edit-sender');
const editReceiverInput = document.getElementById('reciver');
const editTransferNoInput = document.getElementById("edit-transfer-no");
const labelEditTransferNO=document.getElementById('label-edit-transfer-no');
const editStatus = document.getElementById('edit-status-input-grp');
const editTransferOperDiv = document.getElementsByClassName('edit-transfer-input-group');
const editSenderInputGroup = document.getElementById('edit-sender-input-group');
const editReceiverInputGroup = document.getElementById('edit-receiver-input-group');
const editCurrency = document.getElementById('edit-currency-input-grp');
const editForOrOn = document.getElementById('edit-for-or-on-input-grp');
const editSelectFrom = document.getElementById('edit-select-from');
const editSelectTo = document.getElementById('edit-select-to');
const editPrice = document.getElementById('edit-price');
const editFeesInp = document.getElementById('edit-fees-input-grp');
const editAmmount = document.getElementById('edit-ammount');


editOperSelectTypeInput.addEventListener("change", () => {
    if (editOperSelectTypeInput.value == "حوالة") {
        editSenderInputGroup.classList.remove('hidden');
        editReceiverInputGroup.classList.remove('hidden');
        editCurrency.classList.remove('hidden');
        editForOrOn.classList.remove('hidden');
        editStatus.classList.remove('hidden');
        Array.from(editTransferOperDiv).forEach(e => {
            e.classList.add('hidden');
        });
        editFeesInp.classList.remove('hidden');
        labelEditSender.textContent='اسم المرسل';
        editSenderNameInput.placeholder = 'اسم المرسل';
        labelEditTransferNO.textContent='رقم الحوالة';
        editTransferNoInput.placeholder = 'رقم الحوالة';
        editSenderNameInput.required = true;
        editReceiverInput.required = true;
        editSelectFrom.required = false;
        editSelectTo.required = false;
        editPrice.required = false;
        editAmmount.readonly=false;
    } else if (editOperSelectTypeInput.value == "إيداع") {
        editSenderInputGroup.classList.remove('hidden');
        editReceiverInputGroup.classList.remove('hidden');
        editCurrency.classList.remove('hidden');
        editForOrOn.classList.remove('hidden');
        editStatus.classList.add('hidden');
        Array.from(editTransferOperDiv).forEach(e => {
            e.classList.add('hidden');
        });
        editFeesInp.classList.add('hidden');
        labelEditSender.textContent='اسم المودع';
        editSenderNameInput.placeholder = 'اسم المودع';
        editTransferNoInput.placeholder = 'رقم السند';
        labelEditTransferNO.textContent='رقم السند';
        editSenderNameInput.required = true;
        editReceiverInput.required = true;
        editSelectFrom.required = false;
        editSelectTo.required = false;
        editPrice.required = false;
        editAmmount.readonly=false;
    } else {
        editTransferNoInput.placeholder = 'رقم التحويل';
        labelEditTransferNO.textContent='رقم التحويل';
        editSenderInputGroup.classList.add('hidden');
        editReceiverInputGroup.classList.add('hidden');
        Array.from(editTransferOperDiv).forEach(e => {
            e.classList.remove('hidden');
        });
        editStatus.classList.add('hidden');
        editCurrency.classList.add('hidden');
        editForOrOn.classList.add('hidden');
        editFeesInp.classList.add('hidden');
        editSenderNameInput.required = false;
        editReceiverInput.required = false;
        editSelectFrom.required = true;
        editSelectTo.required = true;
        editPrice.required = true;
        editAmmount.readonly=true;
    }
});
