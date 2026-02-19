<?php
include 'dbconn.php';
header("Content-Type: application/json");
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // ✅ SECURE CODE
    session_start();
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        exit(json_encode(['error' => 'Unauthorized']));
    }

    $client_id = $_POST['client_id'];
    $user_id = $_SESSION['user_id'];

    // Verify ownership before deletion
    $stmt = $conn->prepare("SELECT CLIENT_ID FROM client WHERE CLIENT_ID = ? AND USER_ID = ?");
    $stmt->bind_param("ii", $client_id, $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        exit(json_encode(['error' => 'Access denied']));
    }

    // Now safe to delete
    $stmt = $conn->prepare("DELETE FROM client WHERE CLIENT_ID = ? AND USER_ID = ?");
    $stmt->bind_param("ii", $client_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => "تم الحذف بنجاح"]);

    } else {
        echo json_encode(["error" => "حدث خطأ  " . $stmt->error]);
    }
    $stmt->close();
    $conn->close();
} else {
    echo '';
}
