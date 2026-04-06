let currentClientId = localStorage.getItem("currentClientId");



let deleteTraNo = null;
function numberFormat(value, maxDecimals = 2) {
    let num = Number(value);
    if (isNaN(num))
        return value;

    // قص الأرقام بدل التقريب
    let factor = Math.pow(10, maxDecimals);
    let truncated = Math.trunc(num * factor) / factor;

    // نفصل العدد إلى جزء صحيح وجزء عشري
    let parts = truncated.toString().split(".");

    // تنسيق الجزء الصحيح مع فواصل الآلاف
    parts[0] = Number(parts[0]).toLocaleString('en-US');

    // إذا فيه كسور، نرجع نضيفها بدون أصفار وهمية
    return parts.length > 1 ? parts[0] + "." + parts[1] : parts[0];
}



function openDeleteModal(traNo, type, forOrOn, transferNo) {
    document.getElementById("deleteModal").classList.remove("hidden");

    document.getElementById("confirmDeleteBtn").addEventListener("click", function () {
        const formData = new FormData();
        formData.append("TRA_ID", traNo);
        formData.append("client_id", currentClientId);
        formData.append('type', type);
        formData.append('forOrOn', forOrOn);
        formData.append('transferNo', transferNo);

        fetch("delete_exchange.php", {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    closeModal("deleteModal");
                    Swal.fire({ icon: 'success', title: 'تم بنجاح', text: response.success, timer: 1500, showConfirmButton: false }).then(() => { location.reload(); });
                } else {
                    Swal.fire({ icon: 'error', title: 'خطأ', text: response.error });
                }
            })
            .catch(err => {
                console.error("خطأ أثناء الحذف:", err);
            });
    });
}


async function getTraData(traNo) {
    let traData;
    const res = await fetch("share_exchange.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "tra_no=" + encodeURIComponent(traNo)
    });

    traData = await res.json();
    return traData;
}
async function openShareModal(traData) {

    try {

        // التعامل مع العملة
        let currency = traData.CURRENCY === 'new' ? 'ري قعيطي' :
            traData.CURRENCY === 'old' ? 'ري قديم' : 'ريال سعودي';

        let ammount = numberFormat(traData.AMMOUNT, 2);

        let textWithoutTotal = `*|بن عبود للصرافة والتحويلات|*\n`;

        if (traData.TYPE == 'حوالة') {
            if (traData.FOR_OR_ON == 'له') {
                textWithoutTotal += `(استلام حوالة)
لكم ${ammount} ${currency}
مقابل حوالة واردة عن طريق ${traData.ATM}
المرسل: ${traData.SENDER_NAME}
المستلم: ${traData.RECEIVER_NAME}
رقم الحوالة: ${traData.TRANSFER_NO}
المبلغ: ${ammount} ${currency}
التاريخ: ${traData.TRA_DATE}`;

            } else {
                traFees = numberFormat(traData.TRA_FEES, 2);
                textWithoutTotal += `(ارسال حوالة)
عليكم ${ammount} ${currency}
وخدمة تحويل : ${traFees} ${currency}
مقابل حوالة صادرة عن طريق ${traData.ATM}
المرسل: ${traData.SENDER_NAME}
المستلم: ${traData.RECEIVER_NAME}
رقم الحوالة: ${traData.TRANSFER_NO}
المبلغ: ${ammount} ${currency}
التاريخ: ${traData.TRA_DATE}`;
            }

        } else if (traData.TYPE == 'إيداع') {
            if (traData.FOR_OR_ON == 'له') {
                textWithoutTotal += `(عملية إيداع لحسابك)
أودع ${traData.SENDER_NAME} لحسابكم مبلغ ${ammount} ${currency}
عن طريق ${traData.ATM}
المودع: ${traData.SENDER_NAME}`;
                if (traData.SENDER_PHONE) textWithoutTotal += `\nرقم المودع: ${traData.SENDER_PHONE}`;
                textWithoutTotal += `\nالمستلم: ${traData.RECEIVER_NAME}`;
                if (traData.RECEIVER_PHONE) textWithoutTotal += `\nرقم المستلم: ${traData.RECEIVER_PHONE}`;
                textWithoutTotal += `\nالمبلغ: ${ammount} ${currency}
التاريخ: ${traData.TRA_DATE}
رقم الإيداع : ${traData.TRANSFER_NO}`;
            } else {
                textWithoutTotal += `(سند قيد بسيط)
تم تحويل مبلغ ${ammount} ${currency} من حسابك إلى حساب ${traData.RECEIVER_NAME}`;
                if (traData.RECEIVER_PHONE) textWithoutTotal += `\nرقم المستلم: ${traData.RECEIVER_PHONE}`;
                textWithoutTotal += `\nعن طريق ${traData.ATM}
التاريخ: ${traData.TRA_DATE}
رقم السند: ${traData.TRANSFER_NO}`;
            }
        } else if (traData.TYPE == 'سحب') {
            textWithoutTotal += `(سند قيد)
تم سحب مبلغ ${ammount} ${currency} من رصيدكم بتاريخ ${traData.TRA_DATE} عبر ${traData.ATM}
رقم السند: ${traData.TRANSFER_NO}`;
        } else {
            if (traData.FROM_CURRENCY == 'new') {
                from_currency = 'ريال يمني قعيطي';
            } else if (traData.FROM_CURRENCY == 'old') {
                from_currency = 'ريال يمني قديم';
            } else {
                from_currency = 'ريال سعودي';
            }
            if (traData.TO_CURRENCY == 'new') {
                to_currency = 'ريال يمني قعيطي';
            } else if (traData.TO_CURRENCY == 'old') {
                to_currency = 'ريال يمني قديم';
            } else {
                to_currency = 'ريال سعودي';
            }
            transfered_ammount = numberFormat(traData.TRANSFERED_AMMOUNT, 2);
            priceTransfer = numberFormat(traData.PRICE, 5);
            textWithoutTotal += `(شراء عملة)
أضيف إلى حسابكم ${transfered_ammount} ${to_currency}
مقابل خصم ${ammount} ${from_currency} من حسابكم
من سعر ${priceTransfer} للريال الواحد
رقم التحويل: ${traData.TRANSFER_NO}
التاريخ: ${traData.TRA_DATE}`
        }

        let note = traData.NOTE ? `\nملاحظة: ${traData.NOTE}` : '';

        const shareText = document.getElementById("shareText");
        shareText.value = textWithoutTotal + getTextOfTotalAmmounts(traData) + note;
        shareText.style.direction = 'rtl';

        document.getElementById("shareModal").classList.remove("hidden");

        // جلب رقم العميل بشكل صحيح
        const phone = await getClientPhone(traData.CLIENT_ID);

        if (!phone) {
            showMessage("لا يوجد رقم هاتف للعميل");
            return;
        }

        // إضافة المستمعين بعد التأكد من الرقم
        document.getElementById("shareBtn").onclick = async () => {
            shareExchange(textWithoutTotal + note, phone);
        };
        document.getElementById("shareWithTotalBtn").onclick = () => {
            shareExchange(textWithoutTotal + getTextOfTotalAmmounts(traData) + note, phone);
        };
    } catch (err) {
        console.error("خطأ أثناء جلب بيانات الحوالة:", err);
        showMessage("حدث خطأ أثناء جلب بيانات الحوالة");
    }
}



function getTextOfTotalAmmounts(traData) {
    textTotal = `
`;
    if (traData.TYPE != 'تحويل') {

        if (traData.CURRENCY == 'new') {
            if (traData.sum_ammount_new > 0) {
                textTotal += `الرصيد لكم `;
            } else {
                textTotal += `الرصيد عليكم `;
            }
            textTotal += ` ${numberFormat(Math.abs(traData.sum_ammount_new), 2)} ري قعيطي`;

        } else if (traData.CURRENCY == 'old') {
            if (traData.sum_ammount_old > 0) {
                textTotal += `الرصيد لكم `;
            } else {
                textTotal += `الرصيد عليكم `;
            }
            textTotal += ` ${numberFormat(Math.abs(traData.sum_ammount_old), 2)} ري قديم`;
        } else {
            if (traData.sum_ammount_sa > 0) {
                textTotal += `الرصيد لكم `;
            } else {
                textTotal += `الرصيد عليكم `;
            }
            textTotal += ` ${numberFormat(Math.abs(traData.sum_ammount_sa), 2)} ريال سعودي`;
        }
    } else {
        if (traData.FROM_CURRENCY == 'new') {
            if (traData.sum_ammount_new > 0) {
                textTotal += `الرصيد لكم `;
            } else {
                textTotal += `الرصيد عليكم `;
            }
            textTotal += ` ${numberFormat(Math.abs(traData.sum_ammount_new), 2)} ري قعيطي`;

        } else if (traData.FROM_CURRENCY == 'old') {
            if (traData.sum_ammount_old > 0) {
                textTotal += `الرصيد لكم `;
            } else {
                textTotal += `الرصيد عليكم `;
            }
            textTotal += ` ${numberFormat(Math.abs(traData.sum_ammount_old), 2)} ري قديم`;
        } else {
            if (traData.sum_ammount_sa > 0) {
                textTotal += `الرصيد لكم `;
            } else {
                textTotal += `الرصيد عليكم `;
            }
            textTotal += ` ${numberFormat(Math.abs(traData.sum_ammount_sa), 2)} ريال سعودي`;
        }
        textTotal += `
`;
        if (traData.TO_CURRENCY == 'new') {
            if (traData.sum_ammount_new > 0) {
                textTotal += `الرصيد لكم `;
            } else {
                textTotal += `الرصيد عليكم `;
            }
            textTotal += ` ${numberFormat(Math.abs(traData.sum_ammount_new), 2)} ري قعيطي`;

        } else if (traData.TO_CURRENCY == 'old') {
            if (traData.sum_ammount_old > 0) {
                textTotal += `الرصيد لكم `;
            } else {
                textTotal += `الرصيد عليكم `;
            }
            textTotal += ` ${numberFormat(Math.abs(traData.sum_ammount_old), 2)} ري قديم`;
        } else {
            if (traData.sum_ammount_sa > 0) {
                textTotal += `الرصيد لكم `;
            } else {
                textTotal += `الرصيد عليكم `;
            }
            textTotal += ` ${numberFormat(Math.abs(traData.sum_ammount_sa), 2)} ريال سعودي`;
        }
    }
    return textTotal;
}


async function getClientPhone(clientId) {
    const response = await fetch(
        `get_client_phone.php?client_id=${encodeURIComponent(clientId)}`
    );
    const data = await response.json();
    return data.phone;
}

function shareExchange(text, phone) {
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

    document.getElementById("shareModal").classList.add("hidden");
}


// ===== دالة عامة لإرسال فورم التعديل =====
function submitEditForm(form, modalId) {
    const formData = new FormData(form);
    formData.append("client_id", currentClientId);
    formData.append("client_phone", currentClientPhone);
    formData.append("client_name", currentClientName);

    fetch("update_exchange.php", {
        method: "POST",
        body: formData
    }).then(res => res.json())
        .then(response => {
            if (document.activeElement) document.activeElement.blur();
            if (response.success) {
                closeModal(modalId);
                form.reset();
                Swal.fire({ icon: 'success', title: 'تم بنجاح', text: response.success, timer: 1500, showConfirmButton: false }).then(() => { location.reload(); });
            } else {
                Swal.fire({ icon: 'error', title: 'خطأ', text: response.error });
            }
        }).catch(er => {
            if (document.activeElement) document.activeElement.blur();
            Swal.fire({ icon: 'error', title: 'خطأ في الاتصال', text: String(er) });
        });
}


// ===== فتح فورم تعديل حوالة =====
function openEditHawalaModal(traData) {
    if (!traData) { console.error("بيانات التعديل غير موجودة"); return; }

    // 1. استنساخ الفورم أولاً لإزالة المستمعات القديمة
    const editForm = document.getElementById("edit-hawala-form");
    const newForm = editForm.cloneNode(true);
    editForm.parentNode.replaceChild(newForm, editForm);

    // 2. تعبئة البيانات بعد الاستنساخ
    document.getElementById("edit-hawala-id").value = traData.TRA_ID;
    document.getElementById("edit-hawala-currency").value = traData.CURRENCY;
    document.getElementById("edit-hawala-for-or-on").value = traData.FOR_OR_ON;
    document.getElementById("edit-hawala-status").value = traData.STATUS;
    document.getElementById("edit-hawala-transfer-no").value = traData.TRANSFER_NO;
    document.getElementById("edit-hawala-ammount").value = traData.AMMOUNT;
    document.getElementById("edit-hawala-date").value = traData.TRA_DATE;
    document.getElementById("edit-hawala-atm").value = traData.ATM;
    document.getElementById("edit-hawala-note").value = traData.NOTE;
    document.getElementById("edit-hawala-fees").value = traData.TRA_FEES;

    // تعبئة الاسم حسب for_or_on
    if (traData.FOR_OR_ON === 'له') {
        document.getElementById("edit-hawala-sender").value = traData.SENDER_NAME;
        document.getElementById("edit-hawala-receiver").value = '';
    } else {
        document.getElementById("edit-hawala-receiver").value = traData.RECEIVER_NAME;
        document.getElementById("edit-hawala-sender").value = '';
    }

    // 3. إعادة ربط toggle بعد الاستنساخ
    setupHawalaToggle('edit-hawala-for-or-on', 'edit-hawala-sender-row', 'edit-hawala-receiver-row', 'edit-hawala-fees-row');

    // تشغيل التبديل
    const forOrOn = document.getElementById("edit-hawala-for-or-on");
    if (forOrOn) forOrOn.dispatchEvent(new Event('change'));

    // 4. ربط الإرسال
    newForm.addEventListener("submit", (event) => {
        event.preventDefault();
        submitEditForm(newForm, 'editHawalaModal');
    });

    document.getElementById("editHawalaModal").classList.remove("hidden");
}


// ===== فتح فورم تعديل إيداع =====
function openEditDepositModal(traData) {
    if (!traData) { console.error("بيانات التعديل غير موجودة"); return; }

    // 1. استنساخ الفورم أولاً
    const editForm = document.getElementById("edit-deposit-form");
    const newForm = editForm.cloneNode(true);
    editForm.parentNode.replaceChild(newForm, editForm);

    // 2. تعبئة البيانات بعد الاستنساخ
    document.getElementById("edit-deposit-id").value = traData.TRA_ID;
    document.getElementById("edit-deposit-currency").value = traData.CURRENCY;
    document.getElementById("edit-deposit-for-or-on").value = traData.FOR_OR_ON;
    document.getElementById("edit-deposit-transfer-no").value = traData.TRANSFER_NO;
    document.getElementById("edit-deposit-ammount").value = traData.AMMOUNT;
    document.getElementById("edit-deposit-date").value = traData.TRA_DATE;
    document.getElementById("edit-deposit-atm").value = traData.ATM;
    document.getElementById("edit-deposit-note").value = traData.NOTE;

    // تعبئة الاسم والرقم حسب for_or_on
    if (traData.FOR_OR_ON === 'له') {
        document.getElementById("edit-deposit-sender").value = traData.SENDER_NAME;
        document.getElementById("edit-deposit-sender-phone").value = traData.SENDER_PHONE;
        document.getElementById("edit-deposit-receiver").value = '';
        document.getElementById("edit-deposit-receiver-phone").value = '';
    } else {
        document.getElementById("edit-deposit-receiver").value = traData.RECEIVER_NAME;
        document.getElementById("edit-deposit-receiver-phone").value = traData.RECEIVER_PHONE;
        document.getElementById("edit-deposit-sender").value = '';
        document.getElementById("edit-deposit-sender-phone").value = '';
    }

    // 3. إعادة ربط toggle بعد الاستنساخ
    setupDepositToggle('edit-deposit-for-or-on', 'edit-deposit-sender-row', 'edit-deposit-receiver-row');

    const forOrOn = document.getElementById("edit-deposit-for-or-on");
    if (forOrOn) forOrOn.dispatchEvent(new Event('change'));

    // 4. ربط الإرسال
    newForm.addEventListener("submit", (event) => {
        event.preventDefault();
        submitEditForm(newForm, 'editDepositModal');
    });

    document.getElementById("editDepositModal").classList.remove("hidden");
}


// ===== فتح فورم تعديل تحويل =====
function openEditTransferModal(traData) {
    if (!traData) { console.error("بيانات التعديل غير موجودة"); return; }

    // 1. استنساخ الفورم أولاً
    const editForm = document.getElementById("edit-transfer-form");
    const newForm = editForm.cloneNode(true);
    editForm.parentNode.replaceChild(newForm, editForm);

    // 2. تعبئة البيانات بعد الاستنساخ
    document.getElementById("edit-transfer-id").value = traData.TRA_ID;
    document.getElementById("edit-transfer-select-from").value = traData.FROM_CURRENCY;
    document.getElementById("edit-transfer-select-to").value = traData.TO_CURRENCY;
    document.getElementById("edit-transfer-ammount").value = traData.AMMOUNT;
    document.getElementById("edit-transfer-price").value = traData.PRICE;
    document.getElementById("edit-transfer-transfer-no").value = traData.TRANSFER_NO;
    document.getElementById("edit-transfer-date").value = traData.TRA_DATE;
    document.getElementById("edit-transfer-atm").value = traData.ATM;
    document.getElementById("edit-transfer-note").value = traData.NOTE;

    // 3. ربط الإرسال
    newForm.addEventListener("submit", (event) => {
        event.preventDefault();
        submitEditForm(newForm, 'editTransferModal');
    });

    document.getElementById("editTransferModal").classList.remove("hidden");
}


function closeModal(id) {
    document.getElementById(id).classList.add("hidden");
}



function openChooseClientModel() {
    document.getElementById("chooseClientModal").classList.remove("hidden");
}
document.addEventListener('click', async function (e) {
    if (e.target.classList.contains("operation")) {
        const id = e.target.dataset.id;
        const operaion = id.slice(0, 4);
        let traNo = Number(id.replace(/\D/g, ""));

        if (operaion == "tras") {
            const type = e.target.dataset.type;
            const forOrOn = e.target.dataset.forOrOn;
            const transferNo = e.target.dataset.transferNo;
            openDeleteModal(traNo, type, forOrOn, transferNo);
        } else if (operaion == "edit") {
            traData = null;
            exchangesListData.forEach(row => {
                if (row.TRA_ID == traNo) {
                    traData = row;
                }
            });

            // توجيه التعديل حسب نوع العملية
            if (traData) {
                if (traData.TYPE === 'سحب') {
                    openEditSanadModal(traData);
                } else if (traData.TYPE === 'حوالة') {
                    openEditHawalaModal(traData);
                } else if (traData.TYPE === 'إيداع') {
                    openEditDepositModal(traData);
                } else if (traData.TYPE === 'تحويل') {
                    openEditTransferModal(traData);
                }
            }
        } else {
            traData = await getTraData(traNo);
            if (traData.TYPE == 'إيداع' && traData.FOR_OR_ON == 'عليه') {
                openChooseClientModel();
                document.getElementById("senderClientBtn").addEventListener("click", async () => {
                    await openShareModal(traData);
                });
                document.getElementById("receiverClientBtn").addEventListener("click", async () => {
                    traNo += 1;
                    traData = await getTraData(traNo);
                    await openShareModal(traData);
                });
            } else {
                await openShareModal(traData);
            }

        }

    }
});