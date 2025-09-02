<?php
function expandFormArray($flatArray) {
    $expanded = [];
    foreach ($flatArray as $key => $value) {
        // ดึง base key + keys ย่อยใน []
        if (preg_match_all('/\[([^\]]*)\]/', $key, $matches)) {
            $base = strstr($key, '[', true);
            $keys = array_merge([$base], $matches[1]);
        } else {
            $keys = [$key];
        }

        // สร้าง array ซ้อนลงไปเรื่อย ๆ
        $ref = &$expanded;
        foreach ($keys as $k) {
            if ($k === '') { // กรณี []
                $k = count($ref);
            }
            if (!isset($ref[$k])) {
                $ref[$k] = [];
            }
            $ref = &$ref[$k];
        }

        // ใส่ค่า value
        $ref = $value;
    }
    return $expanded;
}
?>