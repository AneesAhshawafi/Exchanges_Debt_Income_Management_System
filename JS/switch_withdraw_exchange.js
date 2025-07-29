/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */



const operSelectTypeInput=document.getElementById("oper-type-input");
const senderLabel=document.getElementById("sender-input-label");
const senderNameInput=document.getElementById("sender");

const transferNoInput=document.getElementById("transfer-no-input-group");
const reciverNameInputGroup=document.getElementById("reciver-input-group");

operSelectTypeInput.addEventListener("change",()=>{
    if(operSelectTypeInput.value=="حوالة"){
        senderLabel.textContent='المرسل';
        senderNameInput.placeholder='اسم المرسل';
        editSenderNameInput.placeholder='اسم المرسل';
        transferNoInput.classList.remove('hidden');
        reciverNameInputGroup.classList.remove('hidden');
    }else{
        senderLabel.textContent='المودع';
        senderNameInput.placeholder='اسم المودع';
        editSenderNameInput.placeholder='اسم المودع';
        transferNoInput.classList.add('hidden');
        reciverNameInputGroup.classList.add('hidden');
    }
});

const editOperSelectTypeInput=document.getElementById("edit-type");

const editSenderLabel=document.getElementById("edit-sender-input-label");
const editSenderNameInput=document.getElementById("edit-sender");

const editTransferNoInput=document.getElementById("edit-transfer-no-input-group");
const editReciverNameInputGroup=document.getElementById("edit-reciver-input-group");


editOperSelectTypeInput.addEventListener("change",()=>{
    if(operSelectTypeInput.value=="حوالة"){
        editSenderLabel.textContent='المرسل';
        editSenderNameInput.placeholder='اسم المرسل';
        editTransferNoInput.classList.remove('hidden');
        editReciverNameInputGroup.classList.remove('hidden');
    }else{
        editSenderLabel.textContent='المودع';
        editSenderNameInput.placeholder='اسم المودع';
        editTransferNoInput.classList.add('hidden');
        editReciverNameInputGroup.classList.add('hidden');
    }
});
