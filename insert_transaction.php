<?php

header("Content-Type: application/json");

// التحقق من أن الطلب هو POST وأن جميع البيانات موجودة
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once 'dbconn.php';
    require_once 'insert_transaction_function.php';
    date_default_timezone_set("Asia/Aden");


    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include 'csrf_token.php';
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed');
    }
    // 1. استقبال الرموز الأمنية (المفتاح ومعرف المستخدم)
    // افترضت أن لديك session للموظف، وإلا استخدم القادم من POST مؤقتاً

    $idempotency_key = isset($_POST["idempotency_key"]) ? trim($_POST["idempotency_key"]) : null;
    $user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : 0;
    $client_id = intval($_POST["client_id"]);

    if (!$idempotency_key) {
        echo json_encode(["error" => "رمز الأمان مفقود، يرجى تحديث الصفحة"]);
        exit;
    }



    // تنظيف وتحويل البيانات
    $type = trim($_POST["type"]);
    $currency = isset($_POST["currency"]) ? $_POST["currency"] : '';
    $for_or_on = isset($_POST["for-or-on"]) ? $_POST["for-or-on"] : '';
    if ($for_or_on == 'عليه') {
        $sender_name = isset($_POST["client_name"]) ? $_POST["client_name"] : '';
        $sender_phone = isset($_POST["client_phone"]) ? $_POST["client_phone"] : '';
        $receiver_name = isset($_POST["receiver-name"]) ? $_POST["receiver-name"] : '';
        $receiver_phone = isset($_POST["receiver-phone"]) ? $_POST["receiver-phone"] : '';

    } else {
        $sender_name = isset($_POST["sender-name"]) ? $_POST["sender-name"] : '';
        $sender_phone = isset($_POST["sender-phone"]) ? $_POST["sender-phone"] : '';
        $receiver_name = isset($_POST["client_name"]) ? $_POST["client_name"] : '';
        $receiver_phone = isset($_POST["client_phone"]) ? $_POST["client_phone"] : '';

    }

    $transfer_no = trim($_POST["transfer-no"]);
    $ammount = floatval($_POST["ammount"]);
    $fees = isset($_POST['fees']) ? floatval($_POST["fees"]) : 0;
    $fees_income = isset($_POST['fees-income']) ? floatval($_POST["fees-income"]) : 0;
    // التحقق من أن الرسوم لك لا تتجاوز الرسوم الإجمالي
    if ($fees_income > $fees) {
        echo json_encode(["error" => "الرسوم لك يجب ان لا يتجاوز الرسوم الاجمالي للحوالة"]);
        exit;
    }

    //    $tra_date = trim($_POST["tra-date"]);
    $tra_date_raw = $_POST["tra-date"];
    $tra_date = $tra_date_raw ? date("Y-m-d", strtotime($tra_date_raw)) : date("Y-m-d");

    $atm = trim($_POST["atm"]);
    $note = trim($_POST["note"]);
    // $client_id = intval($_POST["client_id"]);
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    $selectFrom = isset($_POST['select-from']) ? $_POST['select-from'] : '';
    $selectTo = isset($_POST['select-to']) ? $_POST['select-to'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';

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
    // معالجة الحقول بناءً على نوع العملية

    if ($type !== 'تحويل') {
        if ($type === 'إيداع') {
            $status = 'تمت';
        }
        if ($type === 'إيداع' || $for_or_on === 'له') {
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


    //التحقق من تكرار العملية خلال الـ 24 ساعة الماضية 
    // بناءً على الحقول الرئيسية 
    $data_to_hash = $type . $currency . $for_or_on . $sender_name . $sender_phone . $receiver_name . $receiver_phone . $ammount .
        $fees . $fees_income . $atm . $client_id . $user_id .
        $status . $selectFrom . $selectTo . $price;

    $data_hash = md5($data_to_hash);

    // 2. التحقق من طلب التأكيد (Force Save)
    $force_save = isset($_POST['force_save']) && $_POST['force_save'] === 'true';

    if (!$force_save) {
        // البحث عن آخر عملية مطابقة تماماً باستخدام الـ data_hash
        $stmtCheck = $conn->prepare("SELECT created_at FROM idempotency_keys 
                                 WHERE data_hash = ? AND status = 'success' 
                                 ORDER BY created_at DESC LIMIT 1");
        $stmtCheck->bind_param("s", $data_hash);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();

        if ($row = $result->fetch_assoc()) {
            $full_time = $row['created_at']; // التاريخ والوقت من قاعدة البيانات
            $date_only = date("Y-m-d", strtotime($full_time));
            $time_only = date("H:i:s", strtotime($full_time));

            echo json_encode([
                "is_duplicate" => true,
                "message" => "هذه العملية قد تم تنفيذها مسبقاً بتاريخ $date_only الساعة $time_only. هل تريد تنفيذها مرة اخرى؟"
            ]);
            exit;
        }
    } //نهاية التحقق من التكرار

    // تضمين الاتصال بقاعدة البيانات
    $conn->begin_transaction();

    //here
    try {
        // 1. محاولة حجز مفتاح الأمان أولاً
        // $stmtKey = $conn->prepare("INSERT INTO idempotency_keys (request_key, user_id, client_id, status) VALUES (?, ?, ?, 'pending')");
        // $stmtKey->bind_param("sii", $idempotency_key, $user_id, $client_id);
        // إدخال مفتاح الأمان مع البصمة
        $conn->query("SET time_zone = '+03:00'");
        $stmtKey = $conn->prepare("INSERT INTO idempotency_keys (request_key, user_id, client_id, data_hash, status,created_at) VALUES (?, ?, ?, ?, 'pending',NOW())");
        $stmtKey->bind_param("siis", $idempotency_key, $user_id, $client_id, $data_hash);
        if (!$stmtKey->execute()) {
            if ($conn->errno === 1062) { // تكرار المفتاح
                $conn->rollback();
                echo json_encode(["error" => "هذه العملية تم إرسالها مسبقاً!"]);
                exit;
            }
        }

        // 2. استدعاء الدالة المالية (استخدام متغيراتك الأصلية)
        $done = insert_tranaction(null, $type, $currency, $for_or_on, $sender_name, $sender_phone, $receiver_name, $receiver_phone, $transfer_no, $ammount, $fees, $fees_income, $tra_date, $atm, $note, $client_id, $status, $selectFrom, $selectTo, $price, $conn);

        if ($done === "توجد عملية سابقة بهذا الرقم بالفعل!") {
            $conn->rollback();
            echo json_encode(["error" => "توجد عملية سابقة بهذا الرقم بالفعل!"]);
        } elseif ($done === "الرصيد غير كافي") {
            $conn->rollback();
            echo json_encode(["error" => "الرصيد غير كافي"]);
        } elseif ($done === 'حدث خطأ أثناء إضافة الدخل') {
            $conn->rollback();
            echo json_encode(["error" => "حدث خطأ أثناء إضافة الدخل"]);
        } elseif ($done === true) {
            if ($type === 'إيداع' and $for_or_on === 'عليه') {
                $client_id = $receiver_id;
                $done = insert_tranaction(null, $type, $currency, 'له', $sender_name, $sender_phone, $receiver_name, $receiver_phone, $transfer_no, $ammount, $fees, $fees_income, $tra_date, $atm, $note, $client_id, $status, $selectFrom, $selectTo, $price, $conn);
                if ($done === "توجد عملية سابقة بهذا الرقم بالفعل!") {
                    $conn->rollback();
                    echo json_encode(["error" => "توجد عملية سابقة بهذا الرقم بالفعل!"]);
                } elseif ($done === "الرصيد غير كافي") {
                    $conn->rollback();
                    echo json_encode(["error" => "الرصيد غير كافي"]);
                } elseif ($done === 'حدث خطأ أثناء إضافة الدخل') {
                    $conn->rollback();
                    echo json_encode(["error" => "حدث خطأ أثناء إضافة الدخل"]);
                } elseif ($done === true) {
                    // تحديث حالة مفتاح الأمان قبل الـ commit
                    $updateStmt = $conn->prepare("UPDATE idempotency_keys SET status = 'success' WHERE request_key = ?");
                    $updateStmt->bind_param("s", $idempotency_key);
                    $updateStmt->execute();
                    $conn->commit();
                    echo json_encode(["success" => "تمت إضافة المعاملة بنجاح"]);
                } else {
                    $conn->rollback();
                    echo json_encode(["error" => "فشل في إدخال المعاملة"]);
                }
            } else {
                // تحديث حالة مفتاح الأمان قبل الـ commit
                $updateStmt = $conn->prepare("UPDATE idempotency_keys SET status = 'success' WHERE request_key = ?");
                $updateStmt->bind_param("s", $idempotency_key);
                $updateStmt->execute();
                $conn->commit();
                echo json_encode(["success" => "تمت إضافة المعاملة بنجاح"]);
            }
        } else {
            $conn->rollback();
            echo json_encode(["error" => "فشل في إدخال المعاملة"]);
        }
        // 2. استدعاء الدالة المالية (استخدام متغيراتك الأصلية)

    } catch (mysqli_sql_exception $e) {
        // 3. هنا يتم اصطياد أخطاء قاعدة البيانات فور حدوثها
        $conn->rollback();

        // فحص كود الخطأ مباشرة من الكائن $e
        if ($e->getCode() === 1062) {
            echo json_encode(["error" => "هذه العملية تم إرسالها مسبقاً!"]);
        } else {
            echo json_encode(["error" => "خطأ في قاعدة البيانات: " . $e->getMessage()]);
        }
    } catch (Exception $e) {
        // لأي أخطاء أخرى غير قاعدة البيانات
        $conn->rollback();
        echo json_encode(["error" => "خطأ عام: " . $e->getMessage()]);
    }
    //end here
    $conn->autocommit(true);
    $conn->close();
} else {
    echo json_encode(["error" => "بيانات غير مكتملة أو طلب غير صالح"]);
}
