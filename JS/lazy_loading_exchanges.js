let offsetExchanges = 0;
const limitExchanges = 10;
isLoadingExchanges = false;
noMoreDataExchanges = false;

function loadExchanges() {
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
    fetch("get_exchanges_list.php", {
        method: "POST",
        body: requiredData
    })
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    exchangesListBody.innerHTML = `<p>${data.erro}</p>`;
                    //                exchange.classList.remove("hidden");
                    return;
                }
                if (data.length === 0) {
                    if (!exchangesListData) {
                        exchangesListBody.innerHTML = "<p>لا توجد عمليات لهذا العميل.</p>";
                        noMoreDataExchanges = true;
                    } else {
                        noMoreDataExchanges = true;
                        document.getElementById("loading-message").innerText = "تم تحميل جميع العمليات.";
                    }

                } else {
                    exchangesListData.push(...data);
                    data.forEach(row => {
                        exchangeDataContent = `
                        <div class="exchanges-data-container">
                                    <div class="oper">
                                        <i  class="fas fa-trash-alt  operation" data-id="trash${row.TRA_ID}"></i>
                                        <i class="fas fa-edit  operation" data-id="edit${row.TRA_ID}"> </i>
                                        <i class="fas fa-share-alt  operation" data-id="share${row.TRA_ID}"></i>
                                    </div>
                                    <div class="exchanges-data" data-id="exchange-data-${row.TRA_ID}">`;
                        exchangeDataContent += `<h3>${row.TYPE}</h3><h3>${row.SENDER_NAME}</h3><h3>${row.RECEIVER_NAME}</h3><h3>${row.TRANSFER_NO}</h3>`;
                        if (row.TYPE != 'تحويل') {

                            if (row.CURRENCY === "new") {
                                exchangeDataContent += `<h3>${numberFormat(row.AMMOUNT)} ري قعيطي</h3>`;
                            } else if (row.CURRENCY === "old") {
                                exchangeDataContent += `<h3>${numberFormat(row.AMMOUNT)} ري قديم</h3>`;

                            } else {
                                exchangeDataContent += `<h3>${numberFormat(row.AMMOUNT)} ريال سعودي</h3>`;
                            }

                        } else {
                            if (row.FROM_CURRENCY === "new") {
                                exchangeDataContent += `<h3>${numberFormat(row.AMMOUNT)} ري قعيطي</h3>`;
                            } else if (row.FROM_CURRENCY === "old") {
                                exchangeDataContent += `<h3>${numberFormat(row.AMMOUNT)} ري قديم</h3>`;

                            } else {
                                exchangeDataContent += `<h3>${numberFormat(row.AMMOUNT)} ريال سعودي</h3>`;
                            }
                        }
                        exchangeDataContent += `<h3>${row.FOR_OR_ON}</h3><h3 class="date">${row.TRA_DATE}</h3><h3>${row.ATM}</h3><h3>${numberFormat(row.TRA_FEES)}</h3><h3>${numberFormat(row.sum_ammount_new)}</h3><h3>${numberFormat(row.sum_ammount_old)}</h3><h3>${numberFormat(row.sum_ammount_sa)}</h3><textarea class="note">${row.NOTE}</textarea><h3>${row.STATUS}</h3></div></div>`;
                        exchangesListBody.innerHTML += exchangeDataContent;
                    });

                    offsetExchanges += limitExchanges;
                    document.getElementById("loading-message").style.display = "none";
                    isLoadingExchanges = false;
                }
            }).catch(err => {
        exchangesListBody.innerHTML = `<p>حدث خطأأثناء تحميل البيانات          ${err}</p>`;
    });

}


document.addEventListener("DOMContentLoaded", () => {
    loadExchanges();
});

window.addEventListener("scroll", () => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 50) {
        loadExchanges();
    }
});

    