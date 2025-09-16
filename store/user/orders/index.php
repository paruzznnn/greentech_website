<?php
require_once __DIR__ . '/../../vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML('<h1>สวัสดี mPDF!</h1><p>นี่คือ PDF ตัวอย่าง</p>');
$mpdf->Output();
?>