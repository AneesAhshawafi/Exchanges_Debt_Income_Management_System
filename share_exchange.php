
<?php
include 'dbconn.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tra_no'])) {
    $tra_no = intval($_POST['tra_no']);

    $stmt = $conn->prepare("SELECT * FROM transaction WHERE TRA_ID = ?");
    $stmt->bind_param("i", $tra_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $data['TRA_DATE']=date("Y-m-d", strtotime($data['TRA_DATE']));
    if ($data) {
        echo json_encode($data);
    } else {
        echo json_encode(["error" => "لا توجد بيانات"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "طلب غير صالح"]);
}
?>