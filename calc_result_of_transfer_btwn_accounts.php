<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

function get_result_of_transfer_btwn_accounts($selectFrom,$selectTo,$ammount,$price){
    $tempTotalTo=0;
     if (($selectFrom == 'new' && $selectTo == 'old') || ($selectFrom == 'new' && $selectTo == 'sa') || ($selectFrom == 'old' && $selectTo == 'sa')) {
//            fwrite($error_file, 'division : ' . "\r\n");
//            fwrite($error_file, 'ammount : ' . $ammount . "\r\n");
//            fwrite($error_file, 'price : ' . $price . "\r\n");
            $tempTotalTo = $ammount / $price;
//            fwrite($error_file, 'tempTotalTo : ' . $tempTotalTo . '  after division' . "\r\n");
        } else {
//            fwrite($error_file, 'mult : ' . "\r\n");
//            fwrite($error_file, 'ammount : ' . $ammount . "\r\n");
//            fwrite($error_file, 'price : ' . $price . "\r\n");
            $tempTotalTo = $ammount * $price;
//            fwrite($error_file, 'tempTotalTo : ' . $tempTotalTo . '  after mult' . "\r\n");
        }
        return $tempTotalTo;
}