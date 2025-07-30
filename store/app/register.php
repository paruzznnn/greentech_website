<?php
session_start();
if(!empty($_SESSION)){
    if($_SESSION['exp'] < time()){
        header("Location: admin/logout.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRANDAR STORE</title>

    <link rel="icon" type="image/x-icon" href="../favicon.ico">

    <link href="../inc/jquery/css/jquery-ui.css" rel="stylesheet">
    <script src="../inc/jquery/js/jquery-3.6.0.min.js"></script>
    <script src="../inc/jquery/js/jquery-ui.min.js"></script>

    <link href="../inc/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../inc/bootstrap/js/bootstrap.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/fontawesome5-fullcss@1.1.0/css/all.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <link href="css/index_.css?v=<?php echo time();?>" rel="stylesheet">

    <link href="../inc/sweetalert2/css/sweetalert2.min.css" rel="stylesheet">
    <script src="../inc/sweetalert2/js/sweetalert2.all.min.js"></script>

    <link href="../inc/select2/css/select2.min.css" rel="stylesheet">
    <script src="../inc/select2/js/select2.min.js"></script>


</head>
<body>


    <div class="container-fluid">
    <?php include 'template/header.php'?>
    </div>

    <div class="container-fluid">
    <?php include 'template/register_detail.php'?>
    </div>

    <div class="container-fluid">
    <?php include 'template/footer.php'?>
    </div>


<script src="js/index_.js?v=<?php echo time();?>"></script>
<script src="../inc/getLanguage.js"></script>
<script>

$(document).ready(function() {
    var isSubmitting = false;

    // Function to validate a form
    function validateForm(form) {
    var isValid = true;
    
    form.find('input, select, textarea').each(function() {
        if ($(this).prop('required')) {
            // ตรวจสอบเช็คบ็อกซ์ consent
            if ($(this).attr('type') === 'checkbox' && $(this).attr('name') === 'consent' && !$(this).is(':checked')) {
                isValid = false;
                alert('โปรดยืนยันการยินยอมเงื่อนไข (Consent)'); 
                return false; // หยุดการตรวจสอบเพิ่มเติม
            }
            // ตรวจสอบเช็คบ็อกซ์ email_verification
            else if ($(this).attr('type') === 'checkbox' && $(this).attr('name') === 'verify' && !$(this).is(':checked')) {
                isValid = false;
                alert('โปรดยืนยันการยินยอมรับข่าวสารทางอีเมล (Email Verification)'); 
                return false; // หยุดการตรวจสอบเพิ่มเติม
            }
            // ตรวจสอบฟิลด์ที่ต้องกรอก
            else if ($(this).val().trim() === '') {
                isValid = false;
                $(this).addClass('is-invalid'); // เพิ่มคลาส is-invalid เพื่อแสดงข้อผิดพลาด
                return false; // หยุดการตรวจสอบเพิ่มเติม
            } else {
                $(this).removeClass('is-invalid'); // ลบคลาส is-invalid หากฟิลด์นั้นถูกกรอกแล้ว
            }
        }
    });

    return isValid; // คืนค่า isValid ว่าผ่านการตรวจสอบหรือไม่
}

    // Function to submit forms via AJAX
    function submitForms(form1, form2, url, role) {
        if (isSubmitting) return;

        isSubmitting = true;

        var formData1 = form1.serialize();
        var formData2 = form2.serialize();

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                action: 'save_signup',
                register_data: formData1,
                consent_data: formData2,
                role
            },
            dataType: 'json',
            success: function(response) {
                
                if(response.status == 'succeed'){
                    window.location.href = 'admin/logout.php';
                } else {
                    alert('Error:' + response.message);
                }
            },
            error: function() {
                console.error('An error occurred.');
            },
            complete: function() {
                isSubmitting = false;
            }
        });
    }

    // Event handler for #submitC
    $('#submitC').on('click', function(event) {
        event.preventDefault(); 

        var form1 = $('#form1');
        var form2 = $('#form2');

        if (validateForm(form1) && validateForm(form2)) {
            submitForms(form1, form2, 'actions/process_register.php', 2);
        } else {
            isSubmitting = false;
        }
    });


});

</script>

</body>
</html>