<?php
header("Content-Type: application/json");
include 'dbconn.php';
include 'debt_total_ammounts_calc.php';
// التحقق من أن الطلب هو POST وأن جميع البيانات موجودة
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    date_default_timezone_set("Asia/Aden");
    // تنظيف وتحويل البيانات
    $description = trim($_POST["description"]);
    $currency = trim($_POST["currency"]);
    $for_or_on = trim($_POST["for-or-on"]);
    $ammount = floatval($_POST["ammount"]);
    $tra_date_raw = $_POST["date"];
    $tra_date = $tra_date_raw ? date("Y-m-d", strtotime($tra_date_raw)) : date("Y-m-d");
    $note = trim($_POST["note"]);
    $client_id = intval($_POST["client_id"]);
    

    // تضمين الاتصال بقاعدة البيانات

    $sum_ammounts = calc_total_ammounts($client_id);
    $sum_ammount_new = $sum_ammounts[0];
    $sum_ammount_old = $sum_ammounts[1];
    $sum_ammount_sa = $sum_ammounts[2];

    if ($currency == "new") {
        if ($for_or_on == "له") {
            $sum_ammount_new -= $ammount;
        } else {
            $sum_ammount_new += $ammount;
        }
    } elseif ($currency == "old") {
        if ($for_or_on == "له") {
            $sum_ammount_old -= $ammount;
        } else {
            $sum_ammount_old += $ammount;
        }
    } else {
        if ($for_or_on == "له") {
            $sum_ammount_sa -= $ammount;
        } else {
            $sum_ammount_sa += $ammount;
        }
    }


    
        $sql = "INSERT INTO debt (DESCRIPTION, CURRENCY, FOR_OR_ON, AMMOUNT, DEBT_DATE, NOTE, CLIENT_ID,sum_ammount_new,sum_ammount_old,sum_ammount_sa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdssiddd", $description, $currency, $for_or_on,
                $ammount,$tra_date,$note, $client_id, $sum_ammount_new, $sum_ammount_old, $sum_ammount_sa);
 
    
       
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
