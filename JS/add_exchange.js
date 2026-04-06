/**
 * add_exchange.js — منطق إرسال فورم الإضافة (حوالة / إيداع / تحويل)
 */

currentClientId = localStorage.getItem("currentClientId");
const currentClientName = localStorage.getItem("currentClientName");
const currentClientPhone = localStorage.getItem("currentClientPhone");

// ===== دالة إرسال عامة =====
function sendFormData(formId, overlayId, force = false) {
    const form = document.getElementById(formId);
    const formData = new FormData(form);

    formData.append("client_id", currentClientId);
    formData.append("client_name", currentClientName);
    formData.append("client_phone", currentClientPhone);

    if (force) {
        formData.append("force_save", "true");
    }

    const submitBtn = form.querySelector('.form-submit-btn');
    const btnText = submitBtn ? submitBtn.querySelector('.btn-text') : null;
    const spinner = submitBtn ? submitBtn.querySelector('.btn-spinner') : null;

    if (!force && submitBtn && submitBtn.disabled) return;

    if (submitBtn) submitBtn.disabled = true;
    if (spinner) spinner.classList.remove("hidden");
    if (btnText) btnText.innerText = "جاري الحفظ...";

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
                    target: '#' + overlayId,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'نعم، قم بالحفظ',
                    cancelButtonText: 'إلغاء',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        sendFormData(formId, overlayId, true);
                    } else {
                        location.reload();
                    }
                });
            } else if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح',
                    text: response.success,
                    target: '#' + overlayId,
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
                    target: '#' + overlayId,
                });
                resetFormButton(submitBtn, btnText, spinner);
            }
        })
        .catch(err => {
            console.error("Error details:", err);
            Swal.fire({
                icon: 'error',
                title: 'فشل الاتصال',
                text: 'حدث خطأ في الشبكة أو السيرفر: ' + err.message,
                target: '#' + overlayId,
            });
            resetFormButton(submitBtn, btnText, spinner);
        });
}

function resetFormButton(btn, text, spinner) {
    if (btn) btn.disabled = false;
    if (spinner) spinner.classList.add("hidden");
    if (text) text.innerText = "حفظ";
}

// ===== ربط الفورمات =====
// حوالة
document.getElementById('add-hawala-form').addEventListener('submit', function (e) {
    e.preventDefault();
    sendFormData('add-hawala-form', 'addHawalaForm');
});

// إيداع
document.getElementById('add-deposit-form').addEventListener('submit', function (e) {
    e.preventDefault();
    sendFormData('add-deposit-form', 'addDepositForm');
});

// تحويل
document.getElementById('add-transfer-form').addEventListener('submit', function (e) {
    e.preventDefault();
    sendFormData('add-transfer-form', 'addTransferForm');
});