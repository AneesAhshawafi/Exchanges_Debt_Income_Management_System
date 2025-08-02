<?php

require_once __DIR__ . '/vendor/autoload.php';
include 'dbconn.php';

ob_end_clean(); // تنظيف أي buffer محتمل
error_reporting(E_ERROR | E_PARSE); // تجاهل التحذيرات
$client_id = isset($_GET['client_id']) ? intval($_GET['client_id']) : 0;
if (!$client_id) {
    die("معرّف العميل غير صالح.");
}

// اسم العميل
$stmt = $conn->prepare("SELECT CLIENT_NAME FROM client WHERE CLIENT_ID = ? and DEPT_NO = 2");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();
$client = $result->fetch_assoc();
$client_name = $client['CLIENT_NAME'];

// العمليات
$query = "SELECT * FROM debt WHERE CLIENT_ID = ? ORDER BY DEBT_ID DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();

// إعداد mPDF
$mpdf = new \Mpdf\Mpdf([
     'mode' => 'utf-8',
    'format' => 'A3',       // حجم الورقة A3
    'orientation' => 'L',   // الاتجاه أفقي (Landscape)
    'default_font' => 'kfgqpcuthmantahanaskh',
    'default_font_size' => 14,
    'mirrorMargins' => true,
        ]);

$mpdf->SetDirectionality('rtl');

// عنوان التقرير
$html = "<h3 style='text-align: center;'>قائمة العمليات للعميل: $client_name</h3><br>";

// رأس الجدول
$html .= "
<table border='1' cellpadding='5' style='text-align:center' cellspacing='0' width='100%'>
<thead>
<tr style='background-color: #f2f2f2;'>
     <th>الغرض</th>
    <th>المبلغ</th>
    <th>له/عليه</th>
    <th>التاريخ</th>
    <th>الإجمالي قعيطي </th>
    <th>الإجمالي قديم </th>
    <th>الإجمالي سعودي </th>
    <th>ملاحظة</th>

</tr>
</thead>
<tbody>
";

// بيانات العمليات
while ($row = $result->fetch_assoc()) {
    $html .= "<tr>";
    $html .= "<td>" . htmlspecialchars($row['DESCRIPTION']) . "</td>";
    if ($row['CURRENCY'] == 'new') {
        $html .= "<td>" . number_format($row['AMMOUNT'], 2).' ري قعيطي' . "</td>";
//        $html .= "<td>" . number_format(0, 2) . "</td>";
//        $html .= "<td>" . number_format(0, 2) . "</td>";
    } elseif ($row['CURRENCY'] == 'old') {

//        $html .= "<td>" . number_format(0, 2) . "</td>";
        $html .= "<td>" . number_format($row['AMMOUNT'], 2).' ري قديم' . "</td>";
//        $html .= "<td>" . number_format(0, 2) . "</td>";
    } else {
//        $html .= "<td>" . number_format(0, 2) . "</td>";
//        $html .= "<td>" . number_format(0, 2) . "</td>";
        $html .= "<td>" . number_format($row['AMMOUNT'], 2) .' ر سعودي'. "</td>";
    }
    $html .= "<td>" . htmlspecialchars($row['FOR_OR_ON']) . "</td>";
    $html .= "<td>" . htmlspecialchars(date("Y-m-d", strtotime($row['TRA_DATE']))) . "</td>";
    if($row['sum_ammount_new']>=0){
        
    $html .= "<td>" . number_format($row['sum_ammount_new'], 2) .' لكم'. "</td>";
    } else {
        $html .= "<td>" . number_format($row['sum_ammount_new']*-1, 2) .' عليكم'. "</td>";
    }
    if($row['sum_ammount_old']>=0){
        
    $html .= "<td>" . number_format($row['sum_ammount_old'], 2) .' لكم'. "</td>";
    } else {
        $html .= "<td>" . number_format($row['sum_ammount_old']*-1, 2) .' عليكم'. "</td>";
    }
    if($row['sum_ammount_sa']>=0){
        
    $html .= "<td>" . number_format($row['sum_ammount_sa'], 2) .' لكم'. "</td>";
    } else {
        $html .= "<td>" . number_format($row['sum_ammount_sa']*-1, 2) .' عليكم'. "</td>";
    }
    $html .= "<td>" . htmlspecialchars($row['NOTE']) . "</td>";
    $html .= "</tr>";
}

$html .= "</tbody></table>";

// إخراج PDF
$mpdf->WriteHTML($html);
$mpdf->Output("client_debts.pdf", "I");