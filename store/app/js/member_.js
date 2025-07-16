var isSubmitting = false;
$(document).ready(function() {

    setupModal("myModal-shipping", "myBtn-shipping", "modal-close-shipping");
    setupModal("myModal-purchase", "myBtn-purchase", "modal-close-purchase");

    $("#myBtn-shipping").on('click', function(){

       // เรียกใช้ initMap
        initMap('googleMap', 'searchInput', 'inputLatitude', 'inputLongitude', null, null, true);
    });

    var urlParamsTab = new URLSearchParams(window.location.search);
    var getTab = urlParamsTab.get('tab');
    
    // สร้าง map ระหว่างค่าของ getTab กับ id ของ tab pane
    var tabMap = {
        'profile': '#member-profile',
        'shipping': '#member-shipping',
        'pay': '#member-pay',
        'track': '#member-track'
    };
    
    // ตรวจสอบว่า getTab มีค่าที่ตรงกับ key ใน tabMap หรือไม่
    if (tabMap[getTab]) {
        var targetTab = tabMap[getTab];
    
        // แสดง tab ที่ต้องการ
        $('.nav-link[data-target="' + targetTab + '"]').tab('show');
        $('#member-tabContent .tab-pane').removeClass('show active');
        $(targetTab).addClass('show active');
    
        // ถ้า getTab เป็น 'pay' ให้เรียกฟังก์ชัน buildMemberOrderHistory()
        switch (getTab) {
            case 'profile':
                    buildMemberProfile();
                break;
            case 'shipping':
                    buildMemberShipment();
                break;
            case 'pay':
                    buildMemberOrderHitory();
                break;
            case 'track':
                    buildMemberTrack();
                break;
        
            default:
                break;
        }

        // อัปเดต URL โดยลบ query string
        var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.pushState({path: newUrl}, '', newUrl);
    }

    $('.nav-link[data-target="#member-profile"]').on('click', function(event) {
        buildMemberProfile();
    });

    $('.nav-link[data-target="#member-shipping"]').on('click', function(event) {
        buildMemberShipment();
    });

    $('.nav-link[data-target="#member-pay"]').on('click', function(event) {
        buildMemberOrderHitory();
    });

    $('.nav-link[data-target="#member-track"]').on('click', function(event) {
        buildMemberTrack();
    });

    $('#member-tabContent .tab-pane').each(function() {
        if ($(this).hasClass('active')) {
            var activeTab = $(this).attr('id');
            if(activeTab == 'member-profile'){
                buildMemberProfile();
            }
            
        }
    });

    $('#member-tab button').on('click', function() {
        // Remove 'active' class from all buttons
        $('#member-tab button').removeClass('active');
        $(this).addClass('active');
        var target = $(this).data('target');

        // Hide all tab panes
        $('#member-tabContent .tab-pane').removeClass('show active');
        // Show the target tab pane
        $(target).addClass('show active');
    });

    $('#submitAddShipping').on('click', function(event) {
        event.preventDefault(); 

        var form1 = $('#formShipment');

        if (validateForm(form1)) {
            submitForms(form1, 'actions/member_process.php', 1);
        } else {
            isSubmitting = false;
        }
    });

    $("#submitEvidence").click(function(event) {
        event.preventDefault(); 
        var form = $('#uploadForm_evidence')[0];
        submitFormWithFiles(form, 'actions/process_pay.php');
    });

    $("#input-b").fileinput({
        showUpload: false,
        dropZoneEnabled: false,
        maxFileCount: 10,
        inputGroupClass: "input-group-sm",
        // showCaption: false,
        // previewZoomButtonIcons: {
        //     fullscreen: '',   // ปิดไอคอน fullscreen
        //     borderless: '',   // ปิดไอคอน borderless
        //     close: '',        // ปิดไอคอน close
        // },
        // previewZoomButtonClasses: {
        //     fullscreen: '',   // ปิดคลาส fullscreen
        //     borderless: '',   // ปิดคลาส borderless
        //     close: '',        // ปิดคลาส close
        // },
    }).on('filezoomshown', function(event, params) {
        $('.kv-zoom-title').hide();  // ซ่อนชื่อไฟล์เมื่อแสดง preview
        $('.modal-backdrop').css('z-index', 'auto'); 
    });


//close ready
});


// Function to validate a form
function validateForm(form) {
    var isValid = true;
    form.find('input, select, textarea').each(function() {
        if ($(this).prop('required') || $(this).attr('type') === 'radio') {
            if ($(this).attr('type') === 'radio') {
                if (!$('input[name="' + $(this).attr('name') + '"]:checked').length) {
                    isValid = false;
                    alert('โปรดเลือกวิธีการชำระเงิน');
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
function submitForms(form1, url, type) {
    if (isSubmitting) return;

    isSubmitting = true;

    var formData1 = form1.serialize();

    var actionName;
    var dataArray = {};

    switch (type) {
        case 1:
            actionName = 'add_shipment';
            dataArray = { shipping: formData1 };
            break;
        // case 2:
        //     actionName = 'save_member';
        //     dataArray = { member: formData1 };
        //     break;
        default:
            break;
    }


    $.ajax({
        url: url,
        type: 'POST',
        data: {
            action: actionName,
            ...dataArray,
            type
        },
        dataType: 'json',
        success: function(response) {

            if(response.status == "success"){

                    // location.reload();
                    window.location.href = 'member.php?tab=shipping';
            
            }else{
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

function submitFormWithFiles(form, url) {
    var formData = new FormData(form); 

    var checkEvidence = $('#input-b').val();

    if(checkEvidence){
        formData.append('att_file', 'save_attach_file');
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                
                if(response.status == "success") {
                    window.location.href = 'member.php?tab=pay';
                }
                
            },
            error: function() {
                alert("เกิดข้อผิดพลาดในการเชื่อมต่อ.");
            }
        });
    }else{
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
        title: "กรุณาแนบไฟล์"
        });
    }
    
}

const setFormFieldsReadOnly = (Id, type) => {

    if(type == 'shipment'){

        $(`#formShipment_${Id} input, #formShipment_${Id} select, #formShipment_${Id} textarea`).each(function() {

            if (this.tagName.toLowerCase() === 'input') {
                $(this).attr('disabled', true);
            } else if (this.tagName.toLowerCase() === 'textarea') {
                $(this).attr('disabled', true);
            } else if (this.tagName.toLowerCase() === 'select') {
                // $(this).attr('disabled', true);
            }

        });

    }

};

const setupEventHandlers = (id, type) => {

    if(type == 'shipmemt'){

        $('.toggleSwitch input[type="checkbox"]').on('change', function(e) {
            e.preventDefault();
            const dataValue = $(this).is(':checked') ? 1 : 0;
            const dataID = $(this).data('key');
            reDataShipment(dataValue, dataID, 'active');
        });

        $('.remove-shipment').on('click', function(e) {
            e.preventDefault();
            const dataID = $(this).data('key');
            reDataShipment(null, dataID, 'remove');
        });

        $('.edit-shipment').on('click', function(e) {
            e.preventDefault();
            const dataID = $(this).data('key');
            $(`#formShipment_${dataID} input, #formShipment_${dataID} select, #formShipment_${dataID} textarea`).each(function() {
                if (this.tagName.toLowerCase() === 'input') {
                    $(this).attr('disabled', false);
                } else if (this.tagName.toLowerCase() === 'textarea') {
                    $(this).attr('disabled', false);
                } else if (this.tagName.toLowerCase() === 'select') {
                    $(this).attr('disabled', false);
                }
            });
        });

        $('.save-shipment').on('click', function(e) {
            e.preventDefault();
            var arrData = [];
            const dataID = $(this).data('key');
            $(`#formShipment_${dataID} input, #formShipment_${dataID} select, #formShipment_${dataID} textarea`).each(function() {
                if (this.tagName.toLowerCase() === 'input') {

                    const tagName = this.tagName.toLowerCase();
                    const name = $(this).attr('name');
                    const value = $(this).val();
                    
                    if (name) {
                        // Add the name-value pair to the array
                        arrData.push({ [name]: value });
                    }
                    
                } else if (this.tagName.toLowerCase() === 'textarea') {

                    const tagName = this.tagName.toLowerCase();
                    const name = $(this).attr('name');
                    const value = $(this).val();
                    
                    if (name) {
                        // Add the name-value pair to the array
                        arrData.push({ [name]: value });
                    }

                } else if (this.tagName.toLowerCase() === 'select') {

                    const tagName = this.tagName.toLowerCase();
                    const name = $(this).attr('name');
                    const value = $(this).val();
                    
                    if (name) {
                        // Add the name-value pair to the array
                        arrData.push({ [name]: value });
                    }


                }
            });

            reDataShipment(arrData, dataID, 'save');
        });

        $(`#formShipment_${id} input, #formShipment_${id} select, #formShipment_${id} textarea`).each(function() {
            if (this.tagName.toLowerCase() === 'input') {
            } else if (this.tagName.toLowerCase() === 'textarea') {
            } else if (this.tagName.toLowerCase() === 'select') {
                setFormAddress('TH', this.id, this.name, $(this).data('key'));
            }
        });


    }else if(type == 'order_buy'){

        $('.remove-orderBuy').on('click', function(e) {
            e.preventDefault();
            const dataID = $(this).data('key');
            reDataOrder(null, dataID, 'remove');
        });

        $('.print-orderBuy').on('click', function(e) {
            e.preventDefault();
            const dataID = $(this).data('key');
            reDataOrder(null, dataID, 'print');
        });


    }else if(type == 'track_ord'){

    }

};

const renderPaginationControls = (totalPages, currentPage, type) => {

    const buildPagination = (pageUrlFunction, elementHtml) => {

        let paginationHtml = '<nav aria-label="Page navigation"><ul class="pagination">';
        
        // "First" button
        if (currentPage > 1) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="${pageUrlFunction}(1)"><i class="fas fa-angle-double-left"></i></a></li>`;
        }
        
        // "Previous" button
        if (currentPage > 1) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="${pageUrlFunction}(${currentPage - 1})"><i class="fas fa-angle-left"></i></a></li>`;
        }

        if (totalPages <= 2) {
            // Show all pages if total pages is less than or equal to 2
            for (let i = 1; i <= totalPages; i++) {
                paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="${pageUrlFunction}(${i})">${i}</a></li>`;
            }
        } else {
            if (currentPage === 1) {
                // Show first two pages and "..." if current page is the first page
                paginationHtml += `<li class="page-item active"><a class="page-link" href="#" onclick="${pageUrlFunction}(1)">1</a></li>`;
                paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="${pageUrlFunction}(2)">2</a></li>`;
                paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            } else if (currentPage === totalPages) {
                // Show "..." and the last two pages if current page is the last page
                paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="${pageUrlFunction}(${totalPages - 1})">${totalPages - 1}</a></li>`;
                paginationHtml += `<li class="page-item active"><a class="page-link" href="#" onclick="${pageUrlFunction}(${totalPages})">${totalPages}</a></li>`;
            } else {
                // Show "..." before and after the current page
                paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                paginationHtml += `<li class="page-item active"><a class="page-link" href="#" onclick="${pageUrlFunction}(${currentPage})">${currentPage}</a></li>`;
                paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // "Next" button
        if (currentPage < totalPages) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="${pageUrlFunction}(${currentPage + 1})"><i class="fas fa-angle-right"></i></a></li>`;
        }

        // "Last" button
        if (currentPage < totalPages) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="${pageUrlFunction}(${totalPages})"><i class="fas fa-angle-double-right"></i></a></li>`;
        }

        paginationHtml += '</ul></nav>';

        $(elementHtml).html(paginationHtml);
    };

    switch (type) {
        case 'shipmemt':
            buildPagination('buildMemberShipment', '#paginationControls');
            break;
        case 'order_buy':
            buildPagination('buildMemberOrderHitory', '#paginationControls_order');
            break;
        case 'track_ord':
            buildPagination('buildMemberTrack', '#paginationControls_track');
            break;
        default:
            break;
    }
};

// build box
const buildMemberShipment = (page = 1, limit = 1) => {
    $.ajax({
        url: 'actions/member_getData.php',
        type: 'POST',
        data: {
            action: 'get_shipment',
            page: page,
            limit: limit
        },
        dataType: 'json',
        success: function(response) {
            
            if (Array.isArray(response.data)) {
                let dataMemberShipment = response.data;
                const totalItems = response.totalRecords;
                const totalPages = response.totalPages;

                // Calculate showing range
                const startItem = (page - 1) * limit + 1;
                const endItem = Math.min(page * limit, totalItems);

                if (dataMemberShipment.length > 0) {
                    let divShowShipment = '';

                    dataMemberShipment.forEach(shipment => {
                        divShowShipment += `
                        <div class="box-pay-detail" id="shipment_${shipment.address_id}">
                            <div style="text-align: end; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <label class="toggleSwitch nolabel">
                                        <input type="checkbox" data-key="${shipment.address_id}" ${(shipment.is_default > 0) ? 'checked' : ''}/>
                                        <span>
                                            <span data-key-lang="off" lang="US">off</span>
                                            <span data-key-lang="on" lang="US">on</span>
                                        </span>
                                        <a></a>
                                    </label>
                                </div>
                                <div>
                                    <button type="button" class="remove-btn remove-circle remove-shipment" data-key="${shipment.address_id}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <button type="button" class="edit-btn edit-circle edit-shipment" data-key="${shipment.address_id}">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <button type="button" class="save-btn save-circle save-shipment" data-key="${shipment.address_id}">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </div>
                            </div>

                            <form id="formShipment_${shipment.address_id}" class="formShipment_data">
                                <div class="row">

                                    <div class="col-12 col-md-2 p-1 d-flex flex-column">
                                        <span class="text-nowrap">
                                            <label data-key-lang="prefix" lang="US">คำนำหน้า</label> <span>*</span>
                                        </span>
                                        <select class="form-select" id="prefix_${shipment.address_id}" name="prefix" data-key="${shipment.prefix}" required></select>
                                    </div>
                                    <div class="col-12 col-md-5 p-1 d-flex flex-column">
                                        <span class="text-nowrap">
                                            <label data-key-lang="first_name" lang="US">First name</label> <span>*</span>
                                        </span>
                                        <input type="text" class="form-control" id="firstname_${shipment.address_id}" name="firstname" value="${shipment.firstname}" required>
                                    </div>
                                    <div class="col-12 col-md-5 p-1 d-flex flex-column">
                                        <span class="text-nowrap">
                                            <label data-key-lang="last_name" lang="US">Last name</label> <span>*</span>
                                        </span>
                                        <input type="text" class="form-control" id="lastname_${shipment.address_id}" name="lastname" value="${shipment.lastname}" required>
                                    </div>

                                </div>
                                <div class="row">

                                    <div class="col-12 col-md-4 p-1 d-flex flex-column">
                                        <span class="text-nowrap">
                                            <label data-key-lang="country" lang="US">ประเทศ</label> <span>*</span>
                                        </span>
                                        <select class="form-select" id="country_${shipment.address_id}" name="country" data-key="${shipment.country}" required></select>
                                    </div>
                                    <div class="col-12 col-md-4 p-1 d-flex flex-column">
                                        <span class="text-nowrap">
                                            <label data-key-lang="province" lang="US">จังหวัด</label> <span>*</span>
                                        </span>
                                        <select class="form-select" id="province_${shipment.address_id}" name="province" data-key="${shipment.province_id}" required></select>
                                    </div>
                                    <div class="col-12 col-md-4 p-1 d-flex flex-column">
                                        <span class="text-nowrap">
                                            <label data-key-lang="district" lang="US">เขต/อำเภอ</label>  <span>*</span>
                                        </span>
                                        <select class="form-select" id="district_${shipment.address_id}" name="district" data-key="${shipment.district_id}" required></select>
                                    </div>

                                </div>
                                
                                <div class="row">

                                    <div class="col-12 col-md-4 p-1 d-flex flex-column">
                                        <span class="text-nowrap">
                                            <label data-key-lang="subdistrict" lang="US">แขวง/ตำบล</label> <span>*</span>
                                        </span>
                                        <select class="form-select" id="subdistrict_${shipment.address_id}" name="subdistrict" data-key="${shipment.sub_district_id}" required></select>
                                    </div>
                                    <div class="col-12 col-md-4 p-1 d-flex flex-column">
                                        <span class="text-nowrap">
                                            <label data-key-lang="postal_code" lang="US">รหัสไปรษณีย์</label> <span>*</span>
                                        </span>
                                        <select class="form-select" id="post_code_${shipment.address_id}" name="post_code" data-key="${shipment.postcode_id}" required></select>
                                    </div>
                                    <div class="col-12 col-md-4 p-1 d-flex flex-column">
                                        <span class="text-nowrap">
                                            <label data-key-lang="phone_number" lang="US">เบอร์โทรศัพท์</label> <span>*</span>
                                        </span>
                                        <input type="text" class="form-control" id="phone_number_${shipment.address_id}" name="phone_number" value="${shipment.phone_number}" required>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-12 p-1 d-flex flex-column">
                                        <span class="text-nowrap">
                                            <label data-key-lang="address" lang="US">ที่อยู่</label> <span>*</span>
                                        </span>
                                        <textarea id="address_${shipment.address_id}" class="form-control" name="address" required>${shipment.detail}</textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-4 p-1 d-flex flex-column">
                                        <span class="text-nowrap">
                                            <label data-key-lang="company" lang="US">บริษัท</label>
                                        </span>
                                        <input type="text" class="form-control" id="comp_name_${shipment.address_id}" name="comp_name" value="${shipment.comp_name}">
                                    </div>

                                    <div class="col-12 col-md-8 p-1 d-flex flex-column">
                                        <span class="text-nowrap">
                                            <label data-key-lang="tax_id" lang="US">เลขประจําตัวผู้เสียภาษี</label> 
                                        </span>
                                        <input type="text" class="form-control" id="tax_number_${shipment.address_id}" name="tax_number" value="${shipment.tax_number}">
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-12 col-md-12 p-1 d-flex flex-column">
                                        <span class="text-nowrap">
                                            <label data-key-lang="map" lang="US">จุดสังเกต แผนที่</label> 
                                            <span><i class="fas fa-map-marker-alt"></i></span>
                                        </span>
                                        <input type="text" id="searchInput_${shipment.address_id}" class="form-control" placeholder="" >
                                    </div>

                                    <div class="col-12 col-md-6 p-1 d-flex flex-column">
                                        <span class="text-nowrap">
                                            <label data-key-lang="" lang="US">ละติจูด</label>
                                        </span>
                                        <input type="text" class="form-control" id="inputLatitude_${shipment.address_id}" name="inputLatitude" value="${shipment.latitude}" required>
                                    </div>

                                    <div class="col-12 col-md-6 p-1 d-flex flex-column">
                                        <span class="text-nowrap">
                                            <label data-key-lang="" lang="US">ลองจิจูด</label>
                                        </span>
                                        <input type="text" class="form-control" id="inputLongitude_${shipment.address_id}" name="inputLongitude" value="${shipment.longitude}" required>
                                    </div>

                                </div>

                                <div class="row">
                                    <div id="googleMap_${shipment.address_id}" class="myMapData" style="height: 300px; width: 100%;"></div>
                                </div>

                            </form>


                        </div>`;
                    });

                    $('#showMemberShipment').html(`
                        <div style="display: flex; justify-content: space-between;">
                            <div>Showing ${startItem} to ${endItem} of ${totalItems} Shipping</div>
                            <div id="paginationControls"></div>
                        </div>
                        ${divShowShipment}
                    `);

                    dataMemberShipment.forEach(shipment => {
                        setFormFieldsReadOnly(shipment.address_id, 'shipment');
                        setupEventHandlers(shipment.address_id, 'shipmemt');

                        var lat = parseFloat(shipment.latitude);
                        var lng = parseFloat(shipment.longitude);

                        
                            initMap(
                                'googleMap_' + shipment.address_id,
                                'searchInput_' + shipment.address_id,
                                'inputLatitude_' + shipment.address_id,
                                'inputLongitude_' + shipment.address_id,
                                lat,
                                lng,
                                true
                            );
                        

                        
                    });

                    renderPaginationControls(totalPages, page, 'shipmemt');
                    initializeLanguageSwitcher('#language-select', '[lang]', '../api/languages/langs.json');
                    
                } else {
                    $('#showMemberShipment').html('<p>No shipments found.</p>');
                }

            } else {
                console.error('Response is not an array:', response);
            }
        },
        error: function() {
            console.error('Error fetching data.');
        }
    });
};

// buildMemberShipment();

const reDataShipment = (dataValue = null, dataID = null, dataType = null) => {

    switch (dataType) {
        case 'active':

                reShipmentAction(dataValue, dataID, dataType);
            
            break;
        case 'save':

                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you want to save the data?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#4CAF50",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "ok",
                    cancelButtonText: "cancel"
                }).then((result) => {

                    if (result.isConfirmed) {
                        reShipmentAction(dataValue, dataID, dataType);
                    }else{
                    
                    }

                });
            
            break;
        case 'remove':

                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you want to delete the data?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#4CAF50",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "ok",
                    cancelButtonText: "cancel"
                }).then((result) => {

                    if (result.isConfirmed) {
                        reShipmentAction(dataValue, dataID, dataType);
                    }else{
                    
                    }

                });
            
            break;
        default:
            break;
    }
    
};

const reShipmentAction = (dataValue, dataID, dataType) => {

    $.ajax({
        url: 'actions/member_process.php',
        type: 'POST',
        data: {
            action: 're_shipment',
            dataID,
            dataValue,
            dataType
        },
        dataType: 'json',
        success: function(response) {

            if(response.status == 'success'){

                switch (response.message) {
                    case 'active':

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
                            title: "ตั้งค่าที่อยู่เริ่มต้น สำเร็จ !"
                        });
                        
                        break;
                    case 'save':
                        
                        break;
                    case 'remove':
                        // buildMemberShipment();
                        // location.reload();
                        window.location.href = 'member.php?tab=shipping';
                        break;
                    default:
                        break;
                }

            }
            

            

        },
        error: function() {
            console.error('An error .');
        }
    });
        

};

const buildMemberOrderHitory = (page = 1, limit = 1) =>{

    $.ajax({
        url: 'actions/member_getData.php',
        type: 'POST',
        data: {
            action: 'get_order',
            page: page,
            limit: limit
        },
        dataType: 'json',
        success: function(response) {

            if (Array.isArray(response.data)) {
                const dataMemberOrder = response.data;
                const totalItems = response.totalRecords;
                const totalPages = response.totalPages;

                const startItem = (page - 1) * limit + 1;
                const endItem = Math.min(page * limit, totalItems);

                if (dataMemberOrder.length > 0) {
                    let divShowOrderBuy = '';

                    dataMemberOrder.forEach(order => {
                        const productIds = order.product_ids?.split(',') || [];
                        const prices = order.prices?.split(',') || [];
                        const quantities = order.quantities?.split(',') || [];
                        const totalPrices = order.total_prices?.split(',') || [];
                        const keyOrder = order.order_keys?.split(',') || [];

                        let payChannel = '';
                        let transportChannel = '';
                        let statusHtml = '';

                        let totalQty = 0;
                        let totalAmount = 0;
                        const tmsPrice = parseFloat(order.vehicle_price) || 0;

                        // Payment Channel
                        switch (parseInt(order.pay_channel)) {
                            case 1:
                                payChannel = `
                                    <img src="../public/img/bankPay.png" style="width: 80%;">
                                    <label>บจก.แทรนดาร์ อินเตอร์เนชั่นแนล</label>
                                    <div>ธ.กรุงศรีอยุธยา 320-1-13702-8</div>`;
                                break;
                            case 2:
                                payChannel = `
                                    <img src="../public/img/prompt-pay-logo.png" style="width: 70%;">
                                    ${order.qr_pp || ''}
                                `;
                                break;
                        }

                        // Transport Type
                        switch (parseInt(order.type)) {
                            case 1:
                                transportChannel = `<i class="fas fa-truck"></i> ตามที่อยู่ที่กำหนด`;
                                break;
                            case 2:
                                transportChannel = `<i class="fas fa-people-carry"></i> รับหน้าร้าน`;
                                break;
                        }

                        // Order Status
                        switch (parseInt(order.is_status)) {
                            case 0:
                                statusHtml = `
                                    รอส่งหลักฐานการชำระเงิน
                                    <div>
                                        <button type="button" class="btn btn-link btn-PO-Order" data-order="${order.order_id}">
                                            <i class="fas fa-upload"></i>
                                        </button>
                                    </div>`;
                                break;
                            case 1:
                                statusHtml = `ส่งหลักฐานแล้วรอตรวจสอบ`;
                                break;
                            case 3:
                                statusHtml = `
                                    รอส่งหลักฐานการชำระเงินอีกครั้ง
                                    <div>
                                        <button type="button" class="btn btn-link btn-PO-Order" data-order="${order.order_id}">
                                            <i class="fas fa-upload"></i>
                                        </button>
                                    </div>`;
                                break;
                            default:
                                statusHtml = '';
                        }

                        // Calculate totals
                        productIds.forEach((_, index) => {
                            totalQty += parseInt(quantities[index]) || 0;
                            totalAmount += parseFloat(totalPrices[index]) || 0;
                        });

                        const vat = (totalAmount + tmsPrice) * 0.07;
                        const grandTotal = totalAmount + tmsPrice + vat;

                        // Generate order block
                        divShowOrderBuy += `
                            <div class="box-pay-detail" id="orderBuy_${order.order_id}">
                                <div class="text-end d-flex justify-content-end align-items-center">
                                    <div>
                                        <button type="button" class="print-btn print-circle print-orderBuy" data-key="${order.ids}">
                                            <i class="fas fa-print"></i>
                                        </button>
                                        <button type="button" class="remove-btn remove-circle remove-orderBuy" data-key="${order.order_id}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <form id="formOrderBuy_${order.order_id}" class="formOrderBuy_data">
                                    <div class="purchase overflow-auto">
                                        <header>
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <img src="../public/img/trandar_logo.png" class="img-responsive">
                                                </div>
                                                <div class="col-sm-9 company-details">
                                                    <div>Trandar International Co., Ltd.</div>
                                                    <div>102 Soi Pattanakarn 40, Pattanakarn Rd,</div>
                                                    <div>Suanluang, Bangkok 10250, Thailand</div>
                                                </div>
                                            </div>
                                        </header>

                                        <main>
                                            <div class="row my-2">
                                                <div class="col-sm-6">
                                                    <strong>ผู้รับ:</strong> ${order.fullname || 'N/A'}<br>
                                                    <strong>ที่อยู่:</strong> ${order.shipping || 'N/A'}<br>
                                                    <strong>เบอร์โทร:</strong> ${order.phone || 'N/A'}
                                                </div>
                                                <div class="col-sm-6">
                                                    <strong>วันที่:</strong> ${order.date_created}<br>
                                                    <strong>เลขที่สั่งซื้อ:</strong> ${order.order_codes}
                                                </div>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-bordered text-center">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>สินค้า</th>
                                                            <th>ราคา</th>
                                                            <th>ปริมาณ</th>
                                                            <th>ราคารวม</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        ${productIds.map((_, index) => `
                                                            <tr>
                                                                <td>${index + 1}</td>
                                                                <td>${keyOrder[index]}</td>
                                                                <td>${formatNumberWithComma(prices[index])}</td>
                                                                <td>${quantities[index]}</td>
                                                                <td>${formatNumberWithComma(totalPrices[index])}</td>
                                                            </tr>
                                                        `).join('')}
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th rowspan="8" colspan="2" style="width: 250px;" class="text-start">
                                                                ${payChannel}<br>
                                                                ${transportChannel}<br>
                                                                กรุณาชำระเงินภายใน 7 วัน
                                                            </th>
                                                            <th colspan="2">จำนวนรายการ</th>
                                                            <th>${productIds.length}</th>
                                                        </tr>
                                                        <tr><th colspan="2">จำนวนสินค้า</th><th>${totalQty}</th></tr>
                                                        <tr><th colspan="2">รวมเป็นเงิน</th><th>${formatNumberWithComma(totalAmount.toFixed(2))}</th></tr>
                                                        <tr><th colspan="2">ส่วนลด</th><th>0</th></tr>
                                                        <tr><th colspan="2">ค่าจัดส่ง</th><th>${formatNumberWithComma(tmsPrice.toFixed(2))}</th></tr>
                                                        <tr><th colspan="2">ภาษีมูลค่าเพิ่ม(7%)</th><th>${formatNumberWithComma(vat.toFixed(2))}</th></tr>
                                                        <tr><th colspan="2">จำนวนเงินทั้งสิ้น</th><th>${formatNumberWithComma(grandTotal.toFixed(2))}</th></tr>
                                                        <tr><th colspan="2"></th><th>สถานะ ${statusHtml}</th></tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </main>
                                    </div>
                                </form>
                            </div>
                        `;
                    });

                    // Inject HTML to page
                    $('#showMemberPurchase').html(`
                        <div class="d-flex justify-content-between mb-2">
                            <div>Showing ${startItem} to ${endItem} of ${totalItems} PurchaseHistory</div>
                            <div id="paginationControls_order"></div>
                        </div>
                        ${divShowOrderBuy}
                    `);

                    // Set up event handlers
                    $('.btn-PO-Order').click(function () {
                        $('#myBtn-purchase').click();
                    });

                    dataMemberOrder.forEach(orderBuy => {
                        setupEventHandlers(orderBuy.order_id, 'order_buy');
                    });

                    renderPaginationControls(totalPages, page, 'order_buy');
                } else {
                    $('#showMemberPurchase').html('<p>No PurchaseHistory found.</p>');
                }
            } else {
                console.error('Response is not an array:', response);
            }

        },
        error: function() {
            console.error('Error fetching data.');
        }
    });

};

// buildMemberOrderHitory();
const generateRandomCode = (length = 8) => {
    return Math.random().toString(36).substring(2, 2 + length).toUpperCase();
}

const reDataOrder = (dataValue = null, dataID = null, dataType = null) => {

    let actionName = '';
    let urlPath = '';
    
    switch (dataType) {
        case 'remove':
            actionName = 're_orderBuy';
            urlPath = 'actions/member_process.php';
            break;
        case 'print':
            actionName = 'print_orderBuy';
            const randomCode = generateRandomCode();
            urlPath = `member_pdf.php?dataID=${dataID}&code=${randomCode}`;
            window.open(urlPath, '_blank');
            return; 
        default:
            return;
    }

    $.ajax({
        url: urlPath,
        type: 'POST',
        data: {
            action: actionName,
            dataID,
            dataValue
        },
        dataType: 'json',
        success: function(response) {
            
            if(response.status == "success") {

                const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 1000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                },
                willClose: () => {
                    window.location.href = 'member.php?tab=pay';
                }
                });
                
                Toast.fire({
                icon: "success",
                title: "ยกเลิกคำสั่งซื้อเรียบร้อย"
                });

            }

        },
        error: function() {
            console.error('An error occurred.');
        }
    });

};

const buildMemberProfile = () =>{

    $.ajax({
        url: 'actions/member_getData.php',
        type: 'POST',
        data: {
            action: 'get_member',
        },
        dataType: 'json',
        success: function(response) {

            const member_firstname = response.data[0].firstname ?? '';
            const member_lastname = response.data[0].lastname ?? '';
            const member_email = response.data[0].email ?? '';
            const member_phone = response.data[0].phone ?? '';
            const member_pic = response.data[0].file_path ?? 'http://ssl.gstatic.com/accounts/ui/avatar_2x.png';
    
            let divShowProfile = '';
    
            divShowProfile += `
            <div class="container">
            <form id="formProfile" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="text-center">
                            <img src="actions/${member_pic}" class="avatar img-circle img-thumbnail" alt="avatar">
                        </div>
                        <br>
                        <input type="file" class="form-control file-upload" name="profile_image">
    
                    </div>
    
                    <div class="col-sm-8">
                        <hr>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name"><strong data-key-lang="first_name" lang="US">First name</strong></label>
                                    <input type="text" class="form-control" name="first_name" id="first_name" value="${member_firstname}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name"><strong data-key-lang="last_name" lang="US">Last name</strong></label>
                                    <input type="text" class="form-control" name="last_name" id="last_name" value="${member_lastname}">
                                </div>
                            </div>
    
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email"><strong data-key-lang="email" lang="US">Email</strong></label>
                                    <input type="email" class="form-control" name="email" id="email" value="${member_email}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone"><strong data-key-lang="phone_number" lang="US">Phone</strong></label>
                                    <input type="text" class="form-control" name="phone" id="phone" value="${member_phone}">
                                </div>
                            </div>
    
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div style="text-align: end;">
                                        <button class="btn btn-success" id="submitSaveProfile">
                                            <i class="fas fa-save"></i> <span data-key-lang="save" lang="US">save</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <hr>
                    </div>
                </div>
            </form>
            </div>
            `;
    
            $("#showMemberProfile").html(divShowProfile);
    
            // ฟังก์ชันการอัปโหลดไฟล์
            $('#submitSaveProfile').on('click', function(event){
                event.preventDefault(); 
    
                var formProfile = $('#formProfile')[0];
                var formData = new FormData(formProfile);

                // เพิ่ม action ลงใน FormData
                formData.append('action', 'save_member');
    
                $.ajax({
                    url: 'actions/member_process.php',
                    type: 'POST',
                    data: formData,
                    processData: false, 
                    contentType: false,
                    success: function(response) {

                        if(response.status == 'success'){

                            switch (response.message) {
                                case 'save':

                                    Swal.fire({
                                        title: "บันทึกสำเร็จ",
                                        text: "คุณต้องการออกจากระบบเพื่อเป็นข้อมูลล่าสุดหรือไม่",
                                        icon: "success",
                                        showCancelButton: true,
                                        confirmButtonColor: "#4CAF50",
                                        cancelButtonColor: "#d33",
                                        confirmButtonText: "ออกจากระบบ",
                                        cancelButtonText: "อยู่ต่อในระบบ"
                                    }).then((result) => {
                
                                        if (result.isConfirmed) {
                                            window.location.href = 'admin/logout.php';
                                        }else{
                                            window.location.href = 'member.php';
                                        }
                
                                    });
                                    
                                    break;
                            
                                default:
                                    break;
                            }

                        }

                    },
                    error: function(error) {
                        console.log('error', error);
                        
                    }
                });
            });
    
            // แสดงรูปเมื่ออัปโหลด
            var readURL = function(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
    
                    reader.onload = function (e) {
                        $('.avatar').attr('src', e.target.result);
                    }
    
                    reader.readAsDataURL(input.files[0]);
                }
            }
            
            $(".file-upload").on('change', function(){
                readURL(this);
            });

            initializeLanguageSwitcher('#language-select', '[lang]', '../api/languages/langs.json');
        },
        error: function() {
            console.error('Error fetching data.');
        }
    });
    

};

// buildMemberProfile();

const buildMemberTrack = (page = 1, limit = 1) =>{

    $.ajax({
        url: 'actions/member_getData.php',
        type: 'POST',
        data: {
            action: 'get_track',
            page: page,
            limit: limit
        },
        dataType: 'json',
        success: function(response) {

            if (Array.isArray(response.data)) {

                let dataMemberTrack = response.data;
                const totalItems = response.totalRecords;
                const totalPages = response.totalPages;

                // Calculate showing range
                const startItem = (page - 1) * limit + 1;
                const endItem = Math.min(page * limit, totalItems);

                if (dataMemberTrack.length > 0) {
                    let divShowTrack = '';

                    dataMemberTrack.forEach(track => {
                        const productIds = track.product_ids.split(',');
                        const prices = track.prices.split(',');
                        const quantities = track.quantities.split(',');
                        const totalPrices = track.total_prices.split(',');
                        const keyOrder = track.order_keys.split(',');

                        let trackText = '';
                        let trackStep = '';

                        // switch (track.track_id) {
                        //     case 0:
                        //         trackText = `รอส่งหลักฐานการชำระเงิน`;
                        //         break;
                        //     case 1:
                        //         trackText = ``;
                        //         break;
                        //     default:
                        //         break;
                        // }

                        // switch (track.track_id) {
                        //     case 0:
                        //         trackStep = `D/O`;
                        //         break;
                        //     case 1:
                        //         trackStep = ``;
                        //         break;
                        //     default:
                        //         break;
                        // }


                    
                        divShowTrack += `
                        <div class="p-4" style="overflow-x: auto;">
                            <table class="table table-bordered track_tbl">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Step</th>
                                        <th>No</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>`;

                                divShowTrack += `
                                <tr>
                                    <td class="track_dot">
                                        <span class="track_line"></span>
                                    </td>
                                        <td>${trackStep}</td>
                                        <td>${track.order_codes}</td>
                                        <td>${trackText}</td>
                                    </tr>
                                `;

                                // productIds.forEach((productId, index) => {

                                //     let activeTrack = '';

                                //     if (index === 0) {
                                //         activeTrack = 'class="active"';
                                //     }

                                    
                                
                                //     divShowTrack += `
                                //         <tr ${activeTrack}>
                                //             <td class="track_dot">
                                //                 <span class="track_line"></span>
                                //             </td>
                                //             <td>${trackStep}</td>
                                //             <td>${track.order_codes}</td>
                                //             <td>${keyOrder[index]}</td>
                                //             <td>${trackText}</td>
                                //         </tr>
                                //     `;

                                // });

                        divShowTrack += `</tbody>
                            </table>
                        </div>
                        `;
                    });
                    


                    $('#showMemberTrack').html(`
                        <div style="display: flex; justify-content: space-between;">
                            <div>Showing ${startItem} to ${endItem} of ${totalItems} Track</div>
                            <div id="paginationControls_track"></div>
                        </div>
                        ${divShowTrack}
                    `);

                    dataMemberTrack.forEach(track => {
                        // setFormFieldsReadOnly(track.order_id);
                        setupEventHandlers(track.order_id, 'track_ord');
                    });

                    renderPaginationControls(totalPages, page, 'track_ord');
                } else {
                    $('#showMemberTrack').html('<p>No Track found.</p>');
                }

            } else {
                console.error('Response is not an array:', response);
            }
        },
        error: function() {
            console.error('Error fetching data.');
        }
    });

};

// buildMemberTrack();





