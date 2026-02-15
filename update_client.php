<?php

include 'dbconn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $client_id = intval($_POST['client-id']);
    $client_name = trim($_POST['client-name']);
    $phone=trim($_POST['phone']);

    $sql = "UPDATE  client SET CLIENT_NAME = ? , PHONE = ? WHERE CLIENT_ID = ? ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $client_name,$phone,$client_id );
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo $stmt->error;
    }
    $stmt->close();
    $conn->close();
} else {
    echo 'حدث خطا';
}
