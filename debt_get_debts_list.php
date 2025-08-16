<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */
header("Content-Type:application/json");
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["client_id"])) {
    $client_id = intval($_POST["client_id"]);
    $limit = isset($_POST['limit']) ? $_POST['limit'] : 10;
    $offset= isset($_POST['offset']) ? $_POST['offset'] : 0;
    include 'dbconn.php';
    if ($conn->connect_error) {
        echo json_encode(["error" => "فشل الاتصال بقاعدة البيانات"]);
        exit;
    }

    $sql = "SELECT * FROM debt WHERE CLIENT_ID = ? ORDER BY DEBT_ID DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $client_id,$limit,$offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        
        $row['DEBT_DATE']=date("Y-m-d", strtotime($row['DEBT_DATE']));
        $transactions[] = $row;
        
    }

    echo json_encode($transactions);
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "طلب غير صالح"]);
}
?>
