<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */
include 'bdconn.php';
$resualt_sum_ammount_new_on = $conn->query("SELECT SUM(AMMOUNT) as total_on FROM TRANSACTION WHERE CURRENCY ='new'and
CLIENT_ID= " . $row['CLIENT_ID']." and on_OR_ON ='عليه'");
$resualt_sum_ammount_old_on = $conn->query("SELECT SUM(AMMOUNT) as total_on FROM TRANSACTION WHERE CURRENCY ='old' and
CLIENT_ID= " . $row['CLIENT_ID']." and on_OR_ON ='عليه'");
$resualt_sum_ammount_sa_on = $conn->query("SELECT SUM(AMMOUNT) as total_on FROM TRANSACTION WHERE CURRENCY ='sa' and
CLIENT_ID= " . $row['CLIENT_ID']." and on_OR_ON ='عليه'");

$row_new_on = $resualt_sum_ammount_new_on->fetch_assoc();
$sum_ammount_new_on = is_null($row_new_on['total_on']) ? 0 : $row_new_on['total_on'];

$row_old_on = $resualt_sum_ammount_old_on->fetch_assoc();
$sum_ammount_old_on = is_null($row_old_on['total_on']) ? 0 : $row_old_on['total_on'];

$row_sa_on = $resualt_sum_ammount_sa_on->fetch_assoc();
$sum_ammount_sa_on = is_null($row_sa_on['total_on']) ? 0 : $row_sa_on['total_on'];

?>
