<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$error_file = fopen("er_file.txt", "w");
header("Content-Type: application/json");
include 'dbconn.php';
include 'total_ammounts_calc.php';
include 'calc_result_of_transfer_btwn_accounts.php';
// التحقق من أن الطلب هو POST وأن جميع البيانات موجودة
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    date_default_timezone_set("Asia/Aden");
    // تنظيف وتحويل البيانات
    $type = trim($_POST["type"]);
    $currency = isset($_POST["currency"]) ? $_POST["currency"] : '';
    $for_or_on = isset($_POST["for-or-on"]) ? $_POST["for-or-on"] : '';

    $sender_name = isset($_POST["sender-name"]) ? $_POST["sender-name"] : '';
    $receiver_name = isset($_POST["receiver-name"]) ? $_POST["receiver-name"] : '';
    $transfer_no = trim($_POST["transfer-no"]);
    $ammount = floatval($_POST["ammount"]);
    fwrite($error_file, 'ammount : ' . $ammount . "\r\n");
    $fees = isset($_POST['fees']) ? floatval($_POST["fees"]) :0;
    fwrite($error_file, 'fees : ' . $fees . "\r\n");
//    $tra_date = trim($_POST["tra-date"]);
    $tra_date_raw = $_POST["tra-date"];
    $tra_date = $tra_date_raw ? date("Y-m-d", strtotime($tra_date_raw)) : date("Y-m-d");

    $atm = trim($_POST["atm"]);
    $note = trim($_POST["note"]);
    $client_id = intval($_POST["client_id"]);
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    $selectFrom = isset($_POST['select-from']) ? $_POST['select-from'] : '';
    $selectTo = isset($_POST['select-to']) ? $_POST['select-to'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    fwrite($error_file, 'selectFrom : ' . $selectFrom . "\r\n");
    fwrite($error_file, 'selectTo : ' . $selectTo . "\r\n");
    fwrite($error_file, 'price : ' . $price . "\r\n");
    // تضمين الاتصال بقاعدة البيانات

    $sum_ammounts = calc_total_ammounts($client_id);
    $sum_ammount_new = $sum_ammounts[0];
    $sum_ammount_old = $sum_ammounts[1];
    $sum_ammount_sa = $sum_ammounts[2];
    fwrite($error_file, 'operation type : ' . $type . "\r\n");
    $transfered_ammount=0;
    if ($type != 'تحويل') {
        fwrite($error_file, 'حوالة ايداع : ' . "\r\n");
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
        fwrite($error_file, 'تحويل : ' . "\r\n");

//        ------------- minace from
        fwrite($error_file, 'ammount : ' . $ammount . '  before minace' . "\r\n");

        if ($selectFrom == 'new') {
            if ($ammount > $sum_ammount_new) {
                echo json_encode(["success" => "رصيدك غير كافي"]);
                $conn->close();
                exit();
            }
            fwrite($error_file, 'sum_ammount_new : ' . $sum_ammount_new . '  before minace' . "\r\n");
            $sum_ammount_new -= $ammount;
            fwrite($error_file, 'sum_ammount_new : ' . $sum_ammount_new . '  after minace' . "\r\n");
        } elseif ($selectFrom == 'old') {
            if ($ammount > $sum_ammount_old) {
                echo json_encode(["success" => "رصيدك غير كافي"]);
                $conn->close();
                exit();
            }
            fwrite($error_file, 'sum_ammount_old : ' . $sum_ammount_old . '  before minace' . "\r\n");
            $sum_ammount_old -= $ammount;
            fwrite($error_file, 'sum_ammount_old : ' . $sum_ammount_old . '  after minace' . "\r\n");
        } else {
            if ($ammount > $sum_ammount_sa) {
                echo json_encode(["success" => "رصيدك غير كافي"]);
                $conn->close();
                exit();
            }
            fwrite($error_file, 'sum_ammount_sa : ' . $sum_ammount_sa . '  before minace' . "\r\n");
            $sum_ammount_sa -= $ammount;
            fwrite($error_file, 'sum_ammount_sa : ' . $sum_ammount_sa . '  after minace' . "\r\n");
        }
        $transfered_ammount = get_result_of_transfer_btwn_accounts($selectFrom,$selectTo,$ammount,$price);
        fwrite($error_file, 'transfered_ammount : ' . $transfered_ammount . "\r\n");
       

//        ------------- add to
        fwrite($error_file, 'transfered_ammount : ' . $transfered_ammount . '  before add' . "\r\n");
        if ($selectTo == 'new') {
            fwrite($error_file, 'sum_ammount_new : ' . $sum_ammount_new . '  before add' . "\r\n");
            $sum_ammount_new += $transfered_ammount;
            fwrite($error_file, 'sum_ammount_new : ' . $sum_ammount_new . '  after add' . "\r\n");
        } elseif ($selectTo == 'old') {
            fwrite($error_file, 'sum_ammount_old : ' . $sum_ammount_old . '  before add' . "\r\n");
            $sum_ammount_old += $transfered_ammount;
            fwrite($error_file, 'sum_ammount_old : ' . $sum_ammount_old . '  after add' . "\r\n");
        } else {
            fwrite($error_file, 'sum_ammount_sa : ' . $sum_ammount_sa . '  before add' . "\r\n");
            $sum_ammount_sa += $transfered_ammount;
            fwrite($error_file, 'sum_ammount_sa : ' . $sum_ammount_sa . '  after add' . "\r\n");
        }
    }


    if (!$transfer_no) {
        $sql = "SELECT TRA_ID FROM transaction ORDER BY TRA_ID DESC LIMIT 1";
        $result = $conn->query($sql);
        if (mysqli_num_rows($result) > 0) {
            $result_tra_id_for_transfer_no = $result->fetch_assoc();
            $tra_id_for_transfer_no = $result_tra_id_for_transfer_no['TRA_ID'];
        } else {
            $tra_id_for_transfer_no = 0;
        }

        $transfer_no = 'BA-' . date("md", strtotime($tra_date)) . str_pad($tra_id_for_transfer_no + 1, 8, '0', STR_PAD_LEFT);
        // تجهيز الاستعلام
        $sql = "INSERT INTO transaction (TYPE, CURRENCY, FOR_OR_ON, SENDER_NAME,RECEIVER_NAME, TRANSFER_NO, AMMOUNT, TRA_FEES, TRA_DATE, ATM, NOTE, CLIENT_ID,sum_ammount_new,sum_ammount_old,sum_ammount_sa,STATUS,FROM_CURRENCY,PRICE,TO_CURRENCY,TRANSFERED_AMMOUNT) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssddsssidddssdsd", $type, $currency, $for_or_on, $sender_name, $receiver_name, $transfer_no,
                $ammount, $fees, $tra_date, $atm, $note, $client_id, $sum_ammount_new, $sum_ammount_old, $sum_ammount_sa, $status, $selectFrom, $price, $selectTo,$transfered_ammount);
    } else {
        $sql = "INSERT INTO transaction (TYPE, CURRENCY, FOR_OR_ON, SENDER_NAME,RECEIVER_NAME, TRANSFER_NO, AMMOUNT, TRA_FEES, TRA_DATE, ATM, NOTE, CLIENT_ID,sum_ammount_new,sum_ammount_old,sum_ammount_sa,FROM_CURRENCY,PRICE,TO_CURRENCY,TRANSFERED_AMMOUNT) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssddsssidddsdsd", $type, $currency, $for_or_on, $sender_name, $receiver_name, $transfer_no,
                $ammount, $fees, $tra_date, $atm, $note, $client_id, $sum_ammount_new, $sum_ammount_old, $sum_ammount_sa, $selectFrom, $price, $selectTo,$transfered_ammount);
    }

    if ($stmt->execute()) {

        echo json_encode(["success" => "تمت إضافة المعاملة بنجاح"]);
    } else {
        echo json_encode(["error" => "فشل في إدخال المعاملة: " . $stmt->error]);
        fwrite($error_file, "فشل في إدخال المعاملة: " . $stmt->error . "\r\n");
    }



    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "بيانات غير مكتملة أو طلب غير صالح"]);
}
?>