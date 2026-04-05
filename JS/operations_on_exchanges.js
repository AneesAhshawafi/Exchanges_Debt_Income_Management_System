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
المودع: ${traData.SENDER_NAME}
المستلم: ${traData.RECEIVER_NAME}
المبلغ: ${ammount} ${currency}
التاريخ: ${traData.TRA_DATE}
رقم الإيداع : ${traData.TRANSFER_NO}`;
            } else {
                textWithoutTotal += `(سند قيد بسيط)
تم تحويل مبلغ ${ammount} ${currency} من حسابك إلى حساب ${traData.RECEIVER_NAME}
عن طريق ${traData.ATM}
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
        // const phone = currentClientPhone;

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
            // return [textWithoutTotal + getTextOfTotalAmmounts(traData) + note, phone];
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






function openEditModal(traData) {


    if (!traData) {

        console.error("بيانات التعديل غير موجودة");

        return;

    }

    document.getElementById("edit-exchange-id").value = traData.TRA_ID;


    document.getElementById("edit-type").value = traData.TYPE;



    document.getElementById("edit-currency").value = traData.CURRENCY;



    document.getElementById("edit-for-or-on").value = traData.FOR_OR_ON;



    document.getElementById("edit-sender").value = traData.SENDER_NAME;

    document.getElementById("reciver").value = traData.RECEIVER_NAME;
    document.getElementById("edit-sender-phone").value = traData.SENDER_PHONE;
    document.getElementById("edit-receiver-phone").value = traData.RECEIVER_PHONE;

    //    const editTransferInputGroup = document.getElementById('edit-transfer-no-input-group')
    const editStatusIn = document.getElementById('edit-status');
    const transfer_no = document.getElementById("edit-transfer-no");
    transfer_no.value = traData.TRANSFER_NO;
    editStatusIn.value = traData.STATUS;
    if (traData.TYPE == 'حوالة') {
        editStatus.classList.remove('hidden');
    }
    editSelectFrom.value = traData.FROM_CURRENCY;
    editSelectFrom.addEventListener("mousedown", function (e) {
        e.preventDefault();
    });

    editSelectTo.value = traData.TO_CURRENCY;
    editSelectTo.addEventListener("mousedown", function (e) {
        e.preventDefault();
    });
    document.getElementById('edit-price').value = traData.PRICE;


    document.getElementById("edit-ammount").value = traData.AMMOUNT;



    document.getElementById("edit-fees").value = traData.TRA_FEES;


    //    document.getElementById("edit-date").value = traData.TRA_DATE.replace(' ', 'T').slice(0, 16);


    document.getElementById("edit-atm").value = traData.ATM;
    document.getElementById("edit-note").value = traData.NOTE;
    const editExchangeModal = document.getElementById("editExchangeModal");
    editExchangeModal.classList.remove("hidden");
    editCheckState();
    const closeEditExchangeBtn = document.getElementById("closeEditExchangeListBtn");
    const editExchangeForm = document.getElementById("edit-exchange-form");
    editExchangeForm.addEventListener("submit", (event) => {
        event.preventDefault();
        const formData = new FormData(editExchangeForm);
        formData.append("client_id", currentClientId);
        formData.append("client_phone", currentClientPhone);
        formData.append("client_name", currentClientName);
        fetch("update_exchange.php", {
            method: "POST",
            body: formData
        }).then(res => res.json())
            .then(response => {
                // إزالة الـ focus من أي عنصر داخل المودال قبل فتح SweetAlert
                // لتفادي تحذير aria-hidden الذي يحدث عندما يكون زر داخل المودال محدداً
                if (document.activeElement) document.activeElement.blur();
                if (response.success) {
                    closeModal('editExchangeModal');
                    editExchangeForm.reset();
                    Swal.fire({ icon: 'success', title: 'تم بنجاح', text: response.success, timer: 1500, showConfirmButton: false }).then(() => { location.reload(); });
                } else {
                    Swal.fire({ icon: 'error', title: 'خطأ', text: response.error });
                }
            }).catch(er => {
                if (document.activeElement) document.activeElement.blur();
                Swal.fire({ icon: 'error', title: 'خطأ في الاتصال', text: String(er) });
            })



    });

    closeEditExchangeBtn.addEventListener("click", () => {
        closeModal('editExchangeModal');
        editExchangeForm.reset();
    });

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
            if (traData && traData.TYPE === 'سحب') {
                openEditSanadModal(traData);
            } else {
                openEditModal(traData);
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
                // traData = await getTraData(traNo);
                await openShareModal(traData);
            }

        }

    }
});