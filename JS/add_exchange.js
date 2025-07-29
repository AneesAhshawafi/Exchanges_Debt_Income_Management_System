/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */

currentClientId = localStorage.getItem("currentClientId");
const addExchangeFormOverlay=document.getElementById("addExchangeForm");
const addExchangeForm=document.getElementById("add-exchange-form");
const addExchangeBtn=document.getElementById("addExchangeBtn");
const closeAddExchangeBtn=document.getElementById("closeAddExchangeBtn");


addExchangeBtn.addEventListener("click",()=>{
   addExchangeFormOverlay.classList.remove("hidden"); 
});

closeAddExchangeBtn.addEventListener("click",()=>{
   addExchangeFormOverlay.classList.add("hidden"); 
});

addExchangeForm.addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append("client_id", currentClientId);
    fetch("insert_transaction.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(response => {
                console.log("first then");
        if (response.success) {
            alert(response.success + " ✅");
            this.reset();
            location.reload();
        } else {
            alert("❌ " + response.error);
        }
    })
            .catch(err => {
        console.error("خطأ:", err);
        alert("فشل الاتصال بالسيرفر  "+err);
    });
});

