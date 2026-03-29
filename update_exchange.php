<?php

// ini_set('display_errors', 1);
// error_reporting(E_ALL);
//include 'dbconn.php';
//include 'update_sum_ammounts.php';
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'dbconn.php';
    include 'insert_transaction_function.php';
    include 'delete_exchange_function.php';
    session_start();
    include 'csrf_token.php';
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed');
    }


    $id = isset($_POST['exchange_id']) ? intval($_POST['exchange_id']) : 0;
    $client_id = isset($_POST['client_id']) ? intval($_POST['client_id']) : 0;
    $conn->begin_transaction();
    $delete = delete_exchange($id, $client_id, $conn);
    date_default_timezone_set("Asia/Aden");
    // تنظيف وتحويل البيانات
    $type = trim($_POST["type"]);
    $currency = isset($_POST["currency"]) ? $_POST["currency"] : '';
    $for_or_on = isset($_POST["for-or-on"]) ? $_POST["for-or-on"] : '';

    $sender_name = isset($_POST["sender"]) ? $_POST["sender"] : '';
    $receiver_name = isset($_POST["receiver-name"]) ? $_POST["receiver-name"] : '';
    $transfer_no = trim($_POST["transfer-no"]);
    $ammount = floatval($_POST["ammount"]);
    $fees = isset($_POST['fees']) ? floatval($_POST["fees"]) : 0;
    $fees_income = isset($_POST['fees-income']) ? floatval($_POST["fees-income"]) : 0;
    //    $tra_date = trim($_POST["tra-date"]);
    $tra_date_raw = $_POST["date"];
    $tra_date = $tra_date_raw ? date("Y-m-d", strtotime($tra_date_raw)) : date("Y-m-d");

    $atm = trim($_POST["atm"]);
    $note = trim($_POST["note"]);
    $client_id = intval($_POST["client_id"]);
    //    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : "";
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
    if ($delete) {
        $done = insert_tranaction(true, $type, $currency, $for_or_on, $sender_name, $receiver_name, $transfer_no, $ammount, $fees, $fees_income, $tra_date, $atm, $note, $client_id, $status, $selectFrom, $selectTo, $price, $conn);
        if ($done === "توجد عملية سابقة بهذا الرقم بالفعل!") {
            $conn->rollback();
            echo json_encode(["error" => "توجد عملية سابقة بهذا الرقم بالفعل!"]);
        } elseif ($done === "الرصيد غير كافي") {
            $conn->rollback();
            echo json_encode(["success" => "الرصيد غير كافي"]);
        } elseif ($done === 'حدث خطأ أثناء إضافة الدخل') {
            $conn->rollback();
            echo json_encode(["error" => "حدث خطأ أثناء إضافة الدخل"]);
        } elseif ($done === true) {
            $conn->commit();
            echo json_encode(["success" => "تم التعديل بنجاح"]);
        } else {
            $conn->rollback();
            echo json_encode(["error" => "حدث خطأ أثناء تعديل العملية"]);
        }
    }
    $conn->autocommit(true);
    $conn->close();
    exit();
}
