<?php
// ตั้งค่า Content-Type เป็น JSON
header('Content-Type: application/json');
$file = '../../../../api/languages/langs.json';
// ตรวจสอบว่ามีไฟล์ JSON อยู่หรือไม่
if (file_exists($file) && $_GET['isLang']) {

    $isLang = $_GET['isLang'];

    // อ่านข้อมูลจากไฟล์ JSON
    $json_data = file_get_contents($file);
    $data = json_decode($json_data, true);

    // ตรวจสอบว่าอ่านข้อมูลได้หรือไม่
    if ($data !== null) {

        // ตรวจสอบว่ามีคีย์ '$isLang' หรือไม่ และส่งข้อมูลไปยังหน้าเว็บ
        if (isset($data[$isLang])) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['error' => 'Key "'.$isLang.'" not found in JSON data.']);
        }
        
    } else {
        echo json_encode(['error' => 'Error decoding JSON: ' . json_last_error_msg()]);
    }
} else {
    echo json_encode(['error' => 'File not found.']);
}
?>
