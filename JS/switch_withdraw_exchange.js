/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */



const operSelectTypeInput = document.getElementById("oper-type-input");
const senderNameInput = document.getElementById("sender");
const transferNoInput = document.getElementById("transfer-no-input-group");
const status = document.getElementById('status');
//const reciverNameInputGroup=document.getElementById("reciver-input-group");

operSelectTypeInput.addEventListener("change", () => {
    if (operSelectTypeInput.value == "حوالة") {
        senderNameInput.placeholder = 'اسم المرسل';
        editSenderNameInput.placeholder = 'اسم المرسل';
        transferNoInput.classList.remove('hidden');
        status.classList.remove('hidden');
//        reciverNameInputGroup.classList.remove('hidden');
    } else {
        senderNameInput.placeholder = 'اسم المودع';
        editSenderNameInput.placeholder = 'اسم المودع';
        transferNoInput.classList.add('hidden');
        status.classList.add('hidden');
//        reciverNameInputGroup.classList.add('hidden');
    }
});

const editOperSelectTypeInput = document.getElementById("edit-type");

const editSenderNameInput = document.getElementById("edit-sender");

const editTransferNoInput = document.getElementById("edit-transfer-no-input-group");
const editStatus = document.getElementById('edit-status');

//const editReciverNameInputGroup=document.getElementById("edit-reciver-input-group");


editOperSelectTypeInput.addEventListener("change", () => {
    if (editOperSelectTypeInput.value == "حوالة") {
        editSenderNameInput.placeholder = 'اسم المرسل';
        editTransferNoInput.classList.remove('hidden');
        editStatus.classList.remove('hidden');
//        editReciverNameInputGroup.classList.remove('hidden');
    } else {
        editSenderNameInput.placeholder = 'اسم المودع';
        editTransferNoInput.classList.add('hidden');
        editStatus.classList.add('hidden');
//        editReciverNameInputGroup.classList.add('hidden');
    }
});
