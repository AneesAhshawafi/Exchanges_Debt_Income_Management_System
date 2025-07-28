/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */
let currentClientId = null;

document.addEventListener("click", function (e) {
    const client = e.target.closest(".clients-data");
    if (client && client.dataset.id) {
        const rawId = client.dataset.id;
        const clientId = rawId.replace(/\D/g, "");
        currentClientId = clientId;
        localStorage.setItem("currentClientId", currentClientId);
        window.location.href = "exchanges_list.php";
    }
});
