<?php
header('Content-Type: application/json');

// อ่านข้อมูล JSON ที่ส่งมาจาก JavaScript
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);
// ตรวจสอบว่าข้อมูลที่รับมาถูกต้องหรือไม่
if (empty($data) || !isset($data['content'])) {
    echo json_encode(['error' => 'Invalid data received']);
    http_response_code(400); // Bad Request
    exit;
}
// ทดสอบส่งข้อมูลไป API
// header('Content-Type: application/json; charset=UTF-8');
function testTranslationAPI($data) {
    // ข้อมูลทดสอบ
    $language = $data['language'];
$translate = $data['translate'];
$company = $data['company'];
 $code = $data['code'];
 $content = $data['content']; // content จะเป็น array/object ที่มี subject, description, content อยู่ข้างใน
    $test_data = [
        'company' => '2', // comp_id ของคุณ
        'code' => $code,
        'language' => $language,
        'translate' => $translate, 
        'content' => $content
    ];
    
    // เข้ารหัสข้อมูล
    $encoded_data = urlencode(json_encode($test_data, JSON_UNESCAPED_UNICODE));
    
    $url = "https://www.origami.life/api/website/translate.php"; // URL API ของคุณ
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "data=" . $encoded_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    
    if($httpCode === 200) {
        $json_result = json_decode($result, true);
        if($json_result && $json_result['status'] === 'success') {
            return [
                'status' => 'success',
                'subject' => $json_result['data']['translated']['subject'],
                'description' => $json_result['data']['translated']['description'],
                'content' => $json_result['data']['translated']['content'],
            ];
        }
    }
    return [
        'status' => 'error'
    ];
}

// ข้อมูลที่ได้รับจาก JavaScript

// เรียกใช้ฟังก์ชันทดสอบ
$result = testTranslationAPI($data);
echo json_encode($result);
?>