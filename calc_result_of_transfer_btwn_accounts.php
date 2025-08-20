<?php
function get_result_of_transfer_btwn_accounts($selectFrom,$selectTo,$ammount,$price){
    $tempTotalTo=0;
     if (($selectFrom == 'new' && $selectTo == 'old') || ($selectFrom == 'new' && $selectTo == 'sa') || ($selectFrom == 'old' && $selectTo == 'sa')) {
            $tempTotalTo = $ammount / $price;
        } else {
            $tempTotalTo = $ammount * $price;
        }
        return $tempTotalTo;
}
?>