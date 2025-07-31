<?php
require('fpdf/tfpdf.php');
include 'dbconn.php';

function reverseArabic($text) {
    preg_match_all('/./us', $text, $matches);
    return implode('', array_reverse($matches[0]));
}

$client_id = isset($_GET['client_id']) ? intval($_GET['client_id']) : 0;

if (!$client_id) {
    die("معرّف العميل غير صالح.");
}

$stmt = $conn->prepare("SELECT CLIENT_NAME FROM client WHERE CLIENT_ID = ?");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    die("العميل غير موجود.");
}
$client = $result->fetch_assoc();
$client_name = $client['CLIENT_NAME'];

$pdf = new tFPDF();
$pdf->AddPage();
$pdf->AddFont('DejaVu', '', 'DejaVuSans.ttf', true);
$pdf->SetFont('DejaVu', '', 14);
$pdf->SetAutoPageBreak(true, 15);

// عنوان التقرير
$pdf->Cell(0, 10, reverseArabic("قائمة العمليات للعميل: ") . reverseArabic($client_name), 0, 1, 'C');
$pdf->Ln(5);

// رأس الجدول (من اليمين لليسار)
$pdf->SetFont('DejaVu', '', 11);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(30, 10, reverseArabic('التاريخ'), 1, 0, 'C', true);
$pdf->Cell(20, 10, reverseArabic('العملة'), 1, 0, 'C', true);
$pdf->Cell(25, 10, reverseArabic('المبلغ'), 1, 0, 'C', true);
$pdf->Cell(25, 10, reverseArabic('له/عليه'), 1, 0, 'C', true);
$pdf->Cell(30, 10, reverseArabic('المرسل'), 1, 0, 'C', true);
$pdf->Cell(25, 10, reverseArabic('النوع'), 1, 0, 'C', true);
$pdf->Cell(15, 10, 'ID', 1, 0, 'C', true);
$pdf->Ln();

// استعلام البيانات
$query = "SELECT TRA_ID, TYPE, SENDER_NAME, FOR_OR_ON, AMMOUNT, CURRENCY, TRA_DATE FROM transaction WHERE CLIENT_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();

$pdf->SetFont('DejaVu', '', 10);
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(30, 10, $row['TRA_DATE'], 1);
    $pdf->Cell(20, 10, reverseArabic($row['CURRENCY']), 1);
    $pdf->Cell(25, 10, number_format($row['AMMOUNT'], 2), 1);
    $pdf->Cell(25, 10, reverseArabic($row['FOR_OR_ON']), 1);
    $pdf->Cell(30, 10, reverseArabic($row['SENDER_NAME']), 1);
    $pdf->Cell(25, 10, reverseArabic($row['TYPE']), 1);
    $pdf->Cell(15, 10, $row['TRA_ID'], 1);
    $pdf->Ln();
}

$pdf->Output("I", "client_transactions.pdf");
?>
