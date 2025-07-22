<?php
include '../check_permission.php';
// require_once(__DIR__ . '/../../../../lib/connect.php'); // Include your database connection
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Banner</title>

    <link rel="icon" type="image/x-icon" href="../../../public/img/q-removebg-preview1.png">

    <link href="../../../inc/jquery/css/jquery-ui.css" rel="stylesheet">
    <script src="../../../inc/jquery/js/jquery-3.6.0.min.js"></script>
    <script src="../../../inc/jquery/js/jquery-ui.min.js"></script>

    <link href="../../../inc/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../../inc/bootstrap/js/bootstrap.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/fontawesome5-fullcss@1.1.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">

    <link href="../../../inc/sweetalert2/css/sweetalert2.min.css" rel="stylesheet">
    <script src="../../../inc/sweetalert2/js/sweetalert2.all.min.js"></script>

    <link href="../../../inc/select2/css/select2.min.css" rel="stylesheet">
    <script src="../../../inc/select2/js/select2.min.js"></script>

    <link href="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.js"></script>

    <link href='../css/index_.css?v=<?php echo time(); ?>' rel='stylesheet'>

    <style>
        .btn-circle {
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-edit {
            background-color: #FFC107;
            color: white;
        }

        .btn-del {
            background-color: #DC3545;
            color: white;
        }

        .banner-img {
            height: 60px; /* กำหนดความสูงที่ต้องการ */
            width: auto; /* ให้ความกว้างปรับตามสัดส่วนของภาพ */
            max-width: 150px; /* เพิ่มอันนี้ เพื่อจำกัดความกว้างสูงสุด */
            object-fit: cover;
            border: 1px solid #ccc;
            border-radius: 4px; /* เพิ่มมนมุม */
            padding: 2px; /* เพิ่ม padding */
        }
        .line-ref {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            border-left: 5px solid #f57c00;
            padding-left: 10px;
            color: #333;
        }
    </style>
</head>
<body>
<?php include '../template/header.php'; ?>

<div class="content-sticky">
    <div class="container-fluid">
        <div class="box-content p-4 bg-light rounded shadow-sm">
            <div class="responsive-grid">
                <div style="margin: 10px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h4 class="line-ref mb-0">
                            <i class="fa-solid fa-image"></i>
                            Banner List
                        </h4>
                        <a class="btn btn-primary" href="setup_banner.php">
                            <i class="fa-solid fa-plus"></i> เพิ่ม Banner
                        </a>
                    </div>
                    <div style="gap :20px"><h5>
                        <div style="padding-bottom :5px">ความสูงรูปภาพ: 300px;</div>
                        <div style="padding-bottom :5px">ความกว้างรูปภาพ: 1920px;</div>
                    </h5></div>
                    <table id="td_list_Banner" class="table table-hover table-striped table-bordered" style="width:100%;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>รูปภาพ</th>
                                <th>วันที่เพิ่ม</th>
                                <th>การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

<script src='../js/index_.js?v=<?php echo time(); ?>'></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var bannerTable = $('#td_list_Banner').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "actions/process_banner.php",
                "type": "POST",
                "data": function (d) {
                    d.action = 'getData_banner'; // Specify the action for fetching data
                }
            },
            "columns": [
                { "data": null, "orderable": false, "searchable": false, "render": function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1; // Auto-incrementing numbering
                }},
                { "data": "image_path", "render": function (data, type, row) {
                    return '<img src="' + data + '" class="banner-img" alt="Banner Image">';
                }},
                { "data": "created_at", "render": function (data, type, row) { // ใช้ created_at
                    // Format created_at if needed, assuming it's returned as YYYY-MM-DD HH:MM:SS
                    if (data) {
                        const date = new Date(data);
                        return date.toLocaleDateString('th-TH', { year: 'numeric', month: '2-digit', day: '2-digit' }) + ' ' +
                               date.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' });
                    }
                    return '';
                }},
                { "data": "id", "orderable": false, "searchable": false, "render": function (data, type, row) { // ใช้ id
                    // Assuming 'id' is the primary key for actions
                    return `
                        <button class="btn btn-circle btn-edit btn-sm me-2" title="แก้ไข" data-id="${data}">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <button class="btn btn-circle btn-del btn-sm" title="ลบ" data-id="${data}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }}
            ],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Thai.json" // Thai language for DataTables
            }
        });

        // Handle delete button click
        $(document).on('click', '.btn-del', function() {
            var bannerId = $(this).data('id'); // ใช้ id
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: "คุณต้องการลบแบนเนอร์นี้หรือไม่!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'actions/process_banner.php', // Call process_banner.php for deletion
                        type: 'POST',
                        data: {
                            action: 'delbanner',
                            id: bannerId // ส่ง id
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire(
                                    'ลบแล้ว!',
                                    'แบนเนอร์ถูกลบเรียบร้อยแล้ว.',
                                    'success'
                                ).then(() => {
                                    bannerTable.ajax.reload(null, false); // Reload DataTables without resetting pagination
                                });
                            } else {
                                Swal.fire(
                                    'เกิดข้อผิดพลาด!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error deleting banner:", error, xhr.responseText);
                            Swal.fire(
                                'เกิดข้อผิดพลาด!',
                                'ไม่สามารถลบแบนเนอร์ได้.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        // Handle edit button click (redirect to an edit page)
        $(document).on('click', '.btn-edit', function() {
            var bannerId = $(this).data('id'); // ใช้ id
            window.location.href = 'edit_banner.php?id=' + bannerId; // Redirect to an edit page
        });
    });
</script>

</body>
</html>