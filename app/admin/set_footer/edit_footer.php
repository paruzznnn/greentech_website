<?php
// edit_footer.php
include '../check_permission.php';

// require_once(__DIR__ . '/../../../lib/connect.php');
// require_once(__DIR__ . '/../../../lib/base_directory.php');

$footer_id = 1; // ID ของ Footer Settings (เราใช้แค่ 1 ชุด)

$stmt = $conn->prepare("SELECT * FROM footer_settings WHERE id = ?");
$stmt->bind_param("i", $footer_id);
$stmt->execute();
$result = $stmt->get_result();
$footer_data = $result->fetch_assoc();
$stmt->close();

if (!$footer_data) {
    // ถ้ายังไม่มีข้อมูลใน DB ให้สร้างข้อมูลเริ่มต้น (ควรทำในขั้นตอนการติดตั้ง หรือ INSERT SQL ด้านบน)
    // หรือ redirect ไปหน้าแจ้งเตือน
    echo "<script>alert('Footer settings not found. Please ensure initial data is in the database.'); window.location.href='../dashboard.php';</script>";
    exit;
}

// Decode JSON สำหรับ Social Links เพื่อนำมาแสดงผล
$social_links = json_decode($footer_data['social_links_json'], true);
if (json_last_error() !== JSON_ERROR_NONE) {
    $social_links = []; // ถ้า decode ไม่ได้ ให้เป็น array ว่าง
    error_log("Error decoding social_links_json: " . json_last_error_msg());
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไข Footer</title>

    <link rel="icon" type="image/x-icon" href="../../../public/img/q-removebg-preview1.png">

    <link href="../../../inc/jquery/css/jquery-ui.css" rel="stylesheet">
    <script src="../../../inc/jquery/js/jquery-3.6.0.min.js"></script>
    <script src="../../../inc/jquery/js/jquery-ui.min.js"></script>

    <link href="../../../inc/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../../inc/bootstrap/js/bootstrap.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/fontawesome5-fullcss@1.1.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">

    <link href="../../../inc/sweetalert2/css/sweetalert2.min.css" rel="stylesheet">
    <script src="../../../inc/sweetalert2/js/sweetalert2.all.min.js"></script>

    <link href="../../../inc/select2/css/select2.min.css" rel="stylesheet">
    <script src="../../../inc/select2/js/select2.min.js"></script>

    <link href="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.js"></script>

    <!-- ThymeLeaf/Color Picker library for color input (optional) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.css" />

    <link href='../css/index_.css?v=<?php echo time(); ?>' rel='stylesheet'>

    <style>
        .line-ref {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            border-left: 5px solid #f57c00;
            padding-left: 10px;
            color: #333;
        }
        .form-section {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .form-section label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }
        .social-link-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            background-color: #e9e9e9;
            padding: 8px;
            border-radius: 5px;
        }
        .social-link-item i {
            font-size: 20px;
            margin-right: 10px;
            width: 25px; /* Fixed width for icon */
            text-align: center;
        }
        .social-link-item input {
            flex-grow: 1;
            margin-right: 10px;
        }
        .social-link-item button {
            margin-left: 5px;
        }
        #loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
        /* Specific styles for Color Picker */
        .sp-replacer {
            display: flex;
            align-items: center;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            height: calc(2.25rem + 2px); /* Match Bootstrap input height */
            padding: 0.375rem 0.75rem;
            width: 100%;
            cursor: pointer;
        }
        .sp-preview {
            width: 24px;
            height: 24px;
            border: 1px solid #ccc;
            margin-right: 10px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
<?php include '../template/header.php'; ?>

<div id="loading-overlay">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<div class="container mt-4">
    <div class="box-content p-4 bg-light rounded shadow-sm">
        <h4 class="line-ref">
            <i class="fa-solid fa-edit"></i> แก้ไข Footer
        </h4>

        <form id="editFooterForm">
            <input type="hidden" name="footer_id" value="<?= htmlspecialchars($footer_data['id']) ?>">
            <input type="hidden" name="action" value="edit_footer">

            <!-- ส่วนแก้ไขข้อความทั่วไป -->
            <div class="form-section">
                <label for="bg_color">สีพื้นหลัง Footer:</label>
                <input type="text" id="bg_color" name="bg_color" class="form-control" value="<?= htmlspecialchars($footer_data['bg_color']) ?>">
            </div>

            <div class="form-section">
                <label for="footer_top_title">หัวข้อส่วนบน (Register Title):</label>
                <input type="text" id="footer_top_title" name="footer_top_title" class="form-control" value="<?= htmlspecialchars($footer_data['footer_top_title']) ?>">
            </div>

            <div class="form-section">
                <label for="footer_top_subtitle">คำอธิบายส่วนบน (Register Subtitle):</label>
                <textarea id="footer_top_subtitle" name="footer_top_subtitle" class="form-control" rows="2"><?= htmlspecialchars($footer_data['footer_top_subtitle']) ?></textarea>
            </div>

            <div class="form-section">
                <label for="about_heading">หัวข้อเกี่ยวกับเรา:</label>
                <input type="text" id="about_heading" name="about_heading" class="form-control" value="<?= htmlspecialchars($footer_data['about_heading']) ?>">
            </div>

            <div class="form-section">
                <label for="about_text">ข้อความเกี่ยวกับเรา:</label>
                <textarea id="about_text" name="about_text" class="form-control" rows="5"><?= htmlspecialchars($footer_data['about_text']) ?></textarea>
            </div>

            <div class="form-section">
                <label for="contact_heading">หัวข้อติดต่อเรา:</label>
                <input type="text" id="contact_heading" name="contact_heading" class="form-control" value="<?= htmlspecialchars($footer_data['contact_heading']) ?>">
            </div>
            <div class="form-section">
                <label for="contact_address">ที่อยู่:</label>
                <input type="text" id="contact_address" name="contact_address" class="form-control" value="<?= htmlspecialchars($footer_data['contact_address']) ?>">
            </div>
            <div class="form-section">
                <label for="contact_phone">เบอร์โทรศัพท์:</label>
                <input type="text" id="contact_phone" name="contact_phone" class="form-control" value="<?= htmlspecialchars($footer_data['contact_phone']) ?>">
            </div>
            <div class="form-section">
                <label for="contact_email">อีเมล:</label>
                <input type="email" id="contact_email" name="contact_email" class="form-control" value="<?= htmlspecialchars($footer_data['contact_email']) ?>">
            </div>
            <div class="form-section">
                <label for="contact_hours_wk">เวลาทำการ (จันทร์-ศุกร์):</label>
                <input type="text" id="contact_hours_wk" name="contact_hours_wk" class="form-control" value="<?= htmlspecialchars($footer_data['contact_hours_wk']) ?>">
            </div>
            <div class="form-section">
                <label for="contact_hours_sat">เวลาทำการ (เสาร์):</label>
                <input type="text" id="contact_hours_sat" name="contact_hours_sat" class="form-control" value="<?= htmlspecialchars($footer_data['contact_hours_sat']) ?>">
            </div>

            <div class="form-section">
                <label for="social_heading">หัวข้อ Social Media:</label>
                <input type="text" id="social_heading" name="social_heading" class="form-control" value="<?= htmlspecialchars($footer_data['social_heading']) ?>">
            </div>

            <!-- ส่วนจัดการ Social Links (Dynamic) -->
            <div class="form-section">
                <label>Social Media Links:</label>
                <div id="socialLinksContainer">
                    <!-- Social links will be dynamically added here by JavaScript -->
                </div>
                <button type="button" class="btn btn-success mt-3" id="addSocialLink">
                    <i class="fas fa-plus"></i> เพิ่ม Social Link
                </button>
            </div>

            <div class="form-section">
                <label for="copyright_text">ข้อความลิขสิทธิ์ (Copyright Text):</label>
                <input type="text" id="copyright_text" name="copyright_text" class="form-control" value="<?= htmlspecialchars($footer_data['copyright_text']) ?>">
            </div>

            <div class="text-end mt-4">
                <button type="submit" id="submitEditFooter" class="btn btn-primary">
                    <i class="fas fa-save"></i> บันทึกการเปลี่ยนแปลง
                </button>
            </div>
        </form>
    </div>
</div>

<script src='../js/index_.js?v=<?php echo time(); ?>'></script>
<script>
    // Initialize Spectrum Color Picker
    $("#bg_color").spectrum({
        color: "<?= htmlspecialchars($footer_data['bg_color']) ?>",
        flat: false,
        showInput: true,
        allowEmpty: false,
        showPalette: true,
        palette: [
            ["#393939", "#000000", "#FFFFFF", "#FF5733", "#33FF57", "#3357FF"]
        ],
        showInitial: true,
        showSelectionPalette: true,
        preferredFormat: "hex",
    });

    // Handle Social Links Dynamic Management
    let socialLinks = <?= json_encode($social_links); ?>; // PHP data to JS variable

    function renderSocialLinks() {
        const container = $('#socialLinksContainer');
        container.empty(); // Clear existing items

        if (socialLinks.length === 0) {
            container.append('<p class="text-muted">ยังไม่มี Social Link. กรุณาเพิ่มลิงก์.</p>');
        }

        socialLinks.forEach((link, index) => {
            const itemHtml = `
                <div class="social-link-item">
                    <i class="${link.icon}" style="color: ${link.color};"></i>
                    <input type="text" class="form-control form-control-sm" placeholder="Icon Class (e.g., fab fa-facebook-f)" value="${link.icon}" data-index="${index}" data-field="icon">
                    <input type="text" class="form-control form-control-sm ms-2" placeholder="URL (e.g., https://facebook.com/)" value="${link.url}" data-index="${index}" data-field="url">
                    <input type="text" class="form-control form-control-sm ms-2 social-color-picker" value="${link.color}" data-index="${index}" data-field="color">
                    <button type="button" class="btn btn-danger btn-sm delete-social-link" data-index="${index}"><i class="fas fa-trash"></i></button>
                </div>
            `;
            container.append(itemHtml);
        });

        // Initialize color pickers for newly added/rendered items
        $('.social-color-picker').each(function() {
            const initialColor = $(this).val();
            $(this).spectrum({
                color: initialColor,
                flat: false,
                showInput: true,
                allowEmpty: false,
                showPalette: true,
                preferredFormat: "hex",
                change: function(color) {
                    const index = $(this).data('index');
                    socialLinks[index].color = color.toHexString();
                    $(this).prevAll('i').first().css('color', color.toHexString()); // Update icon color preview
                }
            });
             // Update icon color on initial render
            const index = $(this).data('index');
            $(this).prevAll('i').first().css('color', socialLinks[index].color);
        });
    }

    // Add new social link
    $('#addSocialLink').on('click', function() {
        socialLinks.push({ icon: '', url: '', color: '#393939' });
        renderSocialLinks();
    });

    // Handle input changes for social links
    $('#socialLinksContainer').on('input', 'input', function() {
        const index = $(this).data('index');
        const field = $(this).data('field');
        socialLinks[index][field] = $(this).val();
        if (field === 'icon') {
            $(this).prevAll('i').first().attr('class', $(this).val()); // Update icon preview
        }
    });

    // Handle delete social link
    $('#socialLinksContainer').on('click', '.delete-social-link', function() {
        const index = $(this).data('index');
        socialLinks.splice(index, 1); // Remove item from array
        renderSocialLinks(); // Re-render the list
    });

    // Initial render when page loads
    renderSocialLinks();

    // Form Submission Handler
    $('#submitEditFooter').on('click', function(e) {
        e.preventDefault(); // Prevent default form submission

        var formData = new FormData($('#editFooterForm')[0]);

        // Add social links JSON to formData
        formData.append('social_links_json', JSON.stringify(socialLinks));

        Swal.fire({
            title: "ยืนยันการแก้ไข?",
            text: "คุณต้องการอัปเดต Footer นี้ใช่หรือไม่!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FFC107",
            cancelButtonColor: "#d33",
            confirmButtonText: "อัปเดต"
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loading-overlay').fadeIn(); // Show loading overlay

                $.ajax({
                    url: "actions/process_footer.php", // Target the new process_footer.php
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        $('#loading-overlay').fadeOut(); // Hide loading overlay
                        if (response.status === 'success') {
                            Swal.fire(
                                'สำเร็จ!',
                                'แก้ไข Footer เรียบร้อยแล้ว.',
                                'success'
                            ).then(() => {
                                // Reload page to reflect changes
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'เกิดข้อผิดพลาด!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#loading-overlay').fadeOut(); // Hide loading overlay
                        console.error("AJAX Error:", status, error, xhr.responseText);
                        Swal.fire(
                            'เกิดข้อผิดพลาด!',
                            'ไม่สามารถแก้ไข Footer ได้: ' + error,
                            'error'
                        );
                    }
                });
            }
        });
    });
</script>

</body>
</html>