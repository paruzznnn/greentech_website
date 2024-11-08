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
        .input-class {
            background-color: #f0f8ff;
            border: 1px solid #ccc;
            padding: 10px;
            margin: 5px;
        }

        .button-class {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
        }

        .select-class {
            background-color: #ffffff;
            border: 1px solid #ccc;
            padding: 5px;
            margin: 5px;
        }

        .textarea-class {
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            padding: 10px;
        }

        .checkbox-class,
        .radio-class {
            margin-right: 5px;
        }

        .file-class {
            background-color: #ffffff;
            border: 1px solid #ccc;
            padding: 5px;
        }

        .color-class {
            padding: 5px;
            border: 1px solid #ccc;
        }

        .responsive-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
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
    </style>
</head>

<?php include '../template/header.php' ?>

<body>

    <div class="content-sticky" id="">
        <div class="container-fluid">
            <div class="box-content">
                <div class="row">
                <h3><i class="far fa-newspaper"></i> News</h3>
                    <div>
                        <form id="formNews" enctype="multipart/form-data">

                            <div class="responsive-grid">
                                <div style="margin: 10px;">
                                    <div>
                                        <label for="">
                                            <span>Subject</span>:
                                        </label>
                                        <input type="text" class="form-control" name="news_subject">
                                    </div>
                                    <div style="display: none;">
                                        <textarea class="form-control" name="news_content"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="responsive-grid">
                                <div style="margin: 10px;">
                                    <div id="summernote"></div>
                                </div>
                            </div>

                            <div class="responsive-button-container">
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
                        </form>
                    </div>

                </div>
            </div>

            

        </div>
    </div>



    <script src='../js/index_.js'></script>
    <script>

        $(document).ready(function() {

            $('#summernote').summernote({
                placeholder: '',
                tabsize: 2,
                height: 400,
                callbacks: {
                    onKeyup: function(e) {
                        var content = $(this).summernote('code');
                        $('textarea[name="news_content"]').val(content);
                    }
                }
            });


        });

        $('#submitAddNews').on('click', function(event){
            event.preventDefault(); 

            var formNews = $('#formNews')[0];
            var formData = new FormData(formNews);

            formData.append('action', 'addNews');

            $.ajax({
                url: 'actions/process_news.php',
                type: 'POST',
                data: formData,
                processData: false, 
                contentType: false,
                success: function(response) {

                    console.log('response', response);
                    

                    if(response.status == 'success'){

                    }

                },
                error: function(error) {
                    console.log('error', error);
                    
                }
            });
        });

        // var readURL = function(input) {
        //     if (input.files && input.files[0]) {
        //         var reader = new FileReader();

        //         reader.onload = function (e) {
        //             console.log('img', e.target.result);
        //             let previewImage = $('#previewImage');
        //             previewImage.attr('src', e.target.result);
        //             previewImage.css('display', 'block');
        //         }

        //         reader.readAsDataURL(input.files[0]);
        //     }
        // }
        
        // $("#fileInput").on('change', function(){
        //     readURL(this);
        // });

    </script>
</body>

</html>

<!-- <div style="margin: 10px;">
    <div>
        <label for="">
            <span>Subject</span>:
        </label>
        <input type="text" class="form-control" name="news_subject">
    </div>
    <div>
        <label for="">
            <span>Content</span>:
        </label>
        
        <textarea class="form-control" name="news_content"></textarea>
    </div>
    <div>
        <label for="">img:</label>
        <input type="file" class="form-control" id="fileInput" name="news_image[]">
    </div>
</div> -->

<!-- <div class="previewContainer">
    <img id="previewImage" src="" alt="Image Preview" style="max-width: 100%; display: none;">
</div> -->