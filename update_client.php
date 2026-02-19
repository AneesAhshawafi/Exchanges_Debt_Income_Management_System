<?php

include 'dbconn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    session_start();
    // Ensure the user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Send an error response and exit
        http_response_code(401); // Unauthorized
        echo json_encode(['success' => false, 'message' => 'User not logged in.']);
        exit;
    }
    $client_id = intval($_POST['client-id']);
    $client_name = trim($_POST['client-name']);
    $phone = trim($_POST['phone']);

    include 'csrf_token.php';
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed');
    }

    $sql = "UPDATE  client SET CLIENT_NAME = ? , PHONE = ? WHERE CLIENT_ID = ? ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $client_name, $phone, $client_id);
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
