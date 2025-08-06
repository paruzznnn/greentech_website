$(document).ready(function () {

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

    var td_list_Blog = new DataTable('#td_list_Blog', {
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
            url: "actions/process_blog.php",
            method: 'POST',
            dataType: 'json',
            data: function (d) {
                d.action = 'getData_blog';
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
                data: null,
                render: function (data, type, row) {
                    return data.subject_blog;
                }
            },
            {
                "target": 2,
                data: null,
                render: function (data, type, row) {
                    return data.date_create;
                }
            },
            {
                "target": 3,
                data: null,
                render: function (data, type, row) {
                    let divBtn = `<div class="d-flex">`;
                    divBtn += `<span style="margin: 2px;"><button type="button" class="btn-circle btn-edit"><i class="fas fa-pencil-alt"></i></button></span>`;
                    divBtn += `<span style="margin: 2px;"><button type="button" class="btn-circle btn-del"><i class="fas fa-trash-alt"></i></button></span>`;
                    divBtn += `</div>`;
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
        },
        rowCallback: function (row, data, index) {
            var editButton = $(row).find('.btn-edit');
            var deleteButton = $(row).find('.btn-del');

            editButton.off('click').on('click', function () {
                reDirect('edit_blog.php', {
                    Blog_id: data.Blog_id
                });
            });

            deleteButton.off('click').on('click', function () {
                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you want to delete the blog?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#4CAF50",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Accept"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#loading-overlay').fadeIn();
                        $.ajax({
                            url: 'actions/process_blog.php',
                            type: 'POST',
                            data: {
                                action: 'delblog',
                                id: data.Blog_id,
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

function isValidUrl(str) {
    var urlPattern = /^(http|https):\/\/[^\s/$.?#].[^\s]*$/i;
    return urlPattern.test(str) && !str.includes(" ");
}

$("#submitAddblog").on("click", function (event) {
    event.preventDefault();

    var formblog = $("#formblog")[0];
    var formData = new FormData(formblog);
    formData.append("action", "addblog");
    var blogContent = formData.get("Blog_content");

    // ดึงค่าจาก Select2 และเพิ่มลงใน FormData
    var relatedprojects = $("#related_projects_add").val();
    if (relatedprojects && relatedprojects.length > 0) {
        for (var i = 0; i < relatedprojects.length; i++) {
            formData.append("related_projects[]", relatedprojects[i]);
        }
    }

    if (blogContent) {
        var tempDiv = document.createElement("div");
        tempDiv.innerHTML = blogContent;
        var imgTags = tempDiv.getElementsByTagName("img");
        for (var i = 0; i < imgTags.length; i++) {
            var imgSrc = imgTags[i].getAttribute("src");
            var filename = imgTags[i].getAttribute("data-filename");

            var checkIsUrl = false;
            let isUrl = isValidUrl(imgSrc);
            if (!isUrl) {
                var file = base64ToFile(imgSrc, filename);
                if (file) {
                    formData.append("image_files[]", file);
                }
                if (imgSrc.startsWith("data:image")) {
                    imgTags[i].setAttribute("src", "");
                }
            } else {
                checkIsUrl = true;
            }
        }
        formData.set("Blog_content", tempDiv.innerHTML);
    }

    $(".is-invalid").removeClass("is-invalid");
    for (var tag of formData.entries()) {
        if (tag[0] === 'fileInput[]' && tag[1].name === '') {
            alertError("Please add a cover photo.");
            return;
        }
        if (tag[0] === 'Blog_subject' && tag[1].trim() === '') {
            $("#Blog_subject").addClass("is-invalid");
            return;
        }
        if (tag[0] === 'Blog_description' && tag[1].trim() === '') {
            $("#Blog_description").addClass("is-invalid");
            return;
        }
        if (tag[0] === 'Blog_content' && tag[1].trim() === '') {
            alertError("Please fill in content information.");
            return;
        }
    }

    if (checkIsUrl) {
        Swal.fire({
            title: "Image detection system from other websites?",
            text: "Do you want to add blog.!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#4CAF50",
            cancelButtonColor: "#d33",
            confirmButtonText: "Accept"
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loading-overlay').fadeIn();
                $.ajax({
                    url: "actions/process_blog.php",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.status == 'success') {
                            window.location.reload();
                        }
                    },
                    error: function (error) {
                        console.log("error", error);
                    },
                });
            } else {
                $('#loading-overlay').fadeOut();
            }
        });
    } else {
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to add blog.!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#4CAF50",
            cancelButtonColor: "#d33",
            confirmButtonText: "Accept"
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loading-overlay').fadeIn();
                $.ajax({
                    url: "actions/process_blog.php",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.status == 'success') {
                            window.location.reload();
                        }
                    },
                    error: function (error) {
                        console.log("error", error);
                    },
                });
            } else {
                $('#loading-overlay').fadeOut();
            }
        });
    }
});


$("#submitEditblog").on("click", function (event) {
    event.preventDefault();
    var formblog = $("#formBlog_edit")[0];
    var formData = new FormData(formblog);
    formData.set("action", "editblog");
    formData.set("Blog_id", $("#Blog_id").val());
    var contentFromEditor = $("#summernote_update").summernote('code');
    var checkIsUrl = false;
    var finalContent = '';
    
    // ดึงค่าจาก Select2 และเพิ่มลงใน FormData
    var relatedprojects = $("#related_projects_edit").val();
    if (relatedprojects && relatedprojects.length > 0) {
        for (var i = 0; i < relatedprojects.length; i++) {
            formData.append("related_projects[]", relatedprojects[i]);
        }
    }

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
            if (!isValidUrl(imgSrc)) {
                var file = base64ToFile(imgSrc, filename);
                if (file) {
                    formData.append("image_files[]", file);
                } else {
                    console.warn(`⚠️ Failed to convert base64 to file for img[${i}]`);
                }
                if (imgSrc.startsWith("data:image")) {
                    imgTags[i].setAttribute("src", "");
                }
            } else {
                checkIsUrl = true;
            }
        }
        finalContent = tempDiv.innerHTML;
        formData.set("Blog_content", finalContent);
    } else {
        console.warn("⚠️ contentFromEditor is empty");
    }

    $(".is-invalid").removeClass("is-invalid");
    if (!$("#Blog_subject").val().trim()) {
        $("#Blog_subject").addClass("is-invalid");
        console.error("❌ Validation failed: Blog_subject is empty");
        return;
    }
    if (!$("#Blog_description").val().trim()) {
        $("#Blog_description").addClass("is-invalid");
        console.error("❌ Validation failed: Blog_description is empty");
        return;
    }
    if (!finalContent.trim()) {
        alertError("Please fill in content information.");
        console.error("❌ Validation failed: Blog_content is empty");
        return;
    }
    formData.set("Blog_subject", $("#Blog_subject").val());
    formData.set("Blog_description", $("#Blog_description").val());

    Swal.fire({
        title: checkIsUrl ? "Image detection system from other websites?" : "Are you sure?",
        text: "Do you want to edit blog?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#4CAF50",
        cancelButtonColor: "#d33",
        confirmButtonText: "Accept"
    }).then((result) => {
        if (result.isConfirmed) {
            $('#loading-overlay').fadeIn();
            $.ajax({
                url: "actions/process_blog.php",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    try {
                        var json = (typeof response === "string") ? JSON.parse(response) : response;
                        if (json.status === 'success') {
                             location.reload();
                        } else {
                            Swal.fire('Error', json.message || 'Unknown error', 'error');
                        }
                    } catch (e) {
                        console.error("❌ JSON parse error:", e);
                        Swal.fire('Error', 'Invalid response from server', 'error');
                    }
                },
                error: function (xhr) {
                    console.error("❌ AJAX error:", xhr.responseText);
                    Swal.fire('Error', 'AJAX request failed', 'error');
                    $('#loading-overlay').fadeOut();
                },
            });
        } else {
            $('#loading-overlay').fadeOut();
        }
    });
});

$("#backToprojectList").on("click", function () {
    window.location.href = "list_blog.php";
});

function reDirect(url, data) {
    var form = $('<form>', {
        method: 'POST',
        action: url,
        target: '_self'
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