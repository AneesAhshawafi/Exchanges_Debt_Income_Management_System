/**
 * ملف إضافة حوالة عامة عبر AJAX
 * Add Public Exchange via AJAX with SweetAlert2
 * يدعم: كشف التكرار، مفتاح عدم التكرار، رسائل SweetAlert2
 */

// عناصر النموذج
const addPeFormOverlay = document.getElementById("addPeFormOverlay");
const addPeForm = document.getElementById("pe-add-form");
const addPeBtn = document.getElementById("addPeBtn");
const closeAddPeBtn = document.getElementById("closeAddPeBtn");

// فتح نموذج الإضافة
addPeBtn.addEventListener("click", () => {
    addPeFormOverlay.classList.remove("hidden");
});

// إغلاق نموذج الإضافة
closeAddPeBtn.addEventListener("click", () => {
    addPeFormOverlay.classList.add("hidden");
});

/**
 * إرسال بيانات الحوالة العامة عبر AJAX
 * @param {boolean} force - إذا كانت true يتم الحفظ رغم التكرار
 */
function sendPublicExchangeData(force = false) {
    const form = document.getElementById("pe-add-form");
    const formData = new FormData(form);

    if (force) {
        formData.append("force_save", "true");
    }

    // عناصر الزر
    const submitBtn = document.getElementById("pe-add-submit-btn");
    const btnText = document.getElementById("pe-add-btn-text");
    const spinner = document.getElementById("pe-add-spinner");

    // منع النقر المزدوج
    if (!force && submitBtn.disabled) return;

    submitBtn.disabled = true;
    spinner.classList.remove("hidden");
    btnText.innerText = "جاري الحفظ...";

    fetch("insert_public_exchange.php", {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(response => {
            if (response.is_duplicate) {
                // تنبيه SweetAlert2 عند اكتشاف تكرار
                Swal.fire({
                    title: 'عملية مكررة!',
                    text: response.message,
                    icon: 'warning',
                    target: '#addPeFormOverlay',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'نعم، قم بالحفظ',
                    cancelButtonText: 'إلغاء',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        sendPublicExchangeData(true);
                    } else {
                        peResetSubmitButton(submitBtn, btnText, spinner);
                    }
                });
            } else if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح',
                    text: response.success,
                    target: '#addPeFormOverlay',
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
                    target: '#addPeFormOverlay',
                });
                peResetSubmitButton(submitBtn, btnText, spinner);
            }
        })
        .catch(err => {
            console.error("Error details:", err);
            Swal.fire({
                icon: 'error',
                title: 'فشل الاتصال',
                text: 'حدث خطأ في الشبكة أو السيرفر: ' + err.message,
                target: '#addPeFormOverlay',
            });
            peResetSubmitButton(submitBtn, btnText, spinner);
        });
}

// ربط استماع submit
addPeForm.addEventListener("submit", function (e) {
    e.preventDefault();
    sendPublicExchangeData(false);
});

/**
 * إعادة الزر لحالته الطبيعية
 */
function peResetSubmitButton(btn, text, spinner) {
    btn.disabled = false;
    spinner.classList.add("hidden");
    text.innerText = "حفظ";
}
