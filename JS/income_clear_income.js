function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
}
document.getElementById('confirmClearIncomeBtn').addEventListener('click', function () {
    fetch('incoome_clear_income.php')
            .then(res => res.json())
            .then(response => {
                if (response.messege) {
                    closeModal("clear-income-modal");
                    Swal.fire({ icon: 'success', title: 'تم بنجاح', text: response.messege, timer: 1500, showConfirmButton: false }).then(() => { location.reload(); });
                } else {
                    Swal.fire({ icon: 'error', title: 'خطأ', text: response.error });
                }
            }).catch(er => {
        Swal.fire({ icon: 'error', title: 'خطأ في الاتصال', text: String(er) });
    });
});