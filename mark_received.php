<?php
// API تأكيد استلام الحوالة — يدعم الحوالات الخاصة والعامة
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "طلب غير صالح"]);
    exit;
}

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "يجب تسجيل الدخول أولاً"]);
    exit;
}

require_once 'dbconn.php';
include 'csrf_token.php';

if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
    echo json_encode(["error" => "فشل التحقق الأمني، يرجى تحديث الصفحة"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$search_type = isset($_POST['search_type']) ? trim($_POST['search_type']) : 'private';

date_default_timezone_set("Asia/Aden");

if ($search_type === 'public') {
    // ===== تأكيد استلام حوالة عامة =====
    if (!isset($_POST["pe_id"])) {
        echo json_encode(["error" => "طلب غير صالح"]);
        exit;
    }
    $pe_id = intval($_POST["pe_id"]);

    // التحقق من أن الحوالة تخص هذا المستخدم
    $stmt = $conn->prepare("
        SELECT PE_ID, STATUS 
        FROM public_exchange
        WHERE PE_ID = ? AND USER_ID = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $pe_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$row = $result->fetch_assoc()) {
        echo json_encode(["error" => "لم يتم العثور على هذه الحوالة"]);
        exit;
    }

    if ($row['STATUS'] === 'استلمت') {
        echo json_encode(["error" => "هذه الحوالة مستلمة بالفعل"]);
        exit;
    }

    $updateStmt = $conn->prepare("UPDATE public_exchange SET STATUS = 'استلمت', RECEIVED_AT = NOW() WHERE PE_ID = ?");
    $updateStmt->bind_param("i", $pe_id);

    if ($updateStmt->execute()) {
        $received_at = date("Y-m-d H:i");
        echo json_encode([
            "success" => "تم تأكيد استلام الحوالة بنجاح",
            "received_at" => $received_at
        ]);
    } else {
        echo json_encode(["error" => "حدث خطأ أثناء تحديث حالة الحوالة"]);
    }

    $updateStmt->close();

} else {
    // ===== تأكيد استلام حوالة خاصة (السلوك الأصلي) =====
    if (!isset($_POST["tra_id"])) {
        echo json_encode(["error" => "طلب غير صالح"]);
        exit;
    }
    $tra_id = intval($_POST["tra_id"]);

    // التحقق من أن الحوالة تخص عميل تابع لهذا المستخدم
    $stmt = $conn->prepare("
        SELECT t.TRA_ID, t.STATUS 
        FROM transaction t
        INNER JOIN client c ON t.CLIENT_ID = c.CLIENT_ID
        WHERE t.TRA_ID = ? AND c.USER_ID = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $tra_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$row = $result->fetch_assoc()) {
        echo json_encode(["error" => "لم يتم العثور على هذه الحوالة"]);
        exit;
    }

    if ($row['STATUS'] === 'استلمت') {
        echo json_encode(["error" => "هذه الحوالة مستلمة بالفعل"]);
        exit;
    }

    $updateStmt = $conn->prepare("UPDATE transaction SET STATUS = 'استلمت', RECEIVED_AT = NOW() WHERE TRA_ID = ?");
    $updateStmt->bind_param("i", $tra_id);

    if ($updateStmt->execute()) {
        $received_at = date("Y-m-d H:i");
        echo json_encode([
            "success" => "تم تأكيد استلام الحوالة بنجاح",
            "received_at" => $received_at
        ]);
    } else {
        echo json_encode(["error" => "حدث خطأ أثناء تحديث حالة الحوالة"]);
    }

    $updateStmt->close();
}

$conn->close();

