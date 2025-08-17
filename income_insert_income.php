<?php

// insert_transaction.php
header("Content-Type: application/json");
require_once 'dbconn.php';
require_once 'insert_income_function.php';
// التحقق من أن الطلب هو POST وأن جميع البيانات موجودة
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    date_default_timezone_set("Asia/Aden");
    // تنظيف وتحويل البيانات
    $source = trim($_POST["source"]);
    $currency = trim($_POST["currency"]);
    $for_or_on = trim($_POST["for-or-on"]);
    $ammount = floatval($_POST["ammount"]);
    $tra_date_raw = $_POST["date"];
    $tra_date = $tra_date_raw ? date("Y-m-d", strtotime($tra_date_raw)) : date("Y-m-d");
    $note = trim($_POST["note"]);
    
    ;
    
    if (insert_income($source, $currency, $for_or_on, $ammount, $tra_date, $note,$conn)) {

        echo json_encode(["messege" => "تمت إضافة الدخل بنجاح"]);
    } else {
        echo json_encode(["error" => "فشل في إدخال الدخل: " . $stmt->error]);
    }
    $conn->close();
    
} else {
    echo json_encode(["error" => "بيانات غير مكتملة أو طلب غير صالح"]);
}
?>
