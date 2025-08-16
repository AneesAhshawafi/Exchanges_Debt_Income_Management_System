<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */
header("Content-Type:application/json");
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    session_start();

    // $user_id = intval($_POST["user_id"]);
    $user_id=$_SESSION['user_id'];
    include 'dbconn.php';
    if ($conn->connect_error) {
        echo json_encode(["error" => "فشل الاتصال بقاعدة البيانات"]);
        exit;
    }
    $limit = isset($_POST['limit']) ? $_POST['limit'] : 10;
    $offset= isset($_POST['offset']) ? $_POST['offset'] : 0;
    $sql = "SELECT * FROM income WHERE USER_ID = ?  ORDER BY INCM_ID DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id,$limit,$offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        
        $row['INCM_DATE']=date("Y-m-d", strtotime($row['INCM_DATE']));
        $transactions[] = $row;
        
    }

    echo json_encode($transactions);
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "طلب غير صالح"]);
}
?>
