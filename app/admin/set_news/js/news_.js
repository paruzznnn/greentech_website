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
        // สร้าง DOM ชั่วคราวเพื่อแปลง news_content เป็น HTML
        var tempDiv = document.createElement("div");
        tempDiv.innerHTML = newsContent;

        // ดึง tag <img> และแก้ไขค่า src
        var imgTags = tempDiv.getElementsByTagName("img");
        for (var i = 0; i < imgTags.length; i++) {
            var imgSrc = imgTags[i].getAttribute("src");
            var filename = imgTags[i].getAttribute("data-filename");

            var file = base64ToFile(imgSrc, filename);
            if (file) {
                formData.append("image_files[]", file);
            }

            // ตรวจสอบว่าเป็น Base64 หรือไม่
            if (imgSrc.startsWith("data:image")) {
                // ถ้าคือ Base64 ให้ลบ Base64 ออก
                imgTags[i].setAttribute("src", "");
                // หรือใช้ URL ที่ต้องการแทน
            }
        }

        // อัปเดตเนื้อหาใน formData หลังจากลบ Base64
        formData.set("news_content", tempDiv.innerHTML);
    }

    // ตรวจสอบค่าใน formData
    // for (var pair of formData.entries()) {
    //     console.log(pair[0] + ': ' + pair[1]);
    // }

    $.ajax({
        url: "actions/process_news.php",
        type: "POST",
        data: formData,
        processData: false, // ป้องกัน jQuery ไม่ให้แปลงข้อมูล formData เป็น string
        contentType: false, // ป้องกันการตั้งค่า Content-Type โดยอัตโนมัติ
        success: function (response) {
            console.log("response", response);

            // if(response.status == 'success'){

            // }
        },
        error: function (error) {
            console.log("error", error);
        },
    });
});
