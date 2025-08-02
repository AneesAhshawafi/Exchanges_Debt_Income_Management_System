

<?php

include 'dbconn.php';
include 'income_update_sum_ammounts.php';
$error_file = fopen("er_file.txt", "w");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {



    $id = $_POST['income-id'];
    $source = $_POST['source'];
    $newCurrency = $_POST['currency'];
    $newForOrOn = $_POST['for-or-on'];
    $newAmmount = trim($_POST['ammount']);
    $tra_date_raw = $_POST["date"];
    $note = trim($_POST["note"]);
    $exchangesListData = json_decode($_POST['income_list'], true);
    if (!$exchangesListData) {
        echo json_encode(["error" => "بيانات العمليات غير صالحة"]);
        echo 'بيانات العمليات غير صالح';
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM income WHERE INCM_ID = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        fwrite($error_file, ' تم استخراج الببيانات القديمة' . "\r\n");
//        echo json_encode(["success" => "تم التعديل بنجاح"]);
    } else {
        fwrite($error_file, ' لم يتم استخراج الببيانات القديمة' . "\r\n");
    }
    $result = $stmt->get_result();
    $oldData = $result->fetch_assoc();
    $date = $tra_date_raw ? date("Y-m-d", strtotime($tra_date_raw)) : date("Y-m-d", strtotime($oldData['INCM_DATE']));
//    $exchangesListData[array_keys($oldData)]['FOR_OR_ON'] = $newForOrOn;
    $for_or_on = $oldData['FOR_OR_ON'];

    $oldAmmount = is_numeric($oldData['AMMOUNT']) ? $oldData['AMMOUNT'] : floatval($oldData['AMMOUNT']);
    $currency = $oldData['CURRENCY'];
    if ($currency != $newCurrency || $oldAmmount != $newAmmount || $for_or_on != $newForOrOn) {
        if ($currency == $newCurrency) {

            if ($for_or_on == $newForOrOn) {
                $ammount_differ = $oldAmmount - $newAmmount;
                fwrite($error_file, "old ammount=" . $oldAmmount . "\r\n");
                fwrite($error_file, "new ammount=" . $newAmmount . "\r\n");
                fwrite($error_file, "differ ammount=" . $ammount_differ . "\r\n");
                update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ, $id, $error_file);
            } else {
                $ammount_differ = $oldAmmount + $newAmmount;
                fwrite($error_file, "old ammount=" . $oldAmmount . "\r\n");
                fwrite($error_file, "new ammount=" . $newAmmount . "\r\n");
                fwrite($error_file, "differ ammount=" . $ammount_differ . "\r\n");
                update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ, $id, $error_file);
            }
        } else {
            if ($for_or_on == $newForOrOn) {
                $ammount_differ = $oldAmmount;
                fwrite($error_file, "old ammount=" . $oldAmmount . "\r\n");
                fwrite($error_file, "new ammount=" . $newAmmount . "\r\n");
                fwrite($error_file, "differ ammount=" . $ammount_differ . "\r\n");
                update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ, $id, $error_file);
                $ammount_differ = $newAmmount;
                fwrite($error_file, "old ammount=" . $oldAmmount . "\r\n");
                fwrite($error_file, "new ammount=" . $newAmmount . "\r\n");
                fwrite($error_file, "differ ammount=" . $ammount_differ . "\r\n");
                if ($for_or_on == 'له') {

                    update_sum_ammount($newCurrency, 'عليه', $exchangesListData, $ammount_differ, $id, $error_file);
                } else {
                    update_sum_ammount($newCurrency, 'له', $exchangesListData, $ammount_differ, $id, $error_file);
                }
            } else {
                $ammount_differ = $oldAmmount;
                fwrite($error_file, "old ammount=" . $oldAmmount . "\r\n");
                fwrite($error_file, "new ammount=" . $newAmmount . "\r\n");
                fwrite($error_file, "differ ammount=" . $ammount_differ . "\r\n");
                update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ, $id, $error_file);

                $ammount_differ = $newAmmount;
                fwrite($error_file, "old ammount=" . $oldAmmount . "\r\n");
                fwrite($error_file, "new ammount=" . $newAmmount . "\r\n");
                fwrite($error_file, "differ ammount=" . $ammount_differ . "\r\n");
                update_sum_ammount($newCurrency, $for_or_on, $exchangesListData, $ammount_differ, $id, $error_file);
            }
        }
    }



    fwrite($error_file, "done with update sums");

    
           $sql = "UPDATE income SET 
                SOURCE = ?, 
                CURRENCY = ?, 
                FOR_OR_ON = ?,
                AMMOUNT = ?,
                INCM_DATE = ?,
                NOTE = ?
            WHERE INCM_ID = ?";

    $stmt = $conn->prepare($sql);
//    fwrite($error_file, "type:" . $type ."  receiver : ".$receiver."  transfer number : ".$transfer_no. "  currency: " . $newCurrency . "new ammount : " . $newAmmount . "\r\n");
    $stmt->bind_param("sssdssi", $source, $newCurrency, $newForOrOn, $newAmmount, $date, $note, $id);

    
    
 
    if ($stmt->execute()) {
//        echo json_encode(["success" => "تم التعديل بنجاح"]);

        echo 'تم التعديل';
    } else {
//        echo json_encode(["error" => "حدث خطأ اثناء تعديل الاجمالي"]);
        echo 'حدث خطا';
    }

//    $stmt->close();
//    $conn->close();
//    exit();
}
?>