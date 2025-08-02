
/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */
//function openDeleteClientModal(client_id) {
//    document.getElementById("deleteModal").classList.remove("hidden");
//
//
//    document.getElementById("confirmDeleteBtn").addEventListener("click", function () {
//        fetch("delete_client.php", {
//            method: "POST",
//            headers: {"Content-Type": "application/x-www-form-urlencoded"},
//            body: "client_id=" + encodeURIComponent(client_id)
//        }).then(res => res.json())
//                .then(response => {
//                    if (response.success) {
//
//                        alert(response.success);
//                        closeModal("deleteModal");
//                        location.reload();
//                    } else {
//                        alert(response.error);
//                    }
//                })
//            .catch(err => {
//            console.log(err);
//        });
//    });
//}
function openDeleteClientModal(client_id) {
    document.getElementById("deleteModal").classList.remove("hidden");

    const confirmBtn = document.getElementById("confirmDeleteBtn");
    
    // إزالة أي مستمع قديم
    const newBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);

    newBtn.addEventListener("click", function () {
        fetch("delete_client.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "client_id=" + encodeURIComponent(client_id)
        })
        .then(res => {
            if (!res.ok) {
                throw new Error("HTTP error " + res.status);
            }
            return res.json();
        })
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
            console.log("Fetch error:", err);
        });
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
        }else{
            openShareClientModal(client_id);
        }
    }
});


