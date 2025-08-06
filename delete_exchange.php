
<?php

include 'dbconn.php';
include 'update_sum_ammounts.php';
include 'calc_result_of_transfer_btwn_accounts.php';
$error_file = fopen("eror_delet", "w");
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['TRA_ID'])) {
    $tra_id = intval($_POST['TRA_ID']);
    $exchangesListData = json_decode($_POST['exchanges_List'], true);

    //start get old transaction data
    $stmt = $conn->prepare("SELECT * FROM transaction WHERE TRA_ID = ?");
    $stmt->bind_param("i", $tra_id);
    if ($stmt->execute()) {
        fwrite($error_file, ' تم استخراج الببيانات القديمة' . "\r\n");
//        echo json_encode(["success" => "تم التعديل بنجاح"]);
    } else {
        fwrite($error_file, ' لم يتم استخراج الببيانات القديمة' . "\r\n");
    }
    $result = $stmt->get_result();
    $oldData = $result->fetch_assoc();
    //end get old transaction data

    $currency = $oldData['CURRENCY'];
    $for_or_on = $oldData['FOR_OR_ON'];
    if ($oldData['TYPE'] != 'تحويل') {
        $ammount_differ = $oldData['AMMOUNT'];
        update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ, $tra_id, $error_file);
    } else {
        $ammount = $oldData['AMMOUNT'];
        $selectFrom=$oldData['FROM_CURRENCY'];
        update_sum_ammount($selectFrom, 'عليه', $exchangesListData, $ammount, $tra_id, $error_file);
        $selectTo=$oldData['TO_CURRENCY'];
        $price=$oldData['PRICE'];
        $ammount=get_result_of_transfer_btwn_accounts($selectFrom,$selectTo,$ammount,$price);
        update_sum_ammount($selectTo, 'له', $exchangesListData, $ammount, $tra_id, $error_file);

        
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