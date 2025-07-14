$(document).ready(async function() {
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    const productId = id;

    const product = new getProductApi(productId);
    await product.fetchProductData();
    product.findProduct();
    product.combineProductData();
    product.generateProductOptions(); // Added this call
    product.setupEventListeners();

});

/****************************************************************************/

    class getProductApi {
        constructor(productId) {
            this.productId = productId;
            this.productData = null;
            this.productImgDetail = null;
            this.arrayProductOption = [];
            this.selectedPic = null
            this.selectedSize = null;
            this.selectedColor = null;
            this.selectedPrice_Size = null;
            this.selectedPrice_Color = null;
            this.combinedVariat = [];
            this.isMember_id = null;
        }

        async fetchProductData() {
            try {
                const response = await $.ajax({
                    url: 'actions/product_getData.php',
                    type: 'POST',
                    data: { 
                        action: 'getProductDetail', 
                        pro_id: this.productId
                    },
                    dataType: 'json'
                });

                var buildProductData = [];
                var buildProductImgDetail = [];

                // console.log('response', response);

                response.data.forEach(data => {
                    
                    // var objProductData = {
                    //     "dep": data.description,
                    //     "prot": ''
                    // };

                    this.isMember_id = data.member_id;

                    // var strJSONProductData = JSON.stringify(objProductData);

                    var costArrays = data.cost ? data.cost.split(',') : [];
                    var currencyArrays = data.currency ? data.currency.split(',') : [];
                    var uomArrays = data.uom ? data.uom.split(',') : [];
                    
                    buildProductData.push({
                        "pro_id": String(data.id),
                        "pro_name": data.code,
                        "pro_category": data.category_name,
                        "pro_description": data.description,
                        "pro_img": data.pic_icon,
                        "price": parseFloat(costArrays[0]) || 0,
                        "currency": currencyArrays[0] || '',
                        "uom": uomArrays[0] || '',
                    });

                });

                response.data.forEach(data => {

                    var itemArray = data.attb_item && data.attb_item !== 'null' ? data.attb_item.split(',') : [];
                    var valueArray = data.attb_value && data.attb_value !== 'null' ? data.attb_value.split(',') : [];
                    var priceArray = data.attb_price && data.attb_price !== 'null' ? data.attb_price.split(',') : [];

                    buildProductImgDetail.push({
                        "pro_id": String(data.id),
                        "pro_img_sub": [
                            data.pic_icon
                        ],
                        "attb_item": itemArray,
                        "attb_value": valueArray,
                        "attb_price": priceArray
                    });


                });

                this.productData = buildProductData;
                this.productImgDetail = buildProductImgDetail;

                if (!Array.isArray(this.productData) || !Array.isArray(this.productImgDetail)) {
                    throw new Error('Invalid data structure received');
                }

            } catch (error) {
                console.error('Error fetching product data:', error);
                // Add user feedback here
            }
        }

        findProduct() {

            this.product = this.productData.find(item => item.pro_id === this.productId);
            this.imgDetail = this.productImgDetail.find(item => item.pro_id === this.productId);

            if (!this.product || !this.imgDetail) {
                console.warn('Product or image detail not found');
            }
            
        }

        combineProductData() {

            if (this.product && this.imgDetail) {
                this.combinedProduct = { ...this.product, ...this.imgDetail };
            } else {
                console.warn('Product or image detail not found');
            }

        }

        checkOptionsMatch() {
            let combinedVariat = this.combinedVariat;
            let arrayProductOption = this.arrayProductOption;
        
            const matches = {};
            const notMatches = {};
        
            combinedVariat.forEach(variat => {
                const option = variat.item; 
                const value = variat.value;
                
                const found = arrayProductOption.find(item => item[option] === value);
        
                if (found) {
                    
                    matches[option] = value;
                } else {
                    notMatches[option] = value; 
                }
            });
        
        
            return {
                matches,
                notMatches
            };
        }
        
        updateProductOption(key, value) {
            
            this.arrayProductOption = this.arrayProductOption.filter(item => !item.hasOwnProperty(key));
            
            this.arrayProductOption.push({
                [key]: value
            });
        }
        
        updatePrice() {
            
            $('.quantities').slideDown(300);
            $('.totalPrices').slideDown(300);
            $('.action-cart').slideDown(300);

            const sumPrice = this.combinedProduct.price + this.selectedPrice_Size + this.selectedPrice_Color;
            const inputQuantity = $('#quantity').val();
            const totalPrice = sumPrice * inputQuantity;

            $('.price-value').text(sumPrice.toLocaleString() + ' THB');
            $('.price-total').text(totalPrice.toLocaleString() + ' THB');

            this.updateProductOption('pic', this.selectedPic ?? this.combinedProduct.pro_img);
            this.updateProductOption('description', this.combinedProduct.pro_description);
            this.updateProductOption('price', sumPrice);
            this.updateProductOption('quantity', inputQuantity);
            this.updateProductOption('total_price', totalPrice);
            this.updateProductOption('size', this.selectedSize);
            this.updateProductOption('color', this.selectedColor);
            this.updateProductOption('uom', this.combinedProduct.uom);
            this.updateProductOption('currency', this.combinedProduct.currency);
            
        }

        generateProductOptions() {
            if (this.combinedProduct) {

                this.arrayProductOption.push({
                    pic: this.combinedProduct.pro_img,
                    description: this.combinedProduct.pro_description,
                    color: '',
                    size: '',
                    price: this.combinedProduct.price,
                    quantity: '1',
                    total_price: this.combinedProduct.price,
                    uom: this.combinedProduct.uom,
                    currency: this.combinedProduct.currency
                });

                let attb_item = this.combinedProduct.attb_item;
                let attb_value = this.combinedProduct.attb_value;
                let attb_price = this.combinedProduct.attb_price;

                this.combinedVariat = attb_item.map((item, index) => ({
                    item: item,                   
                    value: attb_value[index],       
                    price: attb_price[index]        
                }));

                let productOption = ''; 

                if (
                    (Array.isArray(attb_item) && attb_item.length > 0) &&
                    (Array.isArray(attb_value) && attb_value.length > 0) &&
                    (Array.isArray(attb_price) && attb_price.length > 0)
                ) {

                    let resOpt = {};
                    for (let i = 0; i < attb_item.length; i++) {
                        if (!resOpt[attb_item[i]]) {
                            resOpt[attb_item[i]] = [];
                        }
                        resOpt[attb_item[i]].push({
                            value: attb_value[i],
                            price: attb_price[i]
                        });
                    }
        
                    let optHtml = '';
                    
                    Object.keys(resOpt).forEach(key => {
                        let optHtmlSub = ''; 
                    
                        const mappedItems = resOpt[key].map((item, index) => {
                            const target = `#pic-${index + 1}`;
                            const id = item.value;
                            const price = parseFloat(item.price);
                            return {
                                target,
                                id,
                                price
                            };
                        });
        
                        mappedItems.forEach(item => {
                            if (key === 'size') {
                                optHtmlSub += `<span class="${key} tab-switch" data-target="${item.target}" data-id="${item.id}" data-price="${item.price}">${item.id}</span>`;
                            } else if (key === 'color') {
                                optHtmlSub += `<span class="${key} tab-switch" 
                                data-target="${item.target}" 
                                data-id="${item.id}" 
                                data-price="${item.price}"
                                style="background: ${item.id};"
                                ></span>`;
                            }
                        });
        
                        if (key === 'size') {
                            optHtml += `<h5 class="sizes">Sizes: ${optHtmlSub}</h5>`;
                        } else if (key === 'color') {
                            optHtml += `<h5 class="colors">Colors: ${optHtmlSub}</h5>`;
                        }
                    });
        
                    productOption = `
                        <h5 class="price">Price: <span class="price-value">${this.combinedProduct.price.toLocaleString()} ${this.combinedProduct.currency}</span></h5>
                        ${optHtml}
                        <h6 class="quantities" style="display: none; display: flex; align-items: center;">Quantity:
                            <span class="quantity">
                                <input class="form-control" type="number" id="quantity" name="quantity" min="1" value="1" style="width: 100px;">
                            </span>
                            <span style="margin-left: 15px;">${this.combinedProduct.uom}</span>
                        </h6>
                        <br>
                        <h4 class="totalPrices price" style="display: none;">Total price: 
                            <span class="price-total"></span>
                        </h4>
                        <br>
                        <div class="action-cart" style="display: none;">
                            <button class="add-to-cart btn btn-default" type="button">Add to cart</button>
                        </div>
                    `;
                } else {
                    productOption = `
                        <h5 class="price">Price: <span class="price-value">${this.combinedProduct.price.toLocaleString()} ${this.combinedProduct.currency}</span></h5>
                        <h6 class="quantities" style="display: flex; align-items: center;">Quantity:
                            <span class="quantity">
                                <input class="form-control" type="number" id="quantity" name="quantity" min="1" value="1" style="width: 100px;">
                            </span>
                            <span style="margin-left: 15px;">${this.combinedProduct.uom}</span>
                        </h6>
                        <br>
                        <h4 class="totalPrices price">Total price: 
                            <span class="price-total">${this.combinedProduct.price.toLocaleString()} ${this.combinedProduct.currency}</span>
                        </h4>
                        <br>
                        <div class="action-cart">
                            <button class="add-to-cart btn btn-default" type="button">Add to cart</button>
                        </div>
                    `;
                }
        
                const previewPic = `
                    <div class="tab-pane active" id="pic-1">
                        <div style="text-align: center;">
                            <img src="${this.combinedProduct.pro_img ?? 'http://placehold.it/100x100'}" />
                        </div>
                    </div>
                    ${this.combinedProduct.pro_img_sub.map((img, index) => {
                        return `
                            <div class="tab-pane" id="pic-${index}">
                                <div style="text-align: center;">
                                    <img src="${img}" />
                                </div>
                            </div>
                        `;
                    }).join('')}
                `;
                

                $('.preview-pic').html(previewPic);
                $('.product-title').html('( '+this.combinedProduct.pro_name+' )');
                $('.product-description').html(this.combinedProduct.pro_description);
                $('.product-option').html(productOption);
                // this.updatePrice();

            } else {
                console.warn('Combined product data not available');
            }
        }

        setupEventListeners() {

            $('.size').on('click', (event) => {

                $('.size').removeClass('active');
                $(event.currentTarget).addClass('active');

                this.selectedSize = $(event.currentTarget).data('id');
                this.selectedPrice_Size = $(event.currentTarget).data('price');

                this.updatePrice();
            });

            $('.color').on('click', (event) => {

                $('.color').removeClass('active');
                $(event.currentTarget).addClass('active');

                this.selectedColor = $(event.currentTarget).data('id');
                this.selectedPrice_Color = $(event.currentTarget).data('price');

                this.updatePrice();
            });

            $('#quantity').on('input', (event) => {
                let value = $(event.currentTarget).val().replace(/[^0-9]/g, ''); // ลบตัวอักษรที่ไม่ใช่ตัวเลข
                let numericValue = parseInt(value, 10);
            
                // ถ้าไม่มีค่าใดๆ หรือค่าเป็น 0 ให้ตั้งค่าเป็น 1
                if (isNaN(numericValue) || numericValue === 0) {
                    numericValue = 1;
                }
            
                $(event.currentTarget).val(numericValue); // อัปเดตค่าใน input
                this.updatePrice(); // เรียกใช้ฟังก์ชัน updatePrice
            });

            $('.tab-switch').on('click', (event) => {
                event.preventDefault();
                event.stopPropagation();

                $('.tab-pane').removeClass('active');

                let targetTab = $(event.currentTarget).data('target');
                
                $(targetTab).addClass('active');

                this.selectedPic = $('.tab-pane.active').find("img").attr("src");

            });

            $('.add-to-cart').on('click', async (event) => {
                event.preventDefault();

                let checkVariat = this.checkOptionsMatch();
                const resultCheck = {};

                this.combinedVariat.forEach(entry => {
                    const { item, value } = entry;
                    if (!resultCheck[item]) {
                        resultCheck[item] = [];
                    }
                    if (!resultCheck[item].includes(value)) {
                        resultCheck[item].push(value);
                    }
                });


                const isValueInArray = (value, array) => Array.isArray(array) && array.includes(value);
                const sizeExists = resultCheck.size && isValueInArray(checkVariat.matches.size, resultCheck.size);
                const colorExists = resultCheck.color && isValueInArray(checkVariat.matches.color, resultCheck.color);

                let isValCheck = false;
                if (typeof sizeExists === 'undefined' && typeof colorExists === 'undefined') {
                    isValCheck = true;
                } else if (sizeExists === true && typeof colorExists === 'undefined') {
                    isValCheck = true;
                } else if (typeof sizeExists === 'undefined' && colorExists === true) {
                    isValCheck = true;
                } else if (sizeExists === true && colorExists === true) {
                    isValCheck = true;
                }

                
                if (!isValCheck) {

                    Swal.fire({
                        title: "กรุณาเลือกข้อมูลให้ครบถ้วน",
                        icon: "warning",
                        confirmButtonColor: "#4CAF50",
                        confirmButtonText: "ตกลง"
                    });
                    return;

                }else{

                    await this.sendData();
                }
                

            });
        }

        async sendData(){

            if(this.isMember_id > 0){

                this.arrayProductOption.sort((a, b) => {
                    let keyA = Object.keys(a)[0].toLowerCase();
                    let keyB = Object.keys(b)[0].toLowerCase();
                    return keyA < keyB ? -1 : keyA > keyB ? 1 : 0;
                });

                let mergedObject = Object.assign({}, ...this.arrayProductOption);

                // this.arrayProductOption.push({
                //     pic: this.combinedProduct.pro_img,
                //     description: this.combinedProduct.pro_description,
                //     color: '',
                //     size: '',
                //     price: this.combinedProduct.price,
                //     quantity: '1',
                //     total_price: this.combinedProduct.price,
                //     uom: this.combinedProduct.uom,
                //     currency: this.combinedProduct.currency
                // });
                
            
                var cartObj = {
                    pro_id: this.productId,
                    pic: mergedObject.pic,
                    description: mergedObject.description,
                    color: mergedObject.color,
                    price: mergedObject.price,
                    quantity: mergedObject.quantity,
                    total_price: mergedObject.total_price,
                    size: mergedObject.size,
                    uom: mergedObject.uom,
                    currency: mergedObject.currency
                };
                
                try {
                    
                    const response = await $.ajax({
                        url: 'actions/process_cart.php',
                        type: 'POST',
                        data: {
                            action: 'add_item',
                            cartData: cartObj
                        },
                        dataType: 'json'
                    });

                    Swal.fire({
                        title: "เพิ่มสินค้าในตะกร้า",
                        text: "คุณต้องการไปที่ตะกร้าสินค้าหรือไม่",
                        icon: "success",
                        showCancelButton: true,
                        confirmButtonColor: "#4CAF50",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "ไปที่ตะกร้าสินค้า",
                        cancelButtonText: "ดูสินค้าต่อ"
                    }).then((result) => {

                        if (result.isConfirmed) {
                            window.location.href = 'cart.php';
                        }else{
                            window.location.href = 'index.php';
                        }

                    });

                } catch (error) {

                    console.error('Error adding item to cart:', error);
                    Swal.fire({
                        title: "เกิดข้อผิดพลาด",
                        text: "ไม่สามารถเพิ่มสินค้าในตะกร้าได้",
                        icon: "error",
                        confirmButtonColor: "#4CAF50",
                        confirmButtonText: "ตกลง"
                    });

                }

            }else{

                $('#myBtn-channel').click();

            }
            

        }
    }

/****************************************************************************/

/****************************************************************************/

    // class getDetailProd {
    //     constructor(productId) {
    //         this.productId = productId;
    //         this.dataDetail = null;
    //         this.brandDetail = null;
    //         this.infoDeail = null;
    //         this.review = null;
    //     }

    //     async fetchDetail(){

    //         try {
    //             const response = await $.ajax({
    //                 url: 'actions/product_getData.php',
    //                 type: 'POST',
    //                 data: { 
    //                     action: 'get_Detail',
    //                     id: this.productId 

    //                 },
    //                 dataType: 'json'
    //             });

    //             if(response.data[0].pro_description != ''){
    //                 var jsonObject = JSON.parse(response.data[0].pro_description);
    //                 this.dataDetail = jsonObject;
    //             }
                

    //         } catch (error) {
    //             console.error('Error fetching product data:', error);
    //         }

    //     }

    //     async fetchBrand(){

    //         try {
    //             const response = await $.ajax({
    //                 url: 'actions/product_getData.php',
    //                 type: 'POST',
    //                 data: { 
    //                     action: 'get_Brand', 
    //                     id: this.productId
    //                 },
    //                 dataType: 'json'
    //             });

    //             // this.productData = response.productData;
    //             // this.productImgDetail = response.productImgDetail;

    //             // if (!Array.isArray(this.productData) || !Array.isArray(this.productImgDetail)) {
    //             //     throw new Error('Invalid data structure received');
    //             // }

    //         } catch (error) {
    //             console.error('Error fetching product data:', error);
    //             // Add user feedback here
    //         }

    //     }

    //     async fetchInfo(){

    //         try {
    //             const response = await $.ajax({
    //                 url: 'actions/product_getData.php',
    //                 type: 'POST',
    //                 data: { 
    //                     action: 'get_info',
    //                     id: this.productId
    //                 },
    //                 dataType: 'json'
    //             });

    //             // this.productData = response.productData;
    //             // this.productImgDetail = response.productImgDetail;

    //             // if (!Array.isArray(this.productData) || !Array.isArray(this.productImgDetail)) {
    //             //     throw new Error('Invalid data structure received');
    //             // }

    //         } catch (error) {
    //             console.error('Error fetching product data:', error);
    //             // Add user feedback here
    //         }

    //     }

    //     async fetchReview(pageRv){

    //         try {
    //             const response = await $.ajax({
    //                 url: 'actions/product_getData.php',
    //                 type: 'POST',
    //                 data: { 
    //                     action: 'get_review',
    //                     id: this.productId,
    //                     page: pageRv
    //                 },
    //                 dataType: 'json'
    //             });
                
    //             this.review = response.data;
                
    //             // if (!Array.isArray(this.review)) {
    //             //     throw new Error('Invalid data structure received');
    //             // }

    //         } catch (error) {
    //             console.error('Error fetching product data:', error);
    //             // Add user feedback here
    //         }

    //     }

    //     generateDetail(){

    //         if(this.dataDetail){

    //             $('#dep-box').empty();
    //             $('#prot-box').empty();

    //             $('#dep-box').html(this.dataDetail.dep);
    //             $('#prot-box').html(this.dataDetail.prot);

    //         }

    //     }

    //     generateReview(){

    //             let htmlReview = '';

    //             if(Array.isArray(this.review)){
    //                 this.review.forEach(element => {
    //                     $('#box-review').empty();
                        
    //                     htmlReview += `
    //                         <div class="col-12 p-3 cardlist" style="height: 150px !important;">
    //                             <div class="col-12">

    //                                 <div class="row">
    //                                     <div class="col-12 col-md-2">
    //                                         <a href="" class="w-100">
    //                                             <img src="actions/${element.file_path}" class="mx-auto d-block mb-1" style="width: 60px;">
    //                                         </a>
    //                                         <div style="display: flex; justify-content: center; gap: 10px;">
    //                                             <a href="#"></a>
    //                                         </div>
    //                                     </div>
    //                                     <div class="col-12 col-md-10" style="overflow-x: auto;">
    //                                         <div style="display: flex; align-items: center; justify-content: space-between;">
    //                                             <h6 style="margin-top: 10px;">${element.fullname}</h6>
    //                                             ${createStarRating(element.rating)}
    //                                         </div>
    //                                         <p>${element.create_date}</p>
    //                                         ${element.description}
                                                    
    //                                     </div>
    //                                 </div>
                                    
    //                             </div>
    //                         </div>
    //                     `;
                        
    //                 });
                    
    //                 $('#box-review').html(htmlReview);

    //             }
                
            
    //     }
    // };

/****************************************************************************/



function createStarRating(rating) {
    // Create a variable to store the HTML
    var htmlRating = '<div class="form-group"><div class="star-rating">';

    // Add stars with the correct class based on the rating
    for (var i = 1; i <= 5; i++) {
        if (i <= rating) {
            htmlRating += '<i class="fas fa-star" id="star' + i + '"></i>';
        } else {
            htmlRating += '<i class="far fa-star" id="star' + i + '"></i>';
        }
    }

    // Close the HTML tags
    htmlRating += '</div></div>';

    return htmlRating;
}





