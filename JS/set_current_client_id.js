/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */
let currentClientId = null;
let currentClientName = null;
let currentClientPhone = null;

document.addEventListener("click", function (e) {
    const client = e.target.closest(".clients-data");
    if (client && client.dataset.id) {
        const rawId = client.dataset.id;
        const clientId = rawId.replace(/\D/g, "");
        currentClientId = clientId;
        localStorage.setItem("currentClientId", currentClientId);
        currentClientName = client.dataset.clientName;
        localStorage.setItem("currentClientName", currentClientName);
        currentClientPhone = client.dataset.clientPhone;
        localStorage.setItem("currentClientPhone", currentClientPhone);
        window.location.href = "exchanges_list.php";
    }
});
