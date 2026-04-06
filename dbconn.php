<?php



mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // تفعيل التقارير كاستثناءات

$servername = "localhost";
$username = "root";
$password = ""; // ← غيّره إلى كلمة مرورك الحقيقية
$dbname = "u741539493_exchange_manag";

try {
    $conn = new mysqli($servername, $username, $password,$dbname);
    $conn->set_charset("utf8mb4"); // اختيار الترميز

} catch (mysqli_sql_exception $e) {
    // التقاط الخطأ في الاتصال
    error_log("Database Connection Error: " . $e->getMessage());
    die("فشل الاتصال بقاعدة البيانات.");
}

// try{

//     $connString ="mysql:host=localhost;dbname=exchange_management";
//     $username="root";
//     $password="";
//     $conn=new PDO($connString,$username,$password);
//     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// } catch(PDOException $e){
//     die($e->getMessage());
// }


?>
