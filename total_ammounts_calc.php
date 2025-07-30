<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

function calc_total_ammounts($client_id) {
    include 'dbconn.php';

    $resualt_sum_ammounts = $conn->query("SELECT sum_ammount_new,sum_ammount_old,sum_ammount_sa FROM TRANSACTION WHERE CLIENT_ID = " . $client_id . " ORDER BY TRA_ID DESC LIMIT 1");
    if (mysqli_num_rows($resualt_sum_ammounts) > 0) {
        
    $row_sum_ammounts = $resualt_sum_ammounts->fetch_assoc();

    $sum_ammount_new = is_null($row_sum_ammounts['sum_ammount_new']) ? 0 : $row_sum_ammounts['sum_ammount_new'];
    $sum_ammount_old = is_null($row_sum_ammounts['sum_ammount_old']) ? 0 : $row_sum_ammounts['sum_ammount_old'];
    $sum_ammount_sa = is_null($row_sum_ammounts['sum_ammount_sa']) ? 0 : $row_sum_ammounts['sum_ammount_sa'];

    $sum_ammounts = [$sum_ammount_new, $sum_ammount_old, $sum_ammount_sa];
    } else {
        $sum_ammounts=[0,0,0];
    }

//    
//    $resualt_sum_ammount_new_for = $conn->query("SELECT SUM(AMMOUNT) as total_for FROM TRANSACTION WHERE CURRENCY ='new' and CLIENT_ID= " . $client_id . " and FOR_OR_ON ='له'");
//    $resualt_sum_ammount_old_for = $conn->query("SELECT SUM(AMMOUNT) as total_for FROM TRANSACTION WHERE CURRENCY ='old' and CLIENT_ID= " . $client_id . " and FOR_OR_ON ='له'");
//    $resualt_sum_ammount_sa_for = $conn->query("SELECT SUM(AMMOUNT) as total_for FROM TRANSACTION WHERE CURRENCY ='sa' and CLIENT_ID= " . $client_id . " and FOR_OR_ON ='له'");
//    
//    
//    
//    $row_new_for = $resualt_sum_ammount_new_for->fetch_assoc();
//    $row_old_for = $resualt_sum_ammount_old_for->fetch_assoc();
//    $row_sa_for = $resualt_sum_ammount_sa_for->fetch_assoc();
//    
//    $sum_ammount_new_for = is_null($row_new_for['total_for']) ? 0 : $row_new_for['total_for'];
//    $sum_ammount_old_for = is_null($row_old_for['total_for']) ? 0 : $row_old_for['total_for'];
//    $sum_ammount_sa_for = is_null($row_sa_for['total_for']) ? 0 : $row_sa_for['total_for'];
//
//    $resualt_sum_ammount_new_on = $conn->query("SELECT SUM(AMMOUNT) as total_on FROM TRANSACTION WHERE CURRENCY ='new' and CLIENT_ID= " . $client_id . " and FOR_OR_ON ='عليه'");
//    $resualt_sum_ammount_old_on = $conn->query("SELECT SUM(AMMOUNT) as total_on FROM TRANSACTION WHERE CURRENCY ='old' and CLIENT_ID= " . $client_id . " and FOR_OR_ON ='عليه'");
//    $resualt_sum_ammount_sa_on = $conn->query("SELECT SUM(AMMOUNT) as total_on FROM TRANSACTION WHERE CURRENCY ='sa' and CLIENT_ID= " . $client_id . " and FOR_OR_ON ='عليه'");
//
//    $row_new_on = $resualt_sum_ammount_new_on->fetch_assoc();
//    $row_old_on = $resualt_sum_ammount_old_on->fetch_assoc();
//    $row_sa_on = $resualt_sum_ammount_sa_on->fetch_assoc();
//
//    $sum_ammount_new_on = is_null($row_new_on['total_on']) ? 0 : $row_new_on['total_on'];
//    $sum_ammount_old_on = is_null($row_old_on['total_on']) ? 0 : $row_old_on['total_on'];
//    $sum_ammount_sa_on = is_null($row_sa_on['total_on']) ? 0 : $row_sa_on['total_on'];
//
//    $sum_ammount_new_for -= $sum_ammount_new_on;
//    $sum_ammount_old_for -= $sum_ammount_old_on;
//    $sum_ammount_sa_for -= $sum_ammount_sa_on;
//    $sum_ammounts = [$sum_ammount_new_for, $sum_ammount_old_for, $sum_ammount_sa_for];
//    
    return $sum_ammounts;
}

?>
