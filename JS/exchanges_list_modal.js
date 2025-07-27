/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */
let currentClientId = null;

document.addEventListener("DOMContentLoaded", function () {
    const clientData = document.querySelectorAll(".clients-data-container");
    const exchange = document.getElementById("exchanges-list-overlay");
    const closeExchangeBtn = document.getElementById("closeExchangeListBtn");
    const exchangesListBody = document.getElementById("exchanges-list-body");
    clientData.forEach(function (client) {
        client.addEventListener("click", function () {
//            const clientId = this.dataset.id.replace(/\D/g, "");
//            currentClientId = clientId;
            const rawId = this.dataset.id;
            if (!rawId) {
                console.error("العنصر لا يحتوي على data-id");
                return;
            }
            const clientId = rawId.replace(/\D/g, "");
            currentClientId = clientId;
            fetch("get_exchanges_list.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "client_id=" + encodeURIComponent(clientId)
            })
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) {
                            exchangesListBody.innerHTML = `<p>${data.erro}</p>`;
                            exchange.classList.remove("hidden");
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
                                        <i  class="fas fa-trash-alt fa-1x" id="trash${row.TRA_NO}"></i>
                                        <i class="fas fa-edit fa-1x" id="edit${row.TRA_NO}"> </i>
                                    </div>
                                    <div class="exchanges-data" data-id="exchange-data-${row.TRA_NO}">`;
                                exchangeDataContent += `<h3>${row.SENDER_NAME}</h3><h3>${row.TYPE}</h3><h3>${row.TRANSFER_NO}</h3>`;
                                if (row.CURRENCY === "new") {
                                    exchangeDataContent += `<h3>${row.AMMOUNT}</h3><h3>0</h3><h3>0</h3>`;
                                } else if (row.CURRENCY === "old") {
                                    exchangeDataContent += `<h3>0</h3><h3>${row.AMMOUNT}</h3><h3>0</h3>`;

                                } else {
                                    exchangeDataContent += `<h3>0</h3><h3>0</h3><h3>${row.AMMOUNT}</h3>`;

                                }
                                exchangeDataContent+=`<h3>${row.FOR_OR_ON}</h3><h3>${row.TRA_DATE}</h3><h3>${row.ATM}</h3><h3>${row.TRA_FEES}</h3><h3>${row.sum_ammount_new}</h3><h3>${row.sum_ammount_old}</h3><h3>${row.sum_ammount_sa}</h3><h3>${row.NOTE}</h3></div>`

                                exchangesDataContainer.innerHTML = exchangeDataContent;
                                exchangesListBody.insertBefore(exchangesDataContainer, exchangesListBody.firstChild);
                            });

                        }
                        exchange.classList.remove("hidden");

                    }).catch(err => {
                exchangesListBody.innerHTML = `<p>حدث خطأأثناء تحميل البيانات          ${err}`;
                exchange.classList.remove("hidden");
            });


        });
    }); //forEach End

    closeExchangeBtn.addEventListener("click", () => {
        exchange.classList.add("hidden");
    });
});