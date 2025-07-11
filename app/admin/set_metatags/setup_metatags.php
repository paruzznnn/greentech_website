<?php
require_once('../../../lib/connect.php');
global $conn;

$page_name = $_POST['page_name'] ?? '';
$meta_title = $_POST['meta_title'] ?? '';
$meta_description = $_POST['meta_description'] ?? '';
$meta_keywords = $_POST['meta_keywords'] ?? '';
$og_title = $_POST['og_title'] ?? '';
$og_description = $_POST['og_description'] ?? '';
$id = $_POST['id'] ?? null;

$default_image = '../../public/img/q-removebg-preview1.png'; // ✅ default
$og_image_path = $default_image;

if (isset($_FILES['og_image']) && $_FILES['og_image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../../public/img/';
    $fileName = basename($_FILES['og_image']['name']);
    $targetPath = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['og_image']['tmp_name'], $targetPath)) {
        $og_image_path = '../../public/img/' . $fileName; // ✅ สำหรับบันทึกใน DB (ใช้ path เดียวกับที่ดึงรูปได้)
    }
} elseif ($id) {
    // กรณี edit แล้วไม่ได้อัปโหลดภาพใหม่ → ใช้ภาพเดิมจาก DB
    $stmtOld = $conn->prepare("SELECT og_image FROM metatags WHERE id = ?");
    $stmtOld->bind_param("i", $id);
    $stmtOld->execute();
    $resultOld = $stmtOld->get_result();
    if ($rowOld = $resultOld->fetch_assoc()) {
        $og_image_path = $rowOld['og_image'] ?: $default_image;
    }
}

if ($id) {
    $stmt = $conn->prepare("UPDATE metatags SET page_name=?, meta_title=?, meta_description=?, meta_keywords=?, og_title=?, og_description=?, og_image=? WHERE id=?");
    $stmt->bind_param("sssssssi", $page_name, $meta_title, $meta_description, $meta_keywords, $og_title, $og_description, $og_image_path, $id);
} else {
    $stmt = $conn->prepare("INSERT INTO metatags (page_name, meta_title, meta_description, meta_keywords, og_title, og_description, og_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $page_name, $meta_title, $meta_description, $meta_keywords, $og_title, $og_description, $og_image_path);
}

if ($stmt->execute()) {
    header("Location: list_metatags.php");
    exit;
} else {
    echo "❌ บันทึกข้อมูลไม่สำเร็จ: " . $conn->error;
}
