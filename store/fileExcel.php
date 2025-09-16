<?php
require_once __DIR__ . '/server/connect_sqli.php';
require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// กำหนดค่าเซลล์
$sheet->setCellValue('A1', 'ชื่อ');
$sheet->setCellValue('B1', 'คะแนน');
$sheet->setCellValue('A2', 'สมชาย');
$sheet->setCellValue('B2', 95);

// ตั้งฟอนต์ไทยให้สวย
$sheet->getStyle('A1:B2')->getFont()->setName('TH Sarabun New')->setSize(14);

// ส่งไฟล์ไป browser ให้ดาวน์โหลด
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
