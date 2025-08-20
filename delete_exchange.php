<?php

header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['TRA_ID'])) {
    require_once 'dbconn.php';
    include 'delete_exchange_function.php';

    $tra_id = intval($_POST['TRA_ID']);
    $client_id = intval($_POST['client_id']);
    $conn->begin_transaction();
    if (delete_exchange($tra_id, $client_id, $conn)) {
        $conn->commit();
        
        echo json_encode(["success" => "تم الحذف بنجاح"]);
    } else {
        $conn->rollback();
        echo json_encode(["error" => "حدث خطأ أثناء الحذف" . $stmt->error]);
    }
    $conn->autocommit(true);
    $conn->close();
} else {
    echo "طلب غير صالح";
}
?>