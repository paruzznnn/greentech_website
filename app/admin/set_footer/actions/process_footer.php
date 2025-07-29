<?php
// actions/process_footer.php

// สำหรับ Debugging เท่านั้น: แสดง Error บนหน้าจอ
// ควรปิดเมื่อนำไปใช้จริงบน Production Server เพื่อความปลอดภัย
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
require_once(__DIR__ . '/../../../../lib/base_directory.php');
require_once(__DIR__ . '/../../../../lib/connect.php');
require_once(__DIR__ . '/../../../../inc/getFunctions.php');
global $conn;
global $base_path; // $base_path อาจจะไม่ได้ใช้โดยตรงในไฟล์นี้ แต่เก็บไว้เผื่ออนาคต

$response = ['status' => 'error', 'message' => 'Invalid request.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_footer') {
    $footer_id = $_POST['footer_id'] ?? 0;

    if ($footer_id != 1) { // เราจัดการแค่ Footer ชุดเดียว (ID 1)
        $response['message'] = 'Invalid Footer ID.';
        echo json_encode($response);
        exit;
    }

    // ดึงข้อมูลจาก POST
    $bg_color = $_POST['bg_color'] ?? '#393939';
    $footer_top_title = $_POST['footer_top_title'] ?? '';
    $footer_top_subtitle = $_POST['footer_top_subtitle'] ?? '';
    $about_heading = $_POST['about_heading'] ?? '';
    $about_text = $_POST['about_text'] ?? '';
    $contact_heading = $_POST['contact_heading'] ?? '';
    $contact_address = $_POST['contact_address'] ?? '';
    $contact_phone = $_POST['contact_phone'] ?? '';
    $contact_email = $_POST['contact_email'] ?? '';
    $contact_hours_wk = $_POST['contact_hours_wk'] ?? '';
    $contact_hours_sat = $_POST['contact_hours_sat'] ?? '';
    $social_heading = $_POST['social_heading'] ?? '';
    $copyright_text = $_POST['copyright_text'] ?? '';
    
    // Social links มาในรูปแบบ JSON string จาก frontend
    $social_links_json_string = $_POST['social_links_json'] ?? '[]';
    
    // ตรวจสอบและ Validate JSON
    $social_links_array = json_decode($social_links_json_string, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $response['message'] = 'Invalid Social Links JSON data: ' . json_last_error_msg();
        echo json_encode($response);
        exit;
    }
    // Encode กลับเป็น JSON string เพื่อเก็บใน DB (เผื่อมีการทำความสะอาดข้อมูลใน array ก่อน)
    $social_links_json_for_db = json_encode($social_links_array);


    try {
        $stmt = $conn->prepare("UPDATE footer_settings SET 
            bg_color = ?,
            footer_top_title = ?,
            footer_top_subtitle = ?,
            about_heading = ?,
            about_text = ?,
            contact_heading = ?,
            contact_address = ?,
            contact_phone = ?,
            contact_email = ?,
            contact_hours_wk = ?,
            contact_hours_sat = ?,
            social_heading = ?,
            social_links_json = ?,
            copyright_text = ?,
            updated_at = NOW()
            WHERE id = ?");
        
        $stmt->bind_param("ssssssssssssssi",
            $bg_color,
            $footer_top_title,
            $footer_top_subtitle,
            $about_heading,
            $about_text,
            $contact_heading,
            $contact_address,
            $contact_phone,
            $contact_email,
            $contact_hours_wk,
            $contact_hours_sat,
            $social_heading,
            $social_links_json_for_db,
            $copyright_text,
            $footer_id
        );

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Footer updated successfully.';
        } else {
            throw new Exception('Failed to update footer in database: ' . $stmt->error);
        }
        $stmt->close();
    } catch (Exception $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
}

echo json_encode($response);
exit;
?>