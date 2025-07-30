

<?php

include 'dbconn.php';
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {



    $id = $_POST['exchange_id'];
    $type = $_POST['type'];
    $currency = $_POST['currency'];
    $newForOrOn = $_POST['for-or-on'];
    $sender = trim($_POST['sender']);
    $receiver = trim($_POST['reciever-name']);
    $transfer_no = trim($_POST['transfer_no']);
    $newAmmount = trim($_POST['ammount']);
    $fees = $_POST['fees'];
    $tra_date_raw = $_POST["date"];
    $date = $tra_date_raw ? date("Y-m-d", strtotime($tra_date_raw)) : date("Y-m-d");
    $atm = $_POST['atm'];
    $note = trim($_POST["note"]);
    $exchangesListData = json_decode($_POST['exchanges_list'], true);
    $stmt = $conn->prepare("SELECT * FROM transaction WHERE TRA_ID = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
//        echo json_encode(["success" => "تم التعديل بنجاح"]);
    } else {
        echo json_encode(["error" => "حدث خطأ اثناء تعديل الاجمالي"]);
        exit();
        $stmt->close();
        $conn->close();
    }
    $result = $stmt->get_result();
    $oldData = $result->fetch_assoc();
//    $exchangesListData[array_keys($oldData)]['FOR_OR_ON'] = $newForOrOn;
    $for_or_on=$newForOrOn;
    $oldAmmount = is_numeric($oldData['AMMOUNT']) ? $oldData['AMMOUNT'] : floatval($oldData['AMMOUNT']);
    $ammount_differ = $oldAmmount - $newAmmount;

    if ($currency == "new") {
        if ($for_or_on == "له") {
            foreach ($exchangesListData as $traData) {
                if ($traData['TRA_ID'] >= $id) {
                    $sum_ammount_new = is_numeric($traData['sum_ammount_new']) ? $traData['sum_ammount_new'] : floatval($traData['sum_ammount_new']);
                    $sum_ammount_new -= $ammount_differ;
                    $stmt = $conn->prepare("UPDATE transaction SET sum_ammount_new =? WHERE TRA_ID = ?");
                    $stmt->bind_param("di", $sum_ammount_new,$traData['TRA_ID']);
                    if ($stmt->execute()) {
//                        echo json_encode(["success" => "تم التعديل بنجاح"]);
                    } else {
                        echo json_encode(["error" => "حدث خطأ اثناء تعديل الاجمالي"]);
                        exit();
                        $stmt->close();
                        $conn->close();
                    }
                }
            }
        } //end if new for
        else {
            foreach ($exchangesListData as $traData) {
                if ($traData['TRA_ID'] >= $id) {
                    $sum_ammount_new = is_numeric($traData['sum_ammount_new']) ? $traData['sum_ammount_new'] : floatval($traData['sum_ammount_new']);
                    $sum_ammount_new += $ammount_differ;
                    $stmt = $conn->prepare("UPDATE transaction SET sum_ammount_new =? WHERE TRA_ID = ?");
                    $stmt->bind_param("di", $sum_ammount_new,$traData['TRA_ID']);
                    if ($stmt->execute()) {
//                        echo json_encode(["success" => "تم التعديل بنجاح"]);
                    } else {
                        echo json_encode(["error" => "حدث خطأ اثناء تعديل الاجمالي"]);
                        exit();
                        $stmt->close();
                        $conn->close();
                    }
                }
            }
        }
    }//end if new 
    elseif ($currency == "old") {
        if ($for_or_on == "له") {

//                    $sum_ammount_old += $ammount_differ;
            foreach ($exchangesListData as $traData) {
                if ($traData['TRA_ID'] >= $id) {
                    $sum_ammount_old = is_numeric($traData['sum_ammount_old']) ? $traData['sum_ammount_old'] : floatval($traData['sum_ammount_old']);
                    $sum_ammount_old -= $oldAmmount;
                    $stmt = $conn->prepare("UPDATE transaction SET sum_ammount_old =? WHERE TRA_ID = ?");
                    $stmt->bind_param("di", $sum_ammount_old,$traData['TRA_ID']);
                    if ($stmt->execute()) {
//                        echo json_encode(["success" => "تم التعديل بنجاح"]);
                    } else {
                        echo json_encode(["error" => "حدث خطأ اثناء تعديل الاجمالي"]);
                        exit();
                        $stmt->close();
                        $conn->close();
                    }
                }
            }
        } //end elseif old for
        else {
//                    $sum_ammount_old -= $ammount_differ;
            foreach ($exchangesListData as $traData) {
                if ($traData['TRA_ID'] >= $id) {
                    $sum_ammount_old = is_numeric($traData['sum_ammount_old']) ? $traData['sum_ammount_old'] : floatval($traData['sum_ammount_old']);
                    $sum_ammount_old += $oldAmmount;
                    $stmt = $conn->prepare("UPDATE transaction SET sum_ammount_old =? WHERE TRA_ID = ?");
                    $stmt->bind_param("di", $sum_ammount_old,$traData['TRA_ID']);
                    if ($stmt->execute()) {
//                        echo json_encode(["success" => "تم التعديل بنجاح"]);
                    } else {
                        echo json_encode(["error" => "حدث خطأ اثناء تعديل الاجمالي"]);
                        exit();
                        $stmt->close();
                        $conn->close();
                    }
                }
            }
        }
    }//end elseif old
    else {
        if ($for_or_on == "له") {
//                    $sum_ammount_sa += $ammount_differ;
            foreach ($exchangesListData as $traData) {
                if ($traData['TRA_ID'] >= $id) {
                    $sum_ammount_sa = is_numeric($traData['sum_ammount_sa']) ? $traData['sum_ammount_sa'] : floatval($traData['sum_ammount_sa']);
                    $sum_ammount_sa -= $ammount_differ;
                    $stmt = $conn->prepare("UPDATE transaction SET sum_ammount_sa =? WHERE TRA_ID = ?");
                    $stmt->bind_param("di", $sum_ammount_sa,$traData['TRA_ID']);
                    if ($stmt->execute()) {
//                        echo json_encode(["success" => "تم التعديل بنجاح"]);
                    } else {
                        echo json_encode(["error" => "حدث خطأ اثناء تعديل الاجمالي"]);
                        exit();
                        $stmt->close();
                        $conn->close();
                    }
                }
            }
        }//end else sa for
        else {
//                    $sum_ammount_sa -= $ammount_differ;

            foreach ($exchangesListData as $traData) {
                if ($traData['TRA_ID'] >= $id) {
                    $sum_ammount_sa = is_numeric($traData['sum_ammount_sa']) ? $traData['sum_ammount_sa'] : floatval($traData['sum_ammount_sa']);
                    $sum_ammount_sa += $ammount_differ;
                    $stmt = $conn->prepare("UPDATE transaction SET sum_ammount_sa =? WHERE TRA_ID = ?");
                    $stmt->bind_param("di", $sum_ammount_sa,$traData['TRA_ID']);
                    if ($stmt->execute()) {
//                        echo json_encode(["success" => "تم التعديل بنجاح"]);
                    } else {
                        echo json_encode(["error" => "حدث خطأ اثناء تعديل الاجمالي"]);
                        exit();
                        $stmt->close();
                        $conn->close();
                    }
                }
            }
        }
    }//end else sa





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
    $stmt->bind_param("ssssssddsssi", $type, $currency, $newForOrOn, $sender, $receiver, $transfer_no, $newAmmount, $fees, $date, $atm, $note, $id);

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