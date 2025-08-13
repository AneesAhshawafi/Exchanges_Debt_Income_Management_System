

<?php

include 'dbconn.php';
include 'debt_update_sum_ammounts.php';
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {



    $id = $_POST['debt-id'];
    $description = $_POST['description'];
    $newCurrency = $_POST['currency'];
    $newForOrOn = $_POST['for-or-on'];
    $newAmmount = trim($_POST['ammount']);
    $tra_date_raw = $_POST["date"];
    $note = trim($_POST["note"]);
    $exchangesListData = json_decode($_POST['debts_list'], true);
    if (!$exchangesListData) {
        echo json_encode(["error" => "بيانات العمليات غير صالحة"]);
        echo 'بيانات العمليات غير صالح';
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM debt WHERE DEBT_ID = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
//        echo json_encode(["success" => "تم التعديل بنجاح"]);
    } else {
    }
    $result = $stmt->get_result();
    $oldData = $result->fetch_assoc();
    $date = $tra_date_raw ? date("Y-m-d", strtotime($tra_date_raw)) : date("Y-m-d", strtotime($oldData['DEBT_DATE']));
//    $exchangesListData[array_keys($oldData)]['FOR_OR_ON'] = $newForOrOn;
    $for_or_on = $oldData['FOR_OR_ON'];

    $oldAmmount = is_numeric($oldData['AMMOUNT']) ? $oldData['AMMOUNT'] : floatval($oldData['AMMOUNT']);
    $currency = $oldData['CURRENCY'];
    if ($currency != $newCurrency || $oldAmmount != $newAmmount || $for_or_on != $newForOrOn) {
        if ($currency == $newCurrency) {

            if ($for_or_on == $newForOrOn) {
                $ammount_differ = $oldAmmount - $newAmmount;
                update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ, $id);
            } else {
                $ammount_differ = $oldAmmount + $newAmmount;
                update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ, $id);
            }
        } else {
            if ($for_or_on == $newForOrOn) {
                $ammount_differ = $oldAmmount;
                update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ, $id);
                $ammount_differ = $newAmmount;
                if ($for_or_on == 'له') {

                    update_sum_ammount($newCurrency, 'عليه', $exchangesListData, $ammount_differ, $id);
                } else {
                    update_sum_ammount($newCurrency, 'له', $exchangesListData, $ammount_differ, $id);
                }
            } else {
                $ammount_differ = $oldAmmount;
                update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ, $id);

                $ammount_differ = $newAmmount;
                update_sum_ammount($newCurrency, $for_or_on, $exchangesListData, $ammount_differ, $id);
            }
        }
    }
    
           $sql = "UPDATE debt SET 
                DESCRIPTION = ?, 
                CURRENCY = ?, 
                FOR_OR_ON = ?,
                AMMOUNT = ?,
                DEBT_DATE = ?,
                NOTE = ?
            WHERE DEBT_ID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdssi", $description, $newCurrency, $newForOrOn, $newAmmount, $date, $note, $id);
 
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