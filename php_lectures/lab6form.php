<?php
// if($_SERVER["REQUEST_METHOD"]=="POST"){
    // if(isset($_POST["fileToUpload"])){
        $file_tmp=$_FILES['fileToUpload']['tmp_name'];//location on the server
        $file_size=$_FILES['fileToUpload']['size'];
        $file_type=$_FILES['fileToUpload']['type'];
        $file_error=$_FILES['fileToUpload']['error'];
        echo $file_tmp.'<br>';
        echo $$file_size.'<br>';
        echo $file_type.'<br>';
        echo $file_error.'<br>';

    // }
// }                                                                  