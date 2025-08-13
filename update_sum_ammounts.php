<?php

function update_sum_ammount($currency, $for_or_on, $exchangesListData, $ammount_differ,$id) {
    include 'dbconn.php';
    

    if ($currency == "new") {
        if ($for_or_on == "له") {
            
            foreach ($exchangesListData as $traData) {
                if ($traData['TRA_ID'] >= $id) {
                    
                    $sum_ammount_new = is_numeric($traData['sum_ammount_new']) ? $traData['sum_ammount_new'] : floatval($traData['sum_ammount_new']);
                    
                    $sum_ammount_new -= $ammount_differ;
                    
                    $stmt = $conn->prepare("UPDATE transaction SET sum_ammount_new =? WHERE TRA_ID = ?");
                    $stmt->bind_param("di", $sum_ammount_new, $traData['TRA_ID']);
                    if ($stmt->execute()) {
//                        echo json_encode(["success" => "تم التعديل بنجاح"]);
                    } else {
                        error_log('حدث خطا اثناء تعديل الاجمال');
                    }
                }
            }
        } //end if new for
        else {
            
            foreach ($exchangesListData as $traData) {
                if ($traData['TRA_ID'] >= $id) {
                    
                    
                    $sum_ammount_new = is_numeric($traData['sum_ammount_new']) ? $traData['sum_ammount_new'] : floatval($traData['sum_ammount_new']);
                    
                    $sum_ammount_new += $ammount_differ;
                    
                    $stmt = $conn->prepare("UPDATE transaction SET sum_ammount_new =? WHERE TRA_ID = ?");
                    $stmt->bind_param("di", $sum_ammount_new, $traData['TRA_ID']);
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
            
//                    $sum_ammount_old += $ammount_differ;
            foreach ($exchangesListData as $traData) {
                if ($traData['TRA_ID'] >= $id) {
                    
              
                    $sum_ammount_old = is_numeric($traData['sum_ammount_old']) ? $traData['sum_ammount_old'] : floatval($traData['sum_ammount_old']);
                    
                    $sum_ammount_old -= $ammount_differ;
                    
                    $stmt = $conn->prepare("UPDATE transaction SET sum_ammount_old =? WHERE TRA_ID = ?");
                    $stmt->bind_param("di", $sum_ammount_old, $traData['TRA_ID']);
                    if ($stmt->execute()) {
//                        echo json_encode(["success" => "تم التعديل بنجاح"]);
                    } else {
                        error_log('حدث خطا اثناء تعديل الاجمال');
                    }
                }
            }
        } //end elseif old for
        else {//start old on
            foreach ($exchangesListData as $traData) {
                if ($traData['TRA_ID'] >= $id) {
                    
        
                    $sum_ammount_old = is_numeric($traData['sum_ammount_old']) ? $traData['sum_ammount_old'] : floatval($traData['sum_ammount_old']);
                    
                    $sum_ammount_old += $ammount_differ;
                    
                    $stmt = $conn->prepare("UPDATE transaction SET sum_ammount_old =? WHERE TRA_ID = ?");
                    $stmt->bind_param("di", $sum_ammount_old, $traData['TRA_ID']);
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
                if ($traData['TRA_ID'] >= $id) {
                    
                    
                    $sum_ammount_sa = is_numeric($traData['sum_ammount_sa']) ? $traData['sum_ammount_sa'] : floatval($traData['sum_ammount_sa']);
                    
                    $sum_ammount_sa -= $ammount_differ;
                    
                    $stmt = $conn->prepare("UPDATE transaction SET sum_ammount_sa =? WHERE TRA_ID = ?");
                    $stmt->bind_param("di", $sum_ammount_sa, $traData['TRA_ID']);
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
                if ($traData['TRA_ID'] >= $id) {
                    
                    
                    $sum_ammount_sa = is_numeric($traData['sum_ammount_sa']) ? $traData['sum_ammount_sa'] : floatval($traData['sum_ammount_sa']);
                    
                    $sum_ammount_sa += $ammount_differ;
                    
                    $stmt = $conn->prepare("UPDATE transaction SET sum_ammount_sa =? WHERE TRA_ID = ?");
                    $stmt->bind_param("di", $sum_ammount_sa, $traData['TRA_ID']);
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
