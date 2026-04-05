<?php
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'dbconn.php';
    include 'insert_transaction_function.php';
    include 'delete_exchange_function.php';
    session_start();
    include 'csrf_token.php';
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed');
    }


    $id = isset($_POST['exchange_id']) ? intval($_POST['exchange_id']) : 0;
    $client_id = isset($_POST['client_id']) ? intval($_POST['client_id']) : 0;
    $user_id = $_SESSION['user_id'] ?? 0;
    date_default_timezone_set("Asia/Aden");
    // تنظيف وتحويل البيانات
    $type = trim($_POST["type"]);
    $currency = isset($_POST["currency"]) ? $_POST["currency"] : '';
    $for_or_on = isset($_POST["for-or-on"]) ? $_POST["for-or-on"] : '';

    if ($for_or_on == 'عليه') {
        $sender_name = isset($_POST["client_name"]) ? $_POST["client_name"] : '';
        $sender_phone = isset($_POST["client_phone"]) ? $_POST["client_phone"] : '';
        $receiver_name = isset($_POST["edit-receiver-name"]) ? $_POST["edit-receiver-name"] : '';
        $receiver_phone = isset($_POST["edit-receiver-phone"]) ? $_POST["edit-receiver-phone"] : '';

    } else {
        $sender_name = isset($_POST["edit-sender-name"]) ? $_POST["edit-sender-name"] : '';
        $sender_phone = isset($_POST["edit-sender-phone"]) ? $_POST["edit-sender-phone"] : '';
        $receiver_name = isset($_POST["client_name"]) ? $_POST["client_name"] : '';
        $receiver_phone = isset($_POST["client_phone"]) ? $_POST["client_phone"] : '';

    }

    $transfer_no = trim($_POST["transfer-no"]);
    $ammount = floatval($_POST["ammount"]);
    $fees = isset($_POST['fees']) ? floatval($_POST["fees"]) : 0;
    $fees_income = isset($_POST['fees-income']) ? floatval($_POST["fees-income"]) : 0;
    //    $tra_date = trim($_POST["tra-date"]);
    $tra_date_raw = $_POST["date"];
    $tra_date = $tra_date_raw ? date("Y-m-d", strtotime($tra_date_raw)) : date("Y-m-d");

    $atm = trim($_POST["atm"]);
    $note = trim($_POST["note"]);
    $client_id = intval($_POST["client_id"]);
    //    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : "";
    $selectFrom = isset($_POST['select-from']) ? $_POST['select-from'] : '';
    $selectTo = isset($_POST['select-to']) ? $_POST['select-to'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';

    //delete the Exchange first
    $conn->begin_transaction();
    $delete = delete_exchange($id, $client_id, $conn);


    // التحقق من وجود العميل برقم الهاتف في حالة الإيداع (له)
    // إذا لم يوجد، يتوقف التنفيذ مع رسالة خطأ؛ وإلا يتم حفظ ID العميل في receiver_id
    $receiver_id = 0;
    if ($type === 'إيداع' && $for_or_on === 'عليه') {

        $sql = "SELECT CLIENT_ID FROM client WHERE PHONE = ? AND USER_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $receiver_phone, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $receiver_id = $row['CLIENT_ID'];
        } else {
            echo json_encode(["error" => "لا يمكن الإيداع، هذا العميل غير مسجل لدينا"]);
            exit;
        }

    }


    if ($type !== 'تحويل') {
        if ($type === 'إيداع') {
            $status = 'تمت';
        }
        if ($type === 'سحب') {
            $status = 'تمت';
            $for_or_on = 'عليه';
            $fees = 0;
            $fees_income = 0;
            $sender_name = '';
            $receiver_name = '';
            $sender_phone = '';
            $receiver_phone = '';
        }
        if ($type === 'إيداع' || $type === 'سحب' || $for_or_on === 'له') {
            $fees = 0;
            $fees_income = 0;
        }
        $selectFrom = '';
        $selectTo = '';
        $price = 0;
    } else {
        $fees = 0;
        $fees_income = 0;
        $for_or_on = '';
        $currency = '';
        $sender_name = '';
        $receiver_name = '';
        $status = 'تمت';
    }
    // تضمين الاتصال بقاعدة البيانات
    if ($delete) {
        $done = insert_tranaction(true, $type, $currency, $for_or_on, $sender_name, $sender_phone, $receiver_name, $receiver_phone, $transfer_no, $ammount, $fees, $fees_income, $tra_date, $atm, $note, $client_id, $status, $selectFrom, $selectTo, $price, $conn);
        if ($done === "توجد عملية سابقة بهذا الرقم بالفعل!") {
            $conn->rollback();
            echo json_encode(["error" => "توجد عملية سابقة بهذا الرقم بالفعل!"]);
        } elseif ($done === "الرصيد غير كافي") {
            // ❌ خطأ: الرصيد غير كافي يجب أن يُرسل كـ error وليس success
            $conn->rollback();
            echo json_encode(["error" => "الرصيد غير كافي"]);
        } elseif ($done === 'حدث خطأ أثناء إضافة الدخل') {
            $conn->rollback();
            echo json_encode(["error" => "حدث خطأ أثناء إضافة الدخل"]);
        } elseif ($done === true) {
            // حالة الإيداع (عليه): تتطلب إدخال عملية مرتبطة للمستلم
            if ($type == "إيداع" && $for_or_on == "عليه") {
                // حذف العملية المرتبطة القديمة إن وُجدت
                $transfer_no = 'BA-' . sprintf('%012d', intval(preg_replace('/[^0-9]/', '', $transfer_no)) + 1);
                $sql = "SELECT TRA_ID , TRA_DATE FROM transaction WHERE TRANSFER_NO = ?";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("خطأ بقاعدة البيانات: " . $conn->error);
                }
                $stmt->bind_param("s", $transfer_no);
                if (!$stmt->execute()) {
                    throw new Exception("خطأ أثناء الاستعلام: " . $stmt->error);
                }

                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $tra_date = $row['TRA_DATE'];
                    if (strtotime($tra_date) >= strtotime('2026-04-06')) {
                        if (!delete_exchange($row['TRA_ID'], $client_id, $conn)) {
                            throw new Exception("حدث خطأ أثناء حذف حوالة الإيداع المرتبطة.");
                        }
                    }
                }

                // إدخال العملية المرتبطة لحساب المستلم
                $client_id = $receiver_id;
                $done2 = insert_tranaction(null, $type, $currency, 'له', $sender_name, $sender_phone, $receiver_name, $receiver_phone, $transfer_no, $ammount, $fees, $fees_income, $tra_date, $atm, $note, $client_id, $status, $selectFrom, $selectTo, $price, $conn);
                if ($done2 === "توجد عملية سابقة بهذا الرقم بالفعل!") {
                    $conn->rollback();
                    echo json_encode(["error" => "توجد عملية سابقة بهذا الرقم بالفعل! (العملية المرتبطة)"]);
                } elseif ($done2 === "الرصيد غير كافي") {
                    $conn->rollback();
                    echo json_encode(["error" => "الرصيد غير كافي (العملية المرتبطة)"]);
                } elseif ($done2 === 'حدث خطأ أثناء إضافة الدخل') {
                    $conn->rollback();
                    echo json_encode(["error" => "حدث خطأ أثناء إضافة الدخل (العملية المرتبطة)"]);
                } elseif ($done2 === true) {
                    $conn->commit();
                    echo json_encode(["success" => "تم التعديل بنجاح (مع تحديث حساب المستلم)"]);
                } else {
                    $conn->rollback();
                    echo json_encode(["error" => "فشل في إدخال العملية المرتبطة"]);
                }
            } else {
                // حالة التعديل العادية (غير إيداع عليه)
                $conn->commit();
                echo json_encode(["success" => "تم التعديل بنجاح"]);
            }
        } else {
            $conn->rollback();
            echo json_encode(["error" => "حدث خطأ أثناء تعديل العملية"]);
        }
    }
    $conn->autocommit(true);
    $conn->close();
    exit();
}
