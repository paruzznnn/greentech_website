<?php
// ตั้งค่า Content-Type เป็น JSON
header('Content-Type: application/json');

if ($_POST['action'] === 'add') {

    $isLang = $_POST['language_name'] ?? '';
    $language_key = $_POST['language_key'];
    $language_word = $_POST['language_word'];

    $file = '../../../../api/languages/langs.json';

    // อ่านข้อมูล JSON ที่มีอยู่
    $json_data = file_get_contents($file);
    $array_data = json_decode($json_data, true);

    // ตรวจสอบว่ามีข้อมูลใน "isLang" หรือไม่
    if (!isset($array_data[$isLang])) {
        $array_data[$isLang] = array();
    }

    // เพิ่มข้อมูลใหม่เข้าไปใน "isLang"
    $array_data[$isLang][$language_key] = $language_word;

    // เขียนข้อมูลลงไฟล์ โดยไม่เข้ารหัส Unicode
    file_put_contents($file, json_encode($array_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo json_encode(["message" => "Data added successfully!"]);

}else if ($_POST['action'] === 'update') {

    $isLang = $_POST['language_name'] ?? '';
    $language_key = $_POST['language_key'];
    $language_word = $_POST['language_word'];

    $file = '../../../../api/languages/langs.json';

    // อ่านข้อมูล JSON ที่มีอยู่
    $json_data = file_get_contents($file);
    $array_data = json_decode($json_data, true);

    // ตรวจสอบว่ามีข้อมูลใน "isLang" หรือไม่ ถ้าไม่มีให้สร้าง array
    if (!isset($array_data[$isLang])) {
        $array_data[$isLang] = array();
    }

    // ตรวจสอบว่า "language_key" มีอยู่แล้วหรือไม่
    if (isset($array_data[$isLang][$language_key])) {
        // ถ้ามีอยู่แล้ว ให้ update ค่าใหม่
        $array_data[$isLang][$language_key] = $language_word;
    } else {
        // ถ้ายังไม่มี ให้เพิ่มข้อมูลใหม่
        $array_data[$isLang][$language_key] = $language_word;
    }

    // เขียนข้อมูลลงไฟล์ โดยไม่เข้ารหัส Unicode
    file_put_contents($file, json_encode($array_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // echo "Data added or updated successfully!";
    echo json_encode(["message" => "Data added or updated successfully!"]);

}else if ($_POST['action'] === 'delete') {

    $isLang = $_POST['language_name'] ?? '';
    $language_key = $_POST['language_key'];

    $file = '../../../../api/languages/langs.json';

    // อ่านข้อมูล JSON ที่มีอยู่
    $json_data = file_get_contents($file);
    $array_data = json_decode($json_data, true);

    // ตรวจสอบว่ามีข้อมูลใน "isLang" หรือไม่ และตรวจสอบว่ามี "language_key" อยู่หรือไม่
    if (isset($array_data[$isLang][$language_key])) {
        // ลบข้อมูล "language_key"
        unset($array_data[$isLang][$language_key]);

        // ถ้าภาษานั้นไม่มีข้อมูลเหลืออยู่แล้ว ให้ลบภาษาด้วย
        if (empty($array_data[$isLang])) {
            unset($array_data[$isLang]);
        }

        // เขียนข้อมูลใหม่ลงไฟล์ โดยไม่เข้ารหัส Unicode
        file_put_contents($file, json_encode($array_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // echo "Data deleted successfully!";
        echo json_encode(["message" => "Data deleted successfully!"]);
    } else {
        // echo "No data found for deletion.";
        echo json_encode(["message" => "No data found for deletion."]);

    }
}



?>