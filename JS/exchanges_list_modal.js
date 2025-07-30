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

function openDeleteModal(traNo) {


    document.getElementById("deleteModal").classList.remove("hidden");
    document.getElementById("confirmDeleteBtn").addEventListener("click", function () {
        fetch("delete_exchange.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "tra_id=" + encodeURIComponent(traNo)

        }).then(res => res.text()).then(response => {

            alert("تم الحذف");

            closeModal("deleteModal");
            location.reload();
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

                shareExchange(data);
                document.getElementById("shareModal").classList.remove("hidden");

            });

}

function shareExchange(traData) {
    const text = `
رقم العملية: ${traData.TRANSFER_NO}
النوع: ${traData.TYPE}
المبلغ: ${traData.AMMOUNT} ${traData.CURRENCY}
المرسل: ${traData.SENDER_NAME}
الرسوم: ${traData.TRA_FEES}
التاريخ: ${traData.TRA_DATE}
الملاحظات: ${traData.NOTE}
`;
    const shareText = document.getElementById("shareText");
    shareText.value = text;
    shareText.style.direction = 'rtl';
    document.getElementById("shareBtn").addEventListener("click", () => {

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
    })


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



    document.getElementById("edit-transfer-no").value = traData.TRANSFER_NO;


    document.getElementById("edit-ammount").value = traData.AMMOUNT;



    document.getElementById("edit-fees").value = traData.TRA_FEES;


    document.getElementById("edit-date").value = traData.TRA_DATE.replace(' ', 'T').slice(0, 16);


    document.getElementById("edit-atm").value = traData.ATM;
    document.getElementById("edit-note").value = traData.NOTE;
    const editExchangeModal = document.getElementById("editExchangeModal");
    editExchangeModal.classList.remove("hidden");



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
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        alert(response.success);
                    } else {
                        alert(response.error);
                    }
                })
                .catch(err => alert("حدث خطأ: " + err));

        editExchangeForm.reset();
        location.reload();
    });

    closeEditExchangeBtn.addEventListener("click", () => {
        closeModal('editExchangeModal');

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
                        exchangeDataContent += `<h3>${numberFormat(row.AMMOUNT)}</h3><h3>0</h3><h3>0</h3>`;
                    } else if (row.CURRENCY === "old") {
                        exchangeDataContent += `<h3>0</h3><h3>${numberFormat(row.AMMOUNT)}</h3><h3>0</h3>`;

                    } else {
                        exchangeDataContent += `<h3>0</h3><h3>0</h3><h3>${numberFormat(row.AMMOUNT)}</h3>`;

                    }
                    exchangeDataContent += `<h3>${row.FOR_OR_ON}</h3><h3 class="date">${row.TRA_DATE}</h3><h3>${row.ATM}</h3><h3>${numberFormat(row.TRA_FEES)}</h3><h3>${numberFormat(row.sum_ammount_new)}</h3><h3>${numberFormat(row.sum_ammount_old)}</h3><h3>${numberFormat(row.sum_ammount_sa)}</h3><h3 class="note">${row.NOTE}</h3></div>`

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

                            openDeleteModal(traNo);
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