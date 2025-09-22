<?php
require_once __DIR__ . '/server/connect_sqli.php';
require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

// สร้างเอกสาร Word
$phpWord = new PhpWord();
$section = $phpWord->addSection();

// เพิ่ม "หัวเรื่อง" ภาษาไทย โดยใช้ addText() และ style แทน addTitle()
$section->addText(
    'รายงานภาษาไทย',
    ['name' => 'TH Sarabun New', 'size' => 18, 'bold' => true]
);

// เพิ่มข้อความภาษาไทย
$section->addText(
    'นี่คือเอกสาร Word ภาษาไทย',
    ['name' => 'TH Sarabun New', 'size' => 16]
);

// ส่งไฟล์ Word ให้ดาวน์โหลด
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="'.rawurlencode('รายงาน.docx').'"');
header('Cache-Control: max-age=0');

// สร้าง writer และส่งไฟล์ไป browser
$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save('php://output');
exit;
?>
