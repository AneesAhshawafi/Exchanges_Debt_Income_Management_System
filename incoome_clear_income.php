<?php
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD']==='GET') {
    include 'dbconn.php';
    session_start();
    $user_id=$_SESSION['user_id'];
    $sql="UPDATE income SET sum_ammount_new = 0, sum_ammount_old = 0, sum_ammount_sa = 0 WHERE USER_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i",$user_id);
    if($stmt->execute()){
        echo json_encode(["messege" => "تم سحب كل الرصيد بنجاح"]);
    } else {
        echo json_encode(["error" => "حدث خطأ أثناء عميلة السحب"]);
    }
    $stmt->close();
    $conn->close();
    exit();
} else {
    echo json_encode(["error" => "طلب غير صالح"]);
}