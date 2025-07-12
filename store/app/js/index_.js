var productsPerPage = 6;
var currentPage = 1;
var isFetchingProducts = false;

var storedProductData = null;
var dataShowProduct = null;


function setInLoading(element, delay) {
    setTimeout(function() {
    $(element).fadeIn();
    }, delay);
}

function setOutLoading(element, delay) {
    setTimeout(function() {
    $(element).fadeOut(); 
    }, delay);
}

$(document).ready(function() {

    function getDeviceType() {
        const ua = navigator.userAgent;
        if (/android/i.test(ua)) {
            return /mobile/i.test(ua) ? "Android Phone" : "Android Tablet";
        }
        if (/iPhone/i.test(ua)) {
            return "iPhone";
        }
        if (/iPad/i.test(ua)) {
            return "iPad";
        }
        if (/Windows/i.test(ua) || /Macintosh/i.test(ua)) {
            return "Desktop";
        }
        return "Unknown Device";
    }

    setInLoading('#loading-overlay', 1000);
    setOutLoading('#loading-overlay', 1000);

    getApiTabContent();
    getApiProducts();

    $('#pageHome').on("click", function(e){
        window.location.href = 'index.php';
    });

    // function realTimeSet() {
    //     // ฟังก์ชันสำหรับทำงานวนลูป
    //     function setLoop() {

    //         getApiProducts();
            
    //         // หยุดรอ 2 วินาทีแล้วทำงานใหม่
    //         setTimeout(setLoop, 5000); // เรียก loop ใหม่หลังจาก 2000ms (2 วินาที)
    //     }
    
    //     setLoop(); // เริ่มลูป
    // }
    
    // // เรียกใช้ฟังก์ชัน
    // realTimeSet();


    var currentPath = window.location.pathname;
    var fileName = currentPath.substring(currentPath.lastIndexOf('/') + 1);

    // ตรวจสอบสถานะ active จาก localStorage และตั้งค่า
    var activeStep = localStorage.getItem('activeStep');
    if (activeStep) {
        $('.step-wizard-step').removeClass('active');
        $('#' + activeStep).addClass('active');
        localStorage.removeItem('activeStep');
    }

    // ตรวจสอบเส้นทางของหน้าและตั้งค่าสถานะ active ตามนั้น
    $('.step-wizard-step a').each(function() {
        var linkPath = $(this).attr('href');
        if (linkPath === fileName) {
            $('.step-wizard-step').removeClass('active');
            $(this).closest('.step-wizard-step').addClass('active');
        }
    });

    // เมื่อคลิกที่ step-wizard-step, เก็บข้อมูลและตั้งค่าสถานะ active
    $('.step-wizard-step a').on('click', function(e) {
        e.preventDefault(); // ป้องกันการเปลี่ยนเส้นทาง
        var stepId = $(this).closest('.step-wizard-step').attr('id');
        $('.step-wizard-step').removeClass('active');
        $(this).closest('.step-wizard-step').addClass('active');
        
        localStorage.setItem('activeStep', stepId);
        window.location.href = $(this).attr('href');
    });

    //Main HTML compare
    compareProd(compareArr);
    renderCompareTable(compareArr);
    
    setupModal("myModal", "myBtn", "modal-close");
    setupModal("myModal-channel", "myBtn-channel", "modal-close-channel");
    setupModal("myModal-compare", "myBtn-compare", "modal-close-compare");

    // setupModal("myModal-advertising", "myBtn-advertising", "modal-close-advertising");
    // initMap('googleMap', 'searchInput', 'inputLatitude', 'inputLongitude', null, null, true);

    $.getJSON("../api/languages/nation.json" + '?' + new Date().getTime(), function(data) {

        let nationalities = data.nationalities;
        let $select = $('#language-select');
        $select.empty();
    
        $.each(nationalities, function(key, entry) {
            // สร้าง <option> พร้อมรูปธงชาติ
            let option = $('<option></option>')
                .attr('value', entry.abbreviation)
                .attr('data-flag', entry.flag)  // เก็บ URL ธงชาติใน data-flag
                .text(entry.name);
            
            $select.append(option);
        });
    
        if (nationalities.length > 0) {
            
            $select.select2({
                templateResult: formatState,  // เรียกใช้ function สำหรับแสดงรูปธงชาติ
                templateSelection: formatState // เรียกใช้เมื่อเลือก option
            });

            initializeLanguageSwitcher('#language-select', '[lang]', '../api/languages/langs.json');

        } else {
            console.error("ไม่มีข้อมูลสำหรับสร้าง options");
        }
    });

    
    $('#btn_search_prod').on('click', function(event){
        event.stopPropagation();
        refreshProductData();
    });


    if($('#showAllproduct').length){
        $('#myBtn-advertising').click();
        // getDiscount();
    }

    
    if ($('#slider-range').length) {
        categoryfilter();

        $("#slider-range").slider({
            range: true,
            min: 0,
            max: 100000,
            values: [0, 100000],
            slide: function(event, ui) {
                $("#amountMin").val(ui.values[0]);
                $("#amountMax").val(ui.values[1]);
                refreshProductData();
            }
        });

        $("#amountMin").val($("#slider-range").slider("values", 0));
        $("#amountMax").val($("#slider-range").slider("values", 1));

        // Regular expression for valid numbers (integers and decimals)
        const numberRegex = /^\d+(\.\d+)?$/;

        $("#amountMin, #amountMax").on("input", function() {
            let minVal = $("#amountMin").val();
            let maxVal = $("#amountMax").val();

            // หากช่อง input ว่างเปล่า ให้คืนค่า slider ปัจจุบันไปยัง input นั้น
            if (minVal === "") {
                minVal = $("#slider-range").slider("values", 0);
                $("#amountMin").val(""); // คงค่าว่างใน input
            } else if (!numberRegex.test(minVal) || parseFloat(minVal) < 0 || parseFloat(minVal) > 100000) {
                minVal = $("#slider-range").slider("values", 0);
                $("#amountMin").val(minVal);
            } else {
                minVal = parseFloat(minVal);
            }

            if (maxVal === "") {
                maxVal = $("#slider-range").slider("values", 1);
                $("#amountMax").val(""); // คงค่าว่างใน input
            } else if (!numberRegex.test(maxVal) || parseFloat(maxVal) < 0 || parseFloat(maxVal) > 100000) {
                maxVal = $("#slider-range").slider("values", 1);
                $("#amountMax").val(maxVal);
            } else {
                maxVal = parseFloat(maxVal);
            }

            // Ensure minVal is not greater than maxVal
            if (minVal > maxVal) {
                minVal = maxVal; // Adjust minVal to equal maxVal if it exceeds
                $("#amountMin").val(minVal);
            }

            refreshProductData();
            // Update slider
            $("#slider-range").slider("values", [minVal, maxVal]);
        });

    }

    //login
    $('#togglePasswordPage').on('click', function() {
        const password = $('#password');
        const type = password.attr('type') === 'password' ? 'text' : 'password';
        password.attr('type', type);
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });

    $('#loginModal').on('submit', function(event) {
        event.preventDefault();
    
        const email = $('#email').val().trim();
        const password = $('#password').val().trim();
    
        if (!email || !password) {
            alert('Please enter both email and password');
            return;
        }
    
        $.ajax({
            url: './admin/actions/check_login.php', // Adjust path as needed
            type: 'POST',
            data: {
                email: email,
                password: password
            },
            dataType: 'json',
            success: function(response) {

                if (response.status === "success") {

                    sessionStorage.setItem('jwt', response.jwt);
                    

                    const token = sessionStorage.getItem('jwt');

                    $.ajax({
                        url: './admin/actions/protected.php', 
                        type: 'GET',
                        headers: {
                            'Authorization': 'Bearer ' + token
                        },
                        success: function(response) {
                            
                            if (response.status === "success") {
                                
                                switch (response.data.role) {
                                    case 1:
                                        window.location.href = './admin/index.php';
                                        break;
                                    case 2:
                                        window.location.href = 'index.php';
                                        break;
                                    default:
                                        alert('Unknown role');
                                        break;
                                }

                            } else {
                                alert(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Request failed:", status, error);
                            alert("An error occurred while accessing protected resource.");
                        }
                    });
    
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX request failed:", status, error);
                alert("An error occurred. Please try again.");
            }
        });
    });

});


function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";

    $('.alert-cookie').css('display', 'none');
}

function formatState(state) {
    if (!state.id) {
        return state.text;
    }

    var flagUrl = $(state.element).data('flag'); // ดึงค่า data-flag
    var $state = $(
        '<span><img src="' + flagUrl + '" class="img-flag" style="width:20px; margin-right: 10px;" /> ' + state.text + '</span>'
    );
    return $state;
}

//modal
function setupModal(modalId, btnId, closeClass) {
    var modal = document.getElementById(modalId);
    var btn = document.getElementById(btnId);
    var span = document.getElementsByClassName(closeClass)[0];

    if (modal && btn && span) {
        btn.onclick = function() {
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        // window.onclick = function(event) {
        //     if (event.target == modal) {
        //         modal.style.display = "none";
        //     }
        // }
    } else {
        console.error('One of the modal variables, btn or span, cannot be found in the DOM.');
    }
}

const categoryfilter = (event = null) => {

    if (event) {
        event.preventDefault();
    }

    $.ajax({
        url: 'actions/product_getData.php',
        type: 'POST',
        data: { 
            action: 'getCategory'
        },
        dataType: 'json',
        success: function(response) {

            let itemsCategory = response.data;

            if(itemsCategory){

                const groupedDataCategory = itemsCategory.reduce((acc, item) => {
                    if (!acc[item.category_name]) {
                        acc[item.category_name] = [];
                    }
                    acc[item.category_name].push(item);
                    return acc;
                }, {});

                let dataAllCategory = [];
                let datafilterCategory = [];
            
                for (const categorys in groupedDataCategory) {
                    dataAllCategory.push({
                        "index": categorys
                    });
                    groupedDataCategory[categorys].forEach(item => {
                        datafilterCategory.push({
                            "index": categorys,
                            "pro_id": item.id,
                            "pro_code": item.code,
                            "pro_name": item.description
                        });
                    });
                }

                let ctorHTML = '';

                ctorHTML += `
                    <div class="toggle-btn-filter" data-type="">
                        <div>
                            <span> All</span>
                        </div>
                        <div>
                            <span id="countProd" class="badge" style="background: #ff8c0a;"></span>
                        </div>
                    </div>`;

                // Loop ผ่าน categories
                dataAllCategory.forEach((category) => {

                    ctorHTML += `
                    <div class="toggle-btn-filter" data-type="${category.index}">
                        <div>${category.index}</div>
                        <div>
                            <i class="icon-filter fas fa-chevron-down"></i> 
                            <i class="icon-filter fas fa-chevron-up" style="display: none;"></i>
                        </div>
                    </div>`;

                    ctorHTML += `<div class="content-filter">`;
                    
                    datafilterCategory.forEach((item) => {
                        
                        const idfilter = category.index.replace(/\s+/g, '').trim();
                        const idfilters = item.index.replace(/\s+/g, '').trim();

                        if (idfilter === idfilters) { 
                            ctorHTML += `<div class="filterId" data-id="${item.pro_code}" style="padding: 3px 10px;">${item.pro_name}</div>`; 
                        }
                    });
                    ctorHTML += `</div>`;
                });

                $('#slider-category').html(ctorHTML); 

                
                $('.toggle-btn-filter').on('click', function(event) {
                    event.stopPropagation();
                    $('.filterId').removeClass('active');
                    $(this).next('.content-filter').toggle(); 
                    $(this).find('.icon-filter').toggle(); 

                    $('.toggle-btn-filter').removeClass('active');
                    $(this).addClass('active');

                    refreshProductData();

                });

                $('.filterId').on('click', function(event) {
                    event.stopPropagation();
                    $('.filterId').removeClass('active');
                    $(this).addClass('active');
                    refreshProductData();
                    
                });
            }

        },
        error: function(xhr, status, error) {
            console.log('Error:', error);
        }
    });

}

const getApiTabContent = (event = null) => {

    if (event) {
        event.preventDefault();
    }

    $.ajax({
        url: 'actions/product_getData.php',
        type: 'POST',
        data: { 
            action: 'getCategory'
        },
        dataType: 'json',
        success: function(response) {

            let items = response.data;
            if(items){

                const groupedData = items.reduce((acc, item) => {
                    if (!acc[item.category_name]) {
                        acc[item.category_name] = [];
                    }
                    acc[item.category_name].push(item);
                    return acc;
                }, {});

                let dataAll = [];
                let datafilter = [];
            
                for (const category in groupedData) {

                    dataAll.push({
                        "index": category
                    });
                    
                    
                    groupedData[category].forEach(item => {
                        datafilter.push({
                            "index": category,
                            "pro_id": item.id,
                            "pro_name": item.description
                        });
                    });
                }
            
                const tabKeys = new Set(dataAll.map(tab => tab.index.replace(/\s+/g, '').trim()));
                const filteredData = datafilter.filter(item => tabKeys.has(item.index.replace(/\s+/g, '').trim()));
                
                let divTabContent = '';
                let divTabButtons = '';
            
                
                dataAll.forEach(tab => {
                    const tabKey = tab.index.replace(/\s+/g, '').trim();
                    const tabValue = tab.index.trim();
                
                    divTabButtons += `
                        <button class="tablinks" data-target="category${tabKey}">
                            <span>${tabValue}</span>
                        </button>`;
                
                    divTabContent += `
                        <div id="category${tabKey}" class="tabcontent">
                            <br>
                            <h5>${tabValue}</h5>
                            <hr>
                            <div class="row">`;
                
                    
                    const referItems = filteredData.filter(item => item.index.replace(/\s+/g, '').trim() === tabKey);
                
                    if (referItems.length > 0) {
                        
                        divTabContent += `<div class="col-md-6 col-sm-6 col-xs-6"><ul>`;
                
                        
                        referItems.forEach((item, index) => {
                            if (index % 2 === 0) {
                                const referKey = item.pro_id;
                                const referValue = item.pro_name;
                                divTabContent += `
                                    <li>
                                        <a href="product.php?id=${referKey}" class="list-group-item">
                                            <span>${referValue}</span>
                                        </a>
                                    </li>`;
                            }
                        });
                
                        divTabContent += `</ul></div><div class="col-md-6 col-sm-6 col-xs-6"><ul>`;
                
                        
                        referItems.forEach((item, index) => {
                            if (index % 2 !== 0) {
                                const referKey = item.pro_id;
                                const referValue = item.pro_name;
                                divTabContent += `
                                    <li>
                                        <a href="product.php?id=${referKey}" class="list-group-item">
                                            <span>${referValue}</span>
                                        </a>
                                    </li>`;
                            }
                        });
                
                        divTabContent += `</ul></div>`;
                    }
                
                    divTabContent += `</div>
                        </div>`;
                });
            
            
                $('#showTabContent').html(`
                    <div class="tab" id="tabMenu">
                        ${divTabButtons}
                    </div>
                    ${divTabContent}
                `);
            

                var $tabcontent = $(".tabcontent");
                var $tablinks = $(".tablinks");
        
                function openTab(event, cityName) {
                    $tabcontent.hide();
                    $tablinks.removeClass("active");
            
                    $("#" + cityName).show();
                    $(event.currentTarget).addClass("active");
                }
            
            
                $(".tablinks").on("mouseover", function(event) {
                    var target = $(this).data("target");
                    openTab(event, target);
                });
        
        
                $("#toggleIcon").on("click", function(event) {
                    $tabcontent.hide();
                    $tablinks.removeClass("active");
            
                    $("#tabMenu").toggle();
                    var isVisible = $("#tabMenu").is(":visible");
                    $(this).toggleClass("fa-bars", !isVisible);
                    $(this).toggleClass("fa-times", isVisible);
                    $('#background-blur').toggleClass('tab-open');
                });

                initializeLanguageSwitcher('#language-select', '[lang]', '../api/languages/langs.json');
            }

        },
        error: function(xhr, status, error) {
            console.log('Error:', error);
        }
    });


};

const getApiProducts = (page = 1, event = null) => {
    
        if (event) {
            event.preventDefault();
        }

        if (isFetchingProducts) return; // Prevent multiple calls
        isFetchingProducts = true; // Set the flag to true
        
        $.ajax({
            url: "actions/product_getData.php",
            type: "POST",
            data: { action: 'getProducts' },
            dataType: "json",
            success: function(response) {

                // ทำการ parse แต่ละ string ใน product_jsons เป็น object
                // let product_jsons = response.data.map(product => product.product_json);
                // let product_objs = product_jsons.map(json => JSON.parse(json));

                if(response.status == 'success'){

                    storedProductData = response.data;
                    generateProductData(page, storedProductData);
                }

            },        
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
            },
            complete: function() {
                isFetchingProducts = false; // Reset the flag after the request completes
            }
        });
};

const refreshProductData = (page = 1) => {
    if (storedProductData) {
        generateProductData(page, storedProductData);  
    }
};

const generateProductData = (page, response) =>{

    const minPrice = parseFloat($("#amountMin").val()) || 0;
    const maxPrice = parseFloat($("#amountMax").val()) || Infinity;

    let search_prod = $('#search_prod').val() || 
    $('.filterId.active').data('id') || 
    $('.toggle-btn-filter.active').data('type') ||
    '';

    const buildArrApi = response.map(data => {
        const costArray = data.cost ? data.cost.split(',') : [];
        const currencyArray = data.currency ? data.currency.split(',') : [];
        const uomArray = data.uom ? data.uom.split(',') : [];
        
        return {
            pro_id: String(data.id),
            pro_name: data.code,
            pro_category: data.category_name,
            pro_description: data.description,
            pro_img: data.pic_icon,
            price: parseFloat(costArray[0]) || 0,
            currency: currencyArray[0] || '',
            stock: data.stock,
            uom: uomArray[0] || '',
            isMember: data.member_id
        };
    });
    

    let filterApi = buildArrApi.filter(item => {
        const searchTerm = search_prod.toLowerCase();
        return item.pro_name?.toLowerCase().includes(searchTerm) || 
        item.pro_category?.toLowerCase().includes(searchTerm) || 
        item.pro_description?.toLowerCase().includes(searchTerm);
    });
    
    filterApi = filterApi.filter(item => {
        return item.price >= minPrice && 
        item.price <= maxPrice;
    });


    const totalProducts = filterApi.length;
    const totalPages = Math.ceil(totalProducts / productsPerPage);

    if (page > totalPages) page = totalPages;
    
    const startIndex = (page - 1) * productsPerPage;
    const endIndex = Math.min(startIndex + productsPerPage, totalProducts);
    const productsToShow = filterApi.slice(startIndex, endIndex);
    

    $('#countProd').text(totalProducts);
    
    $('#pageInfo').text(`Showing ${((startIndex + 1) > 0 ? startIndex + 1 : 0)} to ${endIndex} of ${totalProducts} products`);
    $('#showAllproduct').html(generateProductHTML(productsToShow));
    paginationProducts(page, totalPages);
    initializeLanguageSwitcher('#language-select', '[lang]', '../api/languages/langs.json');

    // Adding scroll event listener with throttling
    // window.addEventListener('scroll', throttle(() => handleScroll(totalProducts), 100));
    window.addEventListener('scroll', throttle(() => handleScroll(totalProducts), 100));
    window.addEventListener('touchmove', throttle(() => handleScroll(totalProducts), 100));

}

const generateProductHTML = (products) => {
    let compareKeys = Object.keys(compareArr);
    if(dataShowProduct){
        compareKeys = Object.keys(dataShowProduct)
    }

    return products.map(product => {
        const isChecked = compareKeys.includes(String(product['pro_id'])) ? 'checked' : '';

        var discount = '';
        var rating = '';
        var heart = '';
        var outStock = '';
        var clickDetail = '';
        var clickCpare = '';
        var cssfilter = '';

        if(product.stock > 0){
            discount = '<span class="label-discount" data-key-lang="discount" lang="US">discount</span>';

            rating = `${'<i class="fas fa-star"></i>'.repeat(5)}`;
            heart = '<i class="far fa-heart"></i>';

            clickDetail = `onclick="getDetail('product.php', '${product['pro_id']}');"`;
            clickCpare = `onclick='compareProd(${JSON.stringify(product).replace(/'/g, "\\'")}, event, "${product['pro_id']}");'`;
        }else{

            outStock = `<span class="label-outStock" data-key-lang="" lang="US">สินค้าหมด</span>`;
            cssfilter = 'filter: brightness(0.9);';
        }

        return `
        <div class="col-md-4 col-sm-6 col-xs-6 mt-2 mb-2">
            <div class="card-box-hot" style="width: 100%; height: 100%; ${cssfilter}">
                <div class="label-card-box">
                    <span></span>
                    ${discount}
                </div>
                <div class="label-out-box">
                    ${outStock}
                </div>
                <div class="card-box-body">
                    <div class="image-container" ${clickDetail}>
                        <img src="${product['pro_img'] || 'http://placehold.it/100x100'}" class="card-img-top" alt="${product['pro_name']}">
                    </div>
                    <div class="label-show-box">
                        <span></span>
                        <span class="label-promotion" data-key-lang="" lang="US">${product['pro_name']}</span>
                    </div>
                    <div style="width: 100%; height: 100%;" ${clickDetail}>
                        <p style="word-wrap: break-word; text-align: start;" class="line-clamp">${product['pro_description']}</p>
                    </div>
                    <div style="margin-top: auto; width: 100%;">
                        <div style="text-align: start; margin-bottom: 10px;">
                            ${product['price'].toLocaleString()}
                            ${product['currency']} / ${product['uom']}
                        </div>
                    </div>
                    <div style="margin-top: auto; width: 100%;">
                        <div style="display: flex; justify-content: space-between;">
                            <div style="font-size: 12px; color: #ff8c0a;">
                                ${''}
                            </div>
                            <div style="font-size: 12px;">
                                <span data-key-lang="" lang="US">สินค้าคงคลัง</span>
                                ${(product.stock > 0) ? product.stock : 0}
                            </div>
                        </div>
                        <hr>
                        <div style="display: flex; justify-content: space-between;">
                            <div>
                                <input type="checkbox" class="compareChecked" data-id="${product['pro_id']}" ${isChecked} style="filter: hue-rotate(157deg);" 
                                ${(clickCpare) ? clickCpare : 'disabled' }>
                                <label style="font-size: 14px;" data-key-lang="" lang="US">การเปรียบเทียบ</label>
                            </div>
                            <div>
                                ${''}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    }).join('');
};

const paginationProducts = (currentPage, totalPages) => {
    let paginationProducts = '';

    if (currentPage > 1) {
        paginationProducts += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="refreshProductData(${currentPage - 1})">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>`;
    }

    const maxPagesToShow = 3; // Number of page numbers to show
    const startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
    const endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

    for (let i = startPage; i <= endPage; i++) {
        paginationProducts += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" onclick="refreshProductData(${i})">${i}</a>
            </li>`;
    }

    if (currentPage < totalPages) {
        paginationProducts += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="refreshProductData(${currentPage + 1})">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>`;
    }

    $('#paginationProducts').html(`<ul class="pagination">${paginationProducts}</ul>`);
};

const handleScroll = (totalProducts) => {
    const scrollTop = window.scrollY || document.documentElement.scrollTop; // Support for different browsers
    const windowHeight = window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight;

    const sumScroll = scrollTop + windowHeight + 400;
    
    // Check for scroll to top
    if (scrollTop === 0) {
        if (productsPerPage > 6) {
            productsPerPage -= 6;
            // getApiProducts(currentPage);
            refreshProductData(currentPage);
        }
    } 
    // Check for scroll to bottom
    else if (sumScroll >= documentHeight && documentHeight >= 1080) {
        if (productsPerPage < totalProducts) {
            productsPerPage += 6;
            // getApiProducts(currentPage); 
            refreshProductData(currentPage);
        }
    }
};

const throttle = (func, limit) => {
    let lastFunc;
    let lastRan;
    return function() {
        const context = this;
        const args = arguments;
        if (!lastRan) {
            func.apply(context, args);
            lastRan = Date.now();
        } else {
            clearTimeout(lastFunc);
            lastFunc = setTimeout(function() {
                if ((Date.now() - lastRan) >= limit) {
                    func.apply(context, args);
                    lastRan = Date.now();
                }
            }, limit - (Date.now() - lastRan));
        }
    };
};

const compareProd = async (data, event, pro_id) => {

    if(data.isMember > 0){

        let countCheckbox = $(`input[type="checkbox"].compareChecked:checked`);
        if (countCheckbox.length < 5) {

            let actionData = '';
            if (event) {
                const is_inputCheck = event.target.checked;
                const is_inputBox = event.target.dataset.id;
        
                if (is_inputCheck ) {
                    actionData = {
                        action: 'add_compare',
                        compareData: data
                    };
                } else {
                    actionData = {
                        action: 'removeCompare_item',
                        item_key: is_inputBox
                    };
                }
            }
        
            // if (Object.keys(data).length > 0) {
            //     renderCompareTable(data);
            // }
        
            if (actionData) {
                try {
                    await sendRequest(actionData);
                    await buildCompare(actionData);
                } catch (error) {
                    console.error('Error occurred while updating comparison:', error);
                }
            }
            
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
                title: "เกินขีดจำกัด"
            });
            refreshProductData();
        }

    }else{
        $('#myBtn-channel').click();
        refreshProductData();
    }




};

const sendRequest = async (actionData) => {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: 'actions/process_compare.php',
            type: 'POST',
            data: actionData,
            dataType: 'json',
            success: function (response) {
                resolve(response);
            },
            error: function (error) {
                reject(error);
            }
        });
    });
};

const buildCompare = async (actionData) => {
    try {

        let response = await sendRequest(actionData);
        let responseKeys = Object.keys(response.data).length;

        $('#compareCount').text(responseKeys);
        dataShowProduct = response.data;

        renderCompareTable(response.data);

    } catch (error) {
        console.error('Error in buildCompare:', error);
    }
};

const renderCompareTable = (productsData) => {

    if(productsData){

        let tableHTML = `<table class="table"><tbody>`;
        const headers = {
            ' ': 'pro_img',
            'Code': 'pro_name',
            'Category': 'pro_category',
            'Description': 'pro_description',
            'Price': 'price',
            '': 'btn'
        };

        const products = Object.values(productsData);
        Object.entries(headers).forEach(([header, field]) => {
            tableHTML += '<tr><th>' + header + '</th>';
            products.forEach(product => {

                tableHTML += '<td>';
                
                if (product && product[field]) {
                    if (field === 'pro_img') {
                        tableHTML += `<div style="position: relative;">
                                        <div class="close-compare" data-id="${product['pro_id']}" style="position: absolute; font-size: 30px; right: 15px;">&times;</div>
                                        <img src="${product[field]}" alt="${product['pro_name']}" style="width: 100px;">
                                    </div>`;

                    } else if (field === 'price') {

                        tableHTML += `<div>
                            <span>${product[field]} ${product['currency']} / </span>
                            <span>${product['uom']}</span>
                        </div>`;

                    } else {
                        tableHTML += product[field];
                    }
                }


                if (field === 'btn' && product['pro_id']) {
                    tableHTML += `<button type="button" class="get-ProID" data-id="${product['pro_id']}">เลือกรูปแบบ</button>`;
                }

                tableHTML += '</td>';

            });
            tableHTML += '</tr>';
        });

        tableHTML += `</tbody></table>`;
        $('#compareTableContainer').html(tableHTML);


        // Event handlers for newly rendered elements
        $('.get-ProID').on('click', function (event) {
            event.stopPropagation();
            let proID = $(this).data('id');
            getDetail('product.php', proID);
        });

        $('.close-compare').on('click', function (event) {
            event.stopPropagation();
            let proID = $(this).data('id');
        
            // Find the checkbox with matching proID and the compareChecked class, then uncheck it
            let checkbox = $(`input[type="checkbox"].compareChecked[data-id="${proID}"]`);
            if (checkbox.length > 0) {
                checkbox.prop('checked', false);  // Uncheck the checkbox
            }

            // Call delCompare to remove the product from the comparison list
            delCompare(proID);
        });
    }
    
};

const delCompare = async (proID) => {
    try {
        await sendRequest({
            action: 'removeCompare_item',
            item_key: proID
        });
        await buildCompare({
            action: 'removeCompare_item',
            item_key: proID
        });
    } catch (error) {
        console.error('Error in delCompare:', error);
    }
};

const getDetail = (baseUrl, id) => {

    const stringId = String(id);

    const url = baseUrl && stringId ? `${baseUrl}?id=${encodeURIComponent(stringId)}` : baseUrl;

    if (stringId) {
        window.location.href = url;
    } else {
        $('#myBtn-channel').click();
    }
};

function openNav() {
    document.getElementById("mySidenav").style.width = "350px";
    $('#sidenav-blur').toggleClass('tab-open');
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
    $('#sidenav-blur').toggleClass('tab-open');
}


function formatNumberWithComma(number) {
    const [integerPart, decimalPart] = number.toString().split('.');
    const formattedInteger = parseInt(integerPart, 10).toLocaleString();

    if (decimalPart !== undefined) {
        return `${formattedInteger}.${decimalPart}`;
    }

    return formattedInteger;
}

// const getDiscount = () => {

//     $('#showDiscount').html(htmlShowDiscount);

// };






