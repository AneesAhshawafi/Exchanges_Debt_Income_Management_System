<?php

header("Content-Type: application/json");

// التحقق من أن الطلب هو POST وأن جميع البيانات موجودة
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once 'dbconn.php';
    require_once 'insert_transaction_function.php';
    date_default_timezone_set("Asia/Aden");
    // تنظيف وتحويل البيانات
    $type = trim($_POST["type"]);
    $currency = isset($_POST["currency"]) ? $_POST["currency"] : '';
    $for_or_on = isset($_POST["for-or-on"]) ? $_POST["for-or-on"] : '';

    $sender_name = isset($_POST["sender-name"]) ? $_POST["sender-name"] : '';
    $receiver_name = isset($_POST["receiver-name"]) ? $_POST["receiver-name"] : '';
    $transfer_no = trim($_POST["transfer-no"]);
    $ammount = floatval($_POST["ammount"]);
    $fees = isset($_POST['fees']) ? floatval($_POST["fees"]) : 0;
    $fees_income = isset($_POST['fees-income']) ? floatval($_POST["fees-income"]) : 0;
//    $tra_date = trim($_POST["tra-date"]);
    $tra_date_raw = $_POST["tra-date"];
    $tra_date = $tra_date_raw ? date("Y-m-d", strtotime($tra_date_raw)) : date("Y-m-d");

    $atm = trim($_POST["atm"]);
    $note = trim($_POST["note"]);
    $client_id = intval($_POST["client_id"]);
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    $selectFrom = isset($_POST['select-from']) ? $_POST['select-from'] : '';
    $selectTo = isset($_POST['select-to']) ? $_POST['select-to'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    if ($type !== 'تحويل') {
        if ($type === 'إيداع') {
            $status = 'تمت';
        }
        if ($type === 'إيداع' || $for_or_on === 'له') {
            $fees = 0;
            $fees_income = 0;
        }
        $selectFrom = '';
        $selectTo = '';
        $price = 0;
    } else {
        $fees = 0;
        $fees_income = 0;
        $for_or_on = '';
        $currency = '';
        $sender_name = '';
        $receiver_name = '';
        $status = 'تمت';
    }

    // تضمين الاتصال بقاعدة البيانات
    $conn->begin_transaction();
    $done = insert_tranaction(null, $type, $currency, $for_or_on, $sender_name, $receiver_name, $transfer_no, $ammount, $fees, $fees_income, $tra_date, $atm, $note, $client_id, $status, $selectFrom, $selectTo, $price, $conn);
    if ($done === "الرصيد غير كافي") {
        $conn->rollback();
        echo json_encode(["success" => "الرصيد غير كافي"]);
    } elseif ($done === 'حدث خطأ أثناء إضافة الدخل') {
        $conn->rollback();
        echo json_encode(["error" => "حدث خطأ أثناء إضافة الدخل"]);
    } elseif ($done === true) {
        $conn->commit();
        echo json_encode(["success" => "تمت إضافة المعاملة بنجاح"]);
    } else {
        $conn->rollback();
        echo json_encode(["error" => "فشل في إدخال المعاملة"]);
    }
    $conn->autocommit(true);
    $conn->close();
} else {
    echo json_encode(["error" => "بيانات غير مكتملة أو طلب غير صالح"]);
}
?>