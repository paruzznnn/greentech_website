$(document).ready(function () {
    $("#summernote").summernote({
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['fontname', 'fontsize']], 
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture']],
            ['view', ['fullscreen', 'codeview']],
            ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter']]
        ],
        fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Georgia', 'Times New Roman', 'Verdana', 'sans-serif'], // เพิ่ม sans-serif
        fontsize: ['8', '10', '12', '14', '16', '18', '24', '36'], 
        callbacks: {
            
        }
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
            var file = base64ToFile(imgSrc, filename);
            if (file) {
                formData.append("image_files[]", file);
            }
            if (imgSrc.startsWith("data:image")) {
                imgTags[i].setAttribute("src", "");
            }
        }
        formData.set("news_content", tempDiv.innerHTML);
    }

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
                    if(response.status == 'success'){
                        window.location.reload();
                    }
                },
                error: function (error) {
                    console.log("error", error);
                },
            });

        } else if (result.isDenied) {
            $('#loading-overlay').fadeOut();
        }

    });


});
