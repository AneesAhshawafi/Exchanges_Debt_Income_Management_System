
/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */
function openDeleteClientModal(client_id) {
    document.getElementById("deleteModal").classList.remove("hidden");


    document.getElementById("confirmDeleteBtn").addEventListener("click", function () {
        fetch("delete_client.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "client_id=" + encodeURIComponent(client_id)
        }).then(res => res.json())
                .then(response => {
                    if (response.success) {

                        alert(response.success);
                        closeModal("deleteModal");
                        location.reload();
                    } else {
                        alert(response.error);
                    }
                }).catch(err => {
            console.log(err);
        });
    });
}
function openShareClientModal(traNo) {
    document.getElementById("shareModal").classList.remove("hidden");
    fetch("get_single_exchange.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "tra_no=" + encodeURIComponent(traNo)
    })
            .then(res => res.json())
            .then(data => {
//                const text = `المرسل: ${data.SENDER_NAME}\nرقم الحوالة: ${data.TRANSFER_NO}\nالمبلغ: ${data.AMMOUNT}\nالتاريخ: ${data.TRA_DATE}`;
//                document.getElementById("shareText").value = text;
                shareClient(data);

            });
}

function shareClient(traData) {
    const text = `
رقم العملية: ${traData.TRANSFER_NO}
النوع: ${traData.TYPE}
المبلغ: ${traData.AMMOUNT} ${traData.CURRENCY}
المرسل: ${traData.SENDER_NAME}
الرسوم: ${traData.TRA_FEES}
التاريخ: ${traData.TRA_DATE}
الملاحظات: ${traData.NOTE}
`;
    const shareBtn = document.getElementById("share-btn");
//    const shareText=document.getElementById("shareText");
//    shareText.innerContent=text;
    shareBtn.addEventListener("click", () => {
        // نسخ النص للحافظة
        navigator.clipboard.writeText(text).then(() => {
//        alert("تم نسخ بيانات الحوالة! يمكنك الآن لصقها في أي تطبيق.");
        }).catch(err => {
            console.error("خطأ في النسخ:", err);
        });

        // فتح نافذة المشاركة
        if (navigator.share) {
            navigator.share({
                title: "بيانات الحوالة",
                text: text
            }).catch(err => {
                console.error("فشل المشاركة:", err);
            });
        }
        document.getElementById("shareModal").classList.add("hidden");
    });


}

function closeModal(id) {
    document.getElementById(id).classList.add("hidden");
}

function openEditClientModal(clientData) {
    if (clientData) {
        document.getElementById("client-name-e").value = clientData.client_name;
        document.getElementById("client-id").value=clientData.client_id;

        document.getElementById("edit-client-overlay").classList.remove("hidden");
        const closeEditExchangeBtn = document.getElementById("closeEditClientBtn");
        closeEditExchangeBtn.addEventListener("click", () => {
                                    closeModal("edit-client-overlay");
//            document.getElementById("edit-client-overlay").classList.add("hidden");
        });
    }
}



// إضافة أحداث بعد تحميل العناصر الديناميكية
document.addEventListener("click", function (e) {
    if (e.target.classList.contains("oper-client")) {
        const id = e.target.dataset.id;
        const client_id = Number(id.replace(/\D/g, ""));
        const operation = id.slice(0, 4);

        if (operation === "tras") {
            openDeleteClientModal(client_id);
        } else if (operation === "edit") {
            const client_name_id = "client-name" + client_id;
            const client_name = document.getElementById(client_name_id).textContent.trim();
            const clientData = { client_id: client_id, client_name: client_name };
            openEditClientModal(clientData);
        } else {
            openShareClientModal(client_id);
        }
    }
});


