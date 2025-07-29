<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

include 'dbconn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $client_id = intval($_POST['client-id']);
    $client_name = trim($_POST['client-name']);

    $sql = "UPDATE  client SET CLIENT_NAME = ? WHERE CLIENT_ID = ? ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $client_name,$client_id );
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
