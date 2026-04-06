
<?php
// 1. الإعدادات والاتصال
// --------------------------------------------------

require_once __DIR__ . '/vendor/autoload.php';
include 'dbconn.php';

error_reporting(0);
ini_set('display_errors', 0);

// 2. جلب البيانات والتحقق منها
// --------------------------------------------------

if (!isset($_GET['client_id']) || !filter_var($_GET['client_id'], FILTER_VALIDATE_INT)) {
    die("خطأ: معرّف العميل غير موجود أو غير صالح.");
}
$client_id = intval($_GET['client_id']);

$stmt_client = $conn->prepare("SELECT CLIENT_NAME,PHONE FROM client WHERE CLIENT_ID = ? AND DEPT_NO = 1");
$stmt_client->bind_param("i", $client_id);
$stmt_client->execute();
$result_client = $stmt_client->get_result();

if ($result_client->num_rows === 0) {
    die("خطأ: لم يتم العثور على العميل بالمعرّف المحدد.");
}
$client = $result_client->fetch_assoc();
$client_name = $client['CLIENT_NAME'];
$phone = $client['PHONE'];
$stmt_client->close();

$stmt_transactions = $conn->prepare("SELECT * FROM transaction WHERE CLIENT_ID = ? ORDER BY TRA_ID DESC");
$stmt_transactions->bind_param("i", $client_id);
$stmt_transactions->execute();
$transactions = $stmt_transactions->get_result();

// 3. إنشاء ملف PDF باستخدام mPDF
// --------------------------------------------------

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A3',
    'orientation' => 'L',
    'default_font' => 'kfgqpcuthmantahanaskh',
    'default_font_size' => 12,
    'margin_left' => 10, 'margin_right' => 10,
    'margin_top' => 25, 'margin_bottom' => 25,
    'margin_header' => 10, 'margin_footer' => 10,
]);

$mpdf->SetDirectionality('rtl');
$mpdf->SetDisplayMode('fullpage');

$header = "<p>بن عبود للصرافة والتحويلات</p>";
$footer = "<p style='text-align:left;'>صفحة {PAGENO} من {nb}</p>";
$mpdf->SetHeader($header);
$mpdf->SetFooter($footer);

// بناء محتوى HTML للـ PDF
$html = "
<style>
    body { font-family: 'kfgqpcuthmantahanaskh', sans-serif; }
    h1 { color: #333; text-align: center; }
    table {
        width: 100%; border-collapse: collapse; font-size: 9px;
        table-layout: fixed; word-wrap: break-word;
    }
    th, td { border: 1px solid #999; padding: 5px; text-align: center; }
    thead th { background-color: #e0e0e0; font-weight: bold; }
    tbody tr:nth-child(even) { background-color: #f2f2f2; }
    .col-sender{width:6%} .col-sphone{width:5%} .col-receiver{width:6%} .col-rphone{width:5%}
    .col-type{width:4%} .col-transferno{width:6%} .col-amount{width:7%} .col-foron{width:3%}
    .col-date{width:5%} .col-atm{width:5%} .col-fees{width:3%}
    .col-fromcur{width:4%} .col-price{width:4%} .col-tocur{width:4%} .col-transamount{width:5%}
    .col-balance1{width:6%} .col-balance2{width:6%} .col-balance3{width:6%}
    .col-note{width:6%} .col-status{width:4%}
</style>
<h1>كشف حساب للعميل: " . htmlspecialchars($client_name) . "</h1>
<h2>رقم التلفون: ". htmlspecialchars($phone)."</h2>
<p style='text-align:center;'>تاريخ التقرير: " . date('Y-m-d') . "</p>
<table>
    <thead>
        <tr>
            <th class='col-sender'>المرسل/المودع</th> <th class='col-sphone'>رقم المرسل</th>
            <th class='col-receiver'>المستلم</th> <th class='col-rphone'>رقم المستلم</th>
            <th class='col-type'>نوع العملية</th> <th class='col-transferno'>رقم الحوالة</th>
            <th class='col-amount'>المبلغ</th> <th class='col-foron'>له/عليه</th>
            <th class='col-date'>التاريخ</th> <th class='col-atm'>الصراف</th>
            <th class='col-fees'>الرسوم</th> 
            <th class='col-fromcur'>صرف من</th> <th class='col-price'>السعر</th>
            <th class='col-tocur'>صرف إلى</th> <th class='col-transamount'>المبلغ المقابل</th>
            <th class='col-balance1'>رصيد قعيطي</th> <th class='col-balance2'>رصيد قديم</th> <th class='col-balance3'>رصيد سعودي</th>
            <th class='col-note'>ملاحظة</th> <th class='col-status'>الحالة</th>
        </tr>
    </thead>
    <tbody>";

while ($row = $transactions->fetch_assoc()) {
    $from_cur = '';
    if ($row['FROM_CURRENCY'] == 'new') $from_cur = 'قعيطي';
    elseif ($row['FROM_CURRENCY'] == 'old') $from_cur = 'قديم';
    elseif ($row['FROM_CURRENCY'] == 'sa') $from_cur = 'سعودي';
    
    $to_cur = '';
    if ($row['TO_CURRENCY'] == 'new') $to_cur = 'قعيطي';
    elseif ($row['TO_CURRENCY'] == 'old') $to_cur = 'قديم';
    elseif ($row['TO_CURRENCY'] == 'sa') $to_cur = 'سعودي';

    $price_display = floatval($row['PRICE']) != 0 ? floatval($row['PRICE']) : '';
    $trans_amount = floatval($row['TRANSFERED_AMMOUNT']) != 0 ? number_format(floatval($row['TRANSFERED_AMMOUNT']), 2) : '';

    $html .= "<tr>";
    $html .= "<td>" . htmlspecialchars($row['SENDER_NAME']) . "</td>";
    $html .= "<td>" . htmlspecialchars($row['SENDER_PHONE']) . "</td>";
    $html .= "<td>" . htmlspecialchars($row['RECEIVER_NAME']) . "</td>";
    $html .= "<td>" . htmlspecialchars($row['RECEIVER_PHONE']) . "</td>";
    $html .= "<td>" . htmlspecialchars($row['TYPE']) . "</td>";
    $html .= "<td>" . htmlspecialchars($row['TRANSFER_NO']) . "</td>";
    
    $amount_display = '';
    if ($row['CURRENCY'] == 'new') {
        $amount_display = number_format($row['AMMOUNT'], 2) . ' قعيطي';
    } elseif ($row['CURRENCY'] == 'old') {
        $amount_display = number_format($row['AMMOUNT'], 2) . ' قديم';
    } elseif ($row['CURRENCY'] == 'sa') {
        $amount_display = number_format($row['AMMOUNT'], 2) . ' سعودي';
    } else {
        $amount_display = number_format($row['AMMOUNT'], 2);
    }
    
    $html .= "<td>" . $amount_display . "</td>";
    $html .= "<td>" . htmlspecialchars($row['FOR_OR_ON']) . "</td>";
    $html .= "<td>" . htmlspecialchars(date("Y-m-d", strtotime($row['TRA_DATE']))) . "</td>";
    $html .= "<td>" . htmlspecialchars($row['ATM']) . "</td>";
    $html .= "<td>" . number_format($row['TRA_FEES'], 2) . "</td>";
    
    $html .= "<td>" . $from_cur . "</td>";
    $html .= "<td>" . $price_display . "</td>";
    $html .= "<td>" . $to_cur . "</td>";
    $html .= "<td>" . $trans_amount . "</td>";
    
    $html .= "<td>" . ($row['sum_ammount_new'] >= 0 ? number_format($row['sum_ammount_new'], 2) . ' لكم' : number_format(abs($row['sum_ammount_new']), 2) . ' عليكم') . "</td>";
    $html .= "<td>" . ($row['sum_ammount_old'] >= 0 ? number_format($row['sum_ammount_old'], 2) . ' لكم' : number_format(abs($row['sum_ammount_old']), 2) . ' عليكم') . "</td>";
    $html .= "<td>" . ($row['sum_ammount_sa'] >= 0 ? number_format($row['sum_ammount_sa'], 2) . ' لكم' : number_format(abs($row['sum_ammount_sa']), 2) . ' عليكم') . "</td>";
    $html .= "<td>" . htmlspecialchars($row['NOTE']) . "</td>";
    $html .= "<td>" . htmlspecialchars($row['STATUS']) . "</td>";
    $html .= "</tr>";
}

$html .= "</tbody></table>";
$stmt_transactions->close();
$conn->close();

// 4. إخراج الملف للتنزيل (الطريقة اليدوية المزدوجة والمضمونة)
// --------------------------------------------------

// كتابة محتوى HTML إلى PDF
$mpdf->WriteHTML($html);

// إنشاء اسم ملف نظيف باللغة العربية
$safe_client_name = preg_replace('/[^A-Za-z0-9-_\p{Arabic}]/u', '', $client_name);
$filename_arabic = "كشف_حساب_" . $safe_client_name . "_" . date("Y-m-d") . ".pdf";

// إنشاء اسم ملف احتياطي باللغة الإنجليزية فقط
$filename_fallback = "report_" . date("Y-m-d") . ".pdf";

// منع أي مخرجات أخرى قد تتداخل مع الهيدرات
if (ob_get_contents()) {
    ob_end_clean();
}

// إرسال الهيدرات يدويًا
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename_fallback . '"; filename*=UTF-8\'\'' . rawurlencode($filename_arabic));
header('Content-Length: ' . strlen($mpdf->Output('', 'S'))); // إرسال حجم الملف
header('Connection: close');

// إخراج بيانات PDF الخام إلى المتصفح
echo $mpdf->Output('', 'S');

exit;
?>
