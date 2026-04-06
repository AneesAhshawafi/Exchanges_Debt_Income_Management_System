<?php

function delete_exchange($tra_id, $client_id, $conn)
{
    //    require_once 'dbconn.php';
    require_once 'update_sum_ammounts.php';
    require_once 'calc_result_of_transfer_btwn_accounts.php';
    $sql = "SELECT TRA_ID , sum_ammount_new, sum_ammount_old,sum_ammount_sa FROM transaction WHERE CLIENT_ID = ? ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $exchangesListData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    //    $exchangesListData=$stmt->get_result();
//    $exchangesListData = json_decode($_POST['exchanges_List'], true);
    //start get old transaction data
    $stmt = $conn->prepare("SELECT * FROM transaction WHERE TRA_ID = ?");
    $stmt->bind_param("i", $tra_id);
    if ($stmt->execute()) {
        //        echo json_encode(["success" => "تم التعديل بنجاح"]);
    } else {

    }
    $result = $stmt->get_result();
    $oldData = $result->fetch_assoc();
    //end get old transaction data
    if ($oldData['TYPE'] === 'حوالة' && $oldData['FOR_OR_ON'] === 'عليه') {
        $stmt = $conn->prepare("DELETE FROM income WHERE SOURCE LIKE ?");
        $transfer_no = "%" . $oldData['TRANSFER_NO'] . "%";
        $stmt->bind_param("s", $transfer_no);
        $stmt->execute();
    }

    $currency = $oldData['CURRENCY'];
    $for_or_on = $oldData['FOR_OR_ON'];
    if ($oldData['TYPE'] != 'تحويل') {
        $ammount_differ = $oldData['AMMOUNT'] + $oldData['TRA_FEES'];
        update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ, $tra_id, $conn);
    } else {
        $ammount = $oldData['AMMOUNT'];
        $selectFrom = $oldData['FROM_CURRENCY'];
        update_sum_ammount($selectFrom, 'عليه', $exchangesListData, $ammount, $tra_id, $conn);
        $selectTo = $oldData['TO_CURRENCY'];
        $price = $oldData['PRICE'];
        $ammount = get_result_of_transfer_btwn_accounts($selectFrom, $selectTo, $ammount, $price);
        update_sum_ammount($selectTo, 'له', $exchangesListData, $ammount, $tra_id, $conn);
    }

    $stmt = $conn->prepare("DELETE FROM transaction WHERE TRA_ID = ?");
    $stmt->bind_param("i", $tra_id);
    if ($stmt->execute()) {
        $done = true;
    } else {
        $done = false;
    }

    $stmt->close();

    return $done;
}

?>