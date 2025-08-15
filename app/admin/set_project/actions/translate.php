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

// ข้อมูลที่ได้รับจาก JavaScript
$language = $data['language'];
$translate = $data['translate'];
$company = $data['company'];
$content = $data['content']; // content จะเป็น array/object ที่มี subject, description, content อยู่ข้างใน

// สร้างข้อมูลสำหรับส่งไป API ภายนอก
$api_data = [
    'language' => $language,
    'translate' => $translate,
    'company' => $company,
    'content' => json_encode($content) // ต้อง encode อีกครั้งเพราะ API เดิมต้องการ string
];

$url = "https://www.origami.life/api/website/translate.php";
$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer' // อาจจะต้องระบุ token ที่ถูกต้อง
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($api_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
print_r($result); 
// ส่งผลลัพธ์กลับไปยัง JavaScript
echo $httpCode;
if ($httpCode !== 200) {
    echo json_encode(['error' => 'API request failed', 'http_code' => $httpCode]);
} else {
    // ส่งผลลัพธ์จาก API กลับไปให้ JavaScript โดยตรง
    echo $result;
}
?>