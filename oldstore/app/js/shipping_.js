$(document).ready(function() {

    updateCartDisplay(cartArr);

    setupModal("myModal-pay", "myBtn-pay", "modal-close-pay");

    var isSubmitting = false;

    // Function to validate a form
    function validateForm(form) {
        var isValid = true;

        form.find('input, select, textarea').each(function() {
            var fieldType = $(this).attr('type');
            var fieldName = $(this).attr('name');
            var fieldReq = $(this).prop('required');
    
            if (fieldType === 'radio') {
                    
                if (!$('input[name="' + $(this).attr('name') + '"]:checked').length) {
                    isValid = false;
                    alert('โปรดเลือก ' + $(this).attr('name'));
                    return false; 
                }
                
            } else if(fieldReq){
                if ($(this).val().trim() === '') {
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
    function submitForms(form1, form2, form3, url, type) {
        if (isSubmitting) return;

        var countdownInterval = '';
        isSubmitting = true;

        var formData1 = form1.serialize();
        var formData2 = form2.serialize();
        var formData3 = (form3) ? form3.serialize() : null;

        function getSelectedText(name) {
            return $(`#${name} option:selected`).text();
        }
        
   // Initialize variables for selected texts
    var prefix_1111 = null;
    var province_1111 = null;
    var district_1111 = null;
    var subdistrict_1111 = null;

    var prefix_1010 = null;
    var province_1010 = null;
    var district_1010 = null;
    var subdistrict_1010 = null;

    // Check if formData1 is available
    if (formData1) {
        // Retrieve selected texts for the first form
        prefix_1111 = getSelectedText('prefix_1111');
        province_1111 = getSelectedText('province_1111');
        district_1111 = getSelectedText('district_1111');
        subdistrict_1111 = getSelectedText('subdistrict_1111');

        // Retrieve selected texts for the second form
        prefix_1010 = getSelectedText('prefix_1010');
        province_1010 = getSelectedText('province_1010');
        district_1010 = getSelectedText('district_1010');
        subdistrict_1010 = getSelectedText('subdistrict_1010');
    }
    
    const shippingSub = {
        prefix: prefix_1111 || prefix_1010,
        province: province_1111 || province_1010,
        district: district_1111 || district_1010,
        subdistrict: subdistrict_1111 || subdistrict_1010
    };

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                action: 'add_order',
                shipping: formData1,
                payment: formData2,
                transport: formData3,
                shippingSub,
                type
            },
            dataType: 'json',
            success: function(response) {

                clearInterval(countdownInterval);
            
                if(response.status){
                    $("#myBtn-pay").click();
                    var bankShow = '';
                    if(response.payChannel == 1){
                        bankShow = `
                            <label>บจก.แทรนดาร์ อินเตอร์เนชั่นแนล</label>
                            <p>
                                ธ.กรุงศรีอยุธยา 320-1-13702-8
                                <span id="bank-id" style="display: none;">3201137028</span>
                                <button class="btn btn-sm copy-btn" data-copy="#bank-id">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </p>
                        `;

                    }else{
                        $("#qrCodeInput").val(response.pay);
                        // bankShow = `
                        //     <h5 class="mt-2" data-key-lang="please_payment" lang="US">กรุณาชำระเงินภายในเวลา</h5>
                        //     <h6 id="expirationTime" style="text-align: center;"></h6>
                        // `;


                        // var countdownMinutes = 4; 
                        // var countdownSeconds = 59; 
                        
                        // countdownInterval = setInterval(function() {
                        //     if (countdownSeconds === 0 && countdownMinutes === 0) {
                        //         clearInterval(countdownInterval);
                        //         $(".modal-close-pay").click();

                        //     } else {
                        //         if (countdownSeconds === 0) {
                        //             countdownMinutes--;
                        //             countdownSeconds = 59;
                        //         } else {
                        //             countdownSeconds--;
                        //         }

                        //         var htmlMinutes = countdownMinutes === 0 ? "" : ` <span data-key-lang="minute" lang="US">นาที</span> `;
                        //         var htmlSeconds = countdownSeconds === 0 ? "" : ` <span data-key-lang="second" lang="US">วินาที</span> `;
 
                        //         var countdownMessage = countdownMinutes + htmlMinutes + countdownSeconds + htmlSeconds;

                        //         $('#expirationTime').html(countdownMessage);

                        //         initializeLanguageSwitcher('#language-select', '[lang]', '../api/languages/langs.json');

                        //     }
                        // }, 1000);

                        
                    }

                    var htmlPay = `
                        <div>
                            <div style="text-align: center;">
                                ${response.pay}
                                ${bankShow}
                            </div>

                            <div style="display: flex; flex-direction: column; margin-top: 10px;">
                                <div style="display: flex; margin-bottom: 5px;">
                                    <div style="flex: 2; font-weight: bold;" data-key-lang="order_number" lang="US">หมายเลขคำสั่งซื้อ:</div>
                                    <div style="flex: 2; text-align: end;">${response.orderNumber}</div>
                                </div>
                                <div style="display: flex; margin-bottom: 5px;">
                                    <div style="flex: 2; font-weight: bold;" data-key-lang="numberofitems" lang="US">จำนวนรายการ:</div>
                                    <div style="flex: 2; text-align: end;">${response.totalOrderProduct}</div>
                                </div>
                                <div style="display: flex; margin-bottom: 5px;">
                                    <div style="flex: 2; font-weight: bold;" data-key-lang="numberofproducts" lang="US">จำนวนสินค้า:</div>
                                    <div style="flex: 2; text-align: end;">${response.totalOrderQuantity}</div>
                                </div>
                                <div style="display: flex; margin-bottom: 5px;">
                                    <div style="flex: 2; font-weight: bold;" data-key-lang="sumofmoney" lang="US">รวมเป็นเงิน:</div>
                                    <div style="flex: 2; text-align: end;">${response.totalOrderPrice}</div>
                                </div>
                                <div style="display: flex; margin-bottom: 5px;">
                                    <div style="flex: 2; font-weight: bold;" data-key-lang="" lang="US">ส่วนลด:</div>
                                    <div style="flex: 2; text-align: end;">${0}</div>
                                </div>
                                <div style="display: flex; margin-bottom: 5px;">
                                    <div style="flex: 2; font-weight: bold;" data-key-lang="" lang="US">ค่าจัดส่ง:</div>
                                    <div style="flex: 2; text-align: end;">${response.transportOrder}</div>
                                </div>
                                <div style="display: flex; margin-bottom: 5px;">
                                    <div style="flex: 2; font-weight: bold;" data-key-lang="vat" lang="US">ภาษีมูลค่าเพิ่ม (7%):</div>
                                    <div style="flex: 2; text-align: end;">${response.vatOrder}</div>
                                </div>
                                <div style="display: flex; margin-bottom: 5px;">
                                    <div style="flex: 2; font-weight: bold;" data-key-lang="totalamount" lang="US">จำนวนเงินรวมทั้งสิ้น:</div>
                                    <div style="flex: 2; text-align: end;">${response.totalOrderPriceWithVat}</div>
                                </div>
                            </div>
                        </div>
                    `;

                    $("#showMypay").html(htmlPay);

                    $('.copy-btn').click(function() {
                        var copyTarget = $(this).data('copy');
                        var textToCopy = $(copyTarget).text();
                        
                        var tempInput = $('<input>');
                        $('body').append(tempInput);
                        tempInput.val(textToCopy).select();
                        
                        var successful = document.execCommand('copy');
                        tempInput.remove();
                        
                        if (successful) {

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
                            title: "Successfully copied"
                            });

                        } else {

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
                            title: "Failed to copy"
                            });

                        }
                    });

                    $('.modal-close-pay').click(function(){
                        clearInterval(countdownInterval);
                    });

                    initializeLanguageSwitcher('#language-select', '[lang]', '../api/languages/langs.json');

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
                    title: response.errors.join('<br>')
                    });

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
    
    $("#input-b6b").fileinput({
        showUpload: false,
        dropZoneEnabled: false,
        maxFileCount: 10,
        inputGroupClass: "input-group-sm",
        // showCaption: false,
        // previewZoomButtonIcons: {
        //     fullscreen: '',   // close icon fullscreen
        //     borderless: '',   // close icon borderless
        //     close: '',        // icon close
        // },
        // previewZoomButtonClasses: {
        //     fullscreen: '',   // close class fullscreen
        //     borderless: '',   // close class borderless
        //     close: '',        // class close
        // },
    }).on('filezoomshown', function(event, params) {
        $('.kv-zoom-title').hide();  // hidden preview
        $('.modal-backdrop').css('z-index', 'auto'); 
    });

    // Event handler for #submitA
    $('#submitA').on('click', function(event) {
        event.preventDefault(); 

        var form1 = $('#formShipping_1010');
        var form3 = $('#formShipping_1111');

        var formCode = $('#formCode');
        var formTMS = $('#formTMS');
        var formPay = $('#formPay');

        var form = form1.length ? form1 : form3.length ? form3 : null;

        if (validateForm(form)  && validateForm(formTMS) && validateForm(formPay)) {
            submitForms(form, formPay, formTMS, 'actions/process_shipping.php', 1);
        } else {
            isSubmitting = false;
        }
    });

    // Event handler for #submitB
    $('#submitB').on('click', function(event) {
        event.preventDefault(); 

        var form2 = $('#formShipping_2020');
        var form4 = $('#formShipping_2222');

        var formPay = $('#formPay');

        var form = form2.length ? form2 : form4.length ? form4 : null;

        if (validateForm(form) && validateForm(formPay)) {
            submitForms(form, formPay, null, 'actions/process_shipping.php', 2);
        } else {
            isSubmitting = false;
        }
    });

    $(':radio.channel').on('change', function() {
        var selectedValue = $(this).val();

        if (selectedValue == 1) {
            $('.customized').show();
            $('.storefront').hide();
            
            // updateCartDisplay(cartArr, null);
        } else if (selectedValue == 2) {
            $('.storefront').show();
            $('.customized').hide();
            $(':radio.vehicle').prop('checked', false);
            $(':radio.pay_channel').prop('checked', false);
            updateCartDisplay(cartArr);
        }
    });

    $(':radio.vehicle').on('change', async function() {
        // Find the checked radio button
        var selectedRadio = $(':radio.vehicle:checked');
    
        // Get the value of the checked radio button
        var tms_Id = selectedRadio.val();
        var tms_price = selectedRadio.data('vehicle');

        const actionCart = { action: 'addTms', tmsId: tms_Id, tmsPrice: tms_price };
        await processCartAction(actionCart);

    });


    $(':radio.channel:checked').change();
    // $(':radio.vehicle:checked').change();


    function submitFormWithFiles(form, url) {
        var formData = new FormData(form); 

        var checkEvidence = $('#input-b6b').val();
        formData.append('action', 'save_evidence');

        if(checkEvidence){
            formData.append('att_file', 'save_attach_file');
        }
        
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
    }

    $("#submitPay").click(function(event) {
        event.preventDefault(); 
        var form = $('#uploadForm')[0];
        submitFormWithFiles(form, 'actions/process_pay.php');
    });

    loadeMeberDaTa();

// cloase redy
});

async function processCartAction(cartAction) {
    try {
        await sendCartUpdateRequest(cartAction);
        const response = await sendCartUpdateRequest(cartAction);
        updateCartDisplay(response.data, response.data2);
    } catch (error) {
        alert(`An error occurred while ${cartAction.action.replace('_', ' ')} the item.`);
    }
}

async function sendCartUpdateRequest(cartAction) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: 'actions/process_cart.php',
            type: 'POST',
            data: cartAction,
            dataType: 'json',
            success: resolve,
            error: () => reject(new Error('Failed to update cart')),
        });
    });
}

const updateCartDisplay = (cartArr, cartArrOption) => {

    var htmlShowCart = '';
    if (Object.keys(cartArr).length > 0) {
        let totalItems = 0;
        let totalProducts = 0;
        let totalPrice = 0;
        let vat = 0;
        let currency = null;

        Object.entries(cartArr).forEach(([itemKey, item]) => {
            totalItems += parseInt(item.quantity, 10) || 0; // Ensure parsing and fallback to 0
            totalProducts++;
            totalPrice += parseFloat(item.total_price) || 0; // Ensure parsing and fallback to 0
            currency = item.currency;
        });

        let tmsPrice = 0;
        // Check if cartArrOption is neither null nor undefined
        if (cartArrOption && Object.keys(cartArrOption).length > 0) {
            Object.entries(cartArrOption).forEach(([itemKey2, item2]) => {

                if (itemKey2 === 'tms_price') {
                    tmsPrice += parseFloat(item2) || 0; // Ensure parsing and fallback to 0
                }
            });
        }

        vat = (totalPrice + tmsPrice) * 0.07; 
        let totalPriceWithVat = totalPrice + tmsPrice + vat;  

        htmlShowCart += `
        <div class="box-pay-detail">
        <div class="box-pay-order">
            <span data-key-lang="numberofitems" lang="US">จำนวนรายการ: </span>
            <span>${parseFloat(totalProducts)}</span>
        </div>
        <div class="box-pay-order">
            <span data-key-lang="numberofproducts" lang="US">จำนวนสินค้า:</span>
            <span>${parseFloat(totalItems)}</span>
        </div>
        <div class="box-pay-order">
            <span data-key-lang="sumofmoney" lang="US">รวมเป็นเงิน:</span>
            <span>${formatNumberWithComma(totalPrice.toFixed(2))}</span>
        </div>
        <div class="box-pay-order">
            <span data-key-lang="" lang="US">ส่วนลด:</span>
            <span>0</span>
        </div>
        <div class="box-pay-order">
            <span data-key-lang="" lang="US">ค่าจัดส่ง:</span>
            <span>${formatNumberWithComma(tmsPrice.toFixed(2))}</span>
        </div>
        <div class="box-pay-order">
            <span data-key-lang="vat" lang="US">ภาษีมูลค่าเพิ่ม (7%):</span>
            <span>${formatNumberWithComma(vat.toFixed(2))}</span>
        </div>
        <div class="box-pay-order" style="background-color: #ff8c0a;">
            <span data-key-lang="totalamount" lang="US">จำนวนเงินรวมทั้งสิ้น:</span>
            <span>${formatNumberWithComma(totalPriceWithVat.toFixed(2))} ${currency}</span>
        </div>
    </div>
        `;
    }

    $('#show_summary_order').html(htmlShowCart);
};

const loadeMeberDaTa = () => {
    $.ajax({
        url: 'actions/member_getData.php',
        type: 'POST',
        data: {
            action: 'member_address'
        },
        dataType: 'json',
        success: function(response) {

            if (Array.isArray(response.default_data) && response.default_data.length > 0) {
                
                const shipment = response.default_data[0];

                var htmlShippingDetailCustom = `
                <form id="formShipping_1111" class="formShipping__data">
                    <div class="row">

                        <div class="col-12 col-md-2 p-1 d-flex flex-column">
                            <span class="text-nowrap">
                                <label data-key-lang="prefix" lang="US">คำนำหน้า</label> <span>*</span>
                            </span>
                            <select style="width: auto !important;" class="form-select" id="prefix_1111" name="prefix" data-key="${shipment.prefix}" required></select>
                        </div>
                        <div class="col-12 col-md-5 p-1 d-flex flex-column">
                            <span class="text-nowrap">
                                <label data-key-lang="first_name" lang="US">First name</label> <span>*</span>
                            </span>
                            <input type="text" class="form-control" id="firstname_1111" name="firstname" value="${shipment.firstname}" required>
                        </div>
                        <div class="col-12 col-md-5 p-1 d-flex flex-column">
                            <span class="text-nowrap">
                                <label data-key-lang="last_name" lang="US">Last name</label> <span>*</span>
                            </span>
                            <input type="text" class="form-control" id="lastname_1111" name="lastname" value="${shipment.lastname}" required>
                        </div>

                    </div>
                    <div class="row">

                        <div class="col-12 col-md-4 p-1 d-flex flex-column">
                            <span class="text-nowrap">
                                <label data-key-lang="country" lang="US">ประเทศ</label> <span>*</span>
                            </span>
                            <select style="width: auto !important;" class="form-select" id="country_1111" name="country" data-key="${shipment.country}" required></select>
                        </div>
                        <div class="col-12 col-md-4 p-1 d-flex flex-column">
                            <span class="text-nowrap">
                                <label data-key-lang="province" lang="US">จังหวัด</label> <span>*</span>
                            </span>
                            <select style="width: auto !important;" class="form-select" id="province_1111" name="province" data-key="${shipment.province_id}" required></select>
                        </div>
                        <div class="col-12 col-md-4 p-1 d-flex flex-column">
                            <span class="text-nowrap">
                                <label data-key-lang="district" lang="US">เขต/อำเภอ</label>  <span>*</span>
                            </span>
                            <select style="width: auto !important;" class="form-select" id="district_1111" name="district" data-key="${shipment.district_id}" required></select>
                        </div>

                    </div>
                    
                    <div class="row">

                        <div class="col-12 col-md-4 p-1 d-flex flex-column">
                            <span class="text-nowrap">
                                <label data-key-lang="subdistrict" lang="US">แขวง/ตำบล</label> <span>*</span>
                            </span>
                            <select style="width: auto !important;" class="form-select" id="subdistrict_1111" name="subdistrict" data-key="${shipment.sub_district_id}" required></select>
                        </div>
                        <div class="col-12 col-md-4 p-1 d-flex flex-column">
                            <span class="text-nowrap">
                                <label data-key-lang="postal_code" lang="US">รหัสไปรษณีย์</label> <span>*</span>
                            </span>
                            <select style="width: auto !important;" class="form-select" id="post_code_1111" name="post_code" data-key="${shipment.postcode_id}" required></select>
                        </div>
                        <div class="col-12 col-md-4 p-1 d-flex flex-column">
                            <span class="text-nowrap">
                                <label data-key-lang="phone_number" lang="US">เบอร์โทรศัพท์</label> <span>*</span>
                            </span>
                            <input type="text" class="form-control" id="phone_number_1111" name="phone_number" value="${shipment.phone_number}" required>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-12 p-1 d-flex flex-column">
                            <span class="text-nowrap">
                                <label data-key-lang="address" lang="US">ที่อยู่</label> <span>*</span>
                            </span>
                            <textarea id="address_1111" class="form-control" name="address" required>${shipment.detail}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-4 p-1 d-flex flex-column">
                            <span class="text-nowrap">
                                <label data-key-lang="company" lang="US">บริษัท</label>
                            </span>
                            <input type="text" class="form-control" id="comp_name_1111" name="comp_name" value="${shipment.comp_name}">
                        </div>

                        <div class="col-12 col-md-8 p-1 d-flex flex-column">
                            <span class="text-nowrap">
                                <label data-key-lang="tax_id" lang="US">เลขประจําตัวผู้เสียภาษี</label> 
                            </span>
                            <input type="text" class="form-control" id="tax_number_1111" name="tax_number" value="${shipment.tax_number}">
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-12 col-md-12 p-1 d-flex flex-column">
                            <span class="text-nowrap">
                                <label data-key-lang="map" lang="US">จุดสังเกต แผนที่</label> 
                                <span><i class="fas fa-map-marker-alt"></i></span>
                            </span>
                            <input type="text" id="searchInput_1111" class="form-control" placeholder="" >
                        </div>

                        <div class="col-12 col-md-6 p-1 d-flex flex-column">
                            <span class="text-nowrap">
                                <label data-key-lang="" lang="US">ละติจูด</label>
                            </span>
                            <input type="text" class="form-control" id="inputLatitude_1111" name="inputLatitude" value="${shipment.latitude}" required>
                        </div>

                        <div class="col-12 col-md-6 p-1 d-flex flex-column">
                            <span class="text-nowrap">
                                <label data-key-lang="" lang="US">ลองจิจูด</label>
                            </span>
                            <input type="text" class="form-control" id="inputLongitude_1111" name="inputLongitude" value="${shipment.longitude}" required>
                        </div>

                    </div>

                    <div class="row">
                        <div id="googleMap_1111" class="myMapData" style="height: 300px; width: 100%;"></div>
                    </div>


                </form>
                `;

                $("#shippingDetailCustom").html(htmlShippingDetailCustom);

                $(`#formShipping_1111 input, #formShipping_1111 select, #formShipping_1111 textarea`).each(function() {
                    if (this.tagName.toLowerCase() === 'input') {
                    } else if (this.tagName.toLowerCase() === 'textarea') {
                    } else if (this.tagName.toLowerCase() === 'select') {
                        setFormAddress('TH', this.id, this.name, $(this).data('key'));
                    }
                });

                var lat = parseFloat(shipment.latitude);
                var lng = parseFloat(shipment.longitude);
                
                    initMap(
                        'googleMap_1111',
                        'searchInput_1111',
                        'inputLatitude_1111',
                        'inputLongitude_1111',
                        lat,
                        lng,
                        true
                    );

                var htmlShippingDetailStore = `
                <form id="formShipping_2222">
                    <div class="row">
                        
                        <div class="col-12 col-md-4 p-1 d-flex flex-column">
                            <span class="text-nowrap">
                                <label data-key-lang="first_name" lang="US">ชื่อ</label> <span>*</span>
                            </span>
                            <input type="text" class="form-control" id="first_name_2222" name="firstname" value="" required>
                        </div>
                        
                        <div class="col-12 col-md-4 p-1 d-flex flex-column">
                            <span class="text-nowrap">
                                <label data-key-lang="last_name" lang="US">นามสกุล</label> <span>*</span>
                            </span>
                            <input type="text" class="form-control" id="last_name_2222" name="lastname" value="" required>
                        </div>
                        
                        <div class="col-12 col-md-4 p-1 d-flex flex-column">
                            <span class="text-nowrap">
                                <label data-key-lang="company" lang="US">บริษัท</label>
                            </span>
                            <input type="text" class="form-control" id="comp_name_2222" name="comp_name" placeholder="">
                        </div>
                    </div>
                    <div class="row">
                        
                        <div class="col-12 col-md-4 p-1 d-flex flex-column">
                            <span class="text-nowrap">
                                <label data-key-lang="phone_number" lang="US">เบอร์โทรศัพท์</label> <span>*</span>
                            </span>
                            <input type="text" class="form-control" id="phone_number_2222" name="phone_number" required>
                        </div>
                        
                        <div class="col-12 col-md-8 p-1 d-flex flex-column">
                            <span class="text-nowrap">
                                <label data-key-lang="tax_id" lang="US">เลขประจําตัวผู้เสียภาษี</label>
                            </span>
                            <input type="text" class="form-control" id="tax_number_2222" name="tax_number" placeholder="">
                        </div>
                    </div>
                </form>
                `;

                $("#shippingDetailStore").html(htmlShippingDetailStore);
                initializeLanguageSwitcher('#language-select', '[lang]', '../api/languages/langs.json');
                
            }else{
                // if(Array.isArray(response.default_data) && response.default_data.length == 0){

                    var htmlShippingDetailCustom = `
                    <form id="formShipping_1010" class="formShipping__data">
                        <div class="row">
    
                            <div class="col-12 col-md-2 p-1 d-flex flex-column">
                                <span class="text-nowrap">
                                    <label data-key-lang="prefix" lang="US">คำนำหน้า</label> <span>*</span>
                                </span>
                                <select style="width: auto !important;" class="form-select" id="prefix_1010" name="prefix" data-key="" required></select>
                            </div>
                            <div class="col-12 col-md-5 p-1 d-flex flex-column">
                                <span class="text-nowrap">
                                    <label data-key-lang="first_name" lang="US">First name</label> <span>*</span>
                                </span>
                                <input type="text" class="form-control" id="firstname_1010" name="firstname" value="" required>
                            </div>
                            <div class="col-12 col-md-5 p-1 d-flex flex-column">
                                <span class="text-nowrap">
                                    <label data-key-lang="last_name" lang="US">Last name</label> <span>*</span>
                                </span>
                                <input type="text" class="form-control" id="lastname_1010" name="lastname" value="" required>
                            </div>
    
                        </div>
                        <div class="row">
    
                            <div class="col-12 col-md-4 p-1 d-flex flex-column">
                                <span class="text-nowrap">
                                    <label data-key-lang="country" lang="US">ประเทศ</label> <span>*</span>
                                </span>
                                <select style="width: auto !important;" class="form-select" id="country_1010" name="country" data-key="" required></select>
                            </div>
                            <div class="col-12 col-md-4 p-1 d-flex flex-column">
                                <span class="text-nowrap">
                                    <label data-key-lang="province" lang="US">จังหวัด</label> <span>*</span>
                                </span>
                                <select style="width: auto !important;" class="form-select" id="province_1010" name="province" data-key="" required></select>
                            </div>
                            <div class="col-12 col-md-4 p-1 d-flex flex-column">
                                <span class="text-nowrap">
                                    <label data-key-lang="district" lang="US">เขต/อำเภอ</label>  <span>*</span>
                                </span>
                                <select style="width: auto !important;" class="form-select" id="district_1010" name="district" data-key="" required></select>
                            </div>
    
                        </div>
                        
                        <div class="row">
    
                            <div class="col-12 col-md-4 p-1 d-flex flex-column">
                                <span class="text-nowrap">
                                    <label data-key-lang="subdistrict" lang="US">แขวง/ตำบล</label> <span>*</span>
                                </span>
                                <select style="width: auto !important;" class="form-select" id="subdistrict_1010" name="subdistrict" data-key="" required></select>
                            </div>
                            <div class="col-12 col-md-4 p-1 d-flex flex-column">
                                <span class="text-nowrap">
                                    <label data-key-lang="postal_code" lang="US">รหัสไปรษณีย์</label> <span>*</span>
                                </span>
                                <select style="width: auto !important;" class="form-select" id="post_code_1010" name="post_code" data-key="" required></select>
                            </div>
                            <div class="col-12 col-md-4 p-1 d-flex flex-column">
                                <span class="text-nowrap">
                                    <label data-key-lang="phone_number" lang="US">เบอร์โทรศัพท์</label> <span>*</span>
                                </span>
                                <input type="text" class="form-control" id="phone_number_1010" name="phone_number" value="" required>
                            </div>
                            
                        </div>
    
                        <div class="row">
                            <div class="col-12 col-md-12 p-1 d-flex flex-column">
                                <span class="text-nowrap">
                                    <label data-key-lang="address" lang="US">ที่อยู่</label> <span>*</span>
                                </span>
                                <textarea id="address_1010" class="form-control" name="address" required></textarea>
                            </div>
                        </div>
    
                        <div class="row">
                            <div class="col-12 col-md-4 p-1 d-flex flex-column">
                                <span class="text-nowrap">
                                    <label data-key-lang="company" lang="US">บริษัท</label>
                                </span>
                                <input type="text" class="form-control" id="comp_name_1010" name="comp_name" value="">
                            </div>
    
                            <div class="col-12 col-md-8 p-1 d-flex flex-column">
                                <span class="text-nowrap">
                                    <label data-key-lang="tax_id" lang="US">เลขประจําตัวผู้เสียภาษี</label> 
                                </span>
                                <input type="text" class="form-control" id="tax_number_1010" name="tax_number" value="">
                            </div>
                        </div>
    
                        <div class="row">
    
                            <div class="col-12 col-md-12 p-1 d-flex flex-column">
                                <span class="text-nowrap">
                                    <label data-key-lang="map" lang="US">จุดสังเกต แผนที่</label> 
                                    <span><i class="fas fa-map-marker-alt"></i></span>
                                </span>
                                <input type="text" id="searchInput_1010" class="form-control" placeholder="search" >
                            </div>
    
                            <div class="col-12 col-md-6 p-1 d-flex flex-column">
                                <span class="text-nowrap">
                                    <label data-key-lang="" lang="US">ละติจูด</label>
                                </span>
                                <input type="text" class="form-control" id="inputLatitude_1010" 
                                name="inputLatitude" value="" style="background: #e9ecef;" readonly required>
                            </div>
    
                            <div class="col-12 col-md-6 p-1 d-flex flex-column">
                                <span class="text-nowrap">
                                    <label data-key-lang="" lang="US">ลองจิจูด</label>
                                </span>
                                <input type="text" class="form-control" id="inputLongitude_1010" 
                                name="inputLongitude" value="" style="background: #e9ecef;" readonly required>
                            </div>
    
                        </div>
    
                        <div class="row">
                            <div id="googleMap_1010" class="myMapData" style="height: 300px; width: 100%;"></div>
                        </div>
    
    
                    </form>
                    `;
    
                    $("#shippingDetailCustom").html(htmlShippingDetailCustom);
    
                    $(`#formShipping_1010 input, #formShipping_1010 select, #formShipping_1010 textarea`).each(function() {
                        if (this.tagName.toLowerCase() === 'input') {
                        } else if (this.tagName.toLowerCase() === 'textarea') {
                        } else if (this.tagName.toLowerCase() === 'select') {
                            setFormAddress('TH', this.id, this.name, $(this).data('key'));
                        }
                    });
    
                    var lat = '';
                    var lng = '';
                    
                        initMap(
                            'googleMap_1010',
                            'searchInput_1010',
                            'inputLatitude_1010',
                            'inputLongitude_1010',
                            lat,
                            lng,
                            true
                        );
    
                    var htmlShippingDetailStore = `
                    <form id="formShipping_2020">
                        <div class="row">
                            
                            <div class="col-12 col-md-4 p-1 d-flex flex-column">
                                <span class="text-nowrap">
                                    <label data-key-lang="first_name" lang="US">ชื่อ</label> <span>*</span>
                                </span>
                                <input type="text" class="form-control" id="first_name_2020" name="firstname" value="" required>
                            </div>
                            
                            <div class="col-12 col-md-4 p-1 d-flex flex-column">
                                <span class="text-nowrap">
                                    <label data-key-lang="last_name" lang="US">นามสกุล</label> <span>*</span>
                                </span>
                                <input type="text" class="form-control" id="last_name_2020" name="lastname" value="" required>
                            </div>
                            
                            <div class="col-12 col-md-4 p-1 d-flex flex-column">
                                <span class="text-nowrap">
                                    <label data-key-lang="company" lang="US">บริษัท</label>
                                </span>
                                <input type="text" class="form-control" id="comp_name_2020" name="comp_name" placeholder="">
                            </div>
                        </div>
                        <div class="row">
                            
                            <div class="col-12 col-md-4 p-1 d-flex flex-column">
                                <span class="text-nowrap">
                                    <label data-key-lang="phone_number" lang="US">เบอร์โทรศัพท์</label> <span>*</span>
                                </span>
                                <input type="text" class="form-control" id="phone_number_2020" name="phone_number" required>
                            </div>
                            
                            <div class="col-12 col-md-8 p-1 d-flex flex-column">
                                <span class="text-nowrap">
                                    <label data-key-lang="tax_id" lang="US">เลขประจําตัวผู้เสียภาษี</label>
                                </span>
                                <input type="text" class="form-control" id="tax_number_2020" name="tax_number" placeholder="">
                            </div>
                        </div>
                    </form>
                    `;
    
                    $("#shippingDetailStore").html(htmlShippingDetailStore);
                    initializeLanguageSwitcher('#language-select', '[lang]', '../api/languages/langs.json');
                    
                // }
            }

        },
        error: function(xhr, status, error) {
            // Handle any errors here
            console.error('Error:', status, error);

            


        }
    });
};
