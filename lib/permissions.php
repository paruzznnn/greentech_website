<?php
function checkPermissions($data)
{
    if(!empty($data)){
        global $conn;
        $sql = "SELECT
            mbu.user_id,
            GROUP_CONCAT(DISTINCT mbr.role_id) AS role_id,
            GROUP_CONCAT(DISTINCT mbr.role_type) AS roles,
            GROUP_CONCAT(DISTINCT acrp.permiss_id) AS permiss_id,
            GROUP_CONCAT(DISTINCT mbp.permiss_type) AS permissions,
            GROUP_CONCAT(DISTINCT acmp.menu_id) AS menus_id,
            GROUP_CONCAT(DISTINCT mlm.menu_label) AS accessible_menus
        FROM
            mb_user mbu
        LEFT JOIN 
            acc_user_roles acur ON acur.user_id = mbu.user_id
        LEFT JOIN 
            mb_roles mbr ON mbr.role_id = acur.role_id
        LEFT JOIN 
            acc_role_permissions acrp ON acrp.role_id = mbr.role_id
        LEFT JOIN 
            mb_permissions mbp ON mbp.permiss_id = acrp.permiss_id
        LEFT JOIN 
            acc_menu_permissions acmp ON acmp.role_id = mbr.role_id
        LEFT JOIN 
            ml_menus mlm ON mlm.menu_id = acmp.menu_id
        WHERE 
            mbu.user_id = ?
            AND acrp.del = ?
            AND acmp.del = ?
        GROUP BY
            mbu.user_id";

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(["status" => "error", "message" => "Database error: Unable to prepare statement"]);
            exit();
        }

        $user_id = $data['user_id'];
        $del = 0;
        $stmt->bind_param("iii", $user_id, $del, $del);
        $stmt->execute();
        $result = $stmt->get_result();
        $userData = $result->fetch_assoc();

        return $userData;
    }

}

function checkSession($data){
    global $base_path;
    global $base_path_admin;

    if (empty($data)) {
        header("Location: {$base_path_admin}logout.php");
        exit();
    }

    // if ($data['exp'] < time()) {
    //     header("Location: {$base_path_admin}logout.php");
    //     exit();
    // }

    if ($data['role_id'] <= 0) {
        header("Location: {$base_path_admin}logout.php");
        exit();
    }
}



