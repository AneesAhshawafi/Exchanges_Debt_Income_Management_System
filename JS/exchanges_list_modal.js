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
    shareText.style.direction='rtl';
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





function openEditModal(data) {

    console.log("aaaa");

    if (!data) {

        console.error("بيانات التعديل غير موجودة");

        return;

    }

    document.getElementById("edit-exchange-id").value = data.TRA_ID;


    document.getElementById("edit-type").value = data.TYPE;

   

    document.getElementById("edit-currency").value = data.CURRENCY;

  

    document.getElementById("edit-for-or-on").value = data.FOR_OR_ON;

    

    document.getElementById("edit-sender").value = data.SENDER_NAME;



    document.getElementById("edit-transfer-no").value = data.TRANSFER_NO;


    document.getElementById("edit-ammount").value = data.AMMOUNT;



    document.getElementById("edit-fees").value = data.TRA_FEES;

  
    document.getElementById("edit-date").value = data.TRA_DATE.replace(' ', 'T').slice(0, 16);


    document.getElementById("edit-atm").value = data.ATM;
 document.getElementById("edit-note").value = data.NOTE;

    document.getElementById("editExchangeModal").classList.remove("hidden");



    const closeEditExchangeBtn = document.getElementById("closeEditExchangeListBtn");

    closeEditExchangeBtn.addEventListener("click", () => {

//                                    closeModal("");

        document.getElementById("editExchangeModal").classList.add("hidden");

    });

}

function closeModal(id) {

    document.getElementById(id).classList.add("hidden");

}
//function numberFormat(value, decimals = 2) {
//    let num = Number(value);
//    if (isNaN(num))
//        return value;
//    return num.toLocaleString('en-US', {
//        minimumFractionDigits: decimals,
//        maximumFractionDigits: decimals
//    });
//}
//// استدعِ هذا عندما يضغط المستخدم زر "تعديل"
//function openEditModal(data) {
//    // تعيين القيم في عناصر النموذج
//    document.querySelector('select[name="type"]').value = data.TYPE;
//    document.querySelector('select[name="currency"]').value = data.CURRENCY;
//    document.querySelector('select[name="for-or-on"]').value = data.FOR_OR_ON;
//    document.querySelector('#sender').value = data.SENDER_NAME;
//    document.querySelector('#id-exchange').value = data.TRANSFER_NO;
//    document.querySelector('#ammount').value = data.AMMOUNT;
//    document.querySelector('#fees').value = data.FEES;
//
//    // تحويل التاريخ من '2024-07-25 10:30:00' إلى '2024-07-25T10:30'
//    const dateInput = document.querySelector('#date');
//    dateInput.value = data.TRA_DATE.replace(' ', 'T').slice(0, 16);
//
//    document.querySelector('#atm').value = data.ATM;
//    document.querySelector('#note').value = data.NOTE;
//    document.querySelector('#exchange-id').value = data.exchange_id;
//
//    // تغيير اسم الزر
//    const submitBtn = document.querySelector('#add-exchange-form button[type="submit"]');
//    submitBtn.textContent = 'تحديث العملية';
//
//    // تغيير نوع الإرسال (مثلًا يمكن التحقق من hidden input أو تعديل action)
//    document.querySelector('#add-exchange-form').setAttribute('action', 'update_exchange.php');
//
//    // عرض المودال
//    document.querySelector('#addExchangeForm').classList.remove('hidden');
//}
//

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
                    exchangeDataContent += `<h3>${row.SENDER_NAME}</h3><h3>${row.TYPE}</h3><h3>${row.TRANSFER_NO}</h3>`;
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


//            exchange.classList.remove("hidden");
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
                            openEditModal(traData);
                        } else {
                            
                            openShareModal(traNo);
                        }

                    });
                });
            }

//            document.querySelectorAll(".fa-edit").forEach(icon => {
//                icon.addEventListener("click", function () {
//                    const traNo = this.dataset.id.replace("edit", "");
//                    traData = null;
//                    data.forEach(row => {
//                        if (row.TRA_NO === traNo) {
//                            traData = row;
//                        }
//                    });
//                    openEditModal(traData);
//                });
//            });
//
//            document.querySelectorAll(".fa-share-alt").forEach(icon => {
//                icon.addEventListener("click", function () {
//                    const traNo = this.dataset.id.replace("share", "");
//                    openShareModal(traNo);
//                });
//            });

//    ===============================================================================================================                    

        }).catch(err => {
    exchangesListBody.innerHTML = `<p>حدث خطأأثناء تحميل البيانات          ${err}`;
//    exchange.classList.remove("hidden");
});


//
//document.addEventListener("DOMContentLoaded", function () {
////    const clientData = document.querySelectorAll(".clients-data-container");
////    const exchange = document.getElementById("exchanges-list-overlay");
////    const closeExchangeBtn = document.getElementById("closeExchangeListBtn");
//    const exchangesListBody = document.getElementById("exchanges-list-body");
//    clientData.forEach(function (client) {
//        client.addEventListener("click", function () {
////            const clientId = this.dataset.id.replace(/\D/g, "");
////            currentClientId = clientId;
//            const rawId = this.dataset.id;
//            if (!rawId) {
//                console.error("العنصر لا يحتوي على data-id");
//                return;
//            }
//            const clientId = rawId.replace(/\D/g, "");
//            currentClientId = clientId;
//    
//
//
//        });
//    }); //forEach End
//
//    closeExchangeBtn.addEventListener("click", () => {
//        exchange.classList.add("hidden");
//    });
//});





//let currentClientId = null;

//
//document.addEventListener("DOMContentLoaded", function () {
//
//    const clientData = document.querySelectorAll(".clients-data-container");
//
//    const exchange = document.getElementById("exchanges-list-overlay");
//
//    const closeExchangeBtn = document.getElementById("closeExchangeListBtn");
//
//    const closeEditExchangeBtn = document.getElementById("closeExchangeListBtn");
//
//    const exchangesListBody = document.getElementById("exchanges-list-body");
//
//
//
//    clientData.forEach(function (client) {
//
//        client.addEventListener("click", function () {
//
////            const clientId = this.dataset.id.replace(/\D/g, "");
//
////            currentClientId = clientId;
//
//            const rawId = this.dataset.id;
//
//            if (!rawId) {
//
//                console.error("العنصر لا يحتوي على data-id");
//
//                return;
//
//            }
//
//            const clientId = rawId.replace(/\D/g, "");
//
//            currentClientId = clientId;
//
//            fetch("get_exchanges_list.php", {
//
//                method: "POST",
//
//                headers: {
//
//                    "Content-Type": "application/x-www-form-urlencoded"
//
//                },
//
//                body: "client_id=" + encodeURIComponent(clientId)
//
//            })
//
//                    .then(res => res.json())
//
//                    .then(data => {
//
//                        if (data.error) {
//
//                            exchangesListBody.innerHTML = `<p>${data.erro}</p>`;
//
//                            exchange.classList.remove("hidden");
//
//                            return;
//
//                        }
//
//                        if (data.length === 0) {
//
//                            exchangesListBody.innerHTML = "<p>لا توجد حوالات لهذا العميل.</p>";
//
//
//
//
//
//                        } else {
//
//                            currentExchangesListData = data;
//
//                            data.forEach(row => {
//
//                                const exchangesDataContainer = document.createElement("div");
//
////                            exchangesData.id = "exchangesData" + row.TRA_ID;
//
//                                exchangesDataContainer.classList.add("exchanges-data-container");
//
//                                exchangeDataContent = `
//
//                                <div class="oper">
//
//                                    <i  class="fas fa-trash-alt fa-1x trash-exchange" data-id="trash${row.TRA_ID}"></i>
//
//                                    <i class="fas fa-edit fa-1x edit-exchange" data-id="edit${row.TRA_ID}"> </i>
//
//                                    <i class="fas fa-share-alt fa-1x share-exchange" data-id="share${row.TRA_ID}"></i>
//
//                                </div>
//
//                                <div class="exchanges-data" data-id="exchange-data-${row.TRA_ID}">`;
//
//                                exchangeDataContent += `<h3>${row.SENDER_NAME}</h3><h3>${row.TYPE}</h3><h3>${row.TRANSFER_NO}</h3>`;
//
//                                if (row.CURRENCY === "new") {
//
//                                    exchangeDataContent += `<h3>${numberFormat(row.AMMOUNT)}</h3><h3>0</h3><h3>0</h3>`;
//
//                                } else if (row.CURRENCY === "old") {
//
//                                    exchangeDataContent += `<h3>0</h3><h3>${numberFormat(row.AMMOUNT)}</h3><h3>0</h3>`;
//
//
//
//                                } else {
//
//                                    exchangeDataContent += `<h3>0</h3><h3>0</h3><h3>${numberFormat(row.AMMOUNT)}</h3>`;
//
//
//
//                                }
//
//                                exchangeDataContent += `<h3>${row.FOR_OR_ON}</h3><h3>${row.TRA_DATE}</h3><h3>${row.ATM}</h3><h3>${numberFormat(row.TRA_FEES)}</h3><h3>${numberFormat(row.sum_ammount_new)}</h3><h3>${numberFormat(row.sum_ammount_old)}</h3><h3>${numberFormat(row.sum_ammount_sa)}</h3><h3 class="note">${row.NOTE}</h3></div>`;
//
//
//
//                                exchangesDataContainer.innerHTML = exchangeDataContent;
//
//                                exchangesListBody.insertBefore(exchangesDataContainer, exchangesListBody.firstChild);
//
//                            });
//
//
//
//                        }
//
//                        exchange.classList.remove("hidden");
//
////    ===============================================================================================================
//
//// إضافة أحداث بعد تحميل العناصر الديناميكية
//
//                        document.querySelectorAll(".trash-exchange").forEach(icon => {
//
//                            icon.addEventListener("click", function () {
//
//                                const traNo = this.dataset.id.replace("trash", "");
//
//                                openDeleteModal(traNo);
//
//                            });
//
//                        });
//
//
//
//                        document.querySelectorAll(".edit-exchange").forEach(icon => {
//
//                            icon.addEventListener("click", function () {
//
//                                const traNo = Number(this.dataset.id.replace("edit", ""));
//
//                                let traData = null;
//
//
//
//                                console.log(currentExchangesListData);
//
//                                console.log(traNo);
//
//
//
//                                currentExchangesListData.forEach(row => {
//
//                                    if (row.TRA_ID === traNo) {
//
//                                        traData = row;
//
//                                        console.log("found");
//
////                                        break;
//
//                                    }
//
//                                });
//
//                                console.log(traData);
//
//                                openEditModal(traData);
//
//
//
//                            });
//
//                        });
//
//
//
//                        document.querySelectorAll(".share-exchange").forEach(icon => {
//
//                            icon.addEventListener("click", function () {
//
//                                const traNo = this.dataset.id.replace("share", "");
//
//                                openShareModal(traNo);
//
//                            });
//
//                        });
//
////    ===============================================================================================================
//
//                    }).catch(err => {
//
//                exchangesListBody.innerHTML = `<p>حدث خطأأثناء تحميل البيانات          ${err}`;
//
//                exchange.classList.remove("hidden");
//
//            });
//
//
//
//
//
//        });
//
//    }); //forEach End
//
//
//
//    closeExchangeBtn.addEventListener("click", () => {
//
//        exchange.classList.add("hidden");
//
//    });
//
//});
