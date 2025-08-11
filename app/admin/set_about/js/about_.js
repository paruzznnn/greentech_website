$(document).ready(function () {
    // Summernote Initialization
    $('.summernote').summernote({
        height: 250,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['codeview']]
        ],
        fontNames: ['Arial', 'Kanit', 'Roboto', 'Tahoma', 'Impact', 'Courier New'],
        fontSizes: ['8', '10', '12', '14', '16', '18', '24', '36', '48', '64'],
        fontNamesIgnoreCheck: ['Kanit', 'Roboto'],
        callbacks: {
            onImageUpload: function(files, editor, welEditable) {
                var file = files[0];
                var data = new FormData();
                data.append('action', 'upload_image');
                data.append('image_file', file);
                
                $.ajax({
                    url: 'action/process_about.php', 
                    method: 'POST',
                    data: data,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if(response.status === 'success') {
                            var imgNode = $('<img>').attr('src', response.url);
                            $(welEditable).summernote('insertNode', imgNode[0]);
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("Error uploading image:", textStatus, errorThrown);
                        Swal.fire('Error', "Failed to upload image.", 'error');
                    }
                });
            }
        }
    });

    // Handle form submission for ADDING new content (ใช้ AJAX)
    $('#addAboutForm').on('submit', function (e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        formData.append('action', 'add_new_block'); // กำหนด action สำหรับเพิ่มเนื้อหา
        
        Swal.fire({
            title: 'ยืนยันการเพิ่มเนื้อหา?',
            text: 'คุณต้องการเพิ่มบล็อคเนื้อหาใหม่ใช่หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ใช่, เพิ่มเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loading-overlay').fadeIn();
                $.ajax({
                    url: 'action/process_about.php', // ส่งไปที่ไฟล์จัดการ
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (response) {
                        $('#loading-overlay').fadeOut();
                        Swal.fire({
                            title: response.status === 'success' ? 'เพิ่มสำเร็จ!' : 'ผิดพลาด',
                            icon: response.status,
                            text: response.message
                        }).then(() => {
                            if (response.status === 'success') {
                                location.reload();
                            }
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $('#loading-overlay').fadeOut();
                        Swal.fire('Error', 'เกิดข้อผิดพลาดในการเพิ่มเนื้อหา', 'error');
                    }
                });
            }
        });
    });

    // Handle form submission for EDITING all content (ใช้ AJAX)
    $('#editAboutForm').on('submit', function (e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append('action', 'save_all_blocks'); // กำหนด action สำหรับบันทึกทั้งหมด

        Swal.fire({
            title: 'ยืนยันการบันทึก?',
            text: 'คุณต้องการบันทึกการแก้ไขทั้งหมดใช่หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ใช่, บันทึกเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loading-overlay').fadeIn();
                $.ajax({
                    url: 'action/process_about.php', // ส่งไปที่ไฟล์จัดการ
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (response) {
                        $('#loading-overlay').fadeOut();
                        Swal.fire({
                            title: response.status === 'success' ? 'บันทึกสำเร็จ!' : 'ผิดพลาด',
                            icon: response.status,
                            text: response.message
                        }).then(() => {
                            if (response.status === 'success') {
                                location.reload();
                            }
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $('#loading-overlay').fadeOut();
                        Swal.fire('Error', 'เกิดข้อผิดพลาดในการบันทึก', 'error');
                    }
                });
            }
        });
    });

    // Handle block deletion
    $(document).on('click', '.remove-block', function () {
        const $button = $(this);
        const blockId = $button.data('id');

        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: 'คุณต้องการลบบล็อคนี้จริงหรือ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loading-overlay').fadeIn();
                $.post('delete_about_block.php', { id: blockId }, function (response) {
                    $('#loading-overlay').fadeOut();
                    Swal.fire({
                        title: response.success ? 'ลบแล้ว!' : 'ผิดพลาด',
                        icon: response.success ? 'success' : 'error',
                        text: response.message,
                        timer: 1000,
                        showConfirmButton: false
                    });
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                }, 'json');
            }
        });
    });
});