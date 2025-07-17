<?php 
// *** ตรวจสอบ PATH นี้ให้ถูกต้องที่สุด ***
// สมมติว่า group_management.php อยู่ที่ /admin/set_product/
// check_permission.php อยู่ที่ /admin/check_permission.php
// connect_db.php อยู่ที่ /inc/connect_db.php
include '../check_permission.php'; // ตรวจสอบสิทธิ์การเข้าถึง 
// require_once '../../../inc/connect_db.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าเชื่อมต่อฐานข้อมูลได้หรือไม่ (ถ้า connect_db.php ไม่ได้ die() เมื่อ error)
if (!isset($conn) || !$conn) {
    die("Connection failed: Database connection not established."); // แสดงข้อผิดพลาดร้ายแรงถ้าเชื่อมต่อไม่ได้
}

// กำหนด base URL ของเว็บของคุณ (สำคัญมากสำหรับการแสดงรูปภาพ)
// ถ้าโปรเจกต์คุณอยู่ภายใต้ http://localhost/trandar/
$base_url = 'http://localhost/trandar/'; 

// ดึงข้อมูลกลุ่มทั้งหมด
$main_groups = [];
$sub_groups = [];
$sql_groups = "SELECT group_id, group_name, parent_group_id, image_path FROM dn_shop_groups WHERE del = '0' ORDER BY parent_group_id ASC, group_name ASC";
$result_groups = $conn->query($sql_groups);

if ($result_groups) {
    while ($row = $result_groups->fetch_assoc()) {
        // เตรียม full_image_url สำหรับการแสดงผลใน HTML/JS
        // เนื่องจาก image_path ใน DB เป็น URL เต็มอยู่แล้ว (จากการที่คุณเคยให้รูปมา)
        $row['full_image_url_display'] = !empty($row['image_path']) ? htmlspecialchars($row['image_path']) : $base_url . 'public/img/group_placeholder.jpg';
        
        // เตรียม image_path_for_js สำหรับส่งไปให้ JS (ควรเป็น URL เต็มเหมือนกัน)
        // หรือถ้าอยากส่งเป็น path สัมพัทธ์ ต้องปรับ JS ให้ต่อ BASE_URL เอาเอง
        // ณ ที่นี้ เราจะส่งเป็น URL เต็มเหมือนเดิมเพื่อให้ JS ไม่ต้องแปลง
        $row['image_path_for_js'] = !empty($row['image_path']) ? htmlspecialchars($row['image_path'], ENT_QUOTES) : '';


        if ($row['parent_group_id'] === NULL || $row['parent_group_id'] == 0) { // ถือว่าเป็นกลุ่มแม่
            $main_groups[] = $row;
        } else {
            $sub_groups[] = $row;
        }
    }
} else {
    // กรณี Query ผิดพลาด
    echo "Error fetching groups: " . $conn->error;
}
// ไม่ต้องปิด $conn ตรงนี้ เพราะ Modal ด้านล่างยังต้องใช้
// $conn->close(); // เราจะปิด connection เมื่อจบไฟล์ PHP นี้ หรือใน connect_db.php ถ้ามีการจัดการที่ดี

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
                                        <th>ชื่อหมวดหมู่</th>
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
                                        // ใช้ full_image_url_display ที่เตรียมไว้แล้วสำหรับ src ของ img
                                        echo '<img src="' . $group['full_image_url_display'] . '" class="group-image-preview" alt="Group Image">';
                                        echo '</td>';
                                        echo '<td>' . htmlspecialchars($group['group_name']) . '</td>';
                                        echo '<td>- (หมวดหมู่หลัก)</td>';
                                        echo '<td>';
                                        // ส่ง image_path_for_js (ซึ่งเป็น URL เต็ม) ให้ฟังก์ชัน JavaScript
                                        echo '<button class="btn btn-sm btn-edit me-2" onclick="myApp_editGroup(' . $group['group_id'] . ', \'' . htmlspecialchars($group['group_name'], ENT_QUOTES) . '\', \'main\', \'' . $group['image_path_for_js'] . '\', \'\')"><i class="fas fa-edit"></i> แก้ไข</button>';
                                        echo '<button class="btn btn-sm btn-del" onclick="myApp_deleteGroup(' . $group['group_id'] . ')"><i class="fas fa-trash-alt"></i> ลบ</button>';
                                        echo '</td>';
                                        echo '</tr>';
                                    }

                                    foreach ($sub_groups as $group) {
                                        // ค้นหาชื่อกลุ่มแม่
                                        $parent_name = 'ไม่พบ';
                                        foreach ($main_groups as $main_g) {
                                            if ($main_g['group_id'] == $group['parent_group_id']) {
                                                $parent_name = $main_g['group_name'];
                                                break;
                                            }
                                        }
                                        echo '<tr>';
                                        echo '<td>' . htmlspecialchars($group['group_id']) . '</td>';
                                        echo '<td>-</td>'; // กลุ่มย่อยไม่มีรูปภาพ
                                        echo '<td>' . htmlspecialchars($group['group_name']) . '</td>';
                                        echo '<td>' . htmlspecialchars($parent_name) . '</td>';
                                        echo '<td>';
                                        // สำหรับกลุ่มย่อย image_path เป็นค่าว่างเสมอ
                                        echo '<button class="btn btn-sm btn-edit me-2" onclick="myApp_editGroup(' . $group['group_id'] . ', \'' . htmlspecialchars($group['group_name'], ENT_QUOTES) . '\', \'sub\', \'\', ' . (is_null($group['parent_group_id']) ? 'null' : htmlspecialchars($group['parent_group_id'])) . ')"><i class="fas fa-edit"></i> แก้ไข</button>';
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
                        <div class="mb-3">
                            <label for="newGroupName" class="form-label">ชื่อหมวดหมู่</label>
                            <input type="text" class="form-control" id="newGroupName" name="group_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="newParentGroupId" class="form-label">หมวดหมู่หลัก (ถ้ามี)</label>
                            <select class="form-select" id="newParentGroupId" name="parent_group_id">
                                <option value="">- เลือกหมวดหมู่หลัก -</option>
                                <?php
                                // เนื่องจากเราปิด $conn ไปแล้ว (และจะเปิดใหม่ด้านล่าง) 
                                // เราควรดึงข้อมูลนี้เก็บไว้ในตัวแปรตั้งแต่ต้น หรือส่งผ่าน JSON
                                // แต่เพื่อให้โค้ดนี้ทำงานได้ ถ้า connect_db.php ไม่ได้ปิด connection หลัง fetch groups
                                // ก็สามารถใช้ $conn ได้ต่อ
                                // ณ ตอนนี้คือ $conn ถูกปิดไปแล้ว เราต้องเปิดใหม่สำหรับ Modal นี้
                                // หรือเก็บ $main_groups_for_dropdown ไว้ตั้งแต่ต้น
                                // ผมจะปรับให้ดึง $main_groups_for_dropdown มาใช้
                                // เพื่อไม่ต้อง connect DB ซ้ำซ้อนใน PHP file เดียวกัน
                                
                                // ดึงข้อมูลกลุ่มหลักอีกครั้ง (หรือใช้ $main_groups จากด้านบน)
                                // เพื่อให้ง่ายในการแสดงผลใน modal ที่ต้องการข้อมูลตอนโหลดหน้า
                                // ควรเก็บ main_groups ที่ดึงมาตอนแรกไว้ในตัวแปร
                                // หรือ connect DB อีกครั้ง (แต่ไม่แนะนำ)
                                // ในที่นี้ผมจะสมมติว่าคุณมี $main_groups ที่ดึงมาแล้ว
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
                        <div class="mb-3">
                            <label for="editGroupName" class="form-label">ชื่อหมวดหมู่</label>
                            <input type="text" class="form-control" id="editGroupName" name="group_name" required>
                        </div>
                        <div class="mb-3" id="editParentGroupContainer">
                            <label for="editParentGroupId" class="form-label">หมวดหมู่หลัก (ถ้ามี)</label>
                            <select class="form-select" id="editParentGroupId" name="parent_group_id">
                                <option value="">- เลือกหมวดหมู่หลัก -</option>
                                <?php
                                // ใช้ $main_groups จากที่ดึงมาตั้งแต่ต้น
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
        // กำหนด BASE_URL และ PLACEHOLDER_IMAGE ให้ JavaScript รู้จัก
        // BASE_URL ใช้สำหรับในกรณีที่ต้องสร้าง URL สัมพัทธ์เป็น URL เต็ม
        const GLOBAL_APP_BASE_URL = '<?php echo $base_url; ?>'; 
        const GLOBAL_APP_PLACEHOLDER_IMAGE = GLOBAL_APP_BASE_URL + 'public/img/group_placeholder.jpg';

        // ฟังก์ชันสำหรับเปิด Modal แก้ไขและเติมข้อมูล
        // ทำให้ฟังก์ชันนี้เป็น Global โดยการกำหนดให้กับ window object หรือไม่ใช้ var/let/const
        window.myApp_editGroup = function(groupId, groupName, groupType, imagePath, parentGroupId = null) {
            console.log("DEBUG: myApp_editGroup called with:", { groupId, groupName, groupType, imagePath, parentGroupId });
            
            $('#editGroupId').val(groupId);
            $('#editGroupName').val(groupName);
            $('#editGroupType').val(groupType);

            // Reset image input and preview
            $('#editGroupImage').val('');
            $('#editGroupImagePreview').hide().attr('src', ''); 
            $('#editGroupImagePreview').data('current-image', ''); 

            if (groupType === 'main') {
                $('#editParentGroupContainer').hide();
                $('#editParentGroupId').val(''); 
                $('#editImageContainer').show();
                if (imagePath) {
                    // imagePath ที่ส่งมาเป็น URL เต็มอยู่แล้ว ไม่ต้องต่อ BASE_URL ซ้ำ
                    $('#editGroupImagePreview').attr('src', imagePath).show(); 
                    $('#editGroupImagePreview').data('current-image', imagePath); 
                } else {
                    $('#editGroupImagePreview').attr('src', GLOBAL_APP_PLACEHOLDER_IMAGE).show();
                    $('#editGroupImagePreview').data('current-image', GLOBAL_APP_PLACEHOLDER_IMAGE);
                }
            } else { // sub group
                $('#editParentGroupContainer').show();
                // Set the value for parentGroupId. Handle 'null' string from PHP if needed, convert to actual null or empty string for select
                $('#editParentGroupId').val(parentGroupId === 'null' ? '' : parentGroupId);
                $('#editImageContainer').hide(); // ซ่อนส่วนอัปโหลดรูปสำหรับกลุ่มย่อย
                $('#editGroupImagePreview').hide().attr('src', ''); // ซ่อนพรีวิวรูปภาพ
                $('#editGroupImagePreview').data('current-image', ''); // ลบข้อมูลรูปภาพปัจจุบัน
            }

            // แสดง modal
            $('#editGroupModal').modal('show');
        }

        // ฟังก์ชันสำหรับลบกลุ่ม
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
                        url: 'group_actions.php', // Path สัมพัทธ์ไปยังไฟล์ PHP
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
                                    location.reload(); // โหลดหน้าใหม่เมื่อสำเร็จ
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

        // *** ส่วนโค้ดที่ต้องรันเมื่อ DOM พร้อม (สำหรับ DataTable และ Event Listeners ของ Form) ***
        $(document).ready(function() {
            console.log("DEBUG: DOM is ready. Initializing DataTables and Form Event Listeners.");

            // ตรวจสอบว่า jQuery และ DataTables โหลดมาหรือยัง
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
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Thai.json"
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
                    // ถ้าไม่มีไฟล์ใหม่ถูกเลือก ให้แสดงรูปภาพเดิมถ้ามี หรือซ่อน
                    var currentImage = $('#editGroupImagePreview').data('current-image');
                    if (currentImage) {
                        $('#editGroupImagePreview').attr('src', currentImage).show();
                    } else {
                        $('#editGroupImagePreview').hide();
                    }
                }
            });

            // Add Group Form Submission
            $('#addGroupForm').on('submit', function(e) {
                e.preventDefault(); // หยุดการ submit แบบปกติ
                var formData = new FormData(this);
                formData.append('action', 'add_group');

                console.log("DEBUG: Adding Group with FormData:", Object.fromEntries(formData.entries()));

                $.ajax({
                    url: 'group_actions.php', // Path สัมพัทธ์ไปยังไฟล์ PHP
                    type: 'POST',
                    data: formData,
                    processData: false, // ไม่ต้องประมวลผลข้อมูล
                    contentType: false, // ไม่ต้องตั้งค่า Content-Type (FormData จะตั้งให้เอง)
                    dataType: 'json', // คาดหวัง JSON response
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
                                location.reload(); // โหลดหน้าใหม่เมื่อสำเร็จ
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
                e.preventDefault(); // หยุดการ submit แบบปกติ
                var formData = new FormData(this);
                formData.append('action', 'edit_group');

                console.log("DEBUG: Editing Group with FormData:", Object.fromEntries(formData.entries()));

                $.ajax({
                    url: 'group_actions.php', // Path สัมพัทธ์ไปยังไฟล์ PHP
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json', // Expect JSON response
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
                                location.reload(); // โหลดหน้าใหม่เมื่อสำเร็จ
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