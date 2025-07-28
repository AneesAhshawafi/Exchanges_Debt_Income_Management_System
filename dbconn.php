<?php



$server='localhost';
$username='root';
$password="";
$dbname="exchange_Management";
$conn=mysqli_connect($server, $username, $password,$dbname);
 if(!$conn){
     echo 'Connection error: '. mysqli_connect_error();
 }
?>
