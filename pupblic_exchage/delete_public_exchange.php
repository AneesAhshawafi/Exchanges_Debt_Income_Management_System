<?php
/**
 * API: حذف حوالة عامة
 * Delete Public Exchange
 * يدعم: CSRF، التحقق من الملكية
 */

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // require_once '../session_config.php';
    session_start();
    require_once '../dbconn.php';
    require_once '../csrf_token.php';

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

    // التحقق من الملكية قبل الحذف
    $checkStmt = $conn->prepare("SELECT PE_ID FROM public_exchange WHERE PE_ID = ? AND USER_ID = ?");
    $checkStmt->bind_param("ii", $pe_id, $user_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 0) {
        echo json_encode(["error" => "الحوالة غير موجودة أو لا تملك صلاحية حذفها"]);
        $checkStmt->close();
        $conn->close();
        exit;
    }
    $checkStmt->close();

    // تنفيذ الحذف
    $stmt = $conn->prepare("DELETE FROM public_exchange WHERE PE_ID = ? AND USER_ID = ?");
    $stmt->bind_param("ii", $pe_id, $user_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => "تم حذف الحوالة بنجاح"]);
        } else {
            echo json_encode(["error" => "لم يتم العثور على الحوالة"]);
        }
    } else {
        echo json_encode(["error" => "فشل في حذف الحوالة: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "طلب غير صالح"]);
}
