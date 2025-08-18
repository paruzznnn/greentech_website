<?php
// *** ตรวจสอบ PATH นี้ให้ถูกต้องที่สุด ***
// สมมติว่า group_management.php อยู่ที่ /admin/set_product/
// check_permission.php อยู่ที่ /admin/check_permission.php
// include '../../../lib/connect.php';
// include '../../../lib/base_directory.php';
include '../check_permission.php';
// require_once '../../../inc/connect_db.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าเชื่อมต่อฐานข้อมูลได้หรือไม่ (ถ้า connect_db.php ไม่ได้ die() เมื่อ error)
if (!isset($conn) || !$conn) {
    die("Connection failed: Database connection not established."); // แสดงข้อผิดพลาดร้ายแรงถ้าเชื่อมต่อไม่ได้
}

// กำหนด base URL ของเว็บของคุณ (สำคัญมากสำหรับการแสดงรูปภาพ)
// ถ้าโปรเจกต์คุณอยู่ภายใต้ http://localhost/trandar/
// กำหนด base URL ของเว็บของคุณ
$base_url = 'http://localhost/trandar/';

// ดึงข้อมูลกลุ่มทั้งหมด พร้อมกับฟิลด์ภาษาอังกฤษ
$main_groups = [];
$sub_groups = [];
$sql_groups = "SELECT group_id, group_name, group_name_en, group_name_cn, description, description_en, description_cn, parent_group_id, image_path FROM dn_shop_groups WHERE del = '0' ORDER BY parent_group_id ASC, group_name ASC";
$result_groups = $conn->query($sql_groups);

if ($result_groups) {
    while ($row = $result_groups->fetch_assoc()) {
        $row['full_image_url_display'] = !empty($row['image_path']) ? htmlspecialchars($row['image_path']) : $base_url . 'public/img/group_placeholder.jpg';
        $row['image_path_for_js'] = !empty($row['image_path']) ? htmlspecialchars($row['image_path'], ENT_QUOTES) : '';

        if ($row['parent_group_id'] === NULL || $row['parent_group_id'] == 0) {
            $main_groups[] = $row;
        } else {
            $sub_groups[] = $row;
        }
    }
} else {
    echo "Error fetching groups: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการหมวดหมู่สินค้า</title>

    <link rel="icon" type="image/x-icon" href="../../../public/img/q-removebg-preview1.png">
    <link href="../../../inc/jquery/css/jquery-ui.css" rel="stylesheet">
    <script src="../../../inc/jquery/js/jquery-3.6.0.min.js"></script>
    <script src="../../../inc/jquery/js/jquery-ui.min.js"></script>
    <link href="../../../inc/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../../inc/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/fontawesome5-fullcss@1.10.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css" integrity="sha512-9xKTRVabjVeZmc+GUW8GgSmcREDunMM+Dt/GrzchfN8tkwHizc5RP4Ok/MXFFy5rIjJjzhndFScTceq5e6GvVQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="../../../inc/sweetalert2/css/sweetalert2.min.css" rel="stylesheet">
    <script src="../../../inc/sweetalert2/js/sweetalert2.all.min.js"></script>
    <link href="../../../inc/select2/css/select2.min.css" rel="stylesheet">
    <script src="../../../inc/select2/js/select2.min.js"></script>
    <link href="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/css/bootstrap-iconpicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/js/bootstrap-iconpicker.bundle.min.js"></script>
    <link href='../css/index_.css?v=<?php echo time(); ?>' rel='stylesheet'>
    <style>
        .group-image-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.5em 1em;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background-color: #007bff;
            color: white !important;
            border-color: #007bff;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #e9ecef;
            border-color: #e9ecef;
        }
        .language-switcher {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 20px;
        }
        .language-switcher img {
            width: 30px;
            height: auto;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color 0.2s ease-in-out;
        }
        .language-switcher img.active {
            border-color: #007bff;
        }
        .lang-thai-fields, .lang-en-fields, .lang-cn-fields {
            display: none;
        }
    </style>
</head>
<?php include '../template/header.php' ?>
<body>
    <div class="content-sticky">
        <div class="container-fluid">
            <div class="box-content">
                <div class="row">
                    <div class="col-12">
                        <div style="margin: 10px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                <h4 class="line-ref">
                                    <i class="fas fa-layer-group"></i> จัดการหมวดหมู่สินค้า
                                </h4>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addGroupModal">
                                    <i class="fa-solid fa-plus"></i> เพิ่มหมวดหมู่
                                </button>
                            </div>

                            <table id="groupsTable" class="table table-hover" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>รูปภาพ</th>
                                        <th>ชื่อหมวดหมู่ (TH)</th>
                                        <th>ชื่อหมวดหมู่ (EN)</th>
                                        <th>ชื่อหมวดหมู่ (CN)</th>
                                        <th>หมวดหมู่หลัก</th>
                                        <th>การจัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($main_groups as $group) {
                                        echo '<tr>';
                                        echo '<td>' . htmlspecialchars($group['group_id']) . '</td>';
                                        echo '<td>';
                                        echo '<img src="' . $group['full_image_url_display'] . '" class="group-image-preview" alt="Group Image">';
                                        echo '</td>';
                                        echo '<td>' . htmlspecialchars($group['group_name']) . '</td>';
                                        echo '<td>' . htmlspecialchars($group['group_name_en']) . '</td>';
                                        echo '<td>' . htmlspecialchars($group['group_name_cn']) . '</td>';
                                        echo '<td>- (หมวดหมู่หลัก)</td>';
                                        echo '<td>';
                                        echo '<button class="btn btn-sm btn-edit me-2" onclick="myApp_editGroup(' . $group['group_id'] . ', \'' . htmlspecialchars($group['group_name'], ENT_QUOTES) . '\', \'' . htmlspecialchars($group['group_name_en'], ENT_QUOTES) . '\', \'' . htmlspecialchars($group['group_name_cn'], ENT_QUOTES) . '\', \'' . htmlspecialchars($group['description'], ENT_QUOTES) . '\', \'' . htmlspecialchars($group['description_en'], ENT_QUOTES) . '\', \'' . htmlspecialchars($group['description_cn'], ENT_QUOTES) . '\', \'main\', \'' . $group['image_path_for_js'] . '\', \'\')"><i class="fas fa-edit"></i> แก้ไข</button>';
                                        echo '<button class="btn btn-sm btn-del" onclick="myApp_deleteGroup(' . $group['group_id'] . ')"><i class="fas fa-trash-alt"></i> ลบ</button>';
                                        echo '</td>';
                                        echo '</tr>';
                                    }

                                    foreach ($sub_groups as $group) {
                                        $parent_name = 'ไม่พบ';
                                        foreach ($main_groups as $main_g) {
                                            if ($main_g['group_id'] == $group['parent_group_id']) {
                                                $parent_name = $main_g['group_name'];
                                                break;
                                            }
                                        }
                                        echo '<tr>';
                                        echo '<td>' . htmlspecialchars($group['group_id']) . '</td>';
                                        echo '<td>-</td>';
                                        echo '<td>' . htmlspecialchars($group['group_name']) . '</td>';
                                        echo '<td>' . htmlspecialchars($group['group_name_en']) . '</td>';
                                        echo '<td>' . htmlspecialchars($group['group_name_cn']) . '</td>';
                                        echo '<td>' . htmlspecialchars($parent_name) . '</td>';
                                        echo '<td>';
                                        echo '<button class="btn btn-sm btn-edit me-2" onclick="myApp_editGroup(' . $group['group_id'] . ', \'' . htmlspecialchars($group['group_name'], ENT_QUOTES) . '\', \'' . htmlspecialchars($group['group_name_en'], ENT_QUOTES) . '\', \'' . htmlspecialchars($group['group_name_cn'], ENT_QUOTES) . '\', \'' . htmlspecialchars($group['description'], ENT_QUOTES) . '\', \'' . htmlspecialchars($group['description_en'], ENT_QUOTES) . '\', \'' . htmlspecialchars($group['description_cn'], ENT_QUOTES) . '\', \'sub\', \'\', ' . (is_null($group['parent_group_id']) ? 'null' : htmlspecialchars($group['parent_group_id'])) . ')"><i class="fas fa-edit"></i> แก้ไข</button>';
                                        echo '<button class="btn btn-sm btn-del" onclick="myApp_deleteGroup(' . $group['group_id'] . ')"><i class="fas fa-trash-alt"></i> ลบ</button>';
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addGroupModal" tabindex="-1" aria-labelledby="addGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addGroupForm" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addGroupModalLabel">เพิ่มหมวดหมู่ใหม่</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="language-switcher mb-3">
                            <img src="https://flagcdn.com/w320/th.png" alt="Thai" class="lang-flag active" data-lang="th">
                            <img src="https://flagcdn.com/w320/gb.png" alt="English" class="lang-flag" data-lang="en">
                            <img src="https://flagcdn.com/w320/cn.png" alt="Chinese" class="lang-flag" data-lang="cn">
                        </div>
                        <div class="lang-thai-fields" style="display:block;">
                            <div class="mb-3">
                                <label for="newGroupName" class="form-label">ชื่อหมวดหมู่ (TH)</label>
                                <input type="text" class="form-control" id="newGroupName" name="group_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="newGroupDescription" class="form-label">คำอธิบาย (TH)</label>
                                <textarea class="form-control" id="newGroupDescription" name="description" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="lang-en-fields">
                            <div class="mb-3">
                                <label for="newGroupNameEn" class="form-label">ชื่อหมวดหมู่ (EN)</label>
                                <input type="text" class="form-control" id="newGroupNameEn" name="group_name_en">
                            </div>
                            <div class="mb-3">
                                <label for="newGroupDescriptionEn" class="form-label">คำอธิบาย (EN)</label>
                                <textarea class="form-control" id="newGroupDescriptionEn" name="description_en" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="lang-cn-fields">
                            <div class="mb-3">
                                <label for="newGroupNameCn" class="form-label">ชื่อหมวดหมู่ (CN)</label>
                                <input type="text" class="form-control" id="newGroupNameCn" name="group_name_cn">
                            </div>
                            <div class="mb-3">
                                <label for="newGroupDescriptionCn" class="form-label">คำอธิบาย (CN)</label>
                                <textarea class="form-control" id="newGroupDescriptionCn" name="description_cn" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="newParentGroupId" class="form-label">หมวดหมู่หลัก (ถ้ามี)</label>
                            <select class="form-select" id="newParentGroupId" name="parent_group_id">
                                <option value="">- เลือกหมวดหมู่หลัก -</option>
                                <?php
                                foreach ($main_groups as $main_g) {
                                    echo '<option value="' . $main_g['group_id'] . '">' . htmlspecialchars($main_g['group_name']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="newGroupImage" class="form-label">รูปภาพหมวดหมู่ (สำหรับกลุ่มหลักเท่านั้น)</label>
                            <input type="file" class="form-control" id="newGroupImage" name="group_image" accept="image/*">
                            <img id="newGroupImagePreview" src="#" alt="Image Preview" style="display:none; max-width: 150px; margin-top: 10px;">
                            <small class="text-muted">ขนาดไฟล์ไม่เกิน 5MB (JPG, JPEG, PNG, GIF)</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editGroupModal" tabindex="-1" aria-labelledby="editGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editGroupForm" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editGroupModalLabel">แก้ไขหมวดหมู่</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editGroupId" name="group_id">
                        <input type="hidden" id="editGroupType" name="group_type">
                        <div class="language-switcher mb-3">
                            <img src="https://flagcdn.com/w320/th.png" alt="Thai" class="lang-flag active" data-lang="th">
                            <img src="https://flagcdn.com/w320/gb.png" alt="English" class="lang-flag" data-lang="en">
                            <img src="https://flagcdn.com/w320/cn.png" alt="Chinese" class="lang-flag" data-lang="cn">
                        </div>

                        <div class="lang-thai-fields" style="display:block;">
                            <div class="mb-3">
                                <label for="editGroupName" class="form-label">ชื่อหมวดหมู่ (TH)</label>
                                <input type="text" class="form-control" id="editGroupName" name="group_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="editGroupDescription" class="form-label">คำอธิบาย (TH)</label>
                                <textarea class="form-control" id="editGroupDescription" name="description" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="lang-en-fields">
                            <div class="mb-3">
                                <label for="editGroupNameEn" class="form-label">ชื่อหมวดหมู่ (EN)</label>
                                <input type="text" class="form-control" id="editGroupNameEn" name="group_name_en">
                            </div>
                            <div class="mb-3">
                                <label for="editGroupDescriptionEn" class="form-label">คำอธิบาย (EN)</label>
                                <textarea class="form-control" id="editGroupDescriptionEn" name="description_en" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="lang-cn-fields">
                            <div class="mb-3">
                                <label for="editGroupNameCn" class="form-label">ชื่อหมวดหมู่ (CN)</label>
                                <input type="text" class="form-control" id="editGroupNameCn" name="group_name_cn">
                            </div>
                            <div class="mb-3">
                                <label for="editGroupDescriptionCn" class="form-label">คำอธิบาย (CN)</label>
                                <textarea class="form-control" id="editGroupDescriptionCn" name="description_cn" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="mb-3" id="editParentGroupContainer">
                            <label for="editParentGroupId" class="form-label">หมวดหมู่หลัก (ถ้ามี)</label>
                            <select class="form-select" id="editParentGroupId" name="parent_group_id">
                                <option value="">- เลือกหมวดหมู่หลัก -</option>
                                <?php
                                foreach ($main_groups as $main_g) {
                                    echo '<option value="' . $main_g['group_id'] . '">' . htmlspecialchars($main_g['group_name']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3" id="editImageContainer">
                            <label for="editGroupImage" class="form-label">รูปภาพหมวดหมู่ (สำหรับกลุ่มหลักเท่านั้น)</label>
                            <input type="file" class="form-control" id="editGroupImage" name="group_image" accept="image/*">
                            <img id="editGroupImagePreview" src="#" alt="Image Preview" style="max-width: 150px; margin-top: 10px; display: none;">
                            <p class="text-muted mt-2">ปล่อยว่างหากไม่ต้องการเปลี่ยนรูปภาพ. ขนาดไฟล์ไม่เกิน 5MB (JPG, JPEG, PNG, GIF)</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src='../js/index_.js?v=<?php echo time(); ?>'></script>
    <script>
        const GLOBAL_APP_BASE_URL = '<?php echo $base_url; ?>';
        const GLOBAL_APP_PLACEHOLDER_IMAGE = GLOBAL_APP_BASE_URL + 'public/img/group_placeholder.jpg';

        window.myApp_editGroup = function(groupId, groupName, groupNameEn, groupNameCn, description, descriptionEn, descriptionCn, groupType, imagePath, parentGroupId = null) {
            console.log("DEBUG: myApp_editGroup called with:", { groupId, groupName, groupNameEn, groupNameCn, description, descriptionEn, descriptionCn, groupType, imagePath, parentGroupId });

            $('#editGroupId').val(groupId);
            $('#editGroupType').val(groupType);

            // เติมข้อมูลภาษาไทย, อังกฤษ, และจีน
            $('#editGroupName').val(groupName);
            $('#editGroupNameEn').val(groupNameEn);
            $('#editGroupNameCn').val(groupNameCn);
            $('#editGroupDescription').val(description);
            $('#editGroupDescriptionEn').val(descriptionEn);
            $('#editGroupDescriptionCn').val(descriptionCn);

            // Reset image input and preview
            $('#editGroupImage').val('');
            $('#editGroupImagePreview').hide().attr('src', '');
            $('#editGroupImagePreview').data('current-image', '');

            if (groupType === 'main') {
                $('#editParentGroupContainer').hide();
                $('#editParentGroupId').val('');
                $('#editImageContainer').show();
                if (imagePath) {
                    $('#editGroupImagePreview').attr('src', imagePath).show();
                    $('#editGroupImagePreview').data('current-image', imagePath);
                } else {
                    $('#editGroupImagePreview').attr('src', GLOBAL_APP_PLACEHOLDER_IMAGE).show();
                    $('#editGroupImagePreview').data('current-image', GLOBAL_APP_PLACEHOLDER_IMAGE);
                }
            } else { // sub group
                $('#editParentGroupContainer').show();
                $('#editParentGroupId').val(parentGroupId === 'null' ? '' : parentGroupId);
                $('#editImageContainer').hide();
                $('#editGroupImagePreview').hide().attr('src', '');
                $('#editGroupImagePreview').data('current-image', '');
            }

            // แสดง Modal
            $('#editGroupModal').modal('show');
            // ตั้งค่าเริ่มต้นให้แสดงภาษาไทย
            $('.lang-flag[data-lang="th"]').click();
        }

        window.myApp_deleteGroup = function(groupId) {
            console.log("DEBUG: myApp_deleteGroup called for ID:", groupId);
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: "คุณต้องการลบหมวดหมู่นี้หรือไม่? สินค้าภายใต้หมวดหมู่นี้จะไม่มีหมวดหมู่",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'group_actions.php',
                        type: 'POST',
                        data: {
                            action: 'delete_group',
                            group_id: groupId
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log("DEBUG: Delete Group Response:", response);
                            Swal.fire({
                                icon: response.status,
                                title: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                if (response.status === 'success') {
                                    location.reload();
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error (Delete Group):", status, error, xhr.responseText);
                            Swal.fire('เกิดข้อผิดพลาด!', 'ไม่สามารถลบหมวดหมู่ได้. โปรดตรวจสอบ Console และ Network tab สำหรับรายละเอียด.', 'error');
                        }
                    });
                }
            })
        }

        $(document).ready(function() {
            console.log("DEBUG: DOM is ready. Initializing DataTables and Form Event Listeners.");

            if (typeof jQuery === 'undefined') {
                console.error("ERROR: jQuery is not loaded!");
                return;
            }
            if (typeof $.fn.DataTable === 'undefined') {
                console.error("ERROR: DataTables is not loaded!");
                return;
            }

            $('#groupsTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "language": {
                    // "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Thai.json"
                }
            });

            // Add Group Modal Image Preview
            $('#newGroupImage').change(function() {
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#newGroupImagePreview').attr('src', e.target.result).show();
                    }
                    reader.readAsDataURL(this.files[0]);
                } else {
                    $('#newGroupImagePreview').hide();
                }
            });

            // Edit Group Modal Image Preview
            $('#editGroupImage').change(function() {
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#editGroupImagePreview').attr('src', e.target.result).show();
                    }
                    reader.readAsDataURL(this.files[0]);
                } else {
                    var currentImage = $('#editGroupImagePreview').data('current-image');
                    if (currentImage) {
                        $('#editGroupImagePreview').attr('src', currentImage).show();
                    } else {
                        $('#editGroupImagePreview').hide();
                    }
                }
            });

            // Handle language switching in Modals
            function setupLanguageSwitcher(modalId) {
                $(modalId + ' .lang-flag').on('click', function() {
                    const lang = $(this).data('lang');
                    $(modalId + ' .lang-flag').removeClass('active');
                    $(this).addClass('active');

                    $(modalId + ' .lang-thai-fields').hide();
                    $(modalId + ' .lang-en-fields').hide();
                    $(modalId + ' .lang-cn-fields').hide();

                    if (lang === 'th') {
                        $(modalId + ' .lang-thai-fields').show();
                    } else if (lang === 'en') {
                        $(modalId + ' .lang-en-fields').show();
                    } else if (lang === 'cn') {
                        $(modalId + ' .lang-cn-fields').show();
                    }
                });
            }

            setupLanguageSwitcher('#addGroupModal');
            setupLanguageSwitcher('#editGroupModal');

            // Add Group Form Submission
            $('#addGroupForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                formData.append('action', 'add_group');

                console.log("DEBUG: Adding Group with FormData:", Object.fromEntries(formData.entries()));

                $.ajax({
                    url: 'group_actions.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        console.log("DEBUG: Add Group Response:", response);
                        Swal.fire({
                            icon: response.status,
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            if (response.status === 'success') {
                                $('#addGroupModal').modal('hide');
                                location.reload();
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error (Add Group):", status, error, xhr.responseText);
                        Swal.fire('เกิดข้อผิดพลาด!', 'ไม่สามารถเพิ่มหมวดหมู่ได้. โปรดตรวจสอบ Console และ Network tab สำหรับรายละเอียด.', 'error');
                    }
                });
            });

            // Edit Group Form Submission
            $('#editGroupForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                formData.append('action', 'edit_group');

                console.log("DEBUG: Editing Group with FormData:", Object.fromEntries(formData.entries()));

                $.ajax({
                    url: 'group_actions.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        console.log("DEBUG: Edit Group Response:", response);
                        Swal.fire({
                            icon: response.status,
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            if (response.status === 'success') {
                                $('#editGroupModal').modal('hide');
                                location.reload();
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error (Edit Group):", status, error, xhr.responseText);
                        Swal.fire('เกิดข้อผิดพลาด!', 'ไม่สามารถแก้ไขหมวดหมู่ได้. โปรดตรวจสอบ Console และ Network tab สำหรับรายละเอียด.', 'error');
                    }
                });
            });
        });
    </script>
</body>
</html>