<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

// insert_transaction.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$error_file = fopen("er_file.txt", "w");
header("Content-Type: application/json");
//header("Content-Type: application/json");

include 'dbconn.php';
include 'total_ammounts_calc.php';
// التحقق من أن الطلب هو POST وأن جميع البيانات موجودة
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    date_default_timezone_set("Asia/Aden");
    // تنظيف وتحويل البيانات
    $type = trim($_POST["type"]);
    $currency = trim($_POST["currency"]);
    $for_or_on = trim($_POST["for-or-on"]);
    $sender_name = trim($_POST["sender-name"]);
    $receiver_name = trim($_POST['receiver-name']);
    $transfer_no = trim($_POST["transfer-no"]);
    $ammount = floatval($_POST["ammount"]);
    $fees = floatval($_POST["fees"]);
//    $tra_date = trim($_POST["tra-date"]);
    $tra_date_raw = $_POST["tra-date"];
    $tra_date = $tra_date_raw ? date("Y-m-d", strtotime($tra_date_raw)) : date("Y-m-d");

    $atm = trim($_POST["atm"]);
    $note = trim($_POST["note"]);
    $client_id = intval($_POST["client_id"]);
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';

    // تضمين الاتصال بقاعدة البيانات

    $sum_ammounts = calc_total_ammounts($client_id);
    $sum_ammount_new = $sum_ammounts[0];
    $sum_ammount_old = $sum_ammounts[1];
    $sum_ammount_sa = $sum_ammounts[2];

    if ($currency == "new") {
        if ($for_or_on == "له") {
            $sum_ammount_new += $ammount;
        } else {
            $sum_ammount_new -= $ammount;
        }
    } elseif ($currency == "old") {
        if ($for_or_on == "له") {
            $sum_ammount_old += $ammount;
        } else {
            $sum_ammount_old -= $ammount;
        }
    } else {
        if ($for_or_on == "له") {
            $sum_ammount_sa += $ammount;
        } else {
            $sum_ammount_sa -= $ammount;
        }
    }


    if (!$transfer_no && $type == 'حوالة') {
        $sql = "SELECT TRA_ID FROM transaction ORDER BY TRA_ID DESC LIMIT 1";
        $result = $conn->query($sql);
        $result_tra_id_for_transfer_no = $result->fetch_assoc();
        $tra_id_for_transfer_no = $result_tra_id_for_transfer_no['TRA_ID'];
        fwrite($error_file, "tra_id_for_transfer_no = " . $tra_id_for_transfer_no . " type :" . gettype($tra_id_for_transfer_no) . "\r\n");

        $transfer_no = 'BA-' . date("md", strtotime($tra_date)) . str_pad($tra_id_for_transfer_no + 1, 8, '0', STR_PAD_LEFT);
    // تجهيز الاستعلام
    $sql = "INSERT INTO transaction (TYPE, CURRENCY, FOR_OR_ON, SENDER_NAME,RECEIVER_NAME, TRANSFER_NO, AMMOUNT, TRA_FEES, TRA_DATE, ATM, NOTE, CLIENT_ID,sum_ammount_new,sum_ammount_old,sum_ammount_sa,STATUS) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssddsssiddds", $type, $currency, $for_or_on, $sender_name, $receiver_name, $transfer_no,
                $ammount, $fees, $tra_date, $atm, $note, $client_id, $sum_ammount_new, $sum_ammount_old, $sum_ammount_sa, $status);
    } else {
        $sql = "INSERT INTO transaction (TYPE, CURRENCY, FOR_OR_ON, SENDER_NAME,RECEIVER_NAME, TRANSFER_NO, AMMOUNT, TRA_FEES, TRA_DATE, ATM, NOTE, CLIENT_ID,sum_ammount_new,sum_ammount_old,sum_ammount_sa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssddsssiddd", $type, $currency, $for_or_on, $sender_name, $receiver_name, $transfer_no,
                $ammount, $fees, $tra_date, $atm, $note, $client_id, $sum_ammount_new, $sum_ammount_old, $sum_ammount_sa);
 
    }
       
        if ($stmt->execute()) {

            echo json_encode(["success" => "تمت إضافة المعاملة بنجاح"]);
        } else {
            echo json_encode(["error" => "فشل في إدخال المعاملة: " . $stmt->error]);
            fwrite($error_file, "فشل في إدخال المعاملة: " . $stmt->error . "\r\n");
        }
   


    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "بيانات غير مكتملة أو طلب غير صالح"]);
}
?>
