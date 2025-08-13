<?php
// insert_transaction.php
header("Content-Type: application/json");
//header("Content-Type: application/json");

include 'dbconn.php';
include 'income_total_ammounts_calc.php';
// التحقق من أن الطلب هو POST وأن جميع البيانات موجودة
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    date_default_timezone_set("Asia/Aden");
    // تنظيف وتحويل البيانات
    $source = trim($_POST["source"]);
    $currency = trim($_POST["currency"]);
    $for_or_on = trim($_POST["for-or-on"]);
    $ammount = floatval($_POST["ammount"]);
    $tra_date_raw = $_POST["date"];
    $tra_date = $tra_date_raw ? date("Y-m-d", strtotime($tra_date_raw)) : date("Y-m-d");
    $note = trim($_POST["note"]);
    session_start();
    
    $user_id = $_SESSION['user_id'];
    

    // تضمين الاتصال بقاعدة البيانات

    $sum_ammounts = calc_total_ammounts($user_id);
    $sum_ammount_new = $sum_ammounts[0];
    $sum_ammount_old = $sum_ammounts[1];
    $sum_ammount_sa = $sum_ammounts[2];

    if ($currency == "new" && $for_or_on == "له") {
            $sum_ammount_new += $ammount;
    } elseif ($currency == "old"  && $for_or_on == "له") {
            $sum_ammount_old += $ammount;
    } else {
        if ($for_or_on == "له") {
            $sum_ammount_sa += $ammount;
        } 
    }

        $sql = "INSERT INTO income (SOURCE, CURRENCY, FOR_OR_ON, AMMOUNT, INCM_DATE, NOTE, USER_ID,sum_ammount_new,sum_ammount_old,sum_ammount_sa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdssiddd", $source, $currency, $for_or_on,
                $ammount,$tra_date,$note, $user_id, $sum_ammount_new, $sum_ammount_old, $sum_ammount_sa);
 
    
       
        if ($stmt->execute()) {

            echo json_encode(["success" => "تمت إضافة المعاملة بنجاح"]);
        } else {
            echo json_encode(["error" => "فشل في إدخال المعاملة: " . $stmt->error]);
        }
   


    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "بيانات غير مكتملة أو طلب غير صالح"]);
}
?>
