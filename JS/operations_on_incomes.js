/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */


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
        formData.append("INCM_ID", traNo);

        fetch("income_delete_income.php", {
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




function openEditModal(traData) {


    if (!traData) {

        console.error("بيانات التعديل غير موجودة");
        return;
    }

    document.getElementById("edit-exchange-id").value = traData.INCM_ID;
    document.getElementById("edit-currency").value = traData.CURRENCY;
    if (traData.FOR_OR_ON == 'له') {

        document.getElementById("edit-for-or-on").value = 'لم يتم السحب';
    } else {
        document.getElementById("edit-for-or-on").value = 'تم السحب';
    }

    document.getElementById("edit-for-or-on").value = traData.FOR_OR_ON;
    document.getElementById("edit-source").value = traData.SOURCE;
    document.getElementById("edit-ammount").value = traData.AMMOUNT;
    document.getElementById("edit-date").value = traData.INCM_DATE;
    document.getElementById("edit-note").value = traData.NOTE;
    const editExchangeModal = document.getElementById("editExchangeModal");
    editExchangeModal.classList.remove("hidden");
    const closeEditExchangeBtn = document.getElementById("closeEditExchangeListBtn");
    //    const editExchangeForm = document.getElementById("edit-exchange-form");

    const editExchangeForm = document.getElementById("edit-exchange-form");
    editExchangeForm.addEventListener("submit", (event) => {
        event.preventDefault();
        const formData = new FormData(editExchangeForm);
        fetch("income_update_income.php", {
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
                }).catch(err => {
            console.error("خطأ أثناء التعديل:", err);
        });
//        closeEditExchangeBtn.addEventListener("click", () => {
//            closeModal('editExchangeModal');
//            editExchangeForm.reset();
//        });
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
                if (row.INCM_ID == traNo) {

                    traData = row;
                }
            });
            openEditModal(traData);
        } else {

            openShareModal(traNo);
        }

    }
});