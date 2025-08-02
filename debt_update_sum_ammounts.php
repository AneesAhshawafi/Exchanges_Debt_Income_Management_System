<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

function update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ,$id,$error_file) {
    include 'dbconn.php';
    
    fwrite($error_file, "دخلنا صفحة sum"."\r\n");
    if ($currency == "new") {
        if ($for_or_on == "له") {
            fwrite($error_file, 'new_for current-id='.$id."\r\n");
            foreach ($exchangesListData as $traData) {
                if ($traData['DEBT_ID'] >= $id) {
                    fwrite($error_file, "exchanges number " . $traData['DEBT_ID']."\r\n");
                    $sum_ammount_new = is_numeric($traData['sum_ammount_new']) ? $traData['sum_ammount_new'] : floatval($traData['sum_ammount_new']);
                    fwrite($error_file, "sum_ammount_old= ".$sum_ammount_new."\r\n");
                    $sum_ammount_new -= $ammount_differ;
                    fwrite($error_file, "sum_ammount_new= ".$sum_ammount_new."\r\n");
                    $stmt = $conn->prepare("UPDATE debt SET sum_ammount_new =? WHERE DEBT_ID = ?");
                    $stmt->bind_param("di", $sum_ammount_new, $traData['DEBT_ID']);
                    if ($stmt->execute()) {
//                        echo json_encode(["success" => "تم التعديل بنجاح"]);
                    } else {
                        error_log('حدث خطا اثناء تعديل الاجمال');
                    }
                }
            }
        } //end if new for
        else {
            fwrite($error_file, 'old_for current-id='.$id."\r\n");
            foreach ($exchangesListData as $traData) {
                if ($traData['DEBT_ID'] >= $id) {
                    fwrite($error_file, "exchanges number " . $traData['DEBT_ID']."\r\n");
                    
                    $sum_ammount_new = is_numeric($traData['sum_ammount_new']) ? $traData['sum_ammount_new'] : floatval($traData['sum_ammount_new']);
                    fwrite($error_file, "sum_ammount_old= ".$sum_ammount_new."\r\n");
                    $sum_ammount_new += $ammount_differ;
                    fwrite($error_file, "sum_ammount_new= ".$sum_ammount_new."\r\n");
                    $stmt = $conn->prepare("UPDATE debt SET sum_ammount_new =? WHERE DEBT_ID = ?");
                    $stmt->bind_param("di", $sum_ammount_new, $traData['DEBT_ID']);
                    if ($stmt->execute()) {
//                        echo json_encode(["success" => "تم التعديل بنجاح"]);
                    } else {
                        error_log('حدث خطا اثناء تعديل الاجمال');
                    }
                }
            }
        }
    }//end if new 
    elseif ($currency == "old") {
        if ($for_or_on == "له") {
            fwrite($error_file, 'old_for current-id='.$id."\r\n");
//                    $sum_ammount_old += $ammount_differ;
            foreach ($exchangesListData as $traData) {
                if ($traData['DEBT_ID'] >= $id) {
                    $sum_ammount_old = is_numeric($traData['sum_ammount_old']) ? $traData['sum_ammount_old'] : floatval($traData['sum_ammount_old']);
                    fwrite($error_file, "sum_ammount_old= ".$sum_ammount_old."\r\n");
                    $sum_ammount_old -= $ammount_differ;
                    fwrite($error_file, "sum_ammount_new= ".$sum_ammount_old."\r\n");
                    $stmt = $conn->prepare("UPDATE debt SET sum_ammount_old =? WHERE DEBT_ID = ?");
                    $stmt->bind_param("di", $sum_ammount_old, $traData['DEBT_ID']);
                    if ($stmt->execute()) {
//                        echo json_encode(["success" => "تم التعديل بنجاح"]);
                    } else {
                        error_log('حدث خطا اثناء تعديل الاجمال');
                    }
                }
            }
        } //end elseif old for
        else {//start old on
//                    $sum_ammount_old -= $ammount_differ;
            foreach ($exchangesListData as $traData) {
                if ($traData['DEBT_ID'] >= $id) {
                    fwrite($error_file, "exchanges number " . $traData['DEBT_ID']."\r\n");
        
                    $sum_ammount_old = is_numeric($traData['sum_ammount_old']) ? $traData['sum_ammount_old'] : floatval($traData['sum_ammount_old']);
                    fwrite($error_file, "sum_ammount_old= ".$sum_ammount_old."\r\n");
                    $sum_ammount_old += $ammount_differ;
                    fwrite($error_file, "sum_ammount_new= ".$sum_ammount_old."\r\n");
                    $stmt = $conn->prepare("UPDATE debt SET sum_ammount_old =? WHERE DEBT_ID = ?");
                    $stmt->bind_param("di", $sum_ammount_old, $traData['DEBT_ID']);
                    if ($stmt->execute()) {
//                        echo json_encode(["success" => "تم التعديل بنجاح"]);
                    } else {
                        error_log('حدث خطا اثناء تعديل الاجمال');
                    }
                }
            }
        }
    }//end elseif old
    else {//start sa for
        if ($for_or_on == "له") {
//                    $sum_ammount_sa += $ammount_differ;
            foreach ($exchangesListData as $traData) {
                if ($traData['DEBT_ID'] >= $id) {
                    fwrite($error_file, "exchanges number " . $traData['DEBT_ID']."\r\n");
                    
                    $sum_ammount_sa = is_numeric($traData['sum_ammount_sa']) ? $traData['sum_ammount_sa'] : floatval($traData['sum_ammount_sa']);
                    fwrite($error_file, "sum_ammount_old= ".$sum_ammount_sa."\r\n");
                    $sum_ammount_sa -= $ammount_differ;
                    fwrite($error_file, "sum_ammount_new= ".$sum_ammount_sa."\r\n");
                    $stmt = $conn->prepare("UPDATE debt SET sum_ammount_sa =? WHERE DEBT_ID = ?");
                    $stmt->bind_param("di", $sum_ammount_sa, $traData['DEBT_ID']);
                    if ($stmt->execute()) {
//                        echo json_encode(["success" => "تم التعديل بنجاح"]);
                    } else {
                        error_log('حدث خطا اثناء تعديل الاجمال');
                    }
                }
            }
        }//end else sa for
        else {//start sa on
//                    $sum_ammount_sa -= $ammount_differ;

            foreach ($exchangesListData as $traData) {
                if ($traData['DEBT_ID'] >= $id) {
                    fwrite($error_file, "exchanges number " . $traData['DEBT_ID']."\r\n");
                    
                    $sum_ammount_sa = is_numeric($traData['sum_ammount_sa']) ? $traData['sum_ammount_sa'] : floatval($traData['sum_ammount_sa']);
                    fwrite($error_file, "sum_ammount_old= ".$sum_ammount_sa."\r\n");
                    $sum_ammount_sa += $ammount_differ;
                    fwrite($error_file, "sum_ammount_old= ".$sum_ammount_sa."\r\n");
                    $stmt = $conn->prepare("UPDATE debt SET sum_ammount_sa =? WHERE DEBT_ID = ?");
                    $stmt->bind_param("di", $sum_ammount_sa, $traData['DEBT_ID']);
                    if ($stmt->execute()) {
//                        echo json_encode(["success" => "تم التعديل بنجاح"]);
                    } else {
                        error_log('حدث خطا اثناء تعديل الاجمال');
                    }
                }
            }
        }
    }//end else sa
}
