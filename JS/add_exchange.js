/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */

document.getElementById("add-exchange-form").addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append("client_id", currentClientId);
    fetch("insert_transaction.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json()) // لاحظ القوسين ()
    .then(response => {
                
        if (response.success) {
            alert(response.success + " ✅");
            this.reset();
        } else {
            alert("❌ " + response.error);
        }
    }).catch(err => {
//        console.error("خطأ:", err);
        alert("فشل الاتصال بالسيرفر  "+err);
    });
});
