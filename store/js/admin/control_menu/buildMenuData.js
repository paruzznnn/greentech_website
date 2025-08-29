$(document).ready(function () {
    $('#tb_menuLinkRel').DataTable({
        language: { // เปิดใช้งานภาษาไทย
            processing: "กำลังดำเนินการ...",
            search: "ค้นหา:",
            lengthMenu: "แสดง _MENU_ รายการ",
            info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
            infoEmpty: "แสดง 0 ถึง 0 จาก 0 รายการ",
            infoFiltered: "(กรองข้อมูลจากทั้งหมด _MAX_ รายการ)",
            infoPostFix: "",
            loadingRecords: "กำลังโหลด...",
            zeroRecords: "ไม่พบข้อมูลที่ตรงกัน",
            emptyTable: "ไม่มีข้อมูลในตาราง",
            paginate: {
                first: "หน้าแรก",
                previous: "ก่อนหน้า",
                next: "ถัดไป",
                last: "หน้าสุดท้าย"
            },
            aria: {
                sortAscending: ": เรียงจากน้อยไปมาก",
                sortDescending: ": เรียงจากมากไปน้อย"
            }
        },
        autoWidth: true,
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: `${pathConfig.BASE_WEB}service/admin/control_menu/menu-data.php`,
            method: 'GET',
            dataType: 'json',
            data: function (d) {
                d.action = 'getDataMenu';
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'Bearer my_secure_token_123');
                xhr.setRequestHeader('Content-Type', 'application/json');
            },
            dataSrc: function (json) {
                // console.log('Server response:', json); // สำหรับ debug
                return json.data;
            }
        },
        ordering: true,
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        columnDefs: [
            {
                "targets": 0, // ควรเป็น "targets" แทน "target"
                data: null,
                render: function (data, type, row, meta) {
                    // console.log('data for col 0', data);
                    return meta.row + meta.settings._iDisplayStart + 1; // แสดงลำดับ
                }
            },
            {
                "targets": 1, 
                data: 'link_name', 
                // render: function (data, type, row) { return data.link_name; } // ไม่จำเป็นต้องใช้ render ถ้า data เป็น key ตรงๆ
            },
            {
                "targets": 2,
                data: null,
                render: function (data, type, row) {
                    return 'สถานะ: ' + (row.status || 'N/A');
                }
            },
            {
                "targets": 3,
                data: null,
                render: function (data, type, row) {
                    return ``;
                }
            }
        ],
        rowCallback: function (row, data, index) {
        },
    });
});
