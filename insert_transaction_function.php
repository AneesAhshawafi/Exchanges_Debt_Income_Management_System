<?php

function insert_tranaction($isUpdate, $type, $currency, $for_or_on, $sender_name, $sender_phone, $receiver_name, $receiver_phone, $transfer_no, $ammount, $fees, $fees_income, $tra_date, $atm, $note, $client_id, $status, $selectFrom, $selectTo, $price, $conn) {
    $stmt=$conn->prepare('SELECT TRA_ID FROM transaction WHERE TRANSFER_NO = ?');
    $stmt->bind_param("s",$transfer_no);
    $stmt->execute();
    $result_transfer_no=$stmt->get_result();
    if (mysqli_num_rows($result_transfer_no) > 0) {
        $done="توجد عملية سابقة بهذا الرقم بالفعل!";
        return $done;
    }
    require_once 'total_ammounts_calc.php';
    if (!$isUpdate) {
        require_once 'calc_result_of_transfer_btwn_accounts.php';
    }

    $sum_ammounts = calc_total_ammounts($client_id, $conn);
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
        if ($selectFrom === 'new') {
            if ($ammount > $sum_ammount_new) {
                $done = "الرصيد غير كافي";

                return $done;
            }
            $sum_ammount_new -= $ammount;
        } elseif ($selectFrom === 'old') {
            if ($ammount > $sum_ammount_old) {
                $done = "الرصيد غير كافي";
                return $done;
            }
            $sum_ammount_old -= $ammount;
        } else {
            if ($ammount > $sum_ammount_sa) {
                $done = "الرصيد غير كافي";
                return $done;
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

    // تجهيز الاستعلام
    $sql = "INSERT INTO transaction (TYPE,TRANSFER_NO, CURRENCY, FOR_OR_ON, SENDER_NAME,SENDER_PHONE,RECEIVER_NAME,RECEIVER_PHONE, AMMOUNT, TRA_FEES, TRA_DATE, ATM, NOTE, CLIENT_ID,sum_ammount_new,sum_ammount_old,sum_ammount_sa,STATUS,FROM_CURRENCY,PRICE,TO_CURRENCY,TRANSFERED_AMMOUNT) VALUES (?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssddsssidddssdsd", $type, $transfer_no, $currency, $for_or_on, $sender_name, $sender_phone, $receiver_name, $receiver_phone,
            $ammount, $fees, $tra_date, $atm, $note, $client_id, $sum_ammount_new, $sum_ammount_old, $sum_ammount_sa, $status, $selectFrom, $price, $selectTo, $transfered_ammount);
    if ($stmt->execute()) {
        $tra_id = 0;
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
            $sql = "UPDATE transaction SET TRANSFER_NO = ? WHERE TRA_ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $transfer_no, $tra_id);
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
            if (!insert_income($source, $currency, 'له', $fees_income, $tra_date, '', $conn)) {
                $done = 'حدث خطأ أثناء إضافة الدخل';
            }
        }

        $done = true;
    } else {
        $done = false;
    }
    $stmt->close();
    return $done;
}
