/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */

currentClientId   = localStorage.getItem("currentClientId");
const currentClientName  = localStorage.getItem("currentClientName");
const currentClientPhone = localStorage.getItem("currentClientPhone");
const addExchangeFormOverlay = document.getElementById("addExchangeForm");
const addExchangeForm = document.getElementById("add-exchange-form");
const addExchangeBtn = document.getElementById("addExchangeBtn");
const closeAddExchangeBtn = document.getElementById("closeAddExchangeBtn");


addExchangeBtn.addEventListener("click", () => {
    addExchangeFormOverlay.classList.remove("hidden");
});

closeAddExchangeBtn.addEventListener("click", () => {
    addExchangeFormOverlay.classList.add("hidden");
});


// 1. ضع الدالة هنا (خارج أي مستمع أحداث) ليكون من السهل استدعاؤها مرة أخرى
function sendTransactionData(force = false) {
    const form = document.getElementById("add-exchange-form");
    const formData = new FormData(form);

    // تأكد من جلب client_id ووضعه في الـ FormData
    formData.append("client_id", currentClientId);
    formData.append("client_name", currentClientName);
    formData.append("client_phone", currentClientPhone);

    if (force) {
        formData.append("force_save", "true");
    }

    // const submitBtn = document.getElementById('submit-btn');
    // submitBtn.disabled = true;
    // // 1. تحديد الزر والعناصر
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const spinner = document.getElementById('spinner');

    // 2. منع النقر المزدوج وتعطيل الزر فوراً
    if (!force && submitBtn.disabled) return;

    submitBtn.disabled = true;
    spinner.classList.remove("hidden"); // إظهار السبينر
    btnText.innerText = "جاري الحفظ...";

    fetch("insert_transaction.php", {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(response => {
            if (response.is_duplicate) {
                // استدعاء SweetAlert عند اكتشاف تكرار البيانات
                Swal.fire({
                    title: 'عملية مكررة!',
                    text: response.message,
                    icon: 'warning',
                    target: '#addExchangeForm', // حدد معرف الفورم الطافي هنا لكي يظهر التنبيه بداخله
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'نعم، قم بالحفظ',
                    cancelButtonText: 'إلغاء',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // استدعاء الدالة نفسها مرة أخرى مع تمرير true
                        sendTransactionData(true);
                    } else {
                        location.reload();
                    }
                });
            } else if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح',
                    text: response.success,
                    target: '#addExchangeForm',
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
                    target: '#addExchangeForm',
                });
                resetSubmitButton(submitBtn, btnText, spinner);
            }
        })
        .catch(err => {
            console.error("Error details:", err);
            // هنا كان الخطأ، تأكد من استخدام err وليس response
            Swal.fire({
                icon: 'error',
                title: 'فشل الاتصال',
                text: 'حدث خطأ في الشبكة أو السيرفر: ' + err.message,
                target: '#addExchangeForm',
            });
            resetSubmitButton(submitBtn, btnText, spinner);
        });
}

// 2. هنا نقوم بـ "استدعاء" الدالة عند ضغط المستخدم على زر الحفظ
addExchangeForm.addEventListener("submit", function (e) {
    e.preventDefault(); // منع الإرسال التقليدي
    sendTransactionData(false); // استدعاء الدالة للمرة الأولى (بدون فرض حفظ)
});
// وظيفة لإعادة الزر لحالته الطبيعية
function resetSubmitButton(btn, text, spinner) {
    btn.disabled = false;
    spinner.classList.add("hidden");
    text.innerText = "حفظ";
}