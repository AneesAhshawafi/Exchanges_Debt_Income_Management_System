///* 
// * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
// * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
// */
 currentClientId = localStorage.getItem("currentClientId");

 currentExchangesListData = null;

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
        DEBT_ID: traNo,
        debts_list: exchangesList
    };

    document.getElementById("deleteModal").classList.remove("hidden");

    document.getElementById("confirmDeleteBtn").addEventListener("click", function () {
        const formData = new FormData();
        formData.append("DEBT_ID", postData.DEBT_ID);
        formData.append("debts_list", JSON.stringify(postData.debts_list));

        fetch("debt_delete_debt.php", {
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

    fetch("debt_share_debt.php", {

        method: "POST",

        headers: {"Content-Type": "application/x-www-form-urlencoded"},

        body: "debt_id=" + encodeURIComponent(traNo)

    })

            .then(res => res.json())

            .then(data => {
                traData = data;

                if (traData.CURRENCY == 'new') {
                    currency = ' ريال قعيطي ';
                } else if (traData.CURRENCY == 'old') {
                    currency = 'ريال قديم';
                } else {
                    currency = 'ريال سعودي';
                }
                ammount = numberFormat(traData.AMMOUNT, 2);
                textWithoutTotal = `بقالة بن عيود 
`;
if(traData.FOR_OR_ON=='عليه'){
    textWithoutTotal += `
عليكم ${ammount} ${currency} `;
    
}else{
    textWithoutTotal += `
لكم ${ammount} ${currency} `;
}
                textWithoutTotal += `
 مقابل ${traData.DESCRIPTION}
المبلغ: ${ammount} ${currency}
التاريخ: ${traData.DEBT_DATE}`;
                if (traData.NOTE) {
                    textWithoutTotal += `
ملاحظة:
${traData.NOTE}`;
                }

                const shareText = document.getElementById("shareText");
                shareText.value = textWithoutTotal + getTextOfTotalAmmounts(traData);
                shareText.style.direction = 'rtl';

                document.getElementById("shareModal").classList.remove("hidden");
                document.getElementById("shareBtn").addEventListener("click", () => {
                    shareExchange(textWithoutTotal);
                });
                document.getElementById('shareWithTotalBtn').addEventListener("click", () => {
                    shareExchange(textWithoutTotal + getTextOfTotalAmmounts(traData));
                });
            });

}
function getTextOfTotalAmmounts(traData) {
    textTotal = `
 الإجمالي `;

    if (traData.CURRENCY == 'new') {
        if (traData.sum_ammount_new > 0) {
            textTotal += `عليكم `;
        } else {
            textTotal += `لكم `;
        }
        textTotal += ` ${numberFormat(Math.abs(traData.sum_ammount_new), 2)} ريال قعيطي
`;

    } else if (traData.CURRENCY == 'old') {
        if (traData.sum_ammount_old > 0) {
            textTotal += `عليكم `;
        } else {
            textTotal += `لكم `;
        }
        textTotal += ` ${numberFormat(Math.abs(traData.sum_ammount_old), 2)} ريال قديم
`;
    } else {
        if (traData.sum_ammount_sa > 0) {
            textTotal += `عليكم `;
        } else {
            textTotal += `لكم `;
        }
        textTotal += ` ${numberFormat(Math.abs(traData.sum_ammount_sa), 2)} ريال سعودي
`;
    }
    return textTotal;
}
function shareExchange(text) {
    // فتح واجهة المشاركة إن أحببت
    if (navigator.share) {
        navigator.share({
            title: "بيانات الدين",
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

    document.getElementById("edit-exchange-id").value = traData.DEBT_ID;




    document.getElementById("edit-currency").value = traData.CURRENCY;



    document.getElementById("edit-for-or-on").value = traData.FOR_OR_ON;



    document.getElementById("edit-description").value = traData.DESCRIPTION;


    document.getElementById("edit-ammount").value = traData.AMMOUNT;




    document.getElementById("edit-date").value = traData.DEBT_DATE;


    document.getElementById("edit-note").value = traData.NOTE;
    const editExchangeModal = document.getElementById("editExchangeModal");
    editExchangeModal.classList.remove("hidden");



    const closeEditExchangeBtn = document.getElementById("closeEditExchangeListBtn");

//    const editExchangeForm = document.getElementById("edit-exchange-form");

    const editExchangeForm = document.getElementById("edit-exchange-form");
    editExchangeForm.addEventListener("submit", (event) => {
        event.preventDefault();
        const formData = new FormData(editExchangeForm);
        formData.append("debts_list", JSON.stringify(data));
        fetch("debt_update_debt.php", {
            method: "POST",
            body: formData
        })
//                .then(res => res.json())
//                .then(response => {
//                    if (response.success) {
//                        alert(response.success);
//                    } else {
//                        alert(response.error);
//                    }
//                })
//                .catch(err => alert("حدث خطأ: " + err));

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
fetch("debt_get_debts_list.php", {
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
                exchangesListBody.innerHTML = "<p>لا يوجد ديون لهذا العميل.</p>";


            } else {

                data.forEach(row => {
                    const exchangesDataContainer = document.createElement("div");
//                            exchangesData.id = "exchangesData" + row.TRA_ID;
                    exchangesDataContainer.classList.add("exchanges-data-container");
                    exchangeDataContent = `
                                    <div class="oper">
                                        <i  class="fas fa-trash-alt  operation" data-id="trash${row.DEBT_ID}"></i>
                                        <i class="fas fa-edit  operation" data-id="edit${row.DEBT_ID}"> </i>
                                        <i class="fas fa-share-alt  operation" data-id="share${row.DEBT_ID}"></i>
                                    </div>
                                    <div class="debts-data" data-id="exchange-data-${row.DEBT_ID}">`;
                    exchangeDataContent += `<h3 >${row.DESCRIPTION}</h3>`;
                    if (row.CURRENCY === "new") {
                        exchangeDataContent += `<h3>${numberFormat(row.AMMOUNT)} ري قعيطي</h3>`;
                    } else if (row.CURRENCY === "old") {
                        exchangeDataContent += `<h3>${numberFormat(row.AMMOUNT)} ري قديم</h3>`;

                    } else {
                        exchangeDataContent += `<h3>${numberFormat(row.AMMOUNT)} سعودي</h3>`;

                    }
                    exchangeDataContent += `<h3>${row.FOR_OR_ON}</h3><h3 class="date">${row.DEBT_DATE}</h3><h3>${numberFormat(row.sum_ammount_new)}</h3><h3>${numberFormat(row.sum_ammount_old)}</h3><h3>${numberFormat(row.sum_ammount_sa)}</h3><textarea class="note" rows="2" cols="20">${row.NOTE}</textarea></div>`

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
                                if (row.DEBT_ID == traNo) {

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