<?php
include 'dbconn.php';
include 'update_sum_ammounts.php';
$error_file = fopen("er_file.txt", "w");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['exchange_id'];
    $type = $_POST['type'];
    $newCurrency = $_POST['currency'];
    $newForOrOn = $_POST['for-or-on'];
    $sender = trim($_POST['sender']);
    $receiver = trim($_POST['receiver-name']);
    $transfer_no = trim($_POST['transfer-no']);
    $newAmmount = trim($_POST['ammount']);
    $fees = $_POST['fees'];
    $tra_date_raw = $_POST["date"];
    $satus= $_POST['status'];
    $atm = $_POST['atm'];
    $note = trim($_POST["note"]);
    $exchangesListData = json_decode($_POST['exchanges_list'], true);
    if (!$exchangesListData) {
        echo json_encode(["error" => "بيانات العمليات غير صالحة"]);
        echo 'بيانات العمليات غير صالح';
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM transaction WHERE TRA_ID = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
//        echo json_encode(["success" => "تم التعديل بنجاح"]);
    } else {
    }
    $result = $stmt->get_result();
    $oldData = $result->fetch_assoc();
    $date = $tra_date_raw ? date("Y-m-d", strtotime($tra_date_raw)) : date("Y-m-d", strtotime($oldData['TRA_DATE']));
//    $exchangesListData[array_keys($oldData)]['FOR_OR_ON'] = $newForOrOn;
    $for_or_on = $oldData['FOR_OR_ON'];

    $oldAmmount = is_numeric($oldData['AMMOUNT']) ? $oldData['AMMOUNT'] : floatval($oldData['AMMOUNT']);
    $currency = $oldData['CURRENCY'];
    if ($currency != $newCurrency || $oldAmmount != $newAmmount || $for_or_on != $newForOrOn) {
        if ($currency == $newCurrency) {

            if ($for_or_on == $newForOrOn) {
                $ammount_differ = $oldAmmount - $newAmmount;
                update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ, $id, $error_file);
            } else {
                $ammount_differ = $oldAmmount + $newAmmount;
                update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ, $id, $error_file);
            }
        } else {
            if ($for_or_on == $newForOrOn) {
                $ammount_differ = $oldAmmount;
                update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ, $id, $error_file);
                $ammount_differ = $newAmmount;
                if ($for_or_on == 'له') {

                    update_sum_ammount($newCurrency, 'عليه', $exchangesListData, $ammount_differ, $id, $error_file);
                } else {
                    update_sum_ammount($newCurrency, 'له', $exchangesListData, $ammount_differ, $id, $error_file);
                }
            } else {
                $ammount_differ = $oldAmmount;
                update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ, $id, $error_file);

                $ammount_differ = $newAmmount;
                update_sum_ammount($newCurrency, $for_or_on, $exchangesListData, $ammount_differ, $id, $error_file);
            }
        }
    }
      if (!$transfer_no && $type == 'حوالة') {
        $sql = "SELECT TRA_ID FROM transaction ORDER BY TRA_ID DESC LIMIT 1";
        $result = $conn->query($sql);
        $result_tra_id_for_transfer_no = $result->fetch_assoc();
        $tra_id_for_transfer_no = $result_tra_id_for_transfer_no['TRA_ID'];

        $transfer_no = 'BA-' . date("md", strtotime($tra_date)) . str_pad($tra_id_for_transfer_no + 1, 8, '0', STR_PAD_LEFT);
    // تجهيز الاستعلام
       $sql = "UPDATE transaction SET 
                TYPE = ?, 
                CURRENCY = ?, 
                FOR_OR_ON = ?, 
                SENDER_NAME = ?,
                RECEIVER_NAME = ?,
                TRANSFER_NO = ?, 
                AMMOUNT = ?, 
                TRA_FEES = ?, 
                TRA_DATE = ?, 
                ATM = ?, 
                NOTE = ?,
                STATUS = ?
            WHERE TRA_ID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssddssssi", $type, $newCurrency, $newForOrOn, $sender, $receiver, $transfer_no, $newAmmount, $fees, $date, $atm, $note,$status, $id);
    } else {
           $sql = "UPDATE transaction SET 
                TYPE = ?, 
                CURRENCY = ?, 
                FOR_OR_ON = ?, 
                SENDER_NAME = ?,
                RECEIVER_NAME = ?,
                TRANSFER_NO = ?, 
                AMMOUNT = ?, 
                TRA_FEES = ?, 
                TRA_DATE = ?, 
                ATM = ?, 
                NOTE = ?
            WHERE TRA_ID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssddsssi", $type, $newCurrency, $newForOrOn, $sender, $receiver, $transfer_no, $newAmmount, $fees, $date, $atm, $note, $id);

    }
    
 
    if ($stmt->execute()) {
        echo json_encode(["success" => "تم التعديل بنجاح"]);

        echo 'تم التعديل';
    } else {
        echo json_encode(["error" => "حدث خطأ اثناء تعديل الاجمالي"]);
        echo 'حدث خطا';
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>