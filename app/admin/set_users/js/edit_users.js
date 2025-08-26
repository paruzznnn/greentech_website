$(document).ready(function () {
    // โค้ดสำหรับ DataTables
    var usersTable = $('#usersTable').DataTable({
        "autoWidth": false,
        "language": {
            "search": "ค้นหา:",
            "lengthMenu": "แสดง _MENU_ รายการ",
            "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
            "paginate": {
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            },
            "infoEmpty": "แสดง 0 ถึง 0 จาก 0 รายการ",
            "zeroRecords": "ไม่พบข้อมูลที่ตรงกัน"
        }
    });

    // Event listener สำหรับปุ่มแก้ไขและลบ
    $('#usersTable').on('click', '.btn-delete', function() {
        var userId = $(this).data('id');
        Swal.fire({
            title: "คุณแน่ใจหรือไม่?",
            text: "ข้อมูลผู้ใช้จะถูกลบอย่างถาวร!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "ใช่, ลบเลย!",
            cancelButtonText: "ยกเลิก"
        }).then((result) => {
            if (result.isConfirmed) {
                // ส่ง AJAX request ไปยังไฟล์ process_users.php เพื่อลบข้อมูล
                // แก้ไข URL ให้ถูกต้อง
                $.ajax({
                    url: "actions/process_users.php", // แก้ไข URL
                    type: "POST",
                    data: {
                        action: 'delete_user',
                        user_id: userId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire(
                                'ลบสำเร็จ!',
                                'ข้อมูลผู้ใช้ถูกลบเรียบร้อยแล้ว.',
                                'success'
                            ).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire(
                                'เกิดข้อผิดพลาด!',
                                'ไม่สามารถลบข้อมูลผู้ใช้ได้: ' + response.message,
                                'error'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', xhr.responseText); // แสดงข้อความ error ที่ชัดเจนขึ้น
                        Swal.fire(
                            'เกิดข้อผิดพลาด!',
                            'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});