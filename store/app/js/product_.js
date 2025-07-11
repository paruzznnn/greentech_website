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

    // const detailProd = new getDetailProd(productId);
    // await detailProd.fetchDetail();
    // // await detailProd.fetchBrand();
    // // await detailProd.fetchInfo();
    // await detailProd.fetchReview();
    // detailProd.generateDetail();
    // detailProd.generateReview();
    

    // $('#prod-tab button').on('click', function(event) {
    //     event.preventDefault();
    //     event.stopPropagation();
    //     // Remove 'active' class from all buttons
    //     $('#prod-tab button').removeClass('active');
    //     $(this).addClass('active');
    //     var target = $(this).data('target');

    //     // Hide all tab panes
    //     $('#prod-tabContent .prod-tab-pane').removeClass('show active');
    //     // Show the target tab pane
    //     $(target).addClass('show active');
    // });

    // $('#prod-tabContent .prod-tab-pane').each(function() {
    //     if ($(this).hasClass('active')) {
    //         var activeTab = $(this).attr('id');
    //         // if(activeTab == 'member-profile'){
    //         //     buildMemberProfile();
    //         // }
            
    //     }
    // });

    // const saveReview = (member_review, comm_review, pro_id, valRating) => {
        
    //     $.ajax({
    //         url: 'actions/member_process.php',
    //         type: 'POST',
    //         data: {
    //             action: 'saveReview',
    //             member: member_review,
    //             comment: comm_review,
    //             rating: valRating,
    //             prod_id: pro_id
    //         },
    //         dataType: 'json',
    //         success: function(response) {
    //             if (response.status == 'success') {
    //                 $('#textReview').val('');
    //                 detailProd.fetchReview().then(() => {
    //                     detailProd.generateReview();
    //                 });
    //             } else {
    //                 console.error('Failed to save review.');
    //             }
    
    //         },
    //         error: function() {
    //             console.error('Error fetching data.');
    //         }
    //     });
    
    // };


    // $('#sendReview').on('click', function(){

    //     const urlParamsID = new URLSearchParams(window.location.search);
    //     const pro_id = urlParamsID.get('id');

    //     var comm_review = $('#textReview').val();
    //     var member_review = $('#memberReview').val();

    //     if(comm_review && member_review){

    //         var htmlTemp = `
    //                             <div class="feedback">
    //                                 <div class="rating">
    //                                 <input type="radio" name="rating" id="rating-5" value="5">
    //                                 <label for="rating-5"></label>
    //                                 <input type="radio" name="rating" id="rating-4" value="4">
    //                                 <label for="rating-4"></label>
    //                                 <input type="radio" name="rating" id="rating-3" value="3">
    //                                 <label for="rating-3"></label>
    //                                 <input type="radio" name="rating" id="rating-2" value="2">
    //                                 <label for="rating-2"></label>
    //                                 <input type="radio" name="rating" id="rating-1" value="1">
    //                                 <label for="rating-1"></label>
    //                                 <div class="emoji-wrapper">
    //                                     <div class="emoji">
    //                                     <svg class="rating-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
    //                                     <circle cx="256" cy="256" r="256" fill="#ffd93b"/>
    //                                     <path d="M512 256c0 141.44-114.64 256-256 256-80.48 0-152.32-37.12-199.28-95.28 43.92 35.52 99.84 56.72 160.72 56.72 141.36 0 256-114.56 256-256 0-60.88-21.2-116.8-56.72-160.72C474.8 103.68 512 175.52 512 256z" fill="#f4c534"/>
    //                                     <ellipse transform="scale(-1) rotate(31.21 715.433 -595.455)" cx="166.318" cy="199.829" rx="56.146" ry="56.13" fill="#fff"/>
    //                                     <ellipse transform="rotate(-148.804 180.87 175.82)" cx="180.871" cy="175.822" rx="28.048" ry="28.08" fill="#3e4347"/>
    //                                     <ellipse transform="rotate(-113.778 194.434 165.995)" cx="194.433" cy="165.993" rx="8.016" ry="5.296" fill="#5a5f63"/>
    //                                     <ellipse transform="scale(-1) rotate(31.21 715.397 -1237.664)" cx="345.695" cy="199.819" rx="56.146" ry="56.13" fill="#fff"/>
    //                                     <ellipse transform="rotate(-148.804 360.25 175.837)" cx="360.252" cy="175.84" rx="28.048" ry="28.08" fill="#3e4347"/>
    //                                     <ellipse transform="scale(-1) rotate(66.227 254.508 -573.138)" cx="373.794" cy="165.987" rx="8.016" ry="5.296" fill="#5a5f63"/>
    //                                     <path d="M370.56 344.4c0 7.696-6.224 13.92-13.92 13.92H155.36c-7.616 0-13.92-6.224-13.92-13.92s6.304-13.92 13.92-13.92h201.296c7.696.016 13.904 6.224 13.904 13.92z" fill="#3e4347"/>
    //                                     </svg>
    //                                     <svg class="rating-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
    //                                     <circle cx="256" cy="256" r="256" fill="#ffd93b"/>
    //                                     <path d="M512 256A256 256 0 0 1 56.7 416.7a256 256 0 0 0 360-360c58.1 47 95.3 118.8 95.3 199.3z" fill="#f4c534"/>
    //                                     <path d="M328.4 428a92.8 92.8 0 0 0-145-.1 6.8 6.8 0 0 1-12-5.8 86.6 86.6 0 0 1 84.5-69 86.6 86.6 0 0 1 84.7 69.8c1.3 6.9-7.7 10.6-12.2 5.1z" fill="#3e4347"/>
    //                                     <path d="M269.2 222.3c5.3 62.8 52 113.9 104.8 113.9 52.3 0 90.8-51.1 85.6-113.9-2-25-10.8-47.9-23.7-66.7-4.1-6.1-12.2-8-18.5-4.2a111.8 111.8 0 0 1-60.1 16.2c-22.8 0-42.1-5.6-57.8-14.8-6.8-4-15.4-1.5-18.9 5.4-9 18.2-13.2 40.3-11.4 64.1z" fill="#f4c534"/>
    //                                     <path d="M357 189.5c25.8 0 47-7.1 63.7-18.7 10 14.6 17 32.1 18.7 51.6 4 49.6-26.1 89.7-67.5 89.7-41.6 0-78.4-40.1-82.5-89.7A95 95 0 0 1 298 174c16 9.7 35.6 15.5 59 15.5z" fill="#fff"/>
    //                                     <path d="M396.2 246.1a38.5 38.5 0 0 1-38.7 38.6 38.5 38.5 0 0 1-38.6-38.6 38.6 38.6 0 1 1 77.3 0z" fill="#3e4347"/>
    //                                     <path d="M380.4 241.1c-3.2 3.2-9.9 1.7-14.9-3.2-4.8-4.8-6.2-11.5-3-14.7 3.3-3.4 10-2 14.9 2.9 4.9 5 6.4 11.7 3 15z" fill="#fff"/>
    //                                     <path d="M242.8 222.3c-5.3 62.8-52 113.9-104.8 113.9-52.3 0-90.8-51.1-85.6-113.9 2-25 10.8-47.9 23.7-66.7 4.1-6.1 12.2-8 18.5-4.2 16.2 10.1 36.2 16.2 60.1 16.2 22.8 0 42.1-5.6 57.8-14.8 6.8-4 15.4-1.5 18.9 5.4 9 18.2 13.2 40.3 11.4 64.1z" fill="#f4c534"/>
    //                                     <path d="M155 189.5c-25.8 0-47-7.1-63.7-18.7-10 14.6-17 32.1-18.7 51.6-4 49.6 26.1 89.7 67.5 89.7 41.6 0 78.4-40.1 82.5-89.7A95 95 0 0 0 214 174c-16 9.7-35.6 15.5-59 15.5z" fill="#fff"/>
    //                                     <path d="M115.8 246.1a38.5 38.5 0 0 0 38.7 38.6 38.5 38.5 0 0 0 38.6-38.6 38.6 38.6 0 1 0-77.3 0z" fill="#3e4347"/>
    //                                     <path d="M131.6 241.1c3.2 3.2 9.9 1.7 14.9-3.2 4.8-4.8 6.2-11.5 3-14.7-3.3-3.4-10-2-14.9 2.9-4.9 5-6.4 11.7-3 15z" fill="#fff"/>
    //                                     </svg>
    //                                     <svg class="rating-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
    //                                     <circle cx="256" cy="256" r="256" fill="#ffd93b"/>
    //                                     <path d="M512 256A256 256 0 0 1 56.7 416.7a256 256 0 0 0 360-360c58.1 47 95.3 118.8 95.3 199.3z" fill="#f4c534"/>
    //                                     <path d="M336.6 403.2c-6.5 8-16 10-25.5 5.2a117.6 117.6 0 0 0-110.2 0c-9.4 4.9-19 3.3-25.6-4.6-6.5-7.7-4.7-21.1 8.4-28 45.1-24 99.5-24 144.6 0 13 7 14.8 19.7 8.3 27.4z" fill="#3e4347"/>
    //                                     <path d="M276.6 244.3a79.3 79.3 0 1 1 158.8 0 79.5 79.5 0 1 1-158.8 0z" fill="#fff"/>
    //                                     <circle cx="340" cy="260.4" r="36.2" fill="#3e4347"/>
    //                                     <g fill="#fff">
    //                                         <ellipse transform="rotate(-135 326.4 246.6)" cx="326.4" cy="246.6" rx="6.5" ry="10"/>
    //                                         <path d="M231.9 244.3a79.3 79.3 0 1 0-158.8 0 79.5 79.5 0 1 0 158.8 0z"/>
    //                                     </g>
    //                                     <circle cx="168.5" cy="260.4" r="36.2" fill="#3e4347"/>
    //                                     <ellipse transform="rotate(-135 182.1 246.7)" cx="182.1" cy="246.7" rx="10" ry="6.5" fill="#fff"/>
    //                                     </svg>
    //                                     <svg class="rating-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
    //                                 <circle cx="256" cy="256" r="256" fill="#ffd93b"/>
    //                                 <path d="M407.7 352.8a163.9 163.9 0 0 1-303.5 0c-2.3-5.5 1.5-12 7.5-13.2a780.8 780.8 0 0 1 288.4 0c6 1.2 9.9 7.7 7.6 13.2z" fill="#3e4347"/>
    //                                 <path d="M512 256A256 256 0 0 1 56.7 416.7a256 256 0 0 0 360-360c58.1 47 95.3 118.8 95.3 199.3z" fill="#f4c534"/>
    //                                 <g fill="#fff">
    //                                 <path d="M115.3 339c18.2 29.6 75.1 32.8 143.1 32.8 67.1 0 124.2-3.2 143.2-31.6l-1.5-.6a780.6 780.6 0 0 0-284.8-.6z"/>
    //                                 <ellipse cx="356.4" cy="205.3" rx="81.1" ry="81"/>
    //                                 </g>
    //                                 <ellipse cx="356.4" cy="205.3" rx="44.2" ry="44.2" fill="#3e4347"/>
    //                                 <g fill="#fff">
    //                                 <ellipse transform="scale(-1) rotate(45 454 -906)" cx="375.3" cy="188.1" rx="12" ry="8.1"/>
    //                                 <ellipse cx="155.6" cy="205.3" rx="81.1" ry="81"/>
    //                                 </g>
    //                                 <ellipse cx="155.6" cy="205.3" rx="44.2" ry="44.2" fill="#3e4347"/>
    //                                 <ellipse transform="scale(-1) rotate(45 454 -421.3)" cx="174.5" cy="188" rx="12" ry="8.1" fill="#fff"/>
    //                             </svg>
    //                                     <svg class="rating-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
    //                                     <circle cx="256" cy="256" r="256" fill="#ffd93b"/>
    //                                     <path d="M512 256A256 256 0 0 1 56.7 416.7a256 256 0 0 0 360-360c58.1 47 95.3 118.8 95.3 199.3z" fill="#f4c534"/>
    //                                     <path d="M232.3 201.3c0 49.2-74.3 94.2-74.3 94.2s-74.4-45-74.4-94.2a38 38 0 0 1 74.4-11.1 38 38 0 0 1 74.3 11.1z" fill="#e24b4b"/>
    //                                     <path d="M96.1 173.3a37.7 37.7 0 0 0-12.4 28c0 49.2 74.3 94.2 74.3 94.2C80.2 229.8 95.6 175.2 96 173.3z" fill="#d03f3f"/>
    //                                     <path d="M215.2 200c-3.6 3-9.8 1-13.8-4.1-4.2-5.2-4.6-11.5-1.2-14.1 3.6-2.8 9.7-.7 13.9 4.4 4 5.2 4.6 11.4 1.1 13.8z" fill="#fff"/>
    //                                     <path d="M428.4 201.3c0 49.2-74.4 94.2-74.4 94.2s-74.3-45-74.3-94.2a38 38 0 0 1 74.4-11.1 38 38 0 0 1 74.3 11.1z" fill="#e24b4b"/>
    //                                     <path d="M292.2 173.3a37.7 37.7 0 0 0-12.4 28c0 49.2 74.3 94.2 74.3 94.2-77.8-65.7-62.4-120.3-61.9-122.2z" fill="#d03f3f"/>
    //                                     <path d="M411.3 200c-3.6 3-9.8 1-13.8-4.1-4.2-5.2-4.6-11.5-1.2-14.1 3.6-2.8 9.7-.7 13.9 4.4 4 5.2 4.6 11.4 1.1 13.8z" fill="#fff"/>
    //                                     <path d="M381.7 374.1c-30.2 35.9-75.3 64.4-125.7 64.4s-95.4-28.5-125.8-64.2a17.6 17.6 0 0 1 16.5-28.7 627.7 627.7 0 0 0 218.7-.1c16.2-2.7 27 16.1 16.3 28.6z" fill="#3e4347"/>
    //                                     <path d="M256 438.5c25.7 0 50-7.5 71.7-19.5-9-33.7-40.7-43.3-62.6-31.7-29.7 15.8-62.8-4.7-75.6 34.3 20.3 10.4 42.8 17 66.5 17z" fill="#e24b4b"/>
    //                                     </svg>
    //                                     <svg class="rating-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
    //                                     <g fill="#ffd93b">
    //                                         <circle cx="256" cy="256" r="256"/>
    //                                         <path d="M512 256A256 256 0 0 1 56.8 416.7a256 256 0 0 0 360-360c58 47 95.2 118.8 95.2 199.3z"/>
    //                                     </g>
    //                                     <path d="M512 99.4v165.1c0 11-8.9 19.9-19.7 19.9h-187c-13 0-23.5-10.5-23.5-23.5v-21.3c0-12.9-8.9-24.8-21.6-26.7-16.2-2.5-30 10-30 25.5V261c0 13-10.5 23.5-23.5 23.5h-187A19.7 19.7 0 0 1 0 264.7V99.4c0-10.9 8.8-19.7 19.7-19.7h472.6c10.8 0 19.7 8.7 19.7 19.7z" fill="#e9eff4"/>
    //                                     <path d="M204.6 138v88.2a23 23 0 0 1-23 23H58.2a23 23 0 0 1-23-23v-88.3a23 23 0 0 1 23-23h123.4a23 23 0 0 1 23 23z" fill="#45cbea"/>
    //                                     <path d="M476.9 138v88.2a23 23 0 0 1-23 23H330.3a23 23 0 0 1-23-23v-88.3a23 23 0 0 1 23-23h123.4a23 23 0 0 1 23 23z" fill="#e84d88"/>
    //                                     <g fill="#38c0dc">
    //                                         <path d="M95.2 114.9l-60 60v15.2l75.2-75.2zM123.3 114.9L35.1 203v23.2c0 1.8.3 3.7.7 5.4l116.8-116.7h-29.3z"/>
    //                                     </g>
    //                                     <g fill="#d23f77">
    //                                         <path d="M373.3 114.9l-66 66V196l81.3-81.2zM401.5 114.9l-94.1 94v17.3c0 3.5.8 6.8 2.2 9.8l121.1-121.1h-29.2z"/>
    //                                     </g>
    //                                     <path d="M329.5 395.2c0 44.7-33 81-73.4 81-40.7 0-73.5-36.3-73.5-81s32.8-81 73.5-81c40.5 0 73.4 36.3 73.4 81z" fill="#3e4347"/>
    //                                     <path d="M256 476.2a70 70 0 0 0 53.3-25.5 34.6 34.6 0 0 0-58-25 34.4 34.4 0 0 0-47.8 26 69.9 69.9 0 0 0 52.6 24.5z" fill="#e24b4b"/>
    //                                     <path d="M290.3 434.8c-1 3.4-5.8 5.2-11 3.9s-8.4-5.1-7.4-8.7c.8-3.3 5.7-5 10.7-3.8 5.1 1.4 8.5 5.3 7.7 8.6z" fill="#fff" opacity=".2"/>
    //                                     </svg>
    //                                     </div>
    //                                 </div>
    //                                 </div>
    //                             </div>
    //                         `;

    //         Swal.fire({
    //             html: htmlTemp,
    //             showConfirmButton: true,
    //             showCloseButton: false,
    //             showCancelButton: true,
    //             confirmButtonColor: "#4CAF50",
    //             cancelButtonColor: "#d33",
    //             preConfirm: () => {
    //                 let ratingValue = $('input[name="rating"]:checked');
    //                 if (ratingValue.length > 0) {
    //                     var valRating = ratingValue.val();
    //                     saveReview(member_review, comm_review, pro_id, valRating);
    //                     return true;
    //                 } else {
    //                     Swal.showValidationMessage('Please select a rating!');
    //                     return false;
    //                 }
    //             }
    //         });

    //     }else if(!comm_review){
    //     }else if(!member_review){
    //         $('#myBtn-channel').click();
    //     }

    // });

    // var pageRv = 1;
    // $('.loadScroll').on('scroll', function() {
    //     var $this = $(this);
        
    //     var scrollTop = $this.scrollTop(); 
    //     var elementHeight = $this.outerHeight(); 
    //     var containerHeight = $this.height();
    //     var bottomPosition = scrollTop + containerHeight;

    //     var rounded = Math.round(bottomPosition);

    //     if(rounded == 1605){
    //         pageRv ++;
    //         detailProd.fetchReview(pageRv).then(() => {
    //             detailProd.generateReview();
    //         });

    //     }else if(scrollTop == 0 && pageRv > 0){
    //         pageRv --;
    //         detailProd.fetchReview(pageRv).then(() => {
    //             detailProd.generateReview();
    //         });
            
    //     }
        
        
    // });


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





