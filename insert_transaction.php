<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

// insert_transaction.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

// التحقق من أن الطلب هو POST وأن جميع البيانات موجودة
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    date_default_timezone_set("Asia/Aden");
    // تنظيف وتحويل البيانات
    $type = trim($_POST["type"]);
    $currency = trim($_POST["currency"]);
    $for_or_on = trim($_POST["for-or-on"]);
    $sender_name = trim($_POST["sender-name"]);
    $transfer_no = trim($_POST["transfer-no"]);
    $ammount = floatval($_POST["ammount"]);
    $fees = floatval($_POST["fees"]);
//    $tra_date = trim($_POST["tra-date"]);
    $tra_date_raw = trim($_POST["tra-date"]);
    $tra_date = $tra_date_raw ? date("Y/m/d H:i:s A", strtotime($tra_date_raw)) : date("Y/m/d H:i:s  A");

    $atm = trim($_POST["atm"]);
    $note = trim($_POST["note"]);
    $client_id = intval($_POST["client_id"]);
   


    // تضمين الاتصال بقاعدة البيانات
    include("dbconn.php");
    $resualt_sum_ammount_new = $conn->query("SELECT SUM(AMMOUNT) as total FROM TRANSACTION WHERE CURRENCY ='new'and CLIENT_ID= " . $client_id);
    $resualt_sum_ammount_old = $conn->query("SELECT SUM(AMMOUNT) as total FROM TRANSACTION WHERE CURRENCY ='old' and CLIENT_ID= " . $client_id);
    $resualt_sum_ammount_sa = $conn->query("SELECT SUM(AMMOUNT) as total FROM TRANSACTION WHERE CURRENCY ='sa' and CLIENT_ID= " . $client_id);
    $row_new = $resualt_sum_ammount_new->fetch_assoc();
    $sum_ammount_new = is_null($row_new['total']) ? 0 : $row_new['total'];

    $row_old = $resualt_sum_ammount_old->fetch_assoc();
    $sum_ammount_old = is_null($row_old['total']) ? 0 : $row_old['total'];

    $row_sa = $resualt_sum_ammount_sa->fetch_assoc();
    $sum_ammount_sa = is_null($row_sa['total']) ? 0 : $row_sa['total'];

    // تجهيز الاستعلام
    $sql = "INSERT INTO transaction 
        (TYPE, CURRENCY, FOR_OR_ON, SENDER_NAME, TRANSFER_NO, AMMOUNT, TRA_FEES, TRA_DATE, ATM, NOTE, CLIENT_ID,sum_ammount_new,sum_ammount_old,sum_ammount_sa)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssddsssiddd", $type, $currency, $for_or_on, $sender_name, $transfer_no,
            $ammount, $fees, $tra_date, $atm, $note, $client_id,$sum_ammount_new,$sum_ammount_old,$sum_ammount_sa);

    if ($stmt->execute()) {
        echo json_encode(["success" => "تمت إضافة المعاملة بنجاح"]);
    } else {
        echo json_encode(["error" => "فشل في إدخال المعاملة: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "بيانات غير مكتملة أو طلب غير صالح"]);
}
?>
