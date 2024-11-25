$(document).ready(function () {

    $("#submitAddMenu").prop('hidden', true);
    $("#target_iconPickerMenu").prop('hidden', true);

    $('#iconPickerMenu').iconpicker({
        icons: ['fab', 'fas', 'far'],
        iconset: 'fontawesome5',
        selectedClass: 'btn-link',
        unselectedClass: 'btn-light'

    });

    $('#iconPickerMenu').on('change', function (e) {
        const iconClass = e.icon;
        const iconTag = `<i class="${iconClass}"></i>`;
        $('#set_icon').val(iconTag);
        // $('#showIcon').toggleClass(iconClass);
        $('#showIcon').removeClass().addClass(iconClass);

    });

    $(document).on('click', function (event) {
        if (
            !$(event.target).closest('#iconPickerMenu').length && // ถ้าไม่ได้คลิกที่เมนู
            !$(event.target).is('#target_iconPickerMenu')         // และไม่ได้คลิกที่ปุ่มเปิดเมนู
        ) {
            $('#iconPickerMenu').addClass('d-none');             // ซ่อนเมนู
        }
    });
    
    $('#target_iconPickerMenu').on('click', function (event) {
        event.stopPropagation(); // ป้องกัน event bubble เพื่อไม่ให้ trigger document click
        $('#iconPickerMenu').toggleClass('d-none');
    });

    // $("#summernote").summernote({
    //     height: 400,
    //     minHeight: 200,
    //     maxHeight: 500,
    //     toolbar: [
    //         ['style', ['bold', 'italic', 'underline', 'clear']],
    //         ['font', ['fontname', 'fontsize']],
    //         ['para', ['ul', 'ol', 'paragraph']],
    //         ['insert', ['link', 'picture', 'video']],
    //         ['view', ['fullscreen', 'codeview']],
    //         ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter']]
    //     ],
    //     fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Georgia', 'Times New Roman', 'Verdana', 'sans-serif'], // เพิ่ม sans-serif
    //     fontsize: ['8', '10', '12', '14', '16', '18', '24', '36'],
    //     callbacks: {

    //     }
    // });

    // var readURL = function (input) {
    //     if (input.files && input.files[0]) {
    //         var reader = new FileReader();
    //         reader.onload = function (e) {
    //             let previewImage = $('#previewImage');
    //             previewImage.attr('src', e.target.result);
    //             previewImage.css('display', 'block');
    //         }
    //         reader.readAsDataURL(input.files[0]);
    //     }
    // }

    // $("#fileInput").on('change', function () {
    //     readURL(this);
    // });


    var tb_list_menu = new DataTable('#tb_list_menu', {
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
            // "paginate": {
            //     "first":      "First",
            //     "last":       "Last",
            //     "next":       "Next",
            //     "previous":   "Previous"
            // },
            "aria": {
                "orderable": "Order by this column",
                "orderableReverse": "Reverse order this column"
            }
        },
        "processing": true,
        "serverSide": true,
        ajax: {
            url: "actions/process_menu.php",
            method: 'POST',
            dataType: 'json',
            data: function (d) {
                d.action = 'getData_menu';
                // d.filter_date = $('#filter_date').val();
                // d.customParam2 = "value2";
            },
            dataSrc: function (json) {
                
                // let combinedData = [...json.data, ...json.data2];
                // return combinedData;
                return json.data;
            }
        },
        "ordering": true,
        "pageLength": 25,
        "lengthMenu": [10, 25, 50, 100],
        columnDefs: [{
            "target": 0,
            "orderable": true,
            data: null,
            render: function (data, type, row, meta) {
                // return meta.row + 1;
                return data.menu_id;
            }
        },
        {
            "target": 1,
            "orderable": false,
            data: null,
            render: function (data, type, row) {
                return data.menu_icon;
            }
        },
        {
            "target": 2,
            "orderable": false,
            data: null,
            render: function (data, type, row) {
                return '<input type="text" id="" name="" class="form-control" value="' + data.menu_label + '">';
            }
        },
        {
            "target": 3,
            "orderable": false,
            data: null,
            render: function (data, type, row) {
                if (data.parent_id > 0) {
                    return `<select id="old_menu_main${data.menu_id}" class="form-select"></select>`;
                } else {
                    return '<h6>is main</h6>';
                }
            }
        },
        {
            "target": 4,
            "orderable": false,
            data: null,
            render: function (data, type, row) {
                if(data.parent_id > 0){
                    return '<input type="text" id="" name="" class="form-control" value="' + data.menu_link + '">';
                }else{
                    return '';
                }
                
            }
        },
        {
            "target": 5,
            "orderable": false,
            data: null,
            render: function (data, type, row) {
                return data.menu_order;
            }
        },
        {
            "target": 6,
            "orderable": false,
            data: null,
            render: function (data, type, row) {

                let divBtn = '';
                Object.entries(data.arrPermiss).forEach(([key, value]) => {

                    if (value.includes(2)) {
                        $("#submitAddMenu").prop('hidden', false);
                        $("#target_iconPickerMenu").prop('hidden', false);
                    }

                    if (value.includes(3)) {

                        divBtn += `
                            <span style="margin: 2px;">
                                <button type="button" class="btn-circle btn-save">
                                    <i class="fas fa-save"></i>
                                </button>
                            </span>
                        `;
                    }
                
                    if (value.includes(3)) {
                        divBtn += `
                            <span style="margin: 2px;">
                                <button type="button" class="btn-circle btn-edit">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            </span>
                        `;
                    }
                
                    if (value.includes(4)) {
                        divBtn += `
                            <span style="margin: 2px;">
                                <button type="button" class="btn-circle btn-del">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </span>
                        `;
                    }
                });
                
                    return `<div class="d-flex">${divBtn}</div>`;
            
            }
        },
        ],
        drawCallback: function (settings) {

            var targetDivTable = $('div.dt-layout-row.dt-layout-table');
            if (targetDivTable.length) {
                targetDivTable.addClass('tables-over');
                // targetDivTable.css({
                //     'display': 'block',
                //     'width': '100%'
                // });
            }

            var targetDivRow = $('dt-container dt-layout-row dt-empty-footer');
            if (targetDivRow.length) {
                targetDivRow.css({
                    'width': '50%'
                });
            }
        },
        initComplete: function (settings, json) {

            // const headers = [
            //     "No.",
            //     "Date created",
            //     "Subject",
            //     "Date on-air",
            //     "Status",
            //     ""
            // ];

            // cssResponsiveTable('td_list_menu', headers);
        },
        rowCallback: function (row, data, index) {

            $(row).find('#old_menu_main' + data.menu_id).select2({
                ajax: {
                    url: 'actions/process_menu.php',
                    dataType: 'json',
                    type: 'POST',
                    data: function (params) {
                        return {
                            search: params.term,
                            page: params.page || 1,
                            action: 'getMainMenu'
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 10) < data.total_count
                            }
                        };
                    },
                    cache: true
                },
                placeholder: 'Select an option',
                width: '100%'
            });

        },
        createdRow: function (row, data, dataIndex) {

            $(row).attr('data-id', data.menu_id);
            // $(row).attr('data-order', data.menu_order);


            // $(row).find('#old_menu_main' + data.menu_id).select2({
            //     ajax: {
            //         url: 'actions/process_menu.php',
            //         dataType: 'json',
            //         type: 'POST',
            //         data: function (params) {
            //             return {
            //                 search: params.term,
            //                 page: params.page || 1,
            //                 action: 'getMainMenu'
            //             };
            //         },
            //         processResults: function (data, params) {
            //             params.page = params.page || 1;
            //             return {
            //                 results: data.items,
            //                 pagination: {
            //                     more: (params.page * 10) < data.total_count
            //                 }
            //             };
            //         },
            //         cache: true
            //     },
            //     placeholder: 'Select an option',
            //     width: '100%'
            // }).on('select2:open', function () {

            //     const selectElement = $(this);
            //     const parentId = data.parent_id;

            //     $.ajax({
            //         url: 'actions/process_menu.php',
            //         type: 'POST',
            //         dataType: 'json',
            //         data: {
            //             action: 'getMainMenu',
            //             search: '',
            //             page: 1
            //         },
            //         success: function (response) {

            //             const matchedItem = response.items.find(item => item.id === parentId);
            //             if (matchedItem) {

            //                 const option = new Option(matchedItem.text, matchedItem.id, true, true);
            //                 selectElement.append(option).trigger('change');
            //             }
            //         }
            //     });
            // }).trigger('change');

            // $(row).find('#old_menu_main' + data.menu_id).val(data.parent_id).trigger('change');


            $(row).find('#old_menu_main' + data.menu_id).select2().each(function () {
                const selectElement = $(this);
                const parentId = data.parent_id;
            
                $.ajax({
                    url: 'actions/process_menu.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'getMainMenu',
                        search: '',
                        page: 1
                    },
                    success: function (response) {
                        const matchedItem = response.items.find(item => item.id === parentId);
                        if (matchedItem) {
                            const option = new Option(matchedItem.text, matchedItem.id, true, true);
                            selectElement.append(option).trigger('change');
                        }
                    }
                });
            });
        
        }
    });

    $("#tb_list_menu tbody").sortable({
        helper: fixHelper,
        update: function (event, ui) {
            var sortedData = [];

            $("#tb_list_menu tbody tr").each(function (index) {
                var dataId = $(this).data('id');
                // var dataOrder = $(this).data('order');
                var newOrder = index + 1;

                if (dataId) {
                    sortedData.push({
                        id: dataId,
                        // order: dataOrder,
                        newOrder: newOrder
                    });
                }

            });

            $.ajax({
                url: 'actions/process_menu.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'upDateSortMenu',
                    menuArray: sortedData
                },
                success: function(response) {

                    if(response.status == 'success'){
                        alertSuccess(response.message);
                    }else{
                        alertError(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('เกิดข้อผิดพลาด:', error);
                }
            });
            

        }
    }).disableSelection();

    function fixHelper(e, ui) {
        ui.children().each(function () {
            $(this).width($(this).width());
        });
        return ui;
    }


    $('#set_menu_main').select2({
        ajax: {
            url: 'actions/process_menu.php',
            dataType: 'json',
            type: 'POST',
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    action: 'getMainMenu'
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.items, // ชื่อคีย์ต้องตรงกับ PHP
                    pagination: {
                        more: (params.page * 10) < data.total_count
                    }
                };
            },
            cache: true
        },
        placeholder: 'Select an option',
        width: '100%'
    });


});


// function base64ToFile(base64, fileName) {

//     var fileExtension = fileName.split(".").pop().toLowerCase();

//     var mimeType;
//     switch (fileExtension) {
//         case "jpg":
//         case "jpeg":
//             mimeType = "image/jpeg";
//             break;
//         case "png":
//             mimeType = "image/png";
//             break;
//         case "gif":
//             mimeType = "image/gif";
//             break;
//         case "pdf":
//             mimeType = "application/pdf";
//             break;
//         case "txt":
//             mimeType = "text/plain";
//             break;

//         default:
//             mimeType = "application/octet-stream";
//     }

//     var byteString = atob(base64.split(",")[1]);

//     var arrayBuffer = new ArrayBuffer(byteString.length);
//     var uint8Array = new Uint8Array(arrayBuffer);

//     for (var i = 0; i < byteString.length; i++) {
//         uint8Array[i] = byteString.charCodeAt(i);
//     }

//     var blob = new Blob([uint8Array], { type: mimeType });

//     var file = new File([blob], fileName, { type: mimeType });

//     return file;
// }

function alertSuccess(textAlert) {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });

    Toast.fire({
        icon: "success",
        title: textAlert
    }).then(() => {
        window.location.reload();
        // $(".is-invalid").removeClass("is-invalid");
    });
}

function alertError(textAlert) {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });

    Toast.fire({
        icon: "error",
        title: textAlert
    }).then(() => {
        $(".is-invalid").removeClass("is-invalid");
    });
}


// function isValidUrl(str) {
//     var urlPattern = /^(http|https):\/\/[^\s/$.?#].[^\s]*$/i;
//     return urlPattern.test(str) && !str.includes(" ");
// }

$("#submitAddMenu").on("click", function (event) {
    event.preventDefault(); // ป้องกันการส่งฟอร์ม

    // ดึงค่าจาก input แต่ละช่อง
    let set_icon = $('#set_icon').val();
    let set_menu_name = $('#set_menu_name').val();
    let set_menu_main = $('#set_menu_main').val();
    let set_menu_path = $('#set_menu_path').val();

    // ลบ class is-invalid ก่อนทำการตรวจสอบ
    $(".is-invalid").removeClass("is-invalid");

    // ตรวจสอบค่าที่กรอก
    // if (!set_icon) {
    //     alertError("Please add a cover photo.");
    //     $('#set_icon').addClass("is-invalid");
    //     return;
    // }
    if (!set_menu_name) {
        alertError("Please fill in the menu name.");
        $('#set_menu_name').addClass("is-invalid");
        return;
    }
    if (!set_menu_main) {
        alertError("Please select the main menu.");
        $('#set_menu_main').addClass("is-invalid");
        return;
    }
    if (!set_menu_path) {
        alertError("Please provide the menu path.");
        $('#set_menu_path').addClass("is-invalid");
        return;
    }

    let formData = {
        action: 'saveMenu',
        set_icon: set_icon,
        set_menu_name: set_menu_name,
        set_menu_main: set_menu_main,
        set_menu_path: set_menu_path,
    };


    $.ajax({
        url: 'actions/process_menu.php', 
        type: 'POST',
        data: formData,
        success: function (response) {
            if(response.status == 'success'){
                alertSuccess(response.message);
            }else{
                alertError(response.message);
            }
        },
        error: function (error) {
            console.log("Error:", error);
            alertError("An error occurred. Please try again.");
        }
    });
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

            } else {
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

            } else {
                $('#loading-overlay').fadeOut();
            }

        });

    }

});


function reDirect(url, data) {
    var form = $('<form>', {
        method: 'POST',
        action: url,
        target: '_blank'
    });
    $.each(data, function (key, value) {
        $('<input>', {
            type: 'hidden',
            name: key,
            value: value
        }).appendTo(form);
    });
    $('body').append(form);
    form.submit();
}


// var editButton = $(row).find('.btn-edit');
// var deleteButton = $(row).find('.btn-del');

// editButton.off('click').on('click', function() {
//     // reDirect('edit_news.php', data.news_id);
//     reDirect('edit_news.php', {
//         news_id: data.news_id
//     });
// });

// deleteButton.off('click').on('click', function() {

//     Swal.fire({
//         title: "Are you sure?",
//         text: "Do you want to delete the news?",
//         icon: "warning",
//         showCancelButton: true,
//         confirmButtonColor: "#4CAF50",
//         cancelButtonColor: "#d33",
//         confirmButtonText: "Accept"
//     }).then((result) => {

//         if (result.isConfirmed) {

//             $('#loading-overlay').fadeIn();

//             $.ajax({
//                 url: 'actions/process_role.php',
//                 type: 'POST',
//                 data: {
//                     action: 'delNews',
//                     id: data.news_id,
//                 },
//                 dataType: 'json',
//                 success: function(response) {
//                     if (response.status == 'success') {
//                         window.location.reload();
//                     }
//                 },
//                 error: function(xhr, status, error) {
//                     console.error('Error:', error);
//                 }
//             });

//         } else {
//             $('#loading-overlay').fadeOut();
//         }

//     });

// });

