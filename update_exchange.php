<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include 'dbconn.php';
include 'update_sum_ammounts.php';
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // استلام المتغيرات من POST مع التحقق و trim
    $id = isset($_POST['exchange_id']) ? intval($_POST['exchange_id']) : 0;
    $type = isset($_POST['type']) ? trim($_POST['type']) : '';
    $newCurrency = isset($_POST['currency']) ? trim($_POST['currency']) : '';
    $newForOrOn = isset($_POST['for-or-on']) ? trim($_POST['for-or-on']) : '';
    $sender = isset($_POST['sender']) ? trim($_POST['sender']) : '';
    $receiver = isset($_POST['receiver-name']) ? trim($_POST['receiver-name']) : '';
    $transfer_no = isset($_POST['transfer-no']) ? trim($_POST['transfer-no']) : '';
    $newAmmount = isset($_POST['ammount']) ? floatval($_POST['ammount']) : 0;
    $fees = isset($_POST['fees']) ? floatval($_POST['fees']) : 0;
    $tra_date_raw = isset($_POST['date']) ? trim($_POST['date']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : "notset";
    $atm = isset($_POST['atm']) ? trim($_POST['atm']) : '';
    $note = isset($_POST['note']) ? trim($_POST['note']) : '';
    $client_id = isset($_POST['client_id']) ? intval($_POST['client_id']) : 0;

    // جلب بيانات العمليات الخاصة بالعميل
    $sql = "SELECT * FROM transaction WHERE CLIENT_ID = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["error" => "خطأ في تحضير الاستعلام: " . $conn->error]);
        exit();
    }
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $exchangesListData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if (!$exchangesListData) {
        echo json_encode(["error" => "بيانات العمليات غير صالحة"]);
        exit();
    }

    // جلب بيانات العملية القديمة
    $stmt = $conn->prepare("SELECT * FROM transaction WHERE TRA_ID = ?");
    if (!$stmt) {
        echo json_encode(["error" => "خطأ في تحضير الاستعلام: " . $conn->error]);
        exit();
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $oldData = $result->fetch_assoc();

    if (!$oldData) {
        echo json_encode(["error" => "العملية غير موجودة"]);
        exit();
    }

    // تحديد التاريخ
    $date = !empty($tra_date_raw) ? date("Y-m-d", strtotime($tra_date_raw)) : $oldData['TRA_DATE'];

    $for_or_on = $oldData['FOR_OR_ON'];
    $oldAmmount = floatval($oldData['AMMOUNT']);
    $currency = $oldData['CURRENCY'];

    // تحديث إجماليات العمليات إذا تغيرت
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

    // إنشاء TRANSFER_NO إذا كانت الحوالة بدون رقم
    if (!$transfer_no && $type == 'حوالة') {
        $sql = "SELECT TRA_ID FROM transaction ORDER BY TRA_ID DESC LIMIT 1";
        $result = $conn->query($sql);
        $last_id = $result->fetch_assoc()['TRA_ID'] ?? 0;

        $transfer_no = 'BA-' . date("md", strtotime($date)) . str_pad($last_id + 1, 8, '0', STR_PAD_LEFT);
    }

    // تجهيز استعلام التحديث
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
    if (!$stmt) {
        echo json_encode(["error" => "خطأ في تحضير الاستعلام: " . $conn->error]);
        exit();
    }

    $stmt->bind_param(
        "ssssssddssssi",
        $type,
        $newCurrency,
        $newForOrOn,
        $sender,
        $receiver,
        $transfer_no,
        $newAmmount,
        $fees,
        $date,
        $atm,
        $note,
        $status,
        $id
    );

    // تنفيذ الاستعلام والتحقق من النجاح
    if ($stmt->execute()) {
        echo json_encode(["success" => "تم التعديل بنجاح"]);
    } else {
        echo json_encode(["error" => "حدث خطأ أثناء تعديل العملية: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>
