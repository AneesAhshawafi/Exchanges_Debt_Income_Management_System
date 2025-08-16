function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
}
document.getElementById('confirmClearIncomeBtn').addEventListener('click', function () {
    fetch('incoome_clear_income.php')
            .then(res => res.json())
            .then(response => {
                if (response.messege) {
                    alert(response.messege);
                    closeModal("clear-income-modal");
                    location.reload();
                } else {
                    alert(response.error);
                }
            }).catch(er => {
        alert(er);
    });
});