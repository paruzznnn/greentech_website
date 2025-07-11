<?php
require_once('../lib/connect.php');
global $conn;



$subjectTitle = "สินค้า"; // fallback title

if (isset($_GET['id'])) {
    $decodedId = base64_decode(urldecode($_GET['id']));

    if ($decodedId !== false) {
        $stmt = $conn->prepare("SELECT subject_idia FROM dn_idia WHERE del = 0 AND idia_id = ?");
        $stmt->bind_param('i', $decodedId);
        $stmt->execute();
        $resultTitle = $stmt->get_result();
        if ($resultTitle->num_rows > 0) {
            $row = $resultTitle->fetch_assoc();
            $subjectTitle = $row['subject_idia'];
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>

 
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($subjectTitle); ?></title>

    <?php include 'inc_head.php'?>
    <link href="css/index_.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="css/news_.css?v=<?php echo time();?>" rel="stylesheet">

    <style>

        img{
            max-width: 600px;
        }

    </style>


</head>
<body>

    <?php include 'template/header.php'?>
    <?php include 'template/navbar_slide.php'?>

    <div class="content-sticky" id="">
        <div class="container">
            <div class="box-content">

                <div class="row">

                    <div class="col-md-9">
                        <?php

                            if (isset($_GET['id'])) {
                                $decodedId = base64_decode(urldecode($_GET['id']));
                                
                                if ($decodedId !== false) {
                                    $stmt = $conn->prepare("SELECT 
                                        dn.idia_id, 
                                        dn.subject_idia, 
                                        dn.content_idia, 
                                        dn.date_create, 
                                        GROUP_CONCAT(dnc.file_name) AS file_name,
                                        GROUP_CONCAT(dnc.api_path) AS pic_path
                                        FROM dn_idia dn
                                        LEFT JOIN dn_idia_doc dnc ON dn.idia_id = dnc.idia_id
                                        WHERE dn.idia_id = ?
                                        GROUP BY dn.idia_id");

                                    $stmt->bind_param('i', $decodedId); 
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $content = $row['content_idia'];
                                            $paths = explode(',', $row['pic_path']);
                                            $files = explode(',', $row['file_name']);
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

                                            if (!$found) {
                                                echo "";
                                            }

                                            echo '<div style="">';
                                            echo $content = mb_convert_encoding($content, 'UTF-8', 'auto');
                                            echo '</div>';
                                        }
                                    } else {
                                        echo "ไม่มีข้อมูล";
                                    }

                                    $stmt->close(); 
                                } else {
                                    echo "Invalid ID.";
                                }
                            }

                        ?>
                    </div>

                    <div class="col-md-3">
                        <div class="page-plugin mt-3">
                        <iframe src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Ftrandaracoustic%2F&tabs=timeline&width=340&height=500&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId" width="340" height="500" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
                        </div>
                    </div>

                </div>
                        <!-- แสดงฟอร์มด้านล่างนี้ -->
<h3 style ="padding-top: 40px;">ความคิดเห็น</h3>
<p>อีเมลของคุณจะไม่แสดงให้คนอื่นเห็น ช่องข้อมูลจำเป็นถูกทำเครื่องหมาย *</p>
<form id="commentForm" style="max-width: 600px;">
    <textarea id="commentText" name="comment" rows="5" required placeholder="ความคิดเห็น *"
        style="width: 100%; padding: 12px; margin-bottom: 3px; border: 1px solid #ccc; border-radius: 6px;"></textarea><br>
    <button type="submit"
        style="background-color: red; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer;">
        แสดงความคิดเห็น
    </button>
</form>

<script>
document.getElementById("commentForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const jwt = sessionStorage.getItem("jwt");
    const comment = document.getElementById("commentText").value;
    const pageUrl = window.location.pathname;

    if (!jwt) {
        // alert("กรุณาเข้าสู่ระบบก่อนแสดงความคิดเห็น");
        document.getElementById("myBtn-sign-in").click(); // เปิด modal login
        return;
    }

    fetch('actions/protected.php', {
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + jwt
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success" && parseInt(data.data.role_id) === 3) {
            // ส่งคอมเม้นไปเก็บใน database
            fetch('actions/save_comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + jwt
                },
                body: JSON.stringify({
                    comment: comment,
                    page_url: pageUrl
                })
            })
            .then(res => res.json())
            .then(result => {
                if (result.status === 'success') {
                    alert("บันทึกความคิดเห็นเรียบร้อยแล้ว");
                    document.getElementById("commentText").value = '';
                } else {
                    alert("เกิดข้อผิดพลาด: " + result.message);
                }
            });
        } else {
            alert("ต้องเข้าสู่ระบบในฐานะ viewer เท่านั้น");
        }
    })
    .catch(err => {
        console.error("Error verifying user:", err);
        alert("เกิดข้อผิดพลาดในการยืนยันตัวตน");
    });
});
</script>                
            </div>
        </div>
    </div>

    <?php include 'template/footer.php'?>
    

    <script src="js/index_.js?v=<?php echo time();?>"></script>
    <script src="js/idia/idia_.js?v=<?php echo time();?>"></script>

</body>
</html>