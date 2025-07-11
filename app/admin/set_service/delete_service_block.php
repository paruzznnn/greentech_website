<?php
// ตั้งค่าหัวข้อให้ส่ง JSON
header('Content-Type: application/json');

// เชื่อมต่อฐานข้อมูล
require_once('../../../lib/connect.php'); // เปลี่ยน path ให้ตรงกับโปรเจกต์ของคุณ

// รับค่า ID
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// เตรียม response
$response = ['success' => false, 'message' => ''];

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM service_content WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "ลบสำเร็จ";
    } else {
        $response['message'] = "ลบไม่สำเร็จ: " . $stmt->error;
    }

    $stmt->close();
} else {
    $response['message'] = "ID ไม่ถูกต้อง";
}

$conn->close();
echo json_encode($response);
exit;
