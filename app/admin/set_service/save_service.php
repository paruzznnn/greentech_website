<?php
include '../check_permission.php'; 

// 1. อัปเดตข้อมูลที่มีอยู่
if (!empty($_POST['ids'])) {
    foreach ($_POST['ids'] as $index => $id) {
        $type = $_POST['types'][$index];
        $content = $_POST['contents'][$index];
        $image = $_POST['images'][$index];
        $author = $_POST['authors'][$index];
        $position = $_POST['positions'][$index];

        $stmt = $conn->prepare("UPDATE service_content SET type=?, content=?, image_url=?, author=?, position=? WHERE id=?");
        $stmt->bind_param("sssssi", $type, $content, $image, $author, $position, $id);
        $stmt->execute();
        $stmt->close();
    }
}

// 2. เพิ่มเนื้อหาใหม่ (ถ้ามี)
if (!empty($_POST['new_content'])) {
    $new_type = $_POST['new_type'];
    $new_content = $_POST['new_content'];
    $new_image = $_POST['new_image'];
    $new_author = $_POST['new_author'];
    $new_position = $_POST['new_position'];

    $stmt = $conn->prepare("INSERT INTO service_content (type, content, image_url, author, position) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $new_type, $new_content, $new_image, $new_author, $new_position);
    $stmt->execute();
    $stmt->close();
}

echo "<script>alert('บันทึกเรียบร้อย'); window.location.href='edit_service.php';</script>";
