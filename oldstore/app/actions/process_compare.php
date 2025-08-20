<?php
header('Content-Type: application/json');
session_start();

function getCompareContents() {
    return isset($_SESSION['compare']) ? $_SESSION['compare'] : array();
}

function saveCompareContents($compare) {
    $_SESSION['compare'] = $compare;
}

function addItemCompare($data) {
    $compare = getCompareContents(); 

    $item_compare_key = $data['pro_id'];
    if (!isset($compare[$item_compare_key])) {
        $compare[$item_compare_key] = $data;
    }

    saveCompareContents($compare);
}

function removeItemCompare($itemKey) {
    $compare = getCompareContents();

    if (isset($compare[$itemKey])) {
        unset($compare[$itemKey]);
    }

    saveCompareContents($compare);
}

function clearCompare() {
    unset($_SESSION['compare']); // ลบข้อมูลการเปรียบเทียบทั้งหมด
}

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $check_status = false;

    if ($action == 'add_compare') {
        if (isset($_POST['compareData'])) {
            $compareData = $_POST['compareData'];
            addItemCompare($compareData);
        }
        $check_status = true;
    } elseif ($action == 'removeCompare_item') {
        $itemKey = $_POST['item_key'];
        removeItemCompare($itemKey);
        $check_status = true;
    } elseif ($action == 'clear_compare') {
        clearCompare(); // เพิ่มเงื่อนไขเพื่อเคลียร์ข้อมูลการเปรียบเทียบ
        $check_status = true;
    }

    echo json_encode([
        'status' => $check_status,
        'data' => getCompareContents()
    ]);
    exit;
}
?>
