let offsetExchanges = 0;
const limitExchanges = 10;
isLoadingExchanges = false;
noMoreDataExchanges = false;

function loadDebts() {
    if (isLoadingExchanges || noMoreDataExchanges) {
        return;
    }
    isLoadingExchanges = true;
    document.getElementById("loading-message").style.display = "block";
    const requiredData = new FormData();
    requiredData.append("client_id", currentClientId);
    requiredData.append("limit", limitExchanges);
    requiredData.append("offset", offsetExchanges);
    const exchangesListBody = document.getElementById("exchanges-list-body");
    fetch("debt_get_debts_list.php", {
        method: "POST",
        body: requiredData
    })
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    exchangesListBody.innerHTML = `<p>${data.error}</p>`;
                    //                exchange.classList.remove("hidden");
                    return;
                }
                if (data.length === 0) {
                    if (!exchangesListData) {
                        exchangesListBody.innerHTML = "<p>لا توجد ديون على هذا العميل.</p>";
                        noMoreDataExchanges = true;
                    } else {
                        noMoreDataExchanges = true;
                        document.getElementById("loading-message").innerText = "تم تحميل قائمة الديون لهذا العميل.";
                    }

                } else {
                    exchangesListData.push(...data);
                    data.forEach(row => {
//                        const exchangesDataContainer = document.createElement("div");
//                       exchangesDataContainer.classList.add("exchanges-data-container");
                        exchangeDataContent = `
                        <div class="exchanges-data-container">
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
                        exchangeDataContent += `<h3>${row.FOR_OR_ON}</h3><h3 class="date">${row.DEBT_DATE}</h3><h3>${numberFormat(row.sum_ammount_new)}</h3><h3>${numberFormat(row.sum_ammount_old)}</h3><h3>${numberFormat(row.sum_ammount_sa)}</h3><textarea class="note" rows="2" cols="20">${row.NOTE}</textarea></div></div>`;

//                        exchangesDataContainer.innerHTML = exchangeDataContent;
//                        exchangesListBody.insertBefore(exchangesDataContainer, exchangesListBody.firstChild);
                        exchangesListBody.innerHTML += exchangeDataContent;
                    });

                    offsetExchanges += limitExchanges;
                    document.getElementById("loading-message").style.display = "none";
                    isLoadingExchanges = false;


                    //    ===============================================================================================================                    
                    // إضافة أحداث بعد تحميل العناصر الديناميكية

                }



            }).catch(err => {
        exchangesListBody.innerHTML = `<p>حدث خطأأثناء تحميل البيانات          ${err}</p>`;
    });

}


document.addEventListener("DOMContentLoaded", () => {
    loadDebts();
});

window.addEventListener("scroll", () => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight-50) {
        loadDebts();
    }
});













