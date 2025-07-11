<?php
// require_once('../../lib/connect.php');
include '../check_permission.php'; 
// ✅ ถ้ามีการเพิ่มใหม่
if (isset($_POST['add_new'])) {
    $type = $_POST['type'] ?? '';
    $content = $_POST['content'] ?? '';
    $image_url = $_POST['image_url'] ?? '';
    $author = $_POST['author'] ?? '';
    $position = $_POST['position'] ?? '';

    if (!empty($content)) {
        $stmt = $conn->prepare("INSERT INTO about_content (type, content, image_url, author, position) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $type, $content, $image_url, $author, $position);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('เพิ่มเนื้อหาใหม่เรียบร้อย'); window.location.href='edit_about.php';</script>";
        exit;
    } else {
        echo "<script>alert('กรุณากรอกเนื้อหา'); window.location.href='edit_about.php';</script>";
        exit;
    }
}

// ✅ ถ้ามีการอัปเดตหลายรายการ
$ids = $_POST['ids'] ?? [];
$types = $_POST['types'] ?? [];
$contents = $_POST['contents'] ?? [];
$images = $_POST['images'] ?? [];
$authors = $_POST['authors'] ?? [];
$positions = $_POST['positions'] ?? [];

for ($i = 0; $i < count($ids); $i++) {
    $stmt = $conn->prepare("UPDATE about_content SET type=?, content=?, image_url=?, author=?, position=? WHERE id=?");
    $stmt->bind_param("sssssi",
        $types[$i],
        $contents[$i],
        $images[$i],
        $authors[$i],
        $positions[$i],
        $ids[$i]
    );
    $stmt->execute();
    $stmt->close();
}

echo "<script>alert('บันทึกการแก้ไขเรียบร้อย'); window.location.href='edit_about.php';</script>";
