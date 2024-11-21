<?php
include('../../../lib/base_directory.php');
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <link rel="icon" type="image/x-icon" href="../../../public/img/logo-ALLABLE-07.ico">

    <link href="../../../inc/jquery/css/jquery-ui.css" rel="stylesheet">

    <script src="../../../inc/jquery/js/jquery-3.6.0.min.js"></script>
    <script src="../../../inc/jquery/js/jquery-ui.min.js"></script>

    <link href="../../../inc/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../../inc/bootstrap/js/bootstrap.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/fontawesome5-fullcss@1.1.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css" integrity="sha512-9xKTRVabjVeZmc+GUW8GgSmcREDunMM+Dt/GrzchfN8tkwHizc5RP4Ok/MXFFy5rIjJjzhndFScTceq5e6GvVQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

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
        .btn-circle {
            border: none;
            width: 30px;
            height: 28px;
            border-radius: 50%;
            font-size: 14px;
        }

        .btn-edit {
            background-color: #FFC107;
            color: #ffffff;
        }

        .btn-del {
            background-color: #ff4537;
            color: #ffffff;
        }
    </style>
</head>

<?php include '../template/header.php' ?>

<body>

    <div class="content-sticky" id="">
        <div class="container-fluid">
            <div class="box-content">
                <div class="row">

                    <div class="">

                        <table id="td_list_news" class="table table-hover" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Member info</th>
                                    <th>Date created</th>
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

            var td_list_news = new DataTable('#td_list_news', {
                "autoWidth": false,
                "language": {
                    "decimal": "",
                    "emptyTable": "No data available in table",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from MAX total entries)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "loadingRecords": "Loading...",
                    "search": "Search:",
                    "zeroRecords": "No matching records found",
                    // "paginate": {
                    //     "first":      "First",
                    //     "last":       "Last",
                    //     "next":       "Next",
                    //     "previous":   "Previous"
                    // },
                    "aria": {
                        "orderable": "Order by this column",
                        "orderableReverse": "Reverse order this column"
                    }
                },
                "processing": true,
                "serverSide": true,
                ajax: {
                    url: "actions/process_mng_member.php",
                    method: 'POST',
                    dataType: 'json',
                    data: function(d) {
                        d.action = 'getData_news';
                        // d.filter_date = $('#filter_date').val();
                        // d.customParam2 = "value2";
                    },
                    dataSrc: function(json) {
                        return json.data;
                    }
                },
                "ordering": false,
                "pageLength": 25,
                "lengthMenu": [10, 25, 50, 100],
                columnDefs: [
                    // {
                    //     "target": 0,
                    //     data: null,
                    //     render: function ( data, type, row, meta) {
                    //         return meta.row + 1;
                    //     }
                    // },
                    {
                        "target": 0,
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        "target": 1,
                        data: null,
                        render: function(data, type, row) {

                            let htmlConfrim

                            if(data.confirm_email == '1'){

                                htmlConfrim =`
                                <span style="font-size: 12px; color: #88fc00;">
                                    <i class="fas fa-circle"></i>
                                </span>
                                Verified`;

                            }else{

                                htmlConfrim =`
                                <span style="font-size: 12px; color: #ffbf00;">
                                    <i class="fas fa-circle"></i>
                                </span>
                                Waiting for confirmation`;

                            }



                            let memberInfo = `
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <div style="font-size: 14px;">
                                            <i class="fas fa-id-card"></i> 
                                            <span>Name :</span>
                                        </div>
                                        <div class="line-clamp">
                                            ${data.fullname}
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div style="font-size: 14px;">
                                            <i class="fas fa-envelope"></i> 
                                            <span>Email address :</span>
                                        </div>
                                        <div class="line-clamp">
                                            ${data.email}
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div style="font-size: 14px;">
                                            <i class="fas fa-building"></i>
                                            <span>Company :</span>
                                        </div>
                                        <div class="line-clamp">
                                            ${0}
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div style="font-size: 14px;">
                                            <i class="fas fa-id-card-alt"></i>
                                            <span>Position :</span>
                                        </div>
                                        <div class="line-clamp">
                                            ${0}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <div style="font-size: 14px;">
                                            <i class="fas fa-phone-volume"></i>
                                            <span>Telephone number :</span>
                                        </div>
                                        <div class="line-clamp">
                                            ${data.phone_number}
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div style="font-size: 14px;">
                                        
                                            <span>Status :</span>
                                        </div>
                                        <div class="line-clamp">
                                            ${htmlConfrim}
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div style="font-size: 14px;">
                                            <i class="fas fa-briefcase"></i> 
                                            <span>Department :</span>
                                        </div>
                                        <div class="line-clamp">
                                            ${0}
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div style="font-size: 14px;">
                                            <i class="fas fa-chess-pawn"></i> 
                                            <span>Role :</span>
                                        </div>
                                        <div class="line-clamp">
                                            ${0}
                                        </div>
                                    </div>
                                </div>

                            </div>
                            `;

                            return memberInfo;
                        }
                    },
                    // {
                    //     "target": 2,
                    //     data: null,
                    //     render: function(data, type, row) {
                    //         return data;

                    //     }
                    // },
                    {
                        "target": 2,
                        data: null,
                        render: function(data, type, row) {

                            const datetime = data.date_create;
                            const [date, time] = datetime.split(" ");

                            let htmlDateTime = `
                            <div>
                                <div><i class="fas fa-calendar-alt"></i> ${date}</div>
                                <div><i class="fas fa-clock"></i> ${time}</div>
                            </div>
                            `;

                            return htmlDateTime;

                        }
                    }
                    // {
                    //     "target": 4,
                    //     data: null,
                    //     render: function(data, type, row) {

                    //         let divBtn = `
                    //     <div>`;

                    //         divBtn += `
                    //     <span style="margin: 2px;">
                    //         <button type="button" class="btn-circle btn-edit">
                    //         <i class="fas fa-pencil-alt"></i>
                    //         </button>
                    //     </span>
                    //     `;

                    //         divBtn += `
                    //     <span style="margin: 2px;">
                    //         <button type="button" class="btn-circle btn-del">
                    //         <i class="fas fa-trash-alt"></i>
                    //         </button>
                    //     </span>
                    //     `;

                    //         divBtn += `
                    //     </div>
                    //     `;

                    //         return divBtn;

                    //     }
                    // }


                ],



                drawCallback: function(settings) {

                    var targetDivTable = $('div.dt-layout-row.dt-layout-table');
                    if (targetDivTable.length) {
                        targetDivTable.addClass('tables-over');
                        // targetDivTable.css({
                        //     'display': 'block',
                        //     'width': '100%'
                        // });
                    }

                    var targetDivRow = $('dt-container dt-layout-row dt-empty-footer');
                    if (targetDivRow.length) {
                        targetDivRow.css({
                            'width': '50%'
                        });
                    }
                },
                initComplete: function(settings, json) {
                    // const headers = [
                    //     "No.",
                    //     "Date created",
                    //     "Subject",
                    //     "Date on-air",
                    //     "Status",
                    //     ""
                    // ];

                    // cssResponsiveTable('td_list_news', headers);
                },
                rowCallback: function(row, data, index) {
                    // var editButton = $(row).find('.btn-edit');
                    // var deleteButton = $(row).find('.btn-del');

                    // editButton.off('click').on('click', function() {
                    //     // reDirect('edit_news.php', data.news_id);
                    //     reDirect('edit_news.php', {
                    //         news_id: data.news_id
                    //     });
                    // });

                    // deleteButton.off('click').on('click', function() {

                    //     Swal.fire({
                    //         title: "Are you sure?",
                    //         text: "Do you want to delete the news?",
                    //         icon: "warning",
                    //         showCancelButton: true,
                    //         confirmButtonColor: "#4CAF50",
                    //         cancelButtonColor: "#d33",
                    //         confirmButtonText: "Accept"
                    //     }).then((result) => {

                    //         if (result.isConfirmed) {

                    //             $('#loading-overlay').fadeIn();

                    //             $.ajax({
                    //                 url: 'actions/process_role.php',
                    //                 type: 'POST',
                    //                 data: {
                    //                     action: 'delNews',
                    //                     id: data.news_id,
                    //                 },
                    //                 dataType: 'json',
                    //                 success: function(response) {
                    //                     if (response.status == 'success') {
                    //                         window.location.reload();
                    //                     }
                    //                 },
                    //                 error: function(xhr, status, error) {
                    //                     console.error('Error:', error);
                    //                 }
                    //             });

                    //         } else {
                    //             $('#loading-overlay').fadeOut();
                    //         }

                    //     });

                    // });

                }
            });

        });

        function reDirect(url, data) {
            var form = $('<form>', {
                method: 'POST',
                action: url,
                target: '_blank'
            });
            $.each(data, function(key, value) {
                $('<input>', {
                    type: 'hidden',
                    name: key,
                    value: value
                }).appendTo(form);
            });
            $('body').append(form);
            form.submit();
        }
    </script>
</body>

</html>