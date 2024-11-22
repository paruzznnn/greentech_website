<?php include '../check_permission.php' ?>
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

        .btn-save {
            background-color: #4CAF50;
            color: #ffffff;
        }

        .btn-edit {
            background-color: #FFC107;
            color: #ffffff;
        }

        .btn-del {
            background-color: #ff4537;
            color: #ffffff;
        }

        #iconPickerMenu{
            position: absolute;
            right: 0;
            background-color: #fafafa;
            top: 44px;
        }

        .box-icon-picker{
            position: relative;
        }

    </style>
</head>

<?php include '../template/header.php' ?>

<body>

    <div class="content-sticky" id="">
        <div class="container-fluid">
            <div class="box-content">
                <div class="row">

                    <!-- <div style="padding: 10px; margin-top: 10px;">
                        <div class="iconPicker"></div>
                        colspan="2"
                    </div> -->

                    <div class="">

                        <table id="td_control_menu" class="table-styled" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Icon</th>
                                    <th>Menu name</th>
                                    <th>Main</th>
                                    <th>Path</th>
                                    <th>Order</th>
                                    <th>Actions</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th style="max-width: 50px;">
                                        <i id="showIcon" class=""></i>
                                        <input type="text" id="set_icon" name="set_icon" class="form-control" value="" hidden>
                                    </th>
                                    <th><input type="text" id="set_menu_name" name="set_menu_name" class="form-control" value=""></th>
                                    <th><select id="set_menu_main" name="set_menu_main" class="form-select"></select></th>
                                    <th><input type="text" id="set_menu_path" name="set_menu_path" class="form-control" value=""></th>
                                    <th>
                                    </th>
                                    <th>
                                        <div style="display: flex; justify-content: space-between;">
                                            <div>
                                                <button type="button" id="submitAddMenu" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            <div class="box-icon-picker">
                                                <button type="button" id="target_iconPickerMenu" class="btn btn-primary"><i class="fas fa-table"></i></button>
                                                <div id="iconPickerMenu" class="d-none"></div>
                                            </div>
                                        </div>
                                    </th>
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
    <script src='js/control_menu_.js?v=<?php echo time(); ?>'></script>

</body>

</html>