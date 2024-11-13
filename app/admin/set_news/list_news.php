<?php
include('../../../lib/permissions.php');
include('../../../lib/base_directory.php');
// checkPermissions();
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

    <link href='../css/index_.css?v=<?php echo time();?>' rel='stylesheet'>
    <style>
        .button-class {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
        }


        .responsive-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 10px;
        }

        /* Media query for smaller screens */
        @media (max-width: 768px) {

            .responsive-grid {
                grid-template-columns: 1fr;
            }

        }


    </style>
</head>

<?php include '../template/header.php' ?>

<body>

    <div class="content-sticky" id="">
        <div class="container-fluid">
            <div class="box-content">
                <div class="row">

                    <div>
                        <div class="responsive-grid">
                            <div style="margin: 10px;">

                                <table id="td_list_news" class="table table-hover" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Subject</th>
                                            <th>Date created</th>
                                            <!-- <th>Active</th> -->
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
        </div>
    </div>



<script src='../js/index_.js?v=<?php echo time();?>'></script>
<script>
    var td_list_news;
    $(document).ready(function(){

        td_list_news = new DataTable('#td_list_news', {
            "autoWidth": false,
            "language":{
                "decimal":        "",
                "emptyTable":     "No data available in table",
                "infoEmpty":      "Showing 0 to 0 of 0 entries",
                "infoFiltered":   "(filtered from MAX total entries)",
                "infoPostFix":    "",
                "thousands":      ",",
                "loadingRecords": "Loading...",
                "search":         "Search:",
                "zeroRecords":    "No matching records found",
                // "paginate": {
                //     "first":      "First",
                //     "last":       "Last",
                //     "next":       "Next",
                //     "previous":   "Previous"
                // },
                "aria": {
                    "orderable":  "Order by this column",
                    "orderableReverse": "Reverse order this column"
                }
            },
            "processing": true,
            "serverSide": true,
                ajax: {
                    url: "actions/process_news.php",
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
                    render: function ( data, type, row, meta ) {
                        return meta.row + 1;
                    }
                },
                {
                    "target": 1,
                    data: null,
                    render: function ( data, type, row ) {
                        return data.subject_news;
                    }
                },
                {
                    "target": 2,
                    data: null,
                    render: function ( data, type, row ) {
                        return data.date_create;
                        
                    }
                }
                // {
                //     "target": 3,
                //     data: null,
                //     render: function ( data, type, row ) {

                //         let toggleSwitch = `
                //         <div>
                //             <span style="margin: 0 10px;">
                //                 <label class="toggleSwitch nolabel">
                //                     <input type="checkbox" id="theme-toggle"/>
                //                     <span>
                //                         <span></span>
                //                         <span></span>
                //                     </span>
                //                     <a></a>
                //                 </label>
                //             </span>
                //         </div>
                //         `;
                        
                //         return toggleSwitch;
                        
                //     }
                // }
                
                
            ],

            

            drawCallback: function (settings) {
                
                var targetDivTable = $('div.dt-layout-row.dt-layout-table');
                if (targetDivTable.length) {
                    targetDivTable.addClass('tables-overflow');
                    targetDivTable.css({
                        'display': 'block',
                        'width': '100%'
                    });
                }
        
                var targetDivRow = $('dt-container dt-layout-row dt-empty-footer');
                if (targetDivRow.length) {
                    targetDivRow.css({
                        'width': '50%'
                    });
                }
            },
            initComplete: function(settings, json) {
                const headers = [
                    "No.",
                    "Date created",
                    "Subject",
                    "Date on-air",
                    "Status",
                    ""
                ];
        
                // cssResponsiveTable('td_list_news', headers);
            },
            rowCallback: function (row, data, index) {

                // if(data.parent_id == '' || data.parent_id == null){

                //     $(row).addClass('ui-state-disabled');

                // }
                //     $(row).addClass('ui-state-default');
                
                
            }
        });

    });

</script>
</body>

</html>