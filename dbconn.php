<?php



mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // تفعيل التقارير كاستثناءات

$server='localhost';
$username='root';
$password="";
$dbname="exchange_Management";
//$conn=mysqli_connect();
// if(!$conn){
//     echo 'Connection error: '. mysqli_connect_error();
// }

try {
    $conn = new mysqli($server, $username, $password,$dbname);
    $conn->set_charset("utf8mb4"); // اختيار الترميز

} catch (mysqli_sql_exception $e) {
    // التقاط الخطأ في الاتصال
    error_log("Database Connection Error: " . $e->getMessage());
    die("فشل الاتصال بقاعدة البيانات.");
}
?>
