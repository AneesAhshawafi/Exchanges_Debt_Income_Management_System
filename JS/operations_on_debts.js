currentClientId = localStorage.getItem("currentClientId");

currentExchangesListData = null;

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
        formData.append("DEBT_ID", traNo);
        formData.append("client_id", currentClientId);
        fetch("debt_delete_debt.php", {
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


function openShareModal(traNo) {

    fetch("debt_share_debt.php", {

        method: "POST",

        headers: { "Content-Type": "application/x-www-form-urlencoded" },

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
            textWithoutTotal = `*{بقالة بن عبود}*
`;
            if (traData.FOR_OR_ON == 'عليه') {
                textWithoutTotal += `
عليكم ${ammount} ${currency} `;

            } else {
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





function openEditModal(traData) {


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
        console.log(currentClientId);
        formData.append("client_id", currentClientId);
        fetch("debt_update_debt.php", {
            method: "POST",
            body: formData
        }).then(res => res.json())
            .then(response => {
                if (response.success) {
                    Swal.fire({ icon: 'success', title: 'تم بنجاح', text: response.success, timer: 1500, showConfirmButton: false }).then(() => { editExchangeForm.reset(); location.reload(); });
                } else {
                    Swal.fire({ icon: 'error', title: 'خطأ', text: response.error });
                }
            }).catch(er => {
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
                if (row.DEBT_ID == traNo) {

                    traData = row;
                }
            });
            //            console.log("traData",traData);
            openEditModal(traData);
        } else {

            openShareModal(traNo);
        }

    }
}); 