
<?php

include 'dbconn.php';
include 'income_update_sum_ammounts.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['INCM_ID'])) {
    $tra_id = intval($_POST['INCM_ID']);
    
    // جلب بيانات العمليات الخاصة بالعميل
    session_start();
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM income WHERE USER_ID = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["error" => "خطأ في تحضير الاستعلام: " . $conn->error]);
        exit();
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $exchangesListData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

//    $exchangesListData = json_decode($_POST['income_list'], true);
    //start get old transaction data
    $stmt = $conn->prepare("SELECT * FROM income WHERE INCM_ID = ? and USER_ID = ?");
    $stmt->bind_param("ii", $tra_id,$user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $oldData = $result->fetch_assoc();

    //end get old transaction data
    $currency = $oldData['CURRENCY'];
    $for_or_on = $oldData['FOR_OR_ON'];
    $ammount_differ = $oldData['AMMOUNT'];
    update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ, $tra_id);
    $stmt = $conn->prepare("DELETE FROM income WHERE INCM_ID = ? and USER_ID = ?");
    $stmt->bind_param("ii", $tra_id,$user_id);
    if ($stmt->execute()) {
        echo json_encode(["success" => "تم الحذف بنجاح"]);
    } else {
        echo json_encode(["error" => "حدث خطأ أثناء الحذف" . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo "طلب غير صالح";
}
?>