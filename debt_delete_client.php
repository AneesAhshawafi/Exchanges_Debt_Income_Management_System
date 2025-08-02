<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

include 'dbconn.php';
header("Content-Type: application/json");
if ($_SERVER["REQUEST_METHOD"]==="POST") {
    $client_id= $_POST['client_id'];
    
    $sql="DELETE FROM client WHERE CLIENT_ID = ? ";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("s",$client_id);
    if($stmt->execute()){
        echo json_encode(["success" => "تم الحذف بنجاح"]);
        
    } else {
        echo json_encode(["error" => "حدث خطأ  ".$stmt->error]);    
    }
    $stmt->close();
    $conn->close();
} else {
    echo '';    
}
