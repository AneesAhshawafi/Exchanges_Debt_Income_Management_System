/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */



let offset = 0;
const limit = 10;
let isLoading = false;
let noMoreData = false;

function loadClients() {
    if (isLoading || noMoreData)
        return;
    isLoading = true;
    document.getElementById("loading-message").style.display = "block";
    fetch(`get_clients.php?offset=${offset}&limit=${limit}`)
            .then(res => res.text())
            .then(data => {
                if (data.trim() === "") {
                    noMoreData = true;
                    document.getElementById("loading-message").innerText = "تم تحميل جميع العملاء.";
                } else {
                    document.getElementById("clients-list").innerHTML += data;
                    offset += limit;
                    document.getElementById("loading-message").style.display = "none";
                    isLoading = false;
                }
            })
            .catch(err => {
                console.error("فشل تحميل العملاء:", err);
                isLoading = false;
                document.getElementById("loading-message").innerText = "حدث خطأ في التحميل.";
            });
}

document.addEventListener("DOMContentLoaded", () => {
    loadClients();
});

window.addEventListener("scroll", () => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 100) {
        loadClients();
    }
});

    