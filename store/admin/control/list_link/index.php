<?php include '../../../routes.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../../../inc-meta.php'; ?>
    <link href="../../../css/admin/template-admin.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../../../inc-cdn.php'; ?>
    <link href="../../../css/admin/alertMessage-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../../../css/admin/dataTable-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">

    <style>
        .card-list-link {
            border: 1px solid #dee2e6;
            background: #ffffff;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>

</head>

<body>

    <?php include '../../../template/admin/head-bar.php'; ?>
    <main>
        <div>
            <section id="" class="section-space-admin">
                <div class="container">
                    <div class="card-list-link">
                        <table id="tb_listLink" class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width:20%" data-lang="link_name">link name</th>
                                    <th style="width:40%" data-lang="link_url">link url</th>
                                    <th style="width:20%" data-lang="link_icon">link icon</th>
                                    <th style="width:20%" data-lang="actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script>
        $(document).ready(function() {
            let tb_listLink = $('#tb_listLink').DataTable({
                language: {
                    decimal: "",
                    emptyTable: "No data available in table",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from MAX total entries)",
                    thousands: ",",
                    loadingRecords: "Loading...",
                    search: "Search:",
                    zeroRecords: "No matching records found",
                    aria: {
                        orderable: "Order by this column",
                        orderableReverse: "Reverse order this column"
                    }
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: pathConfig.BASE_WEB + "service/admin/control/list-link-data.php",
                    method: "GET",
                    dataType: "json",
                    data: function(d) {
                        d.action = "get_tb_listLink";
                    },
                    dataSrc: function(json) {
                        return json.data;
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader("Authorization", "Bearer my_secure_token_123");
                    }
                },
                ordering: true,
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                columnDefs: [{
                        targets: 0,
                        width: "20%",
                        data: null,
                        render: (data) => data.link_name
                    },
                    {
                        targets: 1,
                        width: "40%",
                        data: null,
                        render: (data) => data.link_url
                    },
                    {
                        targets: 2,
                        width: "20%",
                        className: "text-center",
                        data: null,
                        render: (data) => `<i class="${data.link_icon}"></i>`
                    },
                    {
                        targets: 3,
                        width: "20%",
                        orderable: false,
                        className: "text-center",
                        data: null,
                        render: () => `
                    <div class="store-card-dropdown">
                        <i class="bi bi-three-dots"></i>
                        <div class="store-card-dropdown-content">
                            <a href="#">
                                <i class="bi bi-pencil-square"></i>
                                <span>แก้ไข</span>
                            </a>
                            <a href="#">
                                <i class="bi bi-trash3"></i>
                                <span>ลบ</span>
                            </a>
                        </div>
                    </div>
                `
                    }
                ],
                initComplete: function(settings, json) {
                    // รันตอน DataTable ถูกสร้างเสร็จครั้งแรก (ไม่ต้องรีเฟรชแล้ว)
                    updateTableWrapper();
                },
                drawCallback: function(settings) {
                    // รันทุกครั้งที่ redraw (sort, filter, paginate) เพื่อให้ wrapper ยังทำงานต่อเนื่อง
                    updateTableWrapper();
                },
                rowCallback: function(row, data, index) {
                    // row-specific logic ถ้ามี
                },
                createdRow: function(row, data, dataIndex) {
                    // ใช้สำหรับ add class/attr ตอนสร้าง row
                }
            });

            // ====== Resize & Wrapper Control ======
            function updateTableWrapper() {
                let tableWrapper = $('#tb_listLink').parent();

                // ถ้ายังไม่มี wrapper → สร้าง
                if (!tableWrapper.hasClass('table-wrapper')) {
                    $('#tb_listLink').wrap('<div class="table-wrapper"></div>');
                    tableWrapper = $('#tb_listLink').parent();
                }

                if (window.innerWidth <= 1200) {
                    tableWrapper.addClass("tables-over");
                } else {
                    tableWrapper.removeClass("tables-over");
                }
            }

            // รันตอน DataTable init + redraw
            // $('#tb_listLink').on('init.dt draw.dt', updateTableWrapper);
            $(window).on('resize', updateTableWrapper);
        });
    </script>



    <!-- <script type="module">
        Promise.all([
            import(`${pathConfig.BASE_WEB}js/formHandler.js?v=<?php echo time(); ?>`),
            import(`${pathConfig.BASE_WEB}js/admin/control/linkBuilder.js?v=<?php echo time(); ?>`)
        ])
        .then(async ([formModule, linkModule]) => {
        })
        .catch((e) => console.error("Module import failed", e));
    </script> -->

    <?php include '../../../template/admin/footer-bar.php'; ?>

</body>

</html>