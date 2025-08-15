

$(document).ready(function () {

    // Function to get URL parameter
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Function to initialize or reload DataTable
    function loadListShop(lang) {
        // Destroy existing DataTable instance if it exists
        if ($.fn.DataTable.isDataTable('#td_list_shop')) {
            $('#td_list_shop').DataTable().destroy();
            $('#td_list_shop tbody').empty(); // <-- Add this line to clear the tbody
        }

        new DataTable('#td_list_shop', {
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
                "aria": {
                    "orderable": "Order by this column",
                    "orderableReverse": "Reverse order this column"
                }
            },
            "processing": true,
            "serverSide": true,
            ajax: {
                url: "actions/process_shop.php",
                method: 'POST',
                dataType: 'json',
                data: function (d) {
                    d.action = 'getData_shop';
                    d.lang = lang; // Add language parameter to the AJAX request
                    // d.filter_date = $('#filter_date').val();
                    // d.customParam2 = "value2";
                },
                dataSrc: function (json) {
                    return json.data;
                }
            },
            "ordering": false,
            "pageLength": 25,
            "lengthMenu": [10, 25, 50, 100],
            columnDefs: [
                {
                    "target": 0,
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    "target": 1,
                    data: "main_group_name",
                    render: function (data) {
                        return data || "-";
                    }
                },
                {
                    "target": 2,
                    data: "sub_group_name",
                    render: function (data) {
                        return data || "-";
                    }
                },
                {
                    "target": 3,
                    data: null,
                    render: function (data, type, row) {
                        return data.subject_shop;
                    }
                },
                {
                    "target": 4,
                    data: null,
                    render: function (data, type, row) {
                        return data.date_create;

                    }
                },
                {
                    "target": 5,
                    data: null,
                    render: function (data, type, row) {

                        let divBtn = `
                        <div class="d-flex">`;

                        divBtn += `
                        <span style="margin: 2px;">
                            <button type="button" class="btn-circle btn-edit">
                            <i class="fas fa-pencil-alt"></i>
                            </button>
                        </span>
                        `;

                        divBtn += `
                        <span style="margin: 2px;">
                            <button type="button" class="btn-circle btn-del">
                            <i class="fas fa-trash-alt"></i>
                            </button>
                        </span>
                        `;

                        divBtn += `
                        </div>
                        `;

                        return divBtn;

                    }
                }
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
            initComplete: function (settings, json) {
                // ... (ส่วน initComplete เดิม)
            },
            rowCallback: function (row, data, index) {
                var editButton = $(row).find('.btn-edit');
                var deleteButton = $(row).find('.btn-del');

                editButton.off('click').on('click', function () {
                    reDirect('edit_shop.php', {
                        shop_id: data.shop_id
                    });
                });

                deleteButton.off('click').on('click', function () {
                    Swal.fire({
                        title: "Are you sure?",
                        text: "Do you want to delete the shop?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#4CAF50",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Accept"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#loading-overlay').fadeIn();
                            $.ajax({
                                url: 'actions/process_shop.php',
                                type: 'POST',
                                data: {
                                    action: 'delshop',
                                    id: data.shop_id,
                                },
                                dataType: 'json',
                                success: function (response) {
                                    if (response.status == 'success') {
                                        window.location.reload();
                                    }
                                },
                                error: function (xhr, status, error) {
                                    console.error('Error:', error);
                                }
                            });
                        } else {
                            $('#loading-overlay').fadeOut();
                        }
                    });
                });
            }
        });
    }

    // Event handler for flag clicks
    $('.lang-flag').on('click', function() {
        // Remove 'active' class from all flags and set opacity
        $('.lang-flag').removeClass('active').css('opacity', '0.5');

        // Add 'active' class to the clicked flag and set opacity
        $(this).addClass('active').css('opacity', '1');

        // Get the selected language from data-lang attribute
        let selectedLang = $(this).data('lang');

        // Reload the DataTable with the new language
        loadListShop(selectedLang);
    });

    // Initial load of the table with default language (Thai) or from URL
    let defaultLang = getUrlParameter('lang') || 'th';
    $('.lang-flag[data-lang="' + defaultLang + '"]').addClass('active').css('opacity', '1');
    loadListShop(defaultLang);


    // ... (ส่วน summernote, readURL, และฟังก์ชันอื่นๆ ที่ไม่มีการเปลี่ยนแปลง)
    if ($(".summernote").length > 0) {
        $(".summernote").summernote({
            height: 600,
            minHeight: 600,
            maxHeight: 600,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['fontname', 'fontsize', 'forecolor']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'video', 'table']],
                ['view', ['fullscreen', ['codeview', 'fullscreen']]],
                ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter']]
            ],
            fontNames: ['Kanit', 'Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Georgia', 'Times New Roman', 'Verdana', 'sans-serif'],
            fontNamesIgnoreCheck: ['Kanit'],
            fontsizeUnits: ['px', 'pt'],
            fontsize: ['8', '10', '12', '14', '16', '18', '24', '36'],
            callbacks: {
                // ... (ส่วน callbacks เดิมของคุณ)
            }
        });
    }

    var readURL = function (input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                let previewImage = $('#previewImage');
                previewImage.attr('src', e.target.result);
                previewImage.css('display', 'block');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#fileInput").on('change', function () {
        readURL(this);
    });
    
    $("#submitAddshop").on("click", function (event) {
        event.preventDefault();
    
        let subGroupVal = $('#sub_group_select').val();
        let mainGroupVal = $('#main_group_select').val();
    
        // กำหนด group_id โดยใช้ group_id ของกลุ่มย่อย ถ้ามี ถ้าไม่มีใช้ group_id ของกลุ่มแม่
        let groupId = subGroupVal ? subGroupVal : mainGroupVal;
    
        var formshop = $("#formshop")[0];
        var formData = new FormData(formshop);
        formData.append("action", "addshop");
        formData.set('group_id', groupId); // ✅ กำหนด group_id ชัดเจน
    
        // Get content from Summernote
        var contentFromEditor = $("#summernote").summernote('code'); // ใช้ id #summernote สำหรับหน้าเพิ่ม
        console.log("🔍 contentFromEditor (raw):", contentFromEditor);
    
        var checkIsUrl = false;
        var finalContent = '';
    
        if (contentFromEditor) {
            var tempDiv = document.createElement("div");
            tempDiv.innerHTML = contentFromEditor;
            console.log("🧩 Created tempDiv with innerHTML set");
    
            var imgTags = tempDiv.getElementsByTagName("img");
            console.log("📸 Number of <img> tags found:", imgTags.length);
    
            for (var i = 0; i < imgTags.length; i++) {
                var imgSrc = imgTags[i].getAttribute("src");
                var filename = imgTags[i].getAttribute("data-filename");
                console.log(`🔎 img[${i}] src:`, imgSrc, ", filename:", filename);
    
                if (!imgSrc) {
                    console.warn(`⚠️ img[${i}] has no src, skipping.`);
                    continue;
                }
    
                imgSrc = imgSrc.replace(/ /g, "%20");
    
                // ตรวจสอบว่ารูปภาพเป็น Base64 หรือไม่
                if (imgSrc.startsWith("data:image")) {
                    console.log(`🛠️ img[${i}] src is a Base64 image, converting to file.`);
                    var file = base64ToFile(imgSrc, filename || `image_${Date.now()}.png`); // เพิ่ม filename default
                    if (file) {
                        formData.append("image_files[]", file);
                        console.log(`✅ Appended image_files[] with filename: ${file.name}`);
                        imgTags[i].setAttribute("src", ""); // Clear src to avoid sending base64 again
                    } else {
                        console.warn(`⚠️ Failed to convert base64 to file for img[${i}]`);
                    }
                } else if (isValidUrl(imgSrc)) {
                    console.log(`🌐 img[${i}] src is a valid URL, no conversion needed.`);
                    checkIsUrl = true;
                } else {
                    checkIsUrl = true; // เป็น URL ปกติ
                }
            }
    
            finalContent = tempDiv.innerHTML;
            formData.set("shop_content", finalContent);
            console.log("📝 finalContent (cleaned):", finalContent);
        } else {
            console.warn("⚠️ contentFromEditor is empty");
        }
    
        // Validate
        $(".is-invalid").removeClass("is-invalid");
    
        // ตรวจสอบ Cover photo
        const fileInput = document.getElementById('fileInput');
        if (!fileInput || fileInput.files.length === 0) {
            alertError("Please add a cover photo.");
            // ไม่มี field ให้ class is-invalid แต่สามารถเน้น input file ได้ถ้าต้องการ
            return;
        }
    
        if (!$("#shop_subject").val().trim()) {
            $("#shop_subject").addClass("is-invalid");
            alertError("Please fill in the subject.");
            return;
        }
        if (!$("#shop_description").val().trim()) {
            $("#shop_description").addClass("is-invalid");
            alertError("Please fill in the description.");
            return;
        }
        if (!finalContent.trim()) {
            alertError("Please fill in content information.");
            return;
        }
        if (!groupId) { // ตรวจสอบว่าได้เลือกกลุ่มหรือไม่
            alertError("Please select a group.");
            $('#main_group_select').addClass("is-invalid");
            $('#sub_group_select').addClass("is-invalid");
            return;
        }
    
        // Logging FormData content for debugging (can be large)
        console.log("📤 Form data prepared:");
        for (let [key, value] of formData.entries()) {
            if (value instanceof File) {
                console.log(`  ${key}: File (name: ${value.name}, type: ${value.type}, size: ${value.size} bytes)`);
            } else {
                console.log(`  ${key}: ${value}`);
            }
        }
    
        Swal.fire({
            title: checkIsUrl ? "Image detection system from other websites?" : "Are you sure?",
            text: "Do you want to add shop?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#4CAF50",
            cancelButtonColor: "#d33",
            confirmButtonText: "Accept"
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loading-overlay').fadeIn();
                console.log("🚀 Sending AJAX request...");
    
                $.ajax({
                    url: "actions/process_shop.php",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        console.log("✅ AJAX success:", response);
                        try {
                            var json = (typeof response === "string") ? JSON.parse(response) : response;
                            if (json.status === 'success') {
                                Swal.fire('Success', json.message, 'success').then(() => {
                                    window.location.reload(); // Reload หน้าเมื่อสำเร็จ
                                });
                            } else {
                                Swal.fire('Error', json.message || 'Unknown error', 'error');
                            }
                        } catch (e) {
                            console.error("❌ JSON parse error:", e);
                            Swal.fire('Error', 'Invalid response from server: ' + e.message, 'error');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("❌ AJAX error:", status, error, xhr.responseText);
                        Swal.fire('Error', 'AJAX request failed: ' + xhr.status + ' ' + xhr.statusText, 'error');
                    },
                    complete: function () {
                        $('#loading-overlay').fadeOut();
                    }
                });
            } else {
                console.log("❎ User cancelled action");
                $('#loading-overlay').fadeOut();
            }
        });
    });
    
    
    // ฟังก์ชันสำหรับตรวจสอบว่าเป็น URL ที่ถูกต้องหรือไม่
    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (e) {
            return false;
        }
    }
    
    // ฟังก์ชันแปลง Base64 เป็น File Object
    function base64ToFile(base64, filename) {
        try {
            const arr = base64.split(',');
            if (arr.length < 2) {
                console.error("Invalid base64 string format:", base64);
                return null;
            }
            const mimeMatch = arr[0].match(/:(.*?);/);
            if (!mimeMatch) {
                console.error("Could not extract MIME type from base64 string:", arr[0]);
                return null;
            }
            const mime = mimeMatch[1];
            const bstr = atob(arr[1]);
            let n = bstr.length;
            const u8arr = new Uint8Array(n);
            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }
            return new File([u8arr], filename, { type: mime });
        } catch (e) {
            console.error("Error converting base64 to file:", e);
            return null;
        }
    }
    
    
    // ... (ส่วนบนของโค้ด)
    
    $("#submitEditshop").on("click", function (event) {
        event.preventDefault();
    
        let subGroupVal = $('#sub_group_select').val();
        let mainGroupVal = $('#main_group_select').val();
    
        let groupId = subGroupVal ? subGroupVal : mainGroupVal;
    
        let formData = new FormData(document.getElementById('formshop_edit'));
        formData.append('action', 'editshop');
        formData.set('group_id', groupId);
    
        var contentFromEditor = $("#summernote_update").summernote('code');
        var checkIsUrl = false;
        var finalContent = '';
    
        if (contentFromEditor) {
            var tempDiv = document.createElement("div");
            tempDiv.innerHTML = contentFromEditor;
    
            var imgTags = tempDiv.getElementsByTagName("img");
    
            for (var i = 0; i < imgTags.length; i++) {
                var imgSrc = imgTags[i].getAttribute("src");
                var filename = imgTags[i].getAttribute("data-filename");
    
                if (!imgSrc) {
                    console.warn(`⚠️ img[${i}] has no src, skipping.`);
                    continue;
                }
    
                imgSrc = imgSrc.replace(/ /g, "%20");
    
                if (imgSrc.startsWith("data:image")) {
                    console.log(`🛠️ img[${i}] src is a Base64 image, converting to file.`);
                    var file = base64ToFile(imgSrc, filename || `image_${Date.now()}.png`);
                    if (file) {
                        formData.append("image_files[]", file);
                        console.log(`✅ Appended image_files[] with filename: ${file.name}`);
                        // เมื่อเป็น Base64 ที่กำลังจะถูกอัปโหลดใหม่ ให้ล้าง src เพื่อไม่ให้ส่ง Base64 ไปใน content_shop
                        // แต่ถ้า Server ประสบความสำเร็จในการอัปโหลดรูปและส่ง path กลับมา
                        // edit_shop.php จะเป็นคนจัดการแทนที่ src อีกครั้งเมื่อโหลดหน้า
                        imgTags[i].setAttribute("src", "");
                    } else {
                        console.warn(`⚠️ Failed to convert base64 to file for img[${i}]`);
                    }
                } else if (isValidUrl(imgSrc)) {
                    // ถ้าเป็น URL อยู่แล้ว (เช่น รูปที่อัปโหลดไปก่อนหน้า)
                    // ไม่ต้องทำอะไรกับ src ปล่อยให้มันเป็น URL เดิม
                    checkIsUrl = true; // อาจจะมีรูปจาก URL อื่นๆ หรือรูปที่อัปโหลดไปแล้ว
                    console.log(`🌐 img[${i}] src is a valid URL, keeping original src.`);
                } else {
                    // กรณีที่ไม่ใช่ Base64 และไม่ใช่ URL ที่ถูกต้อง (เช่น อาจจะเป็น blob: URL หรือ path ผิดๆ)
                    // ณ จุดนี้ โค้ดนี้อาจต้องจัดการเพิ่มเติม ขึ้นอยู่กับ Summernote สร้าง src แบบใด
                    // แต่มักจะหมายถึงรูปที่เพิ่งวางและยังไม่ถูกอัปโหลด
                    // สำหรับตอนนี้ ให้คง src เดิมไว้หากไม่สามารถจัดการเป็นไฟล์ได้
                    console.warn(`❓ img[${i}] src is neither Base64 nor a valid URL: ${imgSrc}`);
                    // ถ้าเป็น blob URL (ของ Summernote เอง) เราอาจจะต้องพยายามแปลงเป็นไฟล์ด้วย
                    // แต่มักจะหมายถึงรูปที่เพิ่งวางและยังไม่ถูกอัปโหลด
                    // สำหรับตอนนี้ ให้คง src เดิมไว้หากไม่สามารถจัดการเป็นไฟล์ได้
                }
            }
    
            finalContent = tempDiv.innerHTML;
            formData.set("shop_content", finalContent);
        } else {
            console.warn("⚠️ contentFromEditor is empty");
        }
    
        // ... (ส่วนที่เหลือของโค้ดเหมือนเดิม)
        // ตรวจสอบไฟล์ Cover photo
        const fileInput = document.getElementById('fileInput');
        if (fileInput && fileInput.files.length > 0) {
            formData.append('fileInput', fileInput.files[0]);
            console.log("📤 Appended fileInput (Cover photo).");
        }
    
        // Validate
        $(".is-invalid").removeClass("is-invalid");
    
        if (!$("#shop_subject").val().trim()) {
            $("#shop_subject").addClass("is-invalid");
            console.error("❌ Validation failed: shop_subject is empty");
            alertError("Please fill in the subject.");
            return;
        }
        if (!$("#shop_description").val().trim()) {
            $("#shop_description").addClass("is-invalid");
            console.error("❌ Validation failed: shop_description is empty");
            alertError("Please fill in the description.");
            return;
        }
        if (!finalContent.trim()) {
            alertError("Please fill in content information.");
            console.error("❌ Validation failed: shop_content is empty");
            return;
        }
        if (!groupId) {
            alertError("Please select a group.");
            console.error("❌ Validation failed: group_id is empty");
            $('#main_group_select').addClass("is-invalid");
            $('#sub_group_select').addClass("is-invalid");
            return;
        }
    
    
        formData.set("shop_subject", $("#shop_subject").val());
        formData.set("shop_description", $("#shop_description").val());
    
        Swal.fire({
            title: checkIsUrl ? "Image detection system from other websites?" : "Are you sure?",
            text: "Do you want to edit shop?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#4CAF50",
            cancelButtonColor: "#d33",
            confirmButtonText: "Accept"
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loading-overlay').fadeIn();
                console.log("🚀 Sending AJAX request...");
    
                $.ajax({
                    url: "actions/process_shop.php",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        console.log("✅ AJAX success:", response);
                        try {
                            var json = (typeof response === "string") ? JSON.parse(response) : response;
                            if (json.status === 'success') {
                                Swal.fire('Success', json.message, 'success').then(() => {
                                    location.reload(); // This line refreshes the current page
                                });
                            } else {
                                Swal.fire('Error', json.message || 'Unknown error', 'error');
                            }
                        } catch (e) {
                            console.error("❌ JSON parse error:", e);
                            Swal.fire('Error', 'Invalid response from server: ' + e.message, 'error');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("❌ AJAX error:", status, error, xhr.responseText);
                        Swal.fire('Error', 'AJAX request failed: ' + xhr.status + ' ' + xhr.statusText, 'error');
                    },
                    complete: function () {
                        $('#loading-overlay').fadeOut();
                    }
                });
            } else {
                console.log("❎ User cancelled action");
                $('#loading-overlay').fadeOut();
            }
        });
    });
    
    $("#backToShopList").on("click", function () {
        window.location.href = "list_shop.php";
    });
    
    // ฟังก์ชัน alertError (ถ้ายังไม่มีใน index_.js)
    function alertError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: message,
        });
    }
    
    
    
    function reDirect(url, data) {
        var form = $('<form>', {
            method: 'POST',
            action: url,
            target: '_self'  // เปิดในแท็บเดิม
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
    


});

function base64ToFile(base64, fileName) {
    if (!base64 || typeof base64 !== "string" || !base64.startsWith("data:")) {
        console.error("Invalid base64 input:", base64);
        return null;
    }

    var fileExtension = fileName.split(".").pop().toLowerCase();

    var mimeType;
    switch (fileExtension) {
        case "jpg":
        case "jpeg":
            mimeType = "image/jpeg";
            break;
        case "png":
            mimeType = "image/png";
            break;
        case "gif":
            mimeType = "image/gif";
            break;
        case "pdf":
            mimeType = "application/pdf";
            break;
        case "txt":
            mimeType = "text/plain";
            break;
        default:
            mimeType = "application/octet-stream";
    }

    try {
        const base64Data = base64.split(",")[1];
        const byteString = atob(base64Data);
        const arrayBuffer = new ArrayBuffer(byteString.length);
        const uint8Array = new Uint8Array(arrayBuffer);

        for (let i = 0; i < byteString.length; i++) {
            uint8Array[i] = byteString.charCodeAt(i);
        }

        const blob = new Blob([uint8Array], { type: mimeType });
        const file = new File([blob], fileName, { type: mimeType });

        return file;

    } catch (e) {
        console.error("Failed to decode base64:", e, base64);
        return null;
    }
}

function alertError(textAlert) {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });
    Toast.fire({
        icon: "error",
        title: textAlert
    });
}


// ฟังก์ชันสำหรับอัปโหลดไฟล์ (เรียกใช้จาก Summernote Callback)
function uploadFile(file, editor) {
    var formData = new FormData();
    formData.append('file', file);
    formData.append('action', 'upload_image_summernote'); // ระบุ action สำหรับการอัปโหลดรูป
    $.ajax({
        url: 'actions/process_shop.php', // URL ของ script ที่จะจัดการการอัปโหลด
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        type: 'POST',
        success: function(response) {
            try {
                var json = (typeof response === "string") ? JSON.parse(response) : response;
                if (json.status === 'success') {
                    var imageUrl = json.url; // ดึง URL ของรูปภาพ
                    $(editor).summernote('insertImage', imageUrl); // ใส่รูปภาพใน Summernote
                } else {
                    console.error("Image upload failed:", json.message);
                    alertError(json.message);
                }
            } catch (e) {
                console.error("JSON parse error:", e);
                alertError('Invalid response from server: ' + e.message);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error:", textStatus, errorThrown);
            alertError('AJAX request failed: ' + textStatus);
        }
    });
}

// ฟังก์ชันสำหรับแปลง Base64 เป็น File Object
function base64ToFile(base64, fileName) {
    if (!base64 || typeof base64 !== "string" || !base64.startsWith("data:")) {
        console.error("Invalid base64 input:", base64);
        return null;
    }
    const arr = base64.split(',');
    const mimeMatch = arr[0].match(/:(.*?);/);
    if (!mimeMatch) {
        console.error("Could not extract MIME type from base64 string:", arr[0]);
        return null;
    }
    const mime = mimeMatch[1];
    const bstr = atob(arr[1]);
    let n = bstr.length;
    const u8arr = new Uint8Array(n);
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new File([u8arr], fileName, { type: mime });
}

// ฟังก์ชันสำหรับตรวจสอบว่าเป็น URL ที่ถูกต้องหรือไม่
function isValidUrl(str) {
    var urlPattern = /^(http|https):\/\/[^\s/$.?#].[^\s]*$/i;
    return urlPattern.test(str) && !str.includes(" ");
}

// ฟังก์ชันแสดง alert error แบบ Swal
function alertError(textAlert) {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });
    Toast.fire({
        icon: "error",
        title: textAlert
    });
}

// $(document).ready(function () {
//     // Setup Summernote for "add shop" page
//     if ($('#summernote').length) {
//         $('#summernote').summernote({
//             placeholder: 'เขียนเนื้อหาเกี่ยวกับร้านค้าของคุณที่นี่...',
//             tabsize: 2,
//             height: 600,
//             callbacks: {
//                 onImageUpload: function(files) {
//                     uploadFile(files[0], '#summernote');
//                 }
//             }
//         });
//     }

//     // Setup Summernote for "edit shop" page
//     if ($('#summernote_update').length) {
//         $('#summernote_update').summernote({
//             placeholder: 'เขียนเนื้อหาเกี่ยวกับร้านค้าของคุณที่นี่...',
//             tabsize: 2,
//             height: 600,
//             callbacks: {
//                 onImageUpload: function(files) {
//                     uploadFile(files[0], '#summernote_update');
//                 }
//             }
//         });
//     }

//     // Event listener for the Add Shop button
//     $("#submitAddshop").on("click", function (event) {
//         event.preventDefault();
//         let subGroupVal = $('#sub_group_select').val();
//         let mainGroupVal = $('#main_group_select').val();
//         let groupId = subGroupVal ? subGroupVal : mainGroupVal;
//         var formshop = $("#formshop")[0];
//         var formData = new FormData(formshop);
//         formData.append("action", "addshop");
//         formData.set('group_id', groupId);

//         var contentFromEditor = $("#summernote").summernote('code');
//         var checkIsUrl = false;
//         var finalContent = '';
//         if (contentFromEditor) {
//             var tempDiv = document.createElement("div");
//             tempDiv.innerHTML = contentFromEditor;
//             var imgTags = tempDiv.getElementsByTagName("img");
//             for (var i = 0; i < imgTags.length; i++) {
//                 var imgSrc = imgTags[i].getAttribute("src");
//                 var filename = imgTags[i].getAttribute("data-filename");
//                 if (!imgSrc) {
//                     continue;
//                 }
//                 imgSrc = imgSrc.replace(/ /g, "%20");
//                 if (imgSrc.startsWith("data:image")) {
//                     var file = base64ToFile(imgSrc, filename || `image_${Date.now()}.png`);
//                     if (file) {
//                         formData.append("image_files[]", file);
//                         imgTags[i].setAttribute("src", "");
//                     }
//                 } else if (isValidUrl(imgSrc)) {
//                     checkIsUrl = true;
//                 }
//             }
//             finalContent = tempDiv.innerHTML;
//             formData.set("shop_content", finalContent);
//         }

//         // Form Validation
//         $(".is-invalid").removeClass("is-invalid");
//         if (!formData.get('fileInput') || formData.get('fileInput').name === '') {
//             alertError("Please add a cover photo.");
//             return;
//         }
//         if (!$("#shop_subject").val().trim()) {
//             $("#shop_subject").addClass("is-invalid");
//             alertError("Please fill in the subject.");
//             return;
//         }
//         if (!$("#shop_description").val().trim()) {
//             $("#shop_description").addClass("is-invalid");
//             alertError("Please fill in the description.");
//             return;
//         }
//         if (!finalContent.trim()) {
//             alertError("Please fill in content information.");
//             return;
//         }
//         if (!groupId) {
//             alertError("Please select a group.");
//             $('#main_group_select').addClass("is-invalid");
//             $('#sub_group_select').addClass("is-invalid");
//             return;
//         }

//         Swal.fire({
//             title: checkIsUrl ? "Image detection system from other websites?" : "Are you sure?",
//             text: "Do you want to add shop?",
//             icon: "warning",
//             showCancelButton: true,
//             confirmButtonColor: "#4CAF50",
//             cancelButtonColor: "#d33",
//             confirmButtonText: "Accept"
//         }).then((result) => {
//             if (result.isConfirmed) {
//                 $('#loading-overlay').fadeIn();
//                 $.ajax({
//                     url: "actions/process_shop.php",
//                     type: "POST",
//                     data: formData,
//                     processData: false,
//                     contentType: false,
//                     success: function (response) {
//                         try {
//                             var json = (typeof response === "string") ? JSON.parse(response) : response;
//                             if (json.status === 'success') {
//                                 Swal.fire('Success', json.message, 'success').then(() => {
//                                     window.location.reload();
//                                 });
//                             } else {
//                                 Swal.fire('Error', json.message || 'Unknown error', 'error');
//                             }
//                         } catch (e) {
//                             Swal.fire('Error', 'Invalid response from server: ' + e.message, 'error');
//                         }
//                     },
//                     error: function (xhr, status, error) {
//                         Swal.fire('Error', 'AJAX request failed: ' + xhr.status + ' ' + xhr.statusText, 'error');
//                     },
//                     complete: function () {
//                         $('#loading-overlay').fadeOut();
//                     }
//                 });
//             } else {
//                 $('#loading-overlay').fadeOut();
//             }
//         });
//     });

//     // Event listener for the Edit Shop button
//     $("#submitEditshop").on("click", function (event) {
//         event.preventDefault();
//         let subGroupVal = $('#sub_group_select').val();
//         let mainGroupVal = $('#main_group_select').val();
//         let groupId = subGroupVal ? subGroupVal : mainGroupVal;
//         let formData = new FormData(document.getElementById('formshop_edit'));
//         formData.append('action', 'editshop');
//         formData.set('group_id', groupId);

//         var contentFromEditor = $("#summernote_update").summernote('code');
//         var checkIsUrl = false;
//         var finalContent = '';
//         if (contentFromEditor) {
//             var tempDiv = document.createElement("div");
//             tempDiv.innerHTML = contentFromEditor;
//             var imgTags = tempDiv.getElementsByTagName("img");
//             for (var i = 0; i < imgTags.length; i++) {
//                 var imgSrc = imgTags[i].getAttribute("src");
//                 var filename = imgTags[i].getAttribute("data-filename");
//                 if (!imgSrc) {
//                     continue;
//                 }
//                 imgSrc = imgSrc.replace(/ /g, "%20");
//                 if (imgSrc.startsWith("data:image")) {
//                     var file = base64ToFile(imgSrc, filename || `image_${Date.now()}.png`);
//                     if (file) {
//                         formData.append("image_files[]", file);
//                         imgTags[i].setAttribute("src", "");
//                     }
//                 } else if (isValidUrl(imgSrc)) {
//                     checkIsUrl = true;
//                 }
//             }
//             finalContent = tempDiv.innerHTML;
//             formData.set("shop_content", finalContent);
//         }

//         // Form Validation
//         $(".is-invalid").removeClass("is-invalid");
//         if (!$("#shop_subject").val().trim()) {
//             $("#shop_subject").addClass("is-invalid");
//             alertError("Please fill in the subject.");
//             return;
//         }
//         if (!$("#shop_description").val().trim()) {
//             $("#shop_description").addClass("is-invalid");
//             alertError("Please fill in the description.");
//             return;
//         }
//         if (!finalContent.trim()) {
//             alertError("Please fill in content information.");
//             return;
//         }
//         if (!groupId) {
//             alertError("Please select a group.");
//             $('#main_group_select').addClass("is-invalid");
//             $('#sub_group_select').addClass("is-invalid");
//             return;
//         }
//         formData.set("shop_subject", $("#shop_subject").val());
//         formData.set("shop_description", $("#shop_description").val());

//         Swal.fire({
//             title: checkIsUrl ? "Image detection system from other websites?" : "Are you sure?",
//             text: "Do you want to edit shop?",
//             icon: "warning",
//             showCancelButton: true,
//             confirmButtonColor: "#4CAF50",
//             cancelButtonColor: "#d33",
//             confirmButtonText: "Accept"
//         }).then((result) => {
//             if (result.isConfirmed) {
//                 $('#loading-overlay').fadeIn();
//                 $.ajax({
//                     url: "actions/process_shop.php",
//                     type: "POST",
//                     dataType:"json",
//                     data: formData,
//                     processData: false,
//                     contentType: false,
//                     success: function (response) {
//                         try {
//                             var json = (typeof response === "string") ? JSON.parse(response) : response;
//                             if (json.status === 'success') {
//                                 // เปลี่ยนแปลงตรงนี้
//                                 Swal.fire('Success', json.message, 'success').then(() => {
//                                     location.reload();
//                                 });
//                             } else {
//                                 Swal.fire('Error', json.message || 'Unknown error', 'error');
//                             }
//                         } catch (e) {
//                             Swal.fire('Error', 'Invalid response from server: ' + e.message, 'error');
//                         }
//                     },
//                     error: function (xhr, status, error) {
//                         Swal.fire('Error', 'AJAX request failed: ' + xhr.status + ' ' + xhr.statusText, 'error');
//                     },
//                     complete: function () {
//                         $('#loading-overlay').fadeOut();
//                     }
//                 });
//             } else {
//                 $('#loading-overlay').fadeOut();
//             }
//         });
//     });

//     // Event listener for the "Back" button
//     $("#backToShopList").on("click", function () {
//         window.location.href = "list_shop.php";
//     });

//     // Helper function for redirection
//     function reDirect(url, data) {
//         var form = $('<form>', {
//             method: 'POST',
//             action: url,
//             target: '_self'
//         });
//         $.each(data, function(key, value) {
//             $('<input>', {
//                 type: 'hidden',
//                 name: key,
//                 value: value
//             }).appendTo(form);
//         });
//         $('body').append(form);
//         form.submit();
//     }
// });