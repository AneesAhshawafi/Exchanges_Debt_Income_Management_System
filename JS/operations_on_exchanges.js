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



function openDeleteModal(traNo) {
    document.getElementById("deleteModal").classList.remove("hidden");

    document.getElementById("confirmDeleteBtn").addEventListener("click", function () {
        const formData = new FormData();
        formData.append("TRA_ID", traNo);
        formData.append("client_id", currentClientId);

        fetch("delete_exchange.php", {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    alert(response.success);
                    closeModal("deleteModal");
                    location.reload();
                } else {
                    alert(response.error);
                }
            })
            .catch(err => {
                console.error("خطأ أثناء الحذف:", err);
            });
    });
}


// async function openShareModal(traNo) {

//     await fetch("share_exchange.php", {

//         method: "POST",

//         headers: {"Content-Type": "application/x-www-form-urlencoded"},

//         body: "tra_no=" + encodeURIComponent(traNo)

//     })

//             .then(res => res.json())

//             .then(data => {
//                 traData = data;

//                 if (traData.CURRENCY == 'new') {
//                     currency = ' ري قعيطي ';
//                 } else if (traData.CURRENCY == 'old') {
//                     currency = 'ري قديم';
//                 } else {
//                     currency = 'ريال سعودي';
//                 }
//                 ammount = numberFormat(traData.AMMOUNT, 2);
//                 textWithoutTotal = `*|بن عبود للصرافة والتحويلات|*
// `;
//                 if (traData.TYPE == 'حوالة') {
//                     if (traData.FOR_OR_ON == 'له') {
//                         textWithoutTotal += `(استلام حوالة)
// لكم ${ammount} ${currency}
// مقابل حوالة واردة عن طريق ${traData.ATM}
// المرسل: ${traData.SENDER_NAME}
// المستلم: ${traData.RECEIVER_NAME}
// رقم الحوالة: ${traData.TRANSFER_NO}
// المبلغ: ${ammount} ${currency}
// التاريخ: ${traData.TRA_DATE}`;

//                     } else {
//                         traFees = numberFormat(traData.TRA_FEES, 2);
//                         textWithoutTotal += `(ارسال حوالة)
// عليكم ${ammount} ${currency}
// وخدمة تحويل : ${traFees} ${currency}
// مقابل حوالة صادرة عن طريق ${traData.ATM}
// المرسل: ${traData.SENDER_NAME}
// المستلم: ${traData.RECEIVER_NAME}
// رقم الحوالة: ${traData.TRANSFER_NO}
// المبلغ: ${ammount} ${currency}
// التاريخ: ${traData.TRA_DATE}`;
//                     }

//                 } else if (traData.TYPE == 'إيداع') {
//                     if (traData.FOR_OR_ON == 'له') {
//                         textWithoutTotal += `(عملية إيداع لحسابك)
// أودع ${traData.SENDER_NAME} لحسابكم مبلغ ${ammount} ${currency}
// عن طريق ${traData.ATM}
// المودع: ${traData.SENDER_NAME}
// المستلم: ${traData.RECEIVER_NAME}
// المبلغ: ${ammount} ${currency}
// التاريخ: ${traData.TRA_DATE}
// رقم الإيداع : ${traData.TRANSFER_NO}`;
//                     } else {
//                         textWithoutTotal += `(سند قيد بسيط)
// تم تحويل مبلغ ${ammount} ${currency} من حسابك إلى حساب ${traData.RECEIVER_NAME}
// عن طريق ${traData.ATM}
// التاريخ: ${traData.TRA_DATE}
// رقم السند: ${traData.TRANSFER_NO}`;
//                     }
//                 } else {
//                     if (traData.FROM_CURRENCY == 'new') {
//                         from_currency = 'ريال يمني قعيطي';
//                     } else if (traData.FROM_CURRENCY == 'old') {
//                         from_currency = 'ريال يمني قديم';
//                     } else {
//                         from_currency = 'ريال سعودي';
//                     }
//                     if (traData.TO_CURRENCY == 'new') {
//                         to_currency = 'ريال يمني قعيطي';
//                     } else if (traData.TO_CURRENCY == 'old') {
//                         to_currency = 'ريال يمني قديم';
//                     } else {
//                         to_currency = 'ريال سعودي';
//                     }
//                     transfered_ammount = numberFormat(traData.TRANSFERED_AMMOUNT, 2);
//                     priceTransfer = numberFormat(traData.PRICE, 5);
//                     textWithoutTotal += `(شراء عملة)
// أضيف إلى حسابكم ${transfered_ammount} ${to_currency}
// مقابل خصم ${ammount} ${from_currency} من حسابكم
// من سعر ${priceTransfer} للريال الواحد
// رقم التحويل: ${traData.TRANSFER_NO}
// التاريخ: ${traData.TRA_DATE}`
//                 }
//                 note = '';
//                 if (traData.NOTE) {
//                     note += `
// ملاحظة: ${traData.NOTE}`;
//                 }

//                 const shareText = document.getElementById("shareText");
//                 shareText.value = textWithoutTotal + getTextOfTotalAmmounts(traData) + note;
//                 shareText.style.direction = 'rtl';

//                 document.getElementById("shareModal").classList.remove("hidden");
//                 const phone = await getClientPhone(currentClientId);
//                 document.getElementById("shareBtn").addEventListener("click", () => {


//                     shareExchange(textWithoutTotal + note,phone);
//                 });
//                 document.getElementById('shareWithTotalBtn').addEventListener("click", () => {
//                     shareExchange(textWithoutTotal + getTextOfTotalAmmounts(traData) + note,phone);

//                 });
//             });

// }


async function openShareModal(traNo) {
    try {
        const res = await fetch("share_exchange.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "tra_no=" + encodeURIComponent(traNo)
        });

        const traData = await res.json();

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
        const phone = await getClientPhone(currentClientId);

        if (!phone) {
            showMessage("لا يوجد رقم هاتف للعميل");
            return;
        }

        // إضافة المستمعين بعد التأكد من الرقم
        document.getElementById("shareBtn").onclick = () => {
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
// function shareExchange(text) {
//     // فتح واجهة المشاركة إن أحببت
//     if (navigator.share) {
//         navigator.share({
//             title: "بيانات الحوالة",
//             text: text
//         }).catch(err => {
//             console.error("فشل المشاركة:", err);
//         });
//     }
//     // نسخ النص للحافظة
//     navigator.clipboard.writeText(text).then(() => {
//         //        alert("تم نسخ بيانات الحوالة! يمكنك الآن لصقها في أي تطبيق.");
//     }).catch(err => {
//         console.error("خطأ في النسخ:", err);
//     });


//     document.getElementById("shareModal").classList.add("hidden");



// }


// function getClientPhone(clientId, callback) {
//     fetch(`get_client_phone.php?client_id=${encodeURIComponent(clientId)}`)
//         .then(response => response.json())
//         .then(data => {
//             if (data.phone) {
//                 callback(data.phone);
//             } else {
//                 alert("لم يتم العثور على رقم هاتف لهذا العميل");
//             }
//         })
//         .catch(error => {
//             console.error("خطأ في جلب رقم العميل:", error);
//         });
// }

async function getClientPhone(clientId) {
    const response = await fetch(
        `get_client_phone.php?client_id=${encodeURIComponent(clientId)}`
    );
    const data = await response.json();
    return data.phone;
}

// function shareExchange(text, phone) {

//     // تنظيف الرقم (أرقام فقط)
//     phone = phone.replace(/[^0-9]/g, "");

//     // لو الرقم 9 أرقام (يمني محلي) نضيف مفتاح اليمن
//     if (phone.length === 9) {
//         phone = "967" + phone;
//     }

//     // ترميز النص عشان الروابط
//     const encodedText = encodeURIComponent(text);

//     // رابط واتساب
//     const whatsappUrl = `https://wa.me/${phone}?text=${encodedText}`;

//     // فتح واتساب مباشرة على المحادثة
//     window.open(whatsappUrl, "_blank");

//     // إغلاق المودال
//     document.getElementById("shareModal").classList.add("hidden");
// }
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

    //    const transferOption=document.getElementById('transfer-option');
    // if (editOperSelectTypeInput.value == "حوالة") {
    //     editSenderInputGroup.classList.remove('hidden');
    //     editReceiverInputGroup.classList.remove('hidden');
    //     editCurrency.classList.remove('hidden');
    //     editForOrOnInptGrp.classList.remove('hidden');
    //     editStatus.classList.remove('hidden');
    //     Array.from(editTransferOperDiv).forEach(e => {
    //         e.classList.add('hidden');
    //     });
    //     if (editForOrOn.value == 'عليه') {
    //         Array.from(editFeesInp).forEach(e => {
    //             e.classList.remove('hidden');
    //             e.required = true;
    //         });
    //     } else {
    //         Array.from(editFeesInp).forEach(e => {
    //             e.classList.add('hidden');
    //             e.required = false;
    //         });
    //     }
    //     labelEditSender.textContent = 'اسم المرسل';
    //     editSenderNameInput.placeholder = 'اسم المرسل';
    //     labelEditTransferNO.textContent = 'رقم الحوالة';
    //     editTransferNoInput.placeholder = 'رقم الحوالة';
    //     editSenderNameInput.required = true;
    //     editReceiverInput.required = true;
    //     editSelectFrom.required = false;
    //     editSelectTo.required = false;
    //     editPrice.required = false;
    //     //        editAmmount.readOnly = false;
    // } else if (editOperSelectTypeInput.value == "إيداع") {
    //     editSenderInputGroup.classList.remove('hidden');
    //     editReceiverInputGroup.classList.remove('hidden');
    //     editCurrency.classList.remove('hidden');
    //     editForOrOnInptGrp.classList.remove('hidden');
    //     editStatus.classList.add('hidden');
    //     Array.from(editTransferOperDiv).forEach(e => {
    //         e.classList.add('hidden');
    //     });
    //     Array.from(editFeesInp).forEach(e => {
    //         e.classList.add('hidden');
    //         e.required = false;
    //     });
    //     labelEditSender.textContent = 'اسم المودع';
    //     editSenderNameInput.placeholder = 'اسم المودع';
    //     editTransferNoInput.placeholder = 'رقم السند';
    //     labelEditTransferNO.textContent = 'رقم السند';
    //     editSenderNameInput.required = true;
    //     editReceiverInput.required = true;
    //     editSelectFrom.required = false;
    //     editSelectTo.required = false;
    //     editPrice.required = false;
    //     //        editAmmount.readOnly = false;

    // } else {
    //     editTransferNoInput.placeholder = 'رقم التحويل';
    //     labelEditTransferNO.textContent = 'رقم التحويل';
    //     editSenderInputGroup.classList.add('hidden');
    //     editReceiverInputGroup.classList.add('hidden');
    //     Array.from(editTransferOperDiv).forEach(e => {
    //         e.classList.remove('hidden');
    //     });
    //     editStatus.classList.add('hidden');
    //     editCurrency.classList.add('hidden');
    //     editForOrOnInptGrp.classList.add('hidden');
    //     Array.from(editFeesInp).forEach(e => {
    //         e.classList.add('hidden');
    //         e.required = false;
    //     });
    //     editSenderNameInput.required = false;
    //     editReceiverInput.required = false;
    //     editSelectFrom.required = true;

    //     editSelectTo.required = true;
    //     //        editAmmount.readOnly = true;
    //     editPrice.required = true;
    // }
    editCheckState();


    const closeEditExchangeBtn = document.getElementById("closeEditExchangeListBtn");

    //    const editExchangeForm = document.getElementById("edit-exchange-form");

    const editExchangeForm = document.getElementById("edit-exchange-form");
    editExchangeForm.addEventListener("submit", (event) => {
        event.preventDefault();
        const formData = new FormData(editExchangeForm);
        formData.append("client_id", currentClientId);
        fetch("update_exchange.php", {
            method: "POST",
            body: formData
        }).then(res => res.json())
            .then(response => {
                if (response.success) {
                    alert(response.success);
                    editExchangeForm.reset();
                    location.reload();
                } else {
                    alert(response.error);
                }
            }).catch(er => {
                alert(er);
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



document.addEventListener('click', function (e) {
    if (e.target.classList.contains("operation")) {
        const id = e.target.dataset.id;
        const operaion = id.slice(0, 4);
        const traNo = Number(id.replace(/\D/g, ""));

        if (operaion == "tras") {

            openDeleteModal(traNo);
        } else if (operaion == "edit") {
            traData = null;
            exchangesListData.forEach(row => {
                if (row.TRA_ID == traNo) {

                    traData = row;
                }
            });

            openEditModal(traData);
        } else {

            openShareModal(traNo);
        }

    }
});
//document.querySelectorAll(".operation").forEach(icon => {
//    icon.addEventListener("click", function () {
//
//    });
//});