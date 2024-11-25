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
            !$(event.target).closest('#iconPickerMenu').length && 
            !$(event.target).is('#target_iconPickerMenu')         
        ) {
            $('#iconPickerMenu').addClass('d-none');            
        }
    });
    
    $('#target_iconPickerMenu').on('click', function (event) {
        event.stopPropagation(); 
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
                let divMenuId = `
                ${data.menu_id}
                <input type="text" id="" class="old_set_menu_id form-control hidden" value="${data.menu_id}">
                `;

                return divMenuId;
            }
        },
        {
            "target": 1,
            "orderable": false,
            data: null,
            render: function (data, type, row) {

                let divIcon = `
                <span class="showOldIcon">${data.menu_icon}</span>
                <input type="text" id="" class="old_set_icon form-control hidden" value="${data.spc_icon}">
                `;

                return divIcon;
            }
        },
        {
            "target": 2,
            "orderable": false,
            data: null,
            render: function (data, type, row) {
                return '<input type="text" id="" class="old_set_menu_name form-control" value="' + data.menu_label + '" disabled>';
            }
        },
        {
            "target": 3,
            "orderable": false,
            data: null,
            render: function (data, type, row) {
                if (data.parent_id > 0) {
                    return `<select id="old_menu_main${data.menu_id}" class="old_set_menu_main form-select" disabled></select>`;
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
                    return '<input type="text" id="" class="old_set_menu_path form-control" value="' + data.menu_link + '" disabled>';
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
                                <button type="button" class="btn-circle btn-save hidden">
                                    <i class="fas fa-save"></i>
                                </button>
                            </span>
                        `;

                        divBtn += `
                            <span style="margin: 2px;">
                                <button type="button" class="btn-circle btn-edit">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            </span>
                        `;

                        divBtn += `
                        <span class="box-icon-picker" style="margin: 2px;">
                            <div id="" class="iconMenu d-none"></div>
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

        
            // $('.btn-icon').on('iconpickerSelected', function (event) {
            //     console.log('Icon selected: ' + event.iconpickerValue);
            // });

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

            var iconButton = $(row).find('.iconMenu');
            var showIconMn = $(row).find('.showOldIcon');

            var editButton = $(row).find('.btn-edit');
            var deleteButton = $(row).find('.btn-del');
            var saveButton = $(row).find('.btn-save');

            var inputMenuId = $(row).find('input.old_set_menu_id');
            var inputIcon = $(row).find('input.old_set_icon');
            var inputMenuName = $(row).find('input.old_set_menu_name');
            var inputMenuPath = $(row).find('input.old_set_menu_path');

            var selectMenuMain = $(row).find('select.old_set_menu_main');

            iconButton.iconpicker({
                icons: ['fab', 'fas', 'far'],
                iconset: 'fontawesome5',
                selectedClass: 'btn-link',
                unselectedClass: 'btn-light'
        
            });

            iconButton.on('change', function (e) {
                const iconClass = e.icon;
                const iconTag = `<i class="${iconClass}"></i>`;
                inputIcon.val(iconTag);
                showIconMn.html(iconTag); 
            });

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

            saveButton.off('click').on('click', function() {

                var menuData = {
                    action: 'saveUpdateMenu',
                    menu_id: inputMenuId.val() ?? '',
                    icon: inputIcon.val() ?? '',
                    menu_name: inputMenuName.val() ?? '',
                    menu_path: inputMenuPath.val() ?? '',
                    menu_main: selectMenuMain.val() ?? ''
                };

                changeStatusMenu(menuData, 'Do you want to change the information?');

            });


            editButton.off('click').on('click', function() {
                // First, close any previously opened edit fields and buttons
                $(row).siblings().find('.iconMenu').addClass("d-none");  // Hide the icon buttons of other rows
                $(row).siblings().find('.btn-save').addClass("hidden");  // Hide the save buttons of other rows
                $(row).siblings().find('input.old_set_menu_name').prop('disabled', true);  // Disable the input fields of other rows
                $(row).siblings().find('input.old_set_menu_path').prop('disabled', true);
                $(row).siblings().find('select.old_set_menu_main').prop('disabled', true);
            
                // Now toggle the current row's edit fields and buttons
                iconButton.toggleClass("d-none");
                saveButton.toggleClass("hidden");
                inputMenuName.prop('disabled', !inputMenuName.prop('disabled'));
                inputMenuPath.prop('disabled', !inputMenuPath.prop('disabled'));
                selectMenuMain.prop('disabled', !selectMenuMain.prop('disabled'));
            });
            

            deleteButton.off('click').on('click', function() {
                
                var menuData = {
                    action: 'delMenu',
                    menu_id: inputMenuId.val() ?? ''
                };

                changeStatusMenu(menuData, 'Do you want to delete the data?');
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

function changeStatusMenu(obj, smstext) {

    Swal.fire({
        title: "Are you sure?",
        text: smstext,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#4CAF50",
        cancelButtonColor: "#d33",
        confirmButtonText: "Accept"
    }).then((result) => {

        if (result.isConfirmed) {

            $('#loading-overlay').fadeIn();

            $.ajax({
                url: 'actions/process_menu.php',
                type: 'POST',
                data: obj,
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });

        } else {
            $('#loading-overlay').fadeOut();
        }

    });

}


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


