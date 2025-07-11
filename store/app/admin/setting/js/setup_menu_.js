var table_gameScrollNum;
$(document).ready(function(){

    setupModal("myModal-setup-menu", "myBtn-setup-menu", "modal-close-setup-menu");

    var isSubmitting = false;

    // Function to validate a form
    function validateForm(form) {
        var isValid = true;
        form.find('input, select, textarea').each(function() {
            if ($(this).prop('required') || $(this).attr('type') === 'radio') {
                if ($(this).attr('type') === 'radio') {
                    if (!$('input[name="' + $(this).attr('name') + '"]:checked').length) {
                        isValid = false;
                        return false;
                    }
                } else if ($(this).val().trim() === '') {
                    isValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            }
        });
        return isValid;
    }
    
    // Function to submit forms via AJAX
    function submitForms(form, url, type) {
        if (isSubmitting) return;
    
        isSubmitting = true;
    
        var formData1 = form.serialize();
    
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                action: 'save_set_memu',
                menuData: formData1,
                type
            },
            dataType: 'json',
            success: function(response) {
                
                if (response.status == "success") {

                    table_gameScrollNum.ajax.reload();
                    
                    $('#formSetMenu')[0].reset();
                    $('.modal-close-setup-menu').click();
                    $('#menu_main').val(null).trigger('change');

                } else {
                    console.error('error no product.');
                }

            },
            error: function() {

                console.error('An error occurred.');

            },
            complete: function() {
                isSubmitting = false;
            }
        });
    }
    
    // Event handler for #submitFormSetMenu
    $('#submitFormSetMenu').on('click', function(event) {
        event.preventDefault();
    
        var form1 = $('#formSetMenu');
    
        if (validateForm(form1)) {
            submitForms(form1, 'actions/process_setup_menu.php', 1);
        } else {
            isSubmitting = false;
        }
    });

    $('.iconPicker').iconpicker({
        icons: ['fab', 'fas', 'far'], 
        iconset: 'fontawesome5',
        selectedClass: 'btn-link',
        unselectedClass: 'btn-light'
        
    });
    
    $('.iconPicker').on('change', function(e) {
        const iconClass = e.icon; 
        const iconTag = `<i class="${iconClass}"></i>`;
        $('#set_icon').val(iconTag);
    });

    table_gameScrollNum = new DataTable('#tb_gameScrollNum', {
        "autoWidth": false,
        "language":{
            "decimal":        "",
            "emptyTable":     "No data available in table",
            "infoEmpty":      "Showing 0 to 0 of 0 entries",
            "infoFiltered":   "(filtered from MAX total entries)",
            "infoPostFix":    "",
            "thousands":      ",",
            "loadingRecords": "Loading...",
            "search":         "Search:",
            "zeroRecords":    "No matching records found",
            // "paginate": {
            //     "first":      "First",
            //     "last":       "Last",
            //     "next":       "Next",
            //     "previous":   "Previous"
            // },
            "aria": {
                "orderable":  "Order by this column",
                "orderableReverse": "Reverse order this column"
            }
        },
        "processing": true,
        "serverSide": true,
            ajax: {
                url: "actions/process_setup_menu.php",
                method: 'POST',
                dataType: 'json',
                data: function(d) {
                    d.action = 'getData_menu';
                    // d.filter_date = $('#filter_date').val();
                    // d.customParam2 = "value2";
                },
                dataSrc: function(json) {
                    return json.data;
                }
            },
        "ordering": false,
        "pageLength": 25, 
        "lengthMenu": [10, 25, 50, 100],
        columnDefs: [
            // {
            //     "target": 0,
            //     data: null,
            //     render: function ( data, type, row, meta) {
            //         return meta.row + 1;
            //     }
            // },
            {
                "target": 0,
                data: null,
                render: function ( data, type, row ) {
                    
                    return `<div>${data.menu_id}</div>`;
                    
                }
            },
            {
                "target": 1,
                data: null,
                render: function ( data, type, row ) {
                    return `<div>${data.menu_icon}</div>`;
                }
            },
            {
                "target": 2,
                data: null,
                render: function ( data, type, row ) {

                    var htmlMenuLabel = `<div>${data.menu_label}</div>`;

                    return `<div>${htmlMenuLabel}</div>`;
                }
            },
            {
                "target": 3,
                data: null,
                render: function ( data, type, row ) {

                    var htmlMenuLink = `<div>${data.menu_link ? data.menu_link : ''}</div>`;

                    return `<div>${htmlMenuLink}</div>`;
                }
            },
            {
                "target": 4,
                data: null,
                render: function ( data, type, row ) {

                    var level = '';
                    if(data.parent_id > 0){
                        level = data.parent_id;
                    }else{
                        level = '#';
                    }
                    
                    return `<div>${level}</div>`;
                    
                }
            },
            {
                "target": 5,
                data: null,
                render: function ( data, type, row ) {
                    
                    return '';
                    
                }
            }
            
        ],
        drawCallback: function (settings) {

            // $("#tb_gameScrollNum").sortable({ 
            //     items: "tr",
            //     cursor: "move",
            //     zIndex: 9999,
            //     containment: "parent",
            //     start: function(event, ui) {

            //         ui.item.addClass("dragging");
            //     },
            //     stop: function( event, ui ) {
            //         ui.item.removeClass("dragging");
            //     }
            // });

            // $("#tb_gameScrollNum thead").disableSelection();
            
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
        initComplete: function(settings, json) {
            const headers = [
                // "No.",
                "Icon",
                "Menu name",
                "Path",
                "Level",
                "Sort",
                "Action"
            ];
    
            applyResponsiveTableStyles('tb_gameScrollNum', headers);
        },
        rowCallback: function (row, data, index) {

            // if(data.parent_id == '' || data.parent_id == null){

            //     $(row).addClass('ui-state-disabled');

            // }
            //     $(row).addClass('ui-state-default');
            
            
        }
    });

    $('#menu_main').select2({
        ajax: {
            url: 'actions/process_setup_menu.php',
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

    // $('#menu_main').val(null).trigger('change');

});

function applyResponsiveTableStyles(tableId, headers) {
    const style = document.createElement('style');
    style.type = 'text/css';
    let cssContent = `
        @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
            #${tableId}, #${tableId} thead, #${tableId} tbody, #${tableId} th, #${tableId} td, #${tableId} tr {
                display: block;
            }

            #${tableId} thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            #${tableId} tr {
                margin: 0 0 1rem 0;
            }

            #${tableId} td {
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50%;
            }

            #${tableId} td:before {
                position: absolute;
                top: 0;
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
            }
    `;

    // #${tableId} tr:nth-child(odd) {
    //     background: #ffffff;
    // }

    headers.forEach((header, index) => {
        cssContent += `
            #${tableId} td:nth-of-type(${index + 1}):before { content: "${header}"; font-weight: 700; }
        `;
    });

    cssContent += ` }`;

    style.innerHTML = cssContent;
    document.head.appendChild(style);
}

