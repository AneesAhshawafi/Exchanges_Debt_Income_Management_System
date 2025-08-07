///* 
// * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
// * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
// */
let currentClientId = localStorage.getItem("currentClientId");

let currentExchangesListData = null;

let deleteTraNo = null;

function numberFormat(value, decimals = 2) {

    let num = Number(value);

    if (isNaN(num))
        return value;

    return num.toLocaleString('en-US', {

        minimumFractionDigits: decimals,

        maximumFractionDigits: decimals

    });

}


function openDeleteModal(traNo, exchangesList) {
    const postData = {
        TRA_ID: traNo,
        exchanges_List: exchangesList
    };

    document.getElementById("deleteModal").classList.remove("hidden");

    document.getElementById("confirmDeleteBtn").addEventListener("click", function () {
        const formData = new FormData();
        formData.append("TRA_ID", postData.TRA_ID);
        formData.append("exchanges_List", JSON.stringify(postData.exchanges_List));

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


function openShareModal(traNo) {

    fetch("share_exchange.php", {

        method: "POST",

        headers: {"Content-Type": "application/x-www-form-urlencoded"},

        body: "tra_no=" + encodeURIComponent(traNo)

    })

            .then(res => res.json())

            .then(data => {
                traData = data;

                if (traData.CURRENCY == 'new') {
                    currency = ' ري قعيطي ';
                } else if (traData.CURRENCY == 'old') {
                    currency = 'ري قديم';
                } else {
                    currency = 'ريال سعودي';
                }
                ammount = numberFormat(traData.AMMOUNT, 2);
                textWithoutTotal = `*|بن عبود للصرافة والتحويلات|*
`;
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
                        textWithoutTotal += `(ارسال حوالة)
عليكم ${ammount} ${currency}
مقابل حوالة صادرة عن طريق ${traData.ATM}
المرسل: ${traData.SENDER_NAME}
المستلم: ${traData.RECEIVER_NAME}
رقم الحوالة: ${traData.TRANSFER_NO}
المبلغ: ${ammount} ${currency}
التاريخ: ${traData.TRA_DATE}`;
                    }

                } else {
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
                }
                note = '';
                if (traData.NOTE) {
                    note += `
ملاحظة: ${traData.NOTE}`;
                }

                const shareText = document.getElementById("shareText");
                shareText.value = textWithoutTotal + getTextOfTotalAmmounts(traData) + note;
                shareText.style.direction = 'rtl';

                document.getElementById("shareModal").classList.remove("hidden");
                document.getElementById("shareBtn").addEventListener("click", () => {
                    shareExchange(textWithoutTotal + note);
                });
                document.getElementById('shareWithTotalBtn').addEventListener("click", () => {

                    shareExchange(textWithoutTotal + getTextOfTotalAmmounts(traData) + note);
                });
            });

}
function getTextOfTotalAmmounts(traData) {
    textTotal = `
`;

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
    return textTotal;
}
function shareExchange(text) {
    // فتح واجهة المشاركة إن أحببت
    if (navigator.share) {
        navigator.share({
            title: "بيانات الحوالة",
            text: text
        }).catch(err => {
            console.error("فشل المشاركة:", err);
        });
    }
    // نسخ النص للحافظة
    navigator.clipboard.writeText(text).then(() => {
        //        alert("تم نسخ بيانات الحوالة! يمكنك الآن لصقها في أي تطبيق.");
    }).catch(err => {
        console.error("خطأ في النسخ:", err);
    });


    document.getElementById("shareModal").classList.add("hidden");



}





function openEditModal(traData, data) {


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


    document.getElementById("edit-date").value = traData.TRA_DATE.replace(' ', 'T').slice(0, 16);


    document.getElementById("edit-atm").value = traData.ATM;
    document.getElementById("edit-note").value = traData.NOTE;
    const editExchangeModal = document.getElementById("editExchangeModal");
    editExchangeModal.classList.remove("hidden");


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
    }



    const closeEditExchangeBtn = document.getElementById("closeEditExchangeListBtn");

    //    const editExchangeForm = document.getElementById("edit-exchange-form");

    const editExchangeForm = document.getElementById("edit-exchange-form");
    editExchangeForm.addEventListener("submit", (event) => {
        event.preventDefault();
        const formData = new FormData(editExchangeForm);
        formData.append("exchanges_list", JSON.stringify(data));
        fetch("update_exchange.php", {
            method: "POST",
            body: formData
        })

        editExchangeForm.reset();
        location.reload();
    });

    closeEditExchangeBtn.addEventListener("click", () => {
        closeModal('editExchangeModal');
        editExchangeForm.reset();
    });

}

function closeModal(id) {

    document.getElementById(id).classList.add("hidden");

}


const exchangesListBody = document.getElementById("exchanges-list-body");
fetch("get_exchanges_list.php", {
    method: "POST",
    headers: {
        "Content-Type": "application/x-www-form-urlencoded"
    },
    body: "client_id=" + encodeURIComponent(currentClientId)
})
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                exchangesListBody.innerHTML = `<p>${data.erro}</p>`;
                //                exchange.classList.remove("hidden");
                return;
            }
            if (data.length === 0) {
                exchangesListBody.innerHTML = "<p>لا توجد حوالات لهذا العميل.</p>";


            } else {

                data.forEach(row => {
                    const exchangesDataContainer = document.createElement("div");
                    //                            exchangesData.id = "exchangesData" + row.TRA_ID;
                    exchangesDataContainer.classList.add("exchanges-data-container");
                    exchangeDataContent = `
                                    <div class="oper">
                                        <i  class="fas fa-trash-alt  operation" data-id="trash${row.TRA_ID}"></i>
                                        <i class="fas fa-edit  operation" data-id="edit${row.TRA_ID}"> </i>
                                        <i class="fas fa-share-alt  operation" data-id="share${row.TRA_ID}"></i>
                                    </div>
                                    <div class="exchanges-data" data-id="exchange-data-${row.TRA_ID}">`;
                    exchangeDataContent += `<h3>${row.SENDER_NAME}</h3><h3>${row.RECEIVER_NAME}</h3><h3>${row.TYPE}</h3><h3>${row.TRANSFER_NO}</h3>`;
                    if (row.CURRENCY === "new") {
                        exchangeDataContent += `<h3>${numberFormat(row.AMMOUNT)} ري قعيطي</h3>`;
                    } else if (row.CURRENCY === "old") {
                        exchangeDataContent += `<h3>${numberFormat(row.AMMOUNT)} ري قديم</h3>`;

                    } else {
                        exchangeDataContent += `<h3>${numberFormat(row.AMMOUNT)} سعودي</h3>`;

                    }
                    exchangeDataContent += `<h3>${row.FOR_OR_ON}</h3><h3 class="date">${row.TRA_DATE}</h3><h3>${row.ATM}</h3><h3>${numberFormat(row.TRA_FEES)}</h3><h3>${numberFormat(row.sum_ammount_new)}</h3><h3>${numberFormat(row.sum_ammount_old)}</h3><h3>${numberFormat(row.sum_ammount_sa)}</h3><textarea class="note">${row.NOTE}</textarea><h3>${row.STATUS}</h3></div>`

                    exchangesDataContainer.innerHTML = exchangeDataContent;
                    exchangesListBody.insertBefore(exchangesDataContainer, exchangesListBody.firstChild);
                });



                //    ===============================================================================================================                    
                // إضافة أحداث بعد تحميل العناصر الديناميكية
                document.querySelectorAll(".operation").forEach(icon => {
                    icon.addEventListener("click", function () {
                        const id = this.dataset.id;
                        const operaion = id.slice(0, 4);
                        const traNo = Number(id.replace(/\D/g, ""));

                        if (operaion == "tras") {

                            openDeleteModal(traNo, data);
                        } else if (operaion == "edit") {
                            traData = null;
                            data.forEach(row => {
                                if (row.TRA_ID == traNo) {

                                    traData = row;
                                }
                            });
                            openEditModal(traData, data);
                        } else {

                            openShareModal(traNo);
                        }

                    });
                });
            }



        }).catch(err => {
    exchangesListBody.innerHTML = `<p>حدث خطأأثناء تحميل البيانات          ${err}</p>`;
});