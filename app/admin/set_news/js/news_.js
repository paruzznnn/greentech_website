$(document).ready(function () {

    $("#summernote").summernote({
        height: 400,
        minHeight: 200,
        maxHeight: 500,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['fontname', 'fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview']],
            ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter']]
        ],
        fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Georgia', 'Times New Roman', 'Verdana', 'sans-serif'], // เพิ่ม sans-serif
        fontsize: ['8', '10', '12', '14', '16', '18', '24', '36'],
        callbacks: {

        }
    });

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

});


function base64ToFile(base64, fileName) {

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
    
    var byteString = atob(base64.split(",")[1]);

    var arrayBuffer = new ArrayBuffer(byteString.length);
    var uint8Array = new Uint8Array(arrayBuffer);

    for (var i = 0; i < byteString.length; i++) {
        uint8Array[i] = byteString.charCodeAt(i);
    }

    var blob = new Blob([uint8Array], { type: mimeType });

    var file = new File([blob], fileName, { type: mimeType });

    return file;
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

$("#submitAddNews").on("click", function (event) {
    event.preventDefault();

    var formNews = $("#formNews")[0];
    var formData = new FormData(formNews);
    formData.append("action", "addNews");
    var newsContent = formData.get("news_content");

    if (newsContent) {
        var tempDiv = document.createElement("div");
        tempDiv.innerHTML = newsContent;
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
        formData.set("news_content", tempDiv.innerHTML);
    }

    $(".is-invalid").removeClass("is-invalid");
    for (var tag of formData.entries()) {

        if (tag[0] === 'fileInput[]' && tag[1].name === '') {
            alertError("Please add a cover photo.");
            return;
        }
        if (tag[0] === 'news_subject' && tag[1].trim() === '') {
            $("#news_subject").addClass("is-invalid");
            return;
        }
        if (tag[0] === 'news_description' && tag[1].trim() === '') {
            $("#news_description").addClass("is-invalid");
            return;
        }
        if (tag[0] === 'news_content' && tag[1].trim() === '') {
            alertError("Please fill in content information.");
            return;
        }
    }

    if (checkIsUrl) {

        Swal.fire({
            title: "Image detection system from other websites?",
            text: "Do you want to add news.!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#4CAF50",
            cancelButtonColor: "#d33",
            confirmButtonText: "Accept"
        }).then((result) => {

            if (result.isConfirmed) {

                $('#loading-overlay').fadeIn();

                $.ajax({
                    url: "actions/process_news.php",
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

            }else{
                $('#loading-overlay').fadeOut();
            }
            

        });


    } else {

        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to add news.!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#4CAF50",
            cancelButtonColor: "#d33",
            confirmButtonText: "Accept"
        }).then((result) => {

            if (result.isConfirmed) {

                $('#loading-overlay').fadeIn();

                $.ajax({
                    url: "actions/process_news.php",
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

            }else{
                $('#loading-overlay').fadeOut();
            }

        });

    }

});


$("#submitEditNews").on("click", function (event) {
    event.preventDefault();

    var formNews = $("#formNews_edit")[0];
    var formData = new FormData(formNews);
    formData.append("action", "editNews");
    var newsContent = formData.get("news_content");

    if (newsContent) {
        var tempDiv = document.createElement("div");
        tempDiv.innerHTML = newsContent;
        var imgTags = tempDiv.getElementsByTagName("img");
        for (var i = 0; i < imgTags.length; i++) {
            var imgSrc = imgTags[i].getAttribute("src").replace(/ /g, "%20");
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
        formData.set("news_content", tempDiv.innerHTML);
    }

    $(".is-invalid").removeClass("is-invalid");
    for (var tag of formData.entries()) {

        // if (tag[0] === 'fileInput[]' && tag[1].name === '') {
        //     alertError("Please add a cover photo.");
        //     return;
        // }
        if (tag[0] === 'news_subject' && tag[1].trim() === '') {
            $("#news_subject").addClass("is-invalid");
            return;
        }
        if (tag[0] === 'news_description' && tag[1].trim() === '') {
            $("#news_description").addClass("is-invalid");
            return;
        }
        if (tag[0] === 'news_content' && tag[1].trim() === '') {
            alertError("Please fill in content information.");
            return;
        }
    }

    if (checkIsUrl) {

        Swal.fire({
            title: "Image detection system from other websites?",
            text: "Do you want to add news.!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#4CAF50",
            cancelButtonColor: "#d33",
            confirmButtonText: "Accept"
        }).then((result) => {

            if (result.isConfirmed) {

                $('#loading-overlay').fadeIn();

                $.ajax({
                    url: "actions/process_news.php",
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

            }else{
                $('#loading-overlay').fadeOut();
            }
            

        });


    } else {

        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to add news.!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#4CAF50",
            cancelButtonColor: "#d33",
            confirmButtonText: "Accept"
        }).then((result) => {

            if (result.isConfirmed) {

                $('#loading-overlay').fadeIn();

                $.ajax({
                    url: "actions/process_news.php",
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

            }else{
                $('#loading-overlay').fadeOut();
            }

        });

    }

});
