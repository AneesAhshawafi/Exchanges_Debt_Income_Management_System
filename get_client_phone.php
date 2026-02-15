<?php
header("Content-Type: application/json");
include 'dbconn.php';
// $client_id = isset($_GET['client_id']) ? trim($_GET['client_id']);
$client_id = isset($_GET['client_id']) ? intval($_GET['client_id']) : 0;
$sql = "SELECT PHONE FROM client WHERE CLIENT_ID = ? AND DEPT_NO = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["phone" => $row["PHONE"]]);
} else {
    echo json_encode(["phone" => null]);
}
?>