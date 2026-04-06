<?php
/**
 * API: تحديث حوالة عامة
 * Update Public Exchange
 * يدعم: CSRF، التحقق من الملكية
 * بدون type و for_or_on
 */

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // require_once '../session_config.php';
    session_start();
    require_once '../dbconn.php';
    require_once '../csrf_token.php';

    date_default_timezone_set("Asia/Aden");

    // التحقق من المصادقة
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["error" => "غير مصرح بالوصول"]);
        exit;
    }

    // التحقق من رمز CSRF
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        echo json_encode(["error" => "فشل التحقق من رمز الأمان CSRF"]);
        exit;
    }

    $user_id = intval($_SESSION['user_id']);
    $pe_id = isset($_POST['pe_id']) ? intval($_POST['pe_id']) : 0;

    if ($pe_id <= 0) {
        echo json_encode(["error" => "معرف الحوالة غير صالح"]);
        exit;
    }

    // التحقق من الملكية
    $checkStmt = $conn->prepare("SELECT PE_ID FROM public_exchange WHERE PE_ID = ? AND USER_ID = ?");
    $checkStmt->bind_param("ii", $pe_id, $user_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    if ($checkResult->num_rows === 0) {
        echo json_encode(["error" => "الحوالة غير موجودة أو لا تملك صلاحية تعديلها"]);
        $checkStmt->close();
        $conn->close();
        exit;
    }
    $checkStmt->close();

    // استقبال وتنظيف البيانات (بدون type و for_or_on)
    $currency       = isset($_POST['currency']) ? trim($_POST['currency']) : '';
    $status         = isset($_POST['status']) ? trim($_POST['status']) : '';
    $ammount        = isset($_POST['ammount']) ? floatval($_POST['ammount']) : 0;
    $sender_name    = isset($_POST['sender-name']) ? trim($_POST['sender-name']) : '';
    $sender_phone   = isset($_POST['sender-phone']) ? trim($_POST['sender-phone']) : '';
    $receiver_name  = isset($_POST['receiver-name']) ? trim($_POST['receiver-name']) : '';
    $receiver_phone = isset($_POST['receiver-phone']) ? trim($_POST['receiver-phone']) : '';
    $transfer_no    = isset($_POST['transfer-no']) ? trim($_POST['transfer-no']) : '';
    $fees           = isset($_POST['fees']) ? floatval($_POST['fees']) : 0;
    $fees_income    = isset($_POST['fees-income']) ? floatval($_POST['fees-income']) : 0;
    $tra_date_raw   = isset($_POST['tra-date']) ? $_POST['tra-date'] : '';
    $tra_date       = $tra_date_raw ? date("Y-m-d", strtotime($tra_date_raw)) : date("Y-m-d");
    $atm            = isset($_POST['atm']) ? trim($_POST['atm']) : '';
    $note           = isset($_POST['note']) ? trim($_POST['note']) : '';

    // التحقق من ربح الرسوم
    if ($fees_income > $fees) {
        echo json_encode(["error" => "ربح الرسوم يجب أن لا يتجاوز الرسوم الإجمالية"]);
        exit;
    }

    // تحديث البيانات (بدون TYPE و FOR_OR_ON)
    $sql = "UPDATE public_exchange SET 
                CURRENCY = ?, STATUS = ?,
                AMMOUNT = ?, TRA_DATE = ?, NOTE = ?,
                SENDER_NAME = ?, SENDER_PHONE = ?,
                RECEIVER_NAME = ?, RECEIVER_PHONE = ?,
                TRANSFER_NO = ?, TRA_FEES = ?, FEES_INCOME = ?, ATM = ?
            WHERE PE_ID = ? AND USER_ID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsssssssddsii",
        $currency, $status,
        $ammount, $tra_date, $note,
        $sender_name, $sender_phone,
        $receiver_name, $receiver_phone,
        $transfer_no, $fees, $fees_income, $atm,
        $pe_id, $user_id
    );

    if ($stmt->execute()) {
        echo json_encode(["success" => "تم تعديل الحوالة بنجاح"]);
    } else {
        echo json_encode(["error" => "فشل في تعديل الحوالة: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "طلب غير صالح"]);
}
