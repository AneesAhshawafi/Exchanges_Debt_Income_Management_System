
<?php
include 'dbconn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tra_id'])) {
    $tra_id = $_POST['tra_id'];

    $stmt = $conn->prepare("DELETE FROM transaction WHERE TRA_ID = ?");
    $stmt->bind_param("s", $tra_id);

    if ($stmt->execute()) {
        echo "تم الحذف بنجاح";
    } else {
        echo "حدث خطأ أثناء الحذف";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "طلب غير صالح";
}
?>