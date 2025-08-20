
<?php

include 'dbconn.php';
include 'income_delete_income_function.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['INCM_ID'])) {
    $tra_id = intval($_POST['INCM_ID']);
    $conn->begin_transaction();
    if (delete_income($tra_id, $conn)) {
        $conn->commit();
        echo json_encode(["success" => "تم الحذف بنجاح"]);
    } else {
        $conn->rollback();
        echo json_encode(["error" => "حدث خطأ أثناء الحذف"]);
    }
    $conn->autocommit(true);
    $conn->close();
} else {
    echo "طلب غير صالح";
}
?>