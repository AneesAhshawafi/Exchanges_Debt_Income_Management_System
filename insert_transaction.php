<?php

header("Content-Type: application/json");
require_once 'dbconn.php';
require_once 'total_ammounts_calc.php';
require_once 'calc_result_of_transfer_btwn_accounts.php';
// التحقق من أن الطلب هو POST وأن جميع البيانات موجودة
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    date_default_timezone_set("Asia/Aden");
    // تنظيف وتحويل البيانات
    $type = trim($_POST["type"]);
    $currency = isset($_POST["currency"]) ? $_POST["currency"] : '';
    $for_or_on = isset($_POST["for-or-on"]) ? $_POST["for-or-on"] : '';

    $sender_name = isset($_POST["sender-name"]) ? $_POST["sender-name"] : '';
    $receiver_name = isset($_POST["receiver-name"]) ? $_POST["receiver-name"] : '';
    $transfer_no = trim($_POST["transfer-no"]);
    $ammount = floatval($_POST["ammount"]);
    $fees = isset($_POST['fees']) ? floatval($_POST["fees"]) : 0;
    $fees_income = isset($_POST['fees-income']) ? floatval($_POST["fees-income"]) : 0;
//    $tra_date = trim($_POST["tra-date"]);
    $tra_date_raw = $_POST["tra-date"];
    $tra_date = $tra_date_raw ? date("Y-m-d", strtotime($tra_date_raw)) : date("Y-m-d");

    $atm = trim($_POST["atm"]);
    $note = trim($_POST["note"]);
    $client_id = intval($_POST["client_id"]);
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    $selectFrom = isset($_POST['select-from']) ? $_POST['select-from'] : '';
    $selectTo = isset($_POST['select-to']) ? $_POST['select-to'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    // تضمين الاتصال بقاعدة البيانات

    $sum_ammounts = calc_total_ammounts($client_id);
    $sum_ammount_new = $sum_ammounts[0];
    $sum_ammount_old = $sum_ammounts[1];
    $sum_ammount_sa = $sum_ammounts[2];
    $transfered_ammount = 0;
    if ($type != 'تحويل') {
        if ($currency == "new") {
            if ($for_or_on == "له") {
                $sum_ammount_new += $ammount;
            } else {
                $sum_ammount_new -= $ammount;
                if ($fees) {
                    $sum_ammount_new -= $fees;
                }
            }
        } elseif ($currency == "old") {
            if ($for_or_on == "له") {
                $sum_ammount_old += $ammount;
            } else {
                $sum_ammount_old -= $ammount;
                if ($fees) {
                    $sum_ammount_old -= $fees;
                }
            }
        } else {
            if ($for_or_on == "له") {
                $sum_ammount_sa += $ammount;
            } else {
                $sum_ammount_sa -= $ammount;
                if ($fees) {
                    $sum_ammount_sa -= $fees;
                }
            }
        }
    } else {

//        ------------- minace from

        if ($selectFrom == 'new') {
            if ($ammount > $sum_ammount_new) {
                echo json_encode(["success" => "رصيدك غير كافي"]);
                $conn->close();
                exit();
            }
            $sum_ammount_new -= $ammount;
        } elseif ($selectFrom == 'old') {
            if ($ammount > $sum_ammount_old) {
                echo json_encode(["success" => "رصيدك غير كافي"]);
                $conn->close();
                exit();
            }
            $sum_ammount_old -= $ammount;
        } else {
            if ($ammount > $sum_ammount_sa) {
                echo json_encode(["success" => "رصيدك غير كافي"]);
                $conn->close();
                exit();
            }
            $sum_ammount_sa -= $ammount;
        }
        $transfered_ammount = get_result_of_transfer_btwn_accounts($selectFrom, $selectTo, $ammount, $price);

//        ------------- add to
        if ($selectTo == 'new') {
            $sum_ammount_new += $transfered_ammount;
        } elseif ($selectTo == 'old') {
            $sum_ammount_old += $transfered_ammount;
        } else {
            $sum_ammount_sa += $transfered_ammount;
        }
    }


    if (!$transfer_no) {
        // تجهيز الاستعلام
        $sql = "INSERT INTO transaction (TYPE, CURRENCY, FOR_OR_ON, SENDER_NAME,RECEIVER_NAME, AMMOUNT, TRA_FEES, TRA_DATE, ATM, NOTE, CLIENT_ID,sum_ammount_new,sum_ammount_old,sum_ammount_sa,STATUS,FROM_CURRENCY,PRICE,TO_CURRENCY,TRANSFERED_AMMOUNT) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssddsssidddssdsd", $type, $currency, $for_or_on, $sender_name, $receiver_name,
                $ammount, $fees, $tra_date, $atm, $note, $client_id, $sum_ammount_new, $sum_ammount_old, $sum_ammount_sa, $status, $selectFrom, $price, $selectTo, $transfered_ammount);
    } else {
        $sql = "INSERT INTO transaction (TYPE, CURRENCY, FOR_OR_ON, SENDER_NAME,RECEIVER_NAME, TRANSFER_NO, AMMOUNT, TRA_FEES, TRA_DATE, ATM, NOTE, CLIENT_ID,sum_ammount_new,sum_ammount_old,sum_ammount_sa,FROM_CURRENCY,PRICE,TO_CURRENCY,TRANSFERED_AMMOUNT) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssddsssidddsdsd", $type, $currency, $for_or_on, $sender_name, $receiver_name, $transfer_no,
                $ammount, $fees, $tra_date, $atm, $note, $client_id, $sum_ammount_new, $sum_ammount_old, $sum_ammount_sa, $selectFrom, $price, $selectTo, $transfered_ammount);
    }

    if ($stmt->execute()) {
        $tra_id=0;
        if (!$transfer_no) {
            
            $sql = "SELECT TRA_ID FROM transaction ORDER BY TRA_ID DESC LIMIT 1";
            $result = $conn->query($sql);
            if (mysqli_num_rows($result) > 0) {
                $result_tra_id_for_transfer_no = $result->fetch_assoc();
                $tra_id = $result_tra_id_for_transfer_no['TRA_ID'];
            } else {
                $tra_id = 0;
            }

            $transfer_no = 'BA-' . date("md", strtotime($tra_date)) . str_pad($tra_id, 8, '0', STR_PAD_LEFT);
            $sql="UPDATE transaction SET TRANSFER_NO = ? WHERE TRA_ID = ?";
            $stmt=$conn->prepare($sql);
            $stmt->bind_param("si",$transfer_no,$tra_id);
            $stmt->execute();
        }
        if ($type == 'حوالة' && $for_or_on == 'عليه') {
            require_once 'insert_income_function.php';
            $sql = "SELECT CLIENT_NAME FROM client WHERE CLIENT_ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $client_id);
            $stmt->execute();
            $client_name = $stmt->get_result()->fetch_assoc()['CLIENT_NAME'];
            $source = "حوالة على: $client_name ، رقم الحوالة: <br>$transfer_no";
            $done = insert_income($source, $currency, 'له', $fees_income, $tra_date, '', $conn);
        }
        echo json_encode(["success" => "تمت إضافة المعاملة بنجاح"]);
    } else {
        echo json_encode(["error" => "فشل في إدخال المعاملة: " . $stmt->error]);
    }


//
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "بيانات غير مكتملة أو طلب غير صالح"]);
}
?>