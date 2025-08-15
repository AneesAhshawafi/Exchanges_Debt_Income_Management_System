
<?php

include 'dbconn.php';
include 'update_sum_ammounts.php';
include 'calc_result_of_transfer_btwn_accounts.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['TRA_ID'])) {
    $tra_id = intval($_POST['TRA_ID']);
    $client_id= intval($_POST['client_id']);
    $sql="SELECT TRA_ID , sum_ammount_new, sum_ammount_old,sum_ammount_sa FROM transaction WHERE CLIENT_ID = ? ";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("i",$client_id);
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

    $currency = $oldData['CURRENCY'];
    $for_or_on = $oldData['FOR_OR_ON'];
    if ($oldData['TYPE'] != 'تحويل') {
        $ammount_differ = $oldData['AMMOUNT'];
        update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ, $tra_id);
    } else {
        $ammount = $oldData['AMMOUNT'];
        $selectFrom=$oldData['FROM_CURRENCY'];
        update_sum_ammount($selectFrom, 'عليه', $exchangesListData, $ammount, $tra_id);
        $selectTo=$oldData['TO_CURRENCY'];
        $price=$oldData['PRICE'];
        $ammount=get_result_of_transfer_btwn_accounts($selectFrom,$selectTo,$ammount,$price);
        update_sum_ammount($selectTo, 'له', $exchangesListData, $ammount, $tra_id);

        
    }

    $stmt = $conn->prepare("DELETE FROM transaction WHERE TRA_ID = ?");
    $stmt->bind_param("i", $tra_id);
    if ($stmt->execute()) {
        echo json_encode(["success" => "تم الحذف بنجاح"]);
    } else {
        echo json_encode(["error" => "حدث خطأ أثناء الحذف" . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo "طلب غير صالح";
}
?>