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

// جلب اسم العميل بشكل آمن (لاحظ DEPT_NO = 2)
$stmt_client = $conn->prepare("SELECT CLIENT_NAME FROM client WHERE CLIENT_ID = ? AND DEPT_NO = 2");
$stmt_client->bind_param("i", $client_id);
$stmt_client->execute();
$result_client = $stmt_client->get_result();

if ($result_client->num_rows === 0) {
    die("خطأ: لم يتم العثور على العميل في قسم الديون بالمعرّف المحدد.");
}
$client = $result_client->fetch_assoc();
$client_name = $client['CLIENT_NAME'];
$stmt_client->close();

// جلب جميع عمليات الدين للعميل بشكل آمن
$stmt_debts = $conn->prepare("SELECT * FROM debt WHERE CLIENT_ID = ? ORDER BY DEBT_ID DESC");
$stmt_debts->bind_param("i", $client_id);
$stmt_debts->execute();
$debts = $stmt_debts->get_result();

// 3. إنشاء ملف PDF باستخدام mPDF
// --------------------------------------------------

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4', // A4 مناسب هنا لأن عدد الأعمدة أقل
    'orientation' => 'L', // أفقي
    'default_font' => 'kfgqpcuthmantahanaskh',
    'default_font_size' => 12,
    'margin_left' => 10, 'margin_right' => 10,
    'margin_top' => 25, 'margin_bottom' => 25,
    'margin_header' => 10, 'margin_footer' => 10,
]);

$mpdf->SetDirectionality('rtl');
$mpdf->SetDisplayMode('fullpage');

$header = "<p>بن عبود للصرافة والتحويلات - قسم الديون</p>";
$footer = "<p style='text-align:left;'>صفحة {PAGENO} من {nb}</p>";
$mpdf->SetHeader($header);
$mpdf->SetFooter($footer);

// بناء محتوى HTML للـ PDF مع تحديد عرض الأعمدة
$html = "
<style>
    body { font-family: 'kfgqpcuthmantahanaskh', sans-serif; }
    h1 { color: #333; text-align: center; }
    table {
        width: 100%; border-collapse: collapse; font-size: 11px;
        table-layout: fixed; word-wrap: break-word;
    }
    th, td { border: 1px solid #999; padding: 6px; text-align: center; }
    thead th { background-color: #e0e0e0; font-weight: bold; }
    tbody tr:nth-child(even) { background-color: #f2f2f2; }
    /* تحديد عرض الأعمدة */
    .col-desc      { width: 25%; }
    .col-amount    { width: 15%; }
    .col-foron     { width: 10%; }
    .col-date      { width: 12%; }
    .col-balance1  { width: 12%; }
    .col-balance2  { width: 12%; }
    .col-balance3  { width: 14%; }
</style>

<h1>كشف ديون للعميل: " . htmlspecialchars($client_name) . "</h1>
<p style='text-align:center;'>تاريخ التقرير: " . date('Y-m-d') . "</p>

<table>
    <thead>
        <tr>
            <th class='col-desc'>الغرض</th>
            <th class='col-amount'>المبلغ</th>
            <th class='col-date'>التاريخ</th>
            <th class='col-balance1'>الإجمالي قعيطي</th>
            <th class='col-balance2'>الإجمالي قديم</th>
            <th class='col-balance3'>الإجمالي سعودي</th>
        </tr>
    </thead>
    <tbody>";

// إضافة صفوف العمليات
while ($row = $debts->fetch_assoc()) {
    $html .= "<tr>";
    $html .= "<td>" . htmlspecialchars($row['DESCRIPTION']) . "</td>";
    
    $amount_display = '';
    if ($row['CURRENCY'] == 'new') {
        $amount_display = number_format($row['AMMOUNT'], 2) . ' قعيطي';
    } elseif ($row['CURRENCY'] == 'old') {
        $amount_display = number_format($row['AMMOUNT'], 2) . ' قديم';
    } else {
        $amount_display = number_format($row['AMMOUNT'], 2) . ' سعودي';
    }
    $html .= "<td>" . $amount_display . "</td>";
    $html .= "<td>" . htmlspecialchars(date("Y-m-d",strtotime($row['DEBT_DATE']))) . "</td>";
    $html .= "<td>" . ($row['sum_ammount_new'] >= 0 ? number_format($row['sum_ammount_new'], 2) . ' عليكم' : number_format(abs($row['sum_ammount_new']), 2) . ' لكم') . "</td>";
    $html .= "<td>" . ($row['sum_ammount_old'] >= 0 ? number_format($row['sum_ammount_old'], 2) . ' عليكم' : number_format(abs($row['sum_ammount_old']), 2) . ' لكم') . "</td>";
    $html .= "<td>" . ($row['sum_ammount_sa'] >= 0 ? number_format($row['sum_ammount_sa'], 2) . ' عليكم' : number_format(abs($row['sum_ammount_sa']), 2) . ' لكم') . "</td>";
    
    $html .= "</tr>";
}

$html .= "</tbody></table>";
$stmt_debts->close();
$conn->close();

// 4. إخراج الملف للتنزيل (الطريقة اليدوية المزدوجة والمضمونة)
// --------------------------------------------------

$mpdf->WriteHTML($html);

$safe_client_name = preg_replace('/[^A-Za-z0-9-_\p{Arabic}]/u', '', $client_name);
$filename_arabic = "كشف_ديون_" . $safe_client_name . "_" . date("Y-m-d") . ".pdf";
$filename_fallback = "debts_report_" . date("Y-m-d") . ".pdf";

if (ob_get_contents()) {
    ob_end_clean();
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename_fallback . '"; filename*=UTF-8\'\'' . rawurlencode($filename_arabic));
header('Content-Length: ' . strlen($mpdf->Output('', 'S')));
header('Connection: close');

echo $mpdf->Output('', 'S');

exit;
?>
