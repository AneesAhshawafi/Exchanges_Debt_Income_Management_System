/**
 * عمليات الحوالات العامة (تعديل، حذف)
 * Public Exchange Operations: Edit, Delete
 * يدعم: SweetAlert2، AJAX، تفويض الأحداث
 * النوع ثابت: حوالة فقط (لا يوجد حقل type)
 */

// رمز CSRF (يُنقل من PHP)
const peCsrfToken = document.getElementById("pe-csrf-token-meta")
    ? document.getElementById("pe-csrf-token-meta").value
    : "";

/**
 * فتح مودال الحذف باستخدام SweetAlert2
 * @param {number} peId - معرف الحوالة
 */
function openPeDeleteModal(peId) {
    Swal.fire({
        title: 'تأكيد الحذف',
        text: 'هل أنت متأكد من حذف هذه الحوالة العامة؟ لا يمكن التراجع عن هذا الإجراء.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append("pe_id", peId);
            formData.append("csrf_token", peCsrfToken);

            fetch("delete_public_exchange.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم بنجاح',
                            text: response.success,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: response.error
                        });
                    }
                })
                .catch(err => {
                    console.error("خطأ أثناء الحذف:", err);
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ في الاتصال',
                        text: String(err)
                    });
                });
        }
    });
}

/**
 * فتح مودال التعديل وتعبئة الحقول (بدون TYPE)
 * @param {Object} peData - بيانات الحوالة
 */
function openPeEditModal(peData) {
    if (!peData) {
        console.error("بيانات التعديل غير موجودة");
        return;
    }

    // تعبئة الحقول (بدون type)
    document.getElementById("edit-pe-id").value = peData.PE_ID;
    document.getElementById("edit-pe-currency").value = peData.CURRENCY || '';
    document.getElementById("edit-pe-status").value = peData.STATUS || '';
    document.getElementById("edit-pe-ammount").value = peData.AMMOUNT || '';
    document.getElementById("edit-pe-sender-name").value = peData.SENDER_NAME || '';
    document.getElementById("edit-pe-sender-phone").value = peData.SENDER_PHONE || '';
    document.getElementById("edit-pe-receiver-name").value = peData.RECEIVER_NAME || '';
    document.getElementById("edit-pe-receiver-phone").value = peData.RECEIVER_PHONE || '';
    document.getElementById("edit-pe-transfer-no").value = peData.TRANSFER_NO || '';
    document.getElementById("edit-pe-fees").value = peData.TRA_FEES || '';
    document.getElementById("edit-pe-fees-income").value = peData.FEES_INCOME || '';
    document.getElementById("edit-pe-date").value = peData.TRA_DATE || '';
    document.getElementById("edit-pe-atm").value = peData.ATM || '';
    document.getElementById("edit-pe-note").value = peData.NOTE || '';

    // فتح المودال
    const editOverlay = document.getElementById("editPeFormOverlay");
    editOverlay.classList.remove("hidden");

    // ربط استماع التعديل
    const editForm = document.getElementById("pe-edit-form");

    // إزالة مستمعين سابقين لمنع التكرار (استنساخ النموذج)
    const newEditForm = editForm.cloneNode(true);
    editForm.parentNode.replaceChild(newEditForm, editForm);

    newEditForm.addEventListener("submit", (event) => {
        event.preventDefault();

        const submitBtn = document.getElementById("pe-edit-submit-btn");
        const btnText = document.getElementById("pe-edit-btn-text");
        const spinner = document.getElementById("pe-edit-spinner");

        submitBtn.disabled = true;
        spinner.classList.remove("hidden");
        btnText.innerText = "جاري التحديث...";

        const formData = new FormData(newEditForm);

        fetch("update_public_exchange.php", {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(response => {
                // إزالة focus من أي عنصر داخل المودال
                if (document.activeElement) document.activeElement.blur();

                if (response.success) {
                    editOverlay.classList.add("hidden");
                    newEditForm.reset();
                    Swal.fire({
                        icon: 'success',
                        title: 'تم بنجاح',
                        text: response.success,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: response.error
                    });
                    submitBtn.disabled = false;
                    spinner.classList.add("hidden");
                    btnText.innerText = "تحديث";
                }
            })
            .catch(err => {
                if (document.activeElement) document.activeElement.blur();
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في الاتصال',
                    text: String(err)
                });
                submitBtn.disabled = false;
                spinner.classList.add("hidden");
                btnText.innerText = "تحديث";
            });
    });

    // إغلاق مودال التعديل
    document.getElementById("closeEditPeBtn").addEventListener("click", () => {
        editOverlay.classList.add("hidden");
        newEditForm.reset();
    });
}

/**
 * ========== مشاركة الحوالة العامة عبر واتساب ==========
 * Remittance Sharing via WhatsApp
 * يستخدم نفس قوالب النصوص من operations_on_exchanges.js
 */

/**
 * تنسيق الأرقام (يعيد استخدام peNumberFormat من lazy_loading)
 */
function peShareNumberFormat(value, maxDecimals = 2) {
    let num = Number(value);
    if (isNaN(num)) return value;
    let factor = Math.pow(10, maxDecimals);
    let truncated = Math.trunc(num * factor) / factor;
    let parts = truncated.toString().split(".");
    parts[0] = Number(parts[0]).toLocaleString('en-US');
    return parts.length > 1 ? parts[0] + "." + parts[1] : parts[0];
}

/**
 * فتح مودال اختيار طرف المشاركة (المرسل أو المستلم)
 * يحاكي سلوك openChooseClientModel من operations_on_exchanges.js
 * عند الإيداع بـ for_or_on='عليه'
 * @param {Object} peData - بيانات الحوالة العامة
 */
function openPeChooseClientModal(peData) {
    const modal = document.getElementById("peChooseClientModal");
    modal.classList.remove("hidden");

    // إزالة مستمعين سابقين بالاستنساخ
    const senderBtn = document.getElementById("peSenderClientBtn");
    const receiverBtn = document.getElementById("peReceiverClientBtn");
    const newSenderBtn = senderBtn.cloneNode(true);
    const newReceiverBtn = receiverBtn.cloneNode(true);
    senderBtn.parentNode.replaceChild(newSenderBtn, senderBtn);
    receiverBtn.parentNode.replaceChild(newReceiverBtn, receiverBtn);

    // مشاركة للمرسل — اعتبار for_or_on = 'عليه' (ارسال حوالة)
    // لا نغلق مودال الاختيار حتى يتمكن المستخدم من العودة والمشاركة للطرف الآخر
    newSenderBtn.addEventListener("click", () => {
        openPeShareModal(peData, 'عليه', peData.SENDER_PHONE);
    });

    // مشاركة للمستلم — اعتبار for_or_on = 'له' (استلام حوالة)
    newReceiverBtn.addEventListener("click", () => {
        openPeShareModal(peData, 'له', peData.RECEIVER_PHONE);
    });
}

/**
 * فتح مودال المشاركة وعرض نص الحوالة
 * نصوص المشاركة مأخوذة من تعريفات الحوالة في operations_on_exchanges.js
 * @param {Object} peData - بيانات الحوالة
 * @param {string} forOrOn - 'عليه' أو 'له'
 * @param {string} phone - رقم الهاتف للمشاركة
 */
function openPeShareModal(peData, forOrOn, phone) {
    // التعامل مع العملة
    let currency = peData.CURRENCY === 'new' ? 'ري قعيطي' :
        peData.CURRENCY === 'old' ? 'ري قديم' : 'ريال سعودي';

    let ammount = peShareNumberFormat(peData.AMMOUNT, 2);

    // بناء نص المشاركة — مطابق لقوالب الحوالة في operations_on_exchanges.js
    let shareText = `*|بن عبود للصرافة والتحويلات|*\n`;

    if (forOrOn === 'له') {
        // استلام حوالة (للمستلم)
        shareText += `(استلام حوالة)
لكم ${ammount} ${currency}
مقابل حوالة واردة عن طريق ${peData.ATM}
المرسل: ${peData.SENDER_NAME}
المستلم: ${peData.RECEIVER_NAME}
رقم الحوالة: ${peData.TRANSFER_NO}
المبلغ: ${ammount} ${currency}
التاريخ: ${peData.TRA_DATE}`;
    } else {
        // ارسال حوالة (للمرسل)
        let traFees = peShareNumberFormat(peData.TRA_FEES, 2);
        shareText += `(ارسال حوالة)
عليكم ${ammount} ${currency}
وخدمة تحويل : ${traFees} ${currency}
مقابل حوالة صادرة عن طريق ${peData.ATM}
المرسل: ${peData.SENDER_NAME}
المستلم: ${peData.RECEIVER_NAME}
رقم الحوالة: ${peData.TRANSFER_NO}
المبلغ: ${ammount} ${currency}
التاريخ: ${peData.TRA_DATE}`;
    }

    // إضافة الملاحظة إن وجدت
    let note = peData.NOTE ? `\nملاحظة: ${peData.NOTE}` : '';
    shareText += note;

    // عرض النص في المودال
    const shareTextArea = document.getElementById("peShareText");
    shareTextArea.value = shareText;

    document.getElementById("peShareModal").classList.remove("hidden");

    // ربط زر المشاركة
    const shareBtn = document.getElementById("peShareBtn");
    const newShareBtn = shareBtn.cloneNode(true);
    shareBtn.parentNode.replaceChild(newShareBtn, shareBtn);

    newShareBtn.addEventListener("click", () => {
        peShareExchange(shareText, phone || '');
    });
}

/**
 * مشاركة النص عبر واتساب
 * منسوخة من shareExchange في operations_on_exchanges.js
 * @param {string} text - نص المشاركة
 * @param {string} phone - رقم الهاتف
 */
function peShareExchange(text, phone) {
    if (!phone) {
        Swal.fire({
            icon: 'warning',
            title: 'تنبيه',
            text: 'لا يوجد رقم هاتف للمشاركة'
        });
        return;
    }

    phone = phone.replace(/[^0-9]/g, "");
    if (phone.length === 9) phone = "967" + phone;

    const encodedText = encodeURIComponent(text);

    const whatsappAppUrl = `whatsapp://send?phone=${phone}&text=${encodedText}`;
    const whatsappWebUrl = `https://wa.me/${phone}?text=${encodedText}`;

    const isMobile = /Mobi|Android/i.test(navigator.userAgent);

    if (isMobile) {
        window.location.href = whatsappAppUrl;
    } else {
        let opened = false;
        const iframe = document.createElement("iframe");
        iframe.style.display = "none";
        iframe.src = whatsappAppUrl;
        document.body.appendChild(iframe);

        const fallbackTimeout = setTimeout(() => {
            if (!opened) {
                window.open(whatsappWebUrl, "_blank");
            }
            if (iframe && iframe.parentNode) {
                iframe.parentNode.removeChild(iframe);
            }
        }, 1000);

        window.addEventListener("blur", () => {
            opened = true;
            clearTimeout(fallbackTimeout);
            if (iframe && iframe.parentNode) {
                iframe.parentNode.removeChild(iframe);
            }
        }, { once: true });
    }

    closePeModal("peShareModal");
}

/**
 * تفويض الأحداث (Event Delegation) للعمليات على الحوالات
 */
document.addEventListener('click', function (e) {
    if (e.target.classList.contains("pe-operation")) {
        const id = e.target.dataset.id;
        const operation = id.slice(0, 4);
        const peId = Number(id.replace(/\D/g, ""));

        if (operation === "tras") {
            // عملية الحذف
            openPeDeleteModal(peId);
        } else if (operation === "edit") {
            // عملية التعديل — البحث في البيانات المحملة
            let peData = null;
            publicExchangesData.forEach(row => {
                if (row.PE_ID == peId) {
                    peData = row;
                }
            });
            openPeEditModal(peData);
        } else if (operation === "shar") {
            // عملية المشاركة — البحث في البيانات المحملة
            let peData = null;
            publicExchangesData.forEach(row => {
                if (row.PE_ID == peId) {
                    peData = row;
                }
            });
            if (peData) {
                openPeChooseClientModal(peData);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'لم يتم العثور على بيانات الحوالة'
                });
            }
        }
    }
});

/**
 * إغلاق أي مودال بمعرفه
 */
function closePeModal(id) {
    document.getElementById(id).classList.add("hidden");
}

