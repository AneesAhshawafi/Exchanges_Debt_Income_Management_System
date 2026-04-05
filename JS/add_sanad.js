/**
 * add_sanad.js — منطق فورم إضافة وتعديل سند القيد (سحب)
 */

// ========== فورم الإضافة ==========
const addSanadFormOverlay = document.getElementById("addSanadForm");
const addSanadForm = document.getElementById("add-sanad-form");
const addSanadBtn = document.getElementById("addSanadBtn");
const closeAddSanadBtn = document.getElementById("closeAddSanadBtn");

addSanadBtn.addEventListener("click", () => {
    addSanadFormOverlay.classList.remove("hidden");
});

closeAddSanadBtn.addEventListener("click", () => {
    addSanadFormOverlay.classList.add("hidden");
});

// دالة إرسال بيانات سند القيد
function sendSanadData(force = false) {
    const form = document.getElementById("add-sanad-form");
    const formData = new FormData(form);

    // إضافة البيانات الافتراضية التلقائية
    formData.append("client_id", currentClientId);
    formData.append("client_name", currentClientName);
    formData.append("client_phone", currentClientPhone);
    formData.append("type", "سحب");
    formData.append("for-or-on", "عليه");
    formData.append("status", "تمت");
    formData.append("sender-name", "");
    formData.append("receiver-name", "");
    formData.append("sender-phone", "");
    formData.append("receiver-phone", "");
    formData.append("transfer-no", "");
    formData.append("fees", "0");
    formData.append("fees-income", "0");

    // إذا لم يدخل المستخدم تاريخ، نرسل فارغ والسيرفر يعين التاريخ الحالي
    if (!formData.get("tra-date")) {
        formData.set("tra-date", "");
    }

    if (force) {
        formData.append("force_save", "true");
    }

    const submitBtn = document.getElementById("sanad-submit-btn");
    const btnText = document.getElementById("sanad-btn-text");
    const spinner = document.getElementById("sanad-spinner");

    if (!force && submitBtn.disabled) return;

    submitBtn.disabled = true;
    spinner.classList.remove("hidden");
    btnText.innerText = "جاري الحفظ...";

    fetch("insert_transaction.php", {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(response => {
            if (response.is_duplicate) {
                Swal.fire({
                    title: 'عملية مكررة!',
                    text: response.message,
                    icon: 'warning',
                    target: '#addSanadForm',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'نعم، قم بالحفظ',
                    cancelButtonText: 'إلغاء',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        sendSanadData(true);
                    } else {
                        location.reload();
                    }
                });
            } else if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح',
                    text: response.success,
                    target: '#addSanadForm',
                    timer: 500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: response.error,
                    target: '#addSanadForm',
                });
                resetSanadSubmitButton(submitBtn, btnText, spinner);
            }
        })
        .catch(err => {
            console.error("Error details:", err);
            Swal.fire({
                icon: 'error',
                title: 'فشل الاتصال',
                text: 'حدث خطأ في الشبكة أو السيرفر: ' + err.message,
                target: '#addSanadForm',
            });
            resetSanadSubmitButton(submitBtn, btnText, spinner);
        });
}

addSanadForm.addEventListener("submit", function (e) {
    e.preventDefault();
    sendSanadData(false);
});

function resetSanadSubmitButton(btn, text, spinner) {
    btn.disabled = false;
    spinner.classList.add("hidden");
    text.innerText = "حفظ";
}


// ========== فورم التعديل ==========

function openEditSanadModal(traData) {
    if (!traData) {
        console.error("بيانات التعديل غير موجودة");
        return;
    }

    document.getElementById("edit-sanad-exchange-id").value = traData.TRA_ID;
    document.getElementById("edit-sanad-transfer-no").value = traData.TRANSFER_NO;
    document.getElementById("edit-sanad-ammount").value = traData.AMMOUNT;
    document.getElementById("edit-sanad-currency").value = traData.CURRENCY;
    document.getElementById("edit-sanad-date").value = traData.TRA_DATE;
    document.getElementById("edit-sanad-atm").value = traData.ATM;
    document.getElementById("edit-sanad-note").value = traData.NOTE;

    const editSanadModal = document.getElementById("editSanadModal");
    editSanadModal.classList.remove("hidden");

    const closeEditSanadBtn = document.getElementById("closeEditSanadBtn");
    const editSanadForm = document.getElementById("edit-sanad-form");

    // إزالة المستمع السابق لتجنب التكرار
    const newEditForm = editSanadForm.cloneNode(true);
    editSanadForm.parentNode.replaceChild(newEditForm, editSanadForm);

    newEditForm.addEventListener("submit", (event) => {
        event.preventDefault();
        const formData = new FormData(newEditForm);
        formData.append("client_id", currentClientId);
        formData.append("client_phone", currentClientPhone);
        formData.append("client_name", currentClientName);
        // حقول افتراضية لسند القيد
        formData.append("type", "سحب");
        formData.append("for-or-on", "عليه");
        formData.append("status", "تمت");
        formData.append("edit-sender-name", "");
        formData.append("edit-sender-phone", "");
        formData.append("edit-receiver-name", "");
        formData.append("edit-receiver-phone", "");
        formData.append("fees", "0");
        formData.append("fees-income", "0");

        fetch("update_exchange.php", {
            method: "POST",
            body: formData
        }).then(res => res.json())
            .then(response => {
                if (document.activeElement) document.activeElement.blur();
                if (response.success) {
                    closeModal('editSanadModal');
                    newEditForm.reset();
                    Swal.fire({ icon: 'success', title: 'تم بنجاح', text: response.success, timer: 1500, showConfirmButton: false }).then(() => { location.reload(); });
                } else {
                    Swal.fire({ icon: 'error', title: 'خطأ', text: response.error });
                }
            }).catch(er => {
                if (document.activeElement) document.activeElement.blur();
                Swal.fire({ icon: 'error', title: 'خطأ في الاتصال', text: String(er) });
            });
    });

    document.getElementById("closeEditSanadBtn").addEventListener("click", () => {
        closeModal('editSanadModal');
        newEditForm.reset();
    });
}
