<?php
/**
 * API: جلب قائمة الحوالات العامة مع التقسيم (Pagination)
 * GET Public Exchanges List with Lazy Loading
 */

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // بدء الجلسة والتحقق من المصادقة
    // require_once '../session_config.php';
    session_start();
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["error" => "غير مصرح بالوصول"]);
        exit;
    }

    $user_id = intval($_SESSION['user_id']);
    $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 20;
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;

    // حماية القيم
    if ($limit < 1)
        $limit = 20;
    if ($limit > 100)
        $limit = 100;
    if ($offset < 0)
        $offset = 0;

    require_once '../dbconn.php';

    $sql = "SELECT * FROM public_exchange WHERE USER_ID = ? ORDER BY PE_ID DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $exchanges = [];
    while ($row = $result->fetch_assoc()) {
        $row['TRA_DATE'] = date("Y-m-d", strtotime($row['TRA_DATE']));
        $exchanges[] = $row;
    }

    echo json_encode($exchanges);
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "طلب غير صالح"]);
}
