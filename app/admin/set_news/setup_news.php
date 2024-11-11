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

    <link rel="icon" type="image/x-icon" href="../public/img/logo-ALLABLE-07.ico">

    <link href="../../../inc/jquery/css/jquery-ui.css" rel="stylesheet">

    <script src="../../../inc/jquery/js/jquery-3.6.0.min.js"></script>
    <script src="../../../inc/jquery/js/jquery-ui.min.js"></script>

    <link href="../../../inc/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../../inc/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../../inc/bootstrap/js/bootstrap.bundle.min.js"></script>

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


    <link href="../../../inc/summernote/summernote-lite.min.css" rel="stylesheet">
    <script src="../../../inc/summernote/summernote-lite.min.js"></script>

    
    <link href='../css/index_.css?v=<?php echo time();?>' rel='stylesheet'>


    <style>
        .responsive-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .responsive-button-container {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 10px;
        }

        /* Media query for smaller screens */
        @media (max-width: 768px) {
            .responsive-grid {
                grid-template-columns: 1fr; /* Switch to a single column layout */
            }
        }

        @media (max-width: 480px) {
            .responsive-button-container div {
                text-align: center; /* Center-align button on very small screens */
            }
        }

        .note-editor .note-toolbar, .note-popover .popover-content {
            margin: 0;
            padding: 0 0 5px 5px;
            position: sticky !important;
            top: 0px !important;
            z-index: 999 !important;
        }

    </style>
</head>

<?php include '../template/header.php' ?>

<body>

    <div class="content-sticky" id="">
        <div class="container-fluid">
            <div class="box-content">
                <div class="row">
                <h3><i class="far fa-newspaper"></i> News</h3>
                    
                        <form id="formNews" enctype="multipart/form-data">

                            <div class="row">
                                <div class="col-md-12">
                                    <div style="margin: 10px;">
                                        <label for="">
                                            <span>Subject</span>:
                                        </label>
                                        <input type="text" class="form-control" name="news_subject">
                                    </div>

                                    <div style="margin: 10px;">
                                        <div>
                                            <textarea class="form-control" id="summernote" name="news_content"></textarea>
                                        </div>
                                    </div>

                                    <div style="margin: 10px; text-align: end;">
                                        <button 
                                        type="button" 
                                        id="submitAddNews"
                                        class="btn btn-primary">
                                            <i class="fas fa-plus"></i>
                                            News
                                        </button>
                                    </div>

                                </div>
                            </div>

                        </form>
                    

                </div>
            </div>

        </div>
    </div>



<script src='../js/index_.js?v=<?php echo time();?>'></script>
<script src='js/news_.js?v=<?php echo time();?>'></script>

</body>

</html>
