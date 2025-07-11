<?php include '../check_permission.php'?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit metatags</title>

    <link rel="icon" type="image/x-icon" href="../../../public/img/q-removebg-preview1.png">

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


    <!-- <link href="../../../inc/summernote/summernote-lite.min.css" rel="stylesheet">
    <script src="../../../inc/summernote/summernote-lite.min.js"></script> -->


    <link href='../css/index_.css?v=<?php echo time(); ?>' rel='stylesheet'>


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
                grid-template-columns: 1fr;
                /* Switch to a single column layout */
            }
        }

        @media (max-width: 480px) {
            .responsive-button-container div {
                text-align: center;
                /* Center-align button on very small screens */
            }
        }

        .note-toolbar {
            position: sticky !important;
            top: 70px !important;
            z-index: 1 !important;
        }

        /* .note-editor .note-toolbar, .note-popover .popover-content {
            margin: 0;
            padding: 0 0 5px 5px;
            position: sticky !important;
            top: 0px !important;
            z-index: 999 !important;
        } */
    </style>
</head>

<?php include '../template/header.php' ?>

<body>

    <div class="content-sticky" id="">
        <div class="container-fluid">
            <div class="box-content">
                <div class="row">
                    <h4 class="line-ref mb-3">
                        <i class="far fa-newspaper"></i>  
                        Edit metatags
                    </h4>

<?php
$decodedId = $_POST['metatags_id'];

// Prepare the SQL statement
$stmt = $conn->prepare("
    SELECT 
        dn.metatags_id, 
        dn.subject_metatags, 
        dn.description_metatags,
        dn.content_metatags, 
        dn.date_create, 
        GROUP_CONCAT(dnc.file_name) AS file_name,
        GROUP_CONCAT(dnc.api_path) AS pic_path,
        dnc.status
    FROM dn_metatags dn
    LEFT JOIN dn_metatags_doc dnc ON dn.metatags_id = dnc.metatags_id
    WHERE dn.metatags_id = ?
    GROUP BY dn.metatags_id
");

$stmt->bind_param('i', $decodedId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $content = $row['content_metatags'];
        $paths = explode(',', $row['pic_path']);
        $files = explode(',', $row['file_name']);
        $isCover = $row['status'];

        $isCover = $row['status'];
        $found = false;

        foreach ($files as $index => $file) {
            $pattern = '/<img[^>]+data-filename="' . preg_quote($file, '/') . '"[^>]*>/i';
            if (preg_match($pattern, $content, $matches)) {
                $new_src = $paths[$index];
                $new_img_tag = preg_replace('/(<img[^>]+)(src="[^"]*")/i', '$1 src="' . $new_src . '"', $matches[0]);
                $content = str_replace($matches[0], $new_img_tag, $content);
                $found = true;
            }
        }

        $previewImageSrc = !empty($paths) ? htmlspecialchars($paths[0]) : '';
        $content = mb_convert_encoding($content, 'UTF-8', 'auto');

        echo "
        <form id='formmetatags_edit' enctype='multipart/form-data'>
        <input type='text' class='form-control' id='metatags_id' name='metatags_id' value='" . htmlspecialchars($row['metatags_id']) . "' hidden>
            <div class='row'>
                <div class='col-md-4'>
                    <div style='margin: 10px;'>
                        <label>
                            <span>Cover photo</span>:
                        </label>
                        <div class='previewContainer'>
                            <img id='previewImage' src='{$previewImageSrc}' alt='Image Preview' style='max-width: 100%;'>
                        </div>
                    </div>
                    <div style='margin: 10px;'>
                        <input type='file' class='form-control' id='fileInput' name='fileInput[]'>
                    </div>
                    <div style='margin: 10px;'>
                        <label>
                            <span>Subject</span>:
                        </label>
                        <input type='text' class='form-control' id='metatags_subject' name='metatags_subject' value='" . htmlspecialchars($row['subject_metatags']) . "'>
                    </div>
                    <div style='margin: 10px;'>
                        <label>
                            <span>Description</span>:
                        </label>
                        <div>
                            <textarea class='form-control' id='metatags_description' name='metatags_description'>" . htmlspecialchars($row['description_metatags']) . "</textarea>
                        </div>
                    </div>
                    <div style='margin: 10px; text-align: end;'>
                        <button 
                        type='button' 
                        id='submitEditmetatags'
                        class='btn btn-success'>
                            <i class='fas fa-save'></i>
                            Save Design & metatags
                        </button>
                    </div>
                </div>
                <div class='col-md-8'>
                    <div style='margin: 10px;'>
                        <label>
                            <span>Content</span>:
                        </label>
                        <div>
                            <textarea class='form-control' id='summernote' name='metatags_content'>" . htmlspecialchars(string: $content) . "</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        ";
    }
} else {
    echo "ไม่มีข้อมูล"; // No data found message
}


$stmt->close();
?>

                </div>
            </div>

        </div>
    </div>

<!--  edit_metatags.php -->




    <script src='../js/index_.js?v=<?php echo time(); ?>'></script>
    <script src='js/metatags_.js?v=<?php echo time(); ?>'></script>

</body>

</html>