
<?php
include 'dbconn.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['debt_id'])) {
    $tra_no = intval($_POST['debt_id']);

    $stmt = $conn->prepare("SELECT * FROM debt WHERE DEBT_ID = ?");
    $stmt->bind_param("i", $tra_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $data['DEBT_DATE']=date("Y-m-d", strtotime($data['DEBT_DATE']));
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