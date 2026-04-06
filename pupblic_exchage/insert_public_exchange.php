<?php
/**
 * API: إدخال حوالة عامة جديدة
 * Insert New Public Exchange
 * يدعم: CSRF، مفتاح عدم التكرار (Idempotency)، كشف التكرار
 * النوع ثابت: حوالة فقط (لا يوجد حقل type أو for_or_on)
 */

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // require_once '../session_config.php';
    session_start();
    require_once '../dbconn.php';
    require_once '../csrf_token.php';

    date_default_timezone_set("Asia/Aden");

    // التحقق من المصادقة
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["error" => "غير مصرح بالوصول، يرجى تسجيل الدخول"]);
        exit;
    }

    // التحقق من رمز CSRF
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        echo json_encode(["error" => "فشل التحقق من رمز الأمان CSRF"]);
        exit;
    }

    // التحقق من مفتاح عدم التكرار
    $idempotency_key = isset($_POST["idempotency_key"]) ? trim($_POST["idempotency_key"]) : null;
    if (!$idempotency_key) {
        echo json_encode(["error" => "رمز الأمان مفقود، يرجى تحديث الصفحة"]);
        exit;
    }

    $user_id = intval($_SESSION['user_id']);

    // استقبال وتنظيف البيانات (بدون type و for_or_on)
    $currency       = isset($_POST['currency']) ? trim($_POST['currency']) : '';
    $status         = isset($_POST['status']) ? trim($_POST['status']) : '';
    $ammount        = isset($_POST['ammount']) ? floatval($_POST['ammount']) : 0;
    $sender_name    = isset($_POST['sender-name']) ? trim($_POST['sender-name']) : '';
    $sender_phone   = isset($_POST['sender-phone']) ? trim($_POST['sender-phone']) : '';
    $receiver_name  = isset($_POST['receiver-name']) ? trim($_POST['receiver-name']) : '';
    $receiver_phone = isset($_POST['receiver-phone']) ? trim($_POST['receiver-phone']) : '';
    $transfer_no    = isset($_POST['transfer-no']) ? trim($_POST['transfer-no']) : '';
    $fees           = isset($_POST['fees']) ? floatval($_POST['fees']) : 0;
    $fees_income    = isset($_POST['fees-income']) ? floatval($_POST['fees-income']) : 0;
    $tra_date_raw   = isset($_POST['tra-date']) ? $_POST['tra-date'] : '';
    $tra_date       = $tra_date_raw ? date("Y-m-d", strtotime($tra_date_raw)) : date("Y-m-d");
    $atm            = isset($_POST['atm']) ? trim($_POST['atm']) : '';
    $note           = isset($_POST['note']) ? trim($_POST['note']) : '';

    // التحقق من أن ربح الرسوم لا يتجاوز الرسوم الإجمالية
    if ($fees_income > $fees) {
        echo json_encode(["error" => "ربح الرسوم يجب أن لا يتجاوز الرسوم الإجمالية"]);
        exit;
    }

    // ======= كشف التكرار (Duplicate Detection) =======
    $data_to_hash = $currency . $sender_name . $sender_phone .
        $receiver_name . $receiver_phone . $ammount . $fees . $fees_income .
        $atm . $user_id . $status;
    $data_hash = md5($data_to_hash);

    $force_save = isset($_POST['force_save']) && $_POST['force_save'] === 'true';

    if (!$force_save) {
        $stmtCheck = $conn->prepare("SELECT created_at FROM idempotency_keys 
                                     WHERE data_hash = ? AND status = 'success' 
                                     ORDER BY created_at DESC LIMIT 1");
        $stmtCheck->bind_param("s", $data_hash);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();

        if ($row = $result->fetch_assoc()) {
            $full_time = $row['created_at'];
            $date_only = date("Y-m-d", strtotime($full_time));
            $time_only = date("H:i:s", strtotime($full_time));

            echo json_encode([
                "is_duplicate" => true,
                "message" => "هذه العملية قد تم تنفيذها مسبقاً بتاريخ $date_only الساعة $time_only. هل تريد تنفيذها مرة أخرى؟"
            ]);
            exit;
        }
        $stmtCheck->close();
    }

    // ======= بدء المعاملة (Transaction) =======
    $conn->begin_transaction();

    try {
        // 1. حجز مفتاح الأمان — نمرر NULL لـ client_id لتجنب مشكلة FK
        $conn->query("SET time_zone = '+03:00'");
        $null_client_id = null;
        $stmtKey = $conn->prepare("INSERT INTO idempotency_keys (request_key, user_id, client_id, data_hash, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
        $stmtKey->bind_param("siis", $idempotency_key, $user_id, $null_client_id, $data_hash);

        if (!$stmtKey->execute()) {
            if ($conn->errno === 1062) {
                $conn->rollback();
                echo json_encode(["error" => "هذه العملية تم إرسالها مسبقاً!"]);
                exit;
            }
        }
        $stmtKey->close();

        // 2. إدخال الحوالة العامة (بدون TYPE و FOR_OR_ON)
        $sql = "INSERT INTO public_exchange (USER_ID, CURRENCY, STATUS, AMMOUNT, TRA_DATE, NOTE, SENDER_NAME, SENDER_PHONE, RECEIVER_NAME, RECEIVER_PHONE, TRANSFER_NO, TRA_FEES, FEES_INCOME, ATM) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issdsssssssdds",
            $user_id, $currency, $status,
            $ammount, $tra_date, $note,
            $sender_name, $sender_phone, $receiver_name, $receiver_phone, $transfer_no,
            $fees, $fees_income, $atm
        );

        if ($stmt->execute()) {
            $pe_id = $conn->insert_id;

            // 3. توليد رقم حوالة تلقائي إذا لم يُقدَّم
            if (empty($transfer_no)) {
                $transfer_no = 'PE-' . date("md", strtotime($tra_date)) . str_pad($pe_id, 8, '0', STR_PAD_LEFT);
                $updateStmt = $conn->prepare("UPDATE public_exchange SET TRANSFER_NO = ? WHERE PE_ID = ?");
                $updateStmt->bind_param("si", $transfer_no, $pe_id);
                $updateStmt->execute();
                $updateStmt->close();
            }

            // 4. تسجيل الدخل (ربح الرسوم) — يحاكي سلوك insert_transaction_function.php
            if ($fees_income > 0) {
                require_once '../insert_income_function.php';
                $source = "حوالة عامة ، رقم الحوالة: <br>$transfer_no";
                if (!insert_income($source, $currency, 'له', $fees_income, $tra_date, '', $conn)) {
                    $conn->rollback();
                    echo json_encode(["error" => "حدث خطأ أثناء إضافة الدخل"]);
                    $stmt->close();
                    $conn->autocommit(true);
                    $conn->close();
                    exit;
                }
            }

            // 5. تحديث حالة مفتاح الأمان
            $updateKey = $conn->prepare("UPDATE idempotency_keys SET status = 'success' WHERE request_key = ?");
            $updateKey->bind_param("s", $idempotency_key);
            $updateKey->execute();
            $updateKey->close();

            $conn->commit();
            echo json_encode(["success" => "تمت إضافة الحوالة العامة بنجاح"]);
        } else {
            $conn->rollback();
            echo json_encode(["error" => "فشل في إدخال الحوالة"]);
        }

        $stmt->close();

    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        if ($e->getCode() === 1062) {
            echo json_encode(["error" => "هذه العملية تم إرسالها مسبقاً!"]);
        } else {
            echo json_encode(["error" => "خطأ في قاعدة البيانات: " . $e->getMessage()]);
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["error" => "خطأ عام: " . $e->getMessage()]);
    }

    $conn->autocommit(true);
    $conn->close();
} else {
    echo json_encode(["error" => "طلب غير صالح"]);
}
