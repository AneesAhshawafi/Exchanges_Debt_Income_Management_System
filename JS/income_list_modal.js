/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */


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
        INCM_ID: traNo,
        income_list: exchangesList
    };

    document.getElementById("deleteModal").classList.remove("hidden");

    document.getElementById("confirmDeleteBtn").addEventListener("click", function () {
        const formData = new FormData();
        formData.append("INCM_ID", postData.INCM_ID);
        formData.append("income_list", JSON.stringify(postData.income_list));

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




function openEditModal(traData, data) {


    if (!traData) {

        console.error("بيانات التعديل غير موجودة");

        return;

    }

    document.getElementById("edit-exchange-id").value = traData.INCM_ID;




    document.getElementById("edit-currency").value = traData.CURRENCY;


    if (traData.FOR_OR_ON=='له') {
        
    document.getElementById("edit-for-or-on").value ='لم يتم السحب';
    }else{
        document.getElementById("edit-for-or-on").value ='تم السحب';
    }



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
        formData.append("income_list", JSON.stringify(data));
        fetch("income_update_income.php", {
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
fetch("income_get_income_list.php", {
    method: "POST",
    headers: {
        "Content-Type": "application/x-www-form-urlencoded"
    },
    body: "user_id=" + encodeURIComponent(1)
})
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                exchangesListBody.innerHTML = `<p>${data.erro}</p>`;
//                exchange.classList.remove("hidden");
                return;
            }
            if (data.length === 0) {
                exchangesListBody.innerHTML = "<p>لا توجد مصادر دخل حتى الآن  .</p>";


            } else {

                data.forEach(row => {
                    const exchangesDataContainer = document.createElement("div");
//                            exchangesData.id = "exchangesData" + row.TRA_ID;
                    exchangesDataContainer.classList.add("exchanges-data-container");
                    exchangeDataContent = `
                                    <div class="oper">
                                        <i  class="fas fa-trash-alt  operation" data-id="trash${row.INCM_ID}"></i>
                                        <i class="fas fa-edit  operation" data-id="edit${row.INCM_ID}"> </i>
                                    </div>
                                    <div class="debts-data" data-id="exchange-data-${row.INCM_ID}">`;
                    exchangeDataContent += `<h3>${row.SOURCE}</h3>`;
//                    exchangeDataContent += `<textarea rows="2" cols="20" >${row.SOURCE}</textarea>`; 
                    if (row.CURRENCY === "new") {
                        exchangeDataContent += `<h3>${numberFormat(row.AMMOUNT)} ري قعيطي</h3>`;
                    } else if (row.CURRENCY === "old") {
                        exchangeDataContent += `<h3>${numberFormat(row.AMMOUNT)} ري قديم</h3>`;

                    } else {
                        exchangeDataContent += `<h3>${numberFormat(row.AMMOUNT)} سعودي</h3>`;

                    }
                    
                    if (row.FOR_OR_ON=='له'){
                        exchangeDataContent += '<h3>لم يتم سحبها</h3>';
                    }else{
                        exchangeDataContent +='تم سحبها';
                    }
                    exchangeDataContent += `<h3 class="date">${row.INCM_DATE}</h3><h3>${numberFormat(row.sum_ammount_new)}</h3><h3>${numberFormat(row.sum_ammount_old)}</h3><h3>${numberFormat(row.sum_ammount_sa)}</h3><textarea class="note" rows="2" cols="20">${row.NOTE}</textarea></div>`;

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
                        } else {
                            traData = null;
                            data.forEach(row => {
                                if (row.INCM_ID == traNo) {

                                    traData = row;
                                }
                            });
                            openEditModal(traData, data);
                        } 
                    });
                });
            }



        }).catch(err => {
    exchangesListBody.innerHTML = `<p>حدث خطأأثناء تحميل البيانات          ${err}</p>`;
});