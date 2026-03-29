<?php
// API تأكيد استلام الحوالة — يملأ RECEIVED_AT تلقائياً
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["tra_id"])) {
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
$tra_id = intval($_POST["tra_id"]);

date_default_timezone_set("Asia/Aden");

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

// تحديث الحالة إلى "استلمت" وملء RECEIVED_AT تلقائياً
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
$conn->close();
