<?php

session_start();
// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Send an error response and exit
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

include 'dbconn.php';
include 'csrf_token.php';
if (!verify_csrf_token($_POST['csrf_token'])) {
    die('CSRF token validation failed');
}

$user_id = $_SESSION['user_id'];


// Check if it's a POST request and the client_name is set
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["client_name"])) {
    $client_name = trim($_POST["client_name"]);
    $phone = trim($_POST['phone']);

    // Use prepared statements to prevent SQL injection
    $sql_add_client = "INSERT INTO client (CLIENT_NAME,PHONE, DEPT_NO, USER_ID) VALUES (?,?, 1, ?)";
    $stmt = mysqli_prepare($conn, $sql_add_client);
    mysqli_stmt_bind_param($stmt, "ssi", $client_name, $phone, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        // Send a success response back to the JavaScript
        echo json_encode(['success' => true, 'message' => 'تم إضافة العميل بنجاح']);
    } else {
        // Send an error response
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'فشل إضافة العميل']);
    }
    mysqli_stmt_close($stmt);
} else {
    // Handle cases where the request is not valid
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
mysqli_close($conn);
