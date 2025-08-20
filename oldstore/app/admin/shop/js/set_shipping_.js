var table_setShipping;
$(document).ready(function() {

    setupModal("myModal-setup-shipping", "myBtn-setup-shipping", "modal-close-setup-shipping");

    table_setShipping = new DataTable('#tb_setShipping', {
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
                url: "actions/process_setup_shipping.php",
                method: 'POST',
                dataType: 'json',
                data: function(d) {
                    d.action = 'getData_shipping';
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
                render: function ( data, type, row, meta ) {
                    
                    return meta.row + 1;
                    
                }
            },
            {
                "target": 1,
                data: null,
                render: function ( data, type, row ) {

                    return `<div>${data.vehicle_type}</div>`;
                }
            },
            {
                "target": 2,
                data: null,
                render: function ( data, type, row ) {
                    
                    var htmlCapacityLabel = `<div>${data.capacity} กิโลกรัม</div>`;

                    return htmlCapacityLabel;
                }
            },
            {
                "target": 3,
                data: null,
                render: function ( data, type, row ) {
                    

                    return `<div>${data.price}</div>`;
                }
            },
            {
                "target": 4,
                data: null,
                render: function ( data, type, row ) {

                    // var level = '';
                    // if(data.parent_id > 0){
                    //     level = data.parent_id;
                    // }else{
                    //     level = '#';
                    // }
                    
                    // return `<div>${level}</div>`;
                    
                }
            },
            {
                "target": 5,
                data: null,
                render: function ( data, type, row ) {
                    
                    // return '';
                    
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

            // const headers = [
            //     // "No.",
            //     "Icon",
            //     "Menu name",
            //     "Path",
            //     "Level",
            //     "Sort",
            //     "Action"
            // ];
    
            // applyResponsiveTableStyles('tb_gameScrollNum', headers);
        },
        rowCallback: function (row, data, index) {
            
            // if(data.parent_id == '' || data.parent_id == null){

            //     $(row).addClass('ui-state-disabled');

            // }
            //     $(row).addClass('ui-state-default');
            
            
        }
    });

});
