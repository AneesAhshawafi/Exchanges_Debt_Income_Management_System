<?php

function insert_income($source, $currency, $for_or_on, $ammount, $tra_date, $note, $conn)
{
    //    include 'dbconn.php';
    require_once 'income_total_ammounts_calc.php';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $user_id = $_SESSION['user_id'];

    // تضمين الاتصال بقاعدة البيانات

    $sum_ammounts = calc_total_ammounts_income($user_id);
    $sum_ammount_new = $sum_ammounts[0];
    $sum_ammount_old = $sum_ammounts[1];
    $sum_ammount_sa = $sum_ammounts[2];

    if ($currency == "new" && $for_or_on == "له") {
        $sum_ammount_new += $ammount;
    } elseif ($currency == "old" && $for_or_on == "له") {
        $sum_ammount_old += $ammount;
    } else {
        if ($for_or_on == "له") {
            $sum_ammount_sa += $ammount;
        }
    }

    $sql = "INSERT INTO income (SOURCE, CURRENCY, FOR_OR_ON, AMMOUNT, INCM_DATE, NOTE, USER_ID,sum_ammount_new,sum_ammount_old,sum_ammount_sa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssdssiddd",
        $source,
        $currency,
        $for_or_on,
        $ammount,
        $tra_date,
        $note,
        $user_id,
        $sum_ammount_new,
        $sum_ammount_old,
        $sum_ammount_sa
    );
    $done = false;
    if ($stmt->execute()) {
        $done = true;
        //        echo json_encode(["messege" => "تمت إضافة الدخل بنجاح"]);
    } else {
        $done = false;
        //        echo json_encode(["error" => "فشل في إدخال الدخل: " . $stmt->error]);
    }



    $stmt->close();
    //    $conn->close();
    return $done;
}

?>