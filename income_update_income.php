<?php
include 'dbconn.php';
include 'income_update_sum_ammounts.php';
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {



    $id = $_POST['income-id'];
    $source = $_POST['source'];
    $newCurrency = $_POST['currency'];
    $newForOrOn = $_POST['for-or-on'];
    $newAmmount = trim($_POST['ammount']);
    $tra_date_raw = $_POST["date"];
    $note = trim($_POST["note"]);

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
    if (!$exchangesListData) {
        echo json_encode(["error" => "بيانات العمليات غير صالحة"]);
        echo 'بيانات العمليات غير صالح';
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM income WHERE INCM_ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $oldData = $result->fetch_assoc();
    $date = $tra_date_raw ? date("Y-m-d", strtotime($tra_date_raw)) : date("Y-m-d", strtotime($oldData['INCM_DATE']));
    $for_or_on = $oldData['FOR_OR_ON'];

    $oldAmmount = is_numeric($oldData['AMMOUNT']) ? $oldData['AMMOUNT'] : floatval($oldData['AMMOUNT']);
    $currency = $oldData['CURRENCY'];
    if ($currency != $newCurrency || $oldAmmount != $newAmmount || $for_or_on != $newForOrOn) {
        if ($currency == $newCurrency) {
            if ($for_or_on == $newForOrOn) {
                if ($for_or_on == 'له') {
                    $ammount_differ = $oldAmmount - $newAmmount;
                    update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ, $id);
                }
            } else {
                if ($newForOrOn == 'عليه') {
                    $ammount_differ = $oldAmmount;
                    update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ, $id);
                } else {
                    $ammount_differ = $newAmmount;
                    update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ, $id);
                }
            }
        } else {
            if ($for_or_on == $newForOrOn) {
                if ($for_or_on == 'له') {
                    $ammount_differ = $oldAmmount;
                    update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ, $id);
                    $ammount_differ = $newAmmount;
                    update_sum_ammount($newCurrency, 'عليه', $exchangesListData, $ammount_differ, $id);
                }
            } else {
                if ($for_or_on == 'له') {

                    $ammount_differ = $oldAmmount;
                    update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ, $id);
                }
                if ($newForOrOn == 'له') {

                    $ammount_differ = $newAmmount;
                    update_sum_ammount($newCurrency, 'عليه', $exchangesListData, $ammount_differ, $id);
                }
            }
        }
    }




    $sql = "UPDATE income SET 
                SOURCE = ?, 
                CURRENCY = ?, 
                FOR_OR_ON = ?,
                AMMOUNT = ?,
                INCM_DATE = ?,
                NOTE = ?
            WHERE INCM_ID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdssi", $source, $newCurrency, $newForOrOn, $newAmmount, $date, $note, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => "تم التعديل بنجاح"]);
    } else {
        echo json_encode(["error" => "حدث خطأ اثناء تعديل الاجمالي"]);
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>