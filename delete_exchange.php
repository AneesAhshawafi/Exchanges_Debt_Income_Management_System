<?php

header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['TRA_ID'])) {
    require_once 'dbconn.php';
    include 'delete_exchange_function.php';

    $tra_id = intval($_POST['TRA_ID']);
    $client_id = intval($_POST['client_id']);
    $type = $_POST['type'];
    $forOrOn = $_POST['forOrOn'];
    $transferNo = $_POST['transferNo'];
    try {
        $conn->begin_transaction();

        if (!delete_exchange($tra_id, $client_id, $conn)) {
            throw new Exception("حدث خطأ أثناء الحذف الرئيسي.");
        }

        if ($type == "إيداع" && $forOrOn == "عليه") {
            $transferNo = 'BA-' . sprintf('%012d', intval(preg_replace('/[^0-9]/', '', $transferNo)) + 1);
            $sql = "SELECT TRA_ID , TRA_DATE FROM transaction WHERE TRANSFER_NO = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("خطأ بقاعدة البيانات: " . $conn->error);
            }
            $stmt->bind_param("s", $transferNo);
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
        }

        $conn->commit();
        echo json_encode(["success" => "تم الحذف بنجاح"]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["error" => $e->getMessage()]);
    } finally {
        $conn->autocommit(true);
        $conn->close();
    }
} else {
    echo "طلب غير صالح";
}
?>