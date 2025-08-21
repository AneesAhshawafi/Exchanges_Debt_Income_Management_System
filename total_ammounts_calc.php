<?php

function calc_total_ammounts($client_id,$conn) {
//    include 'dbconn.php';

    $stmt = $conn->prepare("SELECT sum_ammount_new,sum_ammount_old,sum_ammount_sa FROM transaction WHERE CLIENT_ID = ? ORDER BY TRA_ID DESC LIMIT 1");
    $stmt->bind_param("i",$client_id );
    $stmt->execute();
    $resualt_sum_ammounts=$stmt->get_result();
    if (mysqli_num_rows($resualt_sum_ammounts) > 0) {
        
    $row_sum_ammounts = $resualt_sum_ammounts->fetch_assoc();

    $sum_ammount_new = is_null($row_sum_ammounts['sum_ammount_new']) ? 0 : $row_sum_ammounts['sum_ammount_new'];
    $sum_ammount_old = is_null($row_sum_ammounts['sum_ammount_old']) ? 0 : $row_sum_ammounts['sum_ammount_old'];
    $sum_ammount_sa = is_null($row_sum_ammounts['sum_ammount_sa']) ? 0 : $row_sum_ammounts['sum_ammount_sa'];

    $sum_ammounts = [$sum_ammount_new, $sum_ammount_old, $sum_ammount_sa];
    } else {
        $sum_ammounts=[0,0,0];
    }


    return $sum_ammounts;
}

?>
