<?php include '../check_permission.php'?>
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
    <script src="../../../inc/bootstrap/js/bootstrap.bundle.min.js"></script>

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


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
    <link href="../css/index_.css?v=<?php echo time(); ?>" rel="stylesheet">

    <style>
        /* body {
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
            margin: 0;
        } */

        .controls {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 10px;
            background-color: #f0f0f0;
            /* flex: 0 0 200px;  */
            border-right: 2px solid #ddd;
        }


        .dropzone {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 15px;
            flex-grow: 1;
            overflow-y: auto;
            background-color: #fafafa;
            border: 2px dashed #ccc;
            border-radius: 8px;
        }

        .target-row {
            display: grid;
            gap: 10px;
            width: 100%;
            cursor: pointer;
            min-height: 30px;
            background-color: #e0f7fa;
        }

        .target-cell {
            border: 1px solid #ddd;
            background-color: #e0f7fa;
            border-radius: 4px;
            padding: 8px;
            position: relative;
            transition: background-color 0.3s ease;
            height: 60px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .target-cell input,
        .target-cell button,
        .target-cell select,
        .target-cell textarea {
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            overflow: hidden;
        }

        .target-cell textarea {
            resize: none;
        }

        .target-cell:hover {
            background-color: #b2ebf2;
        }

        .selected {
            background-color: #ffcc80;
            box-shadow: 1px 2px 4px rgba(255, 204, 128, 0.6);
            border-color: #ffa726;
        }

        .draggable {
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #fff;
            cursor: grab;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        .draggable:active {
            background-color: #ececec;
            cursor: grabbing;
        }
    </style>
</head>

<body>

    <?php include '../template/header.php' ?>

    <div class="content-sticky" id="">
        <div class="container-fluid">
            <div class="box-content">
                <div class="row">

                    <div class="col-md-12"></div>


                </div>

            </div>
        </div>
    </div>


    <script src="../js/index_.js?v=<?php echo time(); ?>"></script>
</body>

</html>