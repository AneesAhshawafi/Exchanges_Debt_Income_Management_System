<?php
// API البحث عن حوالة برقمها
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["transfer_no"])) {
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

$user_id = $_SESSION['user_id'];
$transfer_no = trim($_POST["transfer_no"]);

if (empty($transfer_no)) {
    echo json_encode(["error" => "يرجى إدخال رقم الحوالة"]);
    exit;
}

// استعلام واحد مع JOIN لتجنب N+1 — يستفيد من UNIQUE INDEX على TRANSFER_NO
$stmt = $conn->prepare("
    SELECT t.TRA_ID, t.TYPE, t.TRANSFER_NO, t.AMMOUNT, t.CURRENCY, t.TRA_DATE, 
           t.STATUS, t.RECEIVED_AT, t.SENDER_NAME, t.SENDER_PHONE,
           t.RECEIVER_NAME, t.RECEIVER_PHONE, t.ATM, t.FOR_OR_ON, t.NOTE,
           c.CLIENT_NAME
    FROM transaction t
    LEFT JOIN client c ON t.CLIENT_ID = c.CLIENT_ID
    WHERE t.TRANSFER_NO = ?
      AND c.USER_ID = ?
      AND t.TYPE = 'حوالة'
    LIMIT 1
");
$stmt->bind_param("si", $transfer_no, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // تنسيق التاريخ
    $row['TRA_DATE'] = date("Y-m-d", strtotime($row['TRA_DATE']));
    if ($row['RECEIVED_AT']) {
        $row['RECEIVED_AT'] = date("Y-m-d H:i", strtotime($row['RECEIVED_AT']));
    }
    echo json_encode(["success" => true, "data" => $row]);
} else {
    echo json_encode(["success" => false, "message" => "لم يتم العثور على حوالة بهذا الرقم"]);
}

$stmt->close();
$conn->close();
