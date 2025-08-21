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

let offsetExchanges = 0;
const limitExchanges = 3;
isLoadingExchanges = false;
noMoreDataExchanges = false;

function loadIncomes() {
    if (isLoadingExchanges || noMoreDataExchanges) {
        return;
    }
    isLoadingExchanges = true;
    document.getElementById("loading-message").style.display = "block";
    const requiredData = new FormData();
    requiredData.append("limit", limitExchanges);
    requiredData.append("offset", offsetExchanges);
    const exchangesListBody = document.getElementById("exchanges-list-body");
    fetch("income_get_income_list.php", {
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
                        exchangesListBody.innerHTML = "<p>ليس لديك مصادر دخل بعد.</p>";
                        noMoreDataExchanges = true;
                    } else {
                        noMoreDataExchanges = true;
                        document.getElementById("loading-message").innerText = "تم تحميل جميع مصادر الدخل.";
                    }

                } else {
                    exchangesListData.push(...data);
                    data.forEach(row => {
//                        const exchangesDataContainer = document.createElement("div");
//                       exchangesDataContainer.classList.add("exchanges-data-container");
                        exchangeDataContent = `
                        <div class="exchanges-data-container">
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

                        if (row.FOR_OR_ON == 'له') {
                            exchangeDataContent += '<h3>لم يتم السحب</h3>';
                        } else {
                            exchangeDataContent += '<h3>تم السحب</h3>';
                        }
                        exchangeDataContent += `<h3 class="date">${row.INCM_DATE}</h3><h3>${numberFormat(row.sum_ammount_new)}</h3><h3>${numberFormat(row.sum_ammount_old)}</h3><h3>${numberFormat(row.sum_ammount_sa)}</h3><textarea name="tINote" class="note" rows="2" cols="20">${row.NOTE}</textarea></div></div>`;

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
    loadIncomes();
});

window.addEventListener("scroll", () => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 100) {
        loadIncomes();
    }
});













