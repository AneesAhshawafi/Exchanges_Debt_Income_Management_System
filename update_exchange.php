

<?php
include 'dbconn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
    
    
    $id = $_POST['exchange_id'];
    $type = $_POST['type'];
    $currency = $_POST['currency'];
    $forOrOn = $_POST['for-or-on'];
    $sender = trim($_POST['sender']);
    $transfer_no = trim($_POST['transfer_no']);
    $ammount = trim($_POST['ammount']);
    $fees = $_POST['fees'];
   $tra_date_raw = $_POST["date"];
    $date = $tra_date_raw ? date("Y-m-d", strtotime($tra_date_raw)) : date("Y-m-d");
    $atm = $_POST['atm'];
    $note = trim($_POST["note"]);

    $sql = "UPDATE transaction SET 
                TYPE = ?, 
                CURRENCY = ?, 
                FOR_OR_ON = ?, 
                SENDER_NAME = ?, 
                TRANSFER_NO = ?, 
                AMMOUNT = ?, 
                TRA_FEES = ?, 
                TRA_DATE = ?, 
                ATM = ?, 
                NOTE = ?
            WHERE TRA_ID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssddsssi", $type, $currency, $forOrOn, $sender, $transfer_no, $ammount, $fees, $date, $atm, $note, $id);

    if ($stmt->execute()) {
        header("Location: exchanges_list.php"); // أو أي صفحة رئيسية
        exit();
    } else {
        echo "خطأ في التحديث: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>