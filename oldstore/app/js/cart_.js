$(document).ready(function() {
    // Initial cart build
    buildCart(cartArr); 
});

const buildCart = (cartArr) => {

    const htmlIsCart = [];
    htmlIsCart.push(`<div class="card border-light bg-white card proviewcard shadow-sm">
                        <div class="card-body" style="margin: 0 4%;">`);

    if (Object.keys(cartArr).length > 0) {
        let totalItems = 0;
        let totalProducts = 0;
        let totalPrice = 0;
        let vat = 0;
        let member_id = null;
        let currency = null;

        Object.entries(cartArr).forEach(([itemKey, item]) => {
            totalItems += parseInt(item.quantity);
            totalProducts++;
            totalPrice += parseFloat(item.total_price);
            member_id = item.isMember;
            currency = item.currency;

            htmlIsCart.push(`<div class="col-12 p-3 cardlist">
                                <div style="float: inline-end;">
                                    <button type="button" class="remove-btn" data-key="${itemKey}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-12 col-md-4">
                                            <a href="">
                                                <img src="${item.pic}" class="mx-auto d-block mb-1" style="width: 100px !important;">
                                            </a>
                                            <div style="display: flex; justify-content: center; gap: 10px;">
                                                <a href="#">${itemKey}</a>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-8" style="overflow-x: auto;">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>รหัส</th>
                                                        <th>ราคา</th>
                                                        <th>จำนวน</th>
                                                        <th class="text-center">หน่วย</th>
                                                        <th class="text-end" style="min-width: 100px;">ราคารวม</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>${itemKey}</td>
                                                        <td>${parseFloat(item.price).toLocaleString()}</td>
                                                        <td>
                                                            <span>
                                                                <input type="hidden" name="item_key" value="${itemKey}">
                                                                <input type="number" style="width: 70px;" 
                                                                class="form-control quantity update-btn" 
                                                                name="quantity" 
                                                                value="${item.quantity}" 
                                                                data-key="${itemKey}" 
                                                                min="1" required>
                                                            </span>
                                                        </td>
                                                        <td class="text-center">${item.uom}</td>
                                                        <td class="text-end">${parseFloat(item.total_price).toLocaleString()}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="5">
                                                            <div style="padding: 15px 0px;">
                                                                <span>รายละเอียดเพิ่มเติม: </span> ${item.description}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>`);
        });

        vat = totalPrice * 0.07;  // 7% VAT
        let totalPriceWithVat = totalPrice + vat;

        htmlIsCart.push(`</div>
                        <div class="card-footer border-light cart-panel-foo-fix">
                            <div class="card-footer-total mt-1">
                                <span data-key-lang="numberofitems" lang="US">Number of items</span>
                                <span>${parseFloat(totalProducts)}</span>
                            </div>
                            <div class="card-footer-total mt-1">
                                <span data-key-lang="numberofproducts" lang="US">Number of products</span>
                                <span>${parseFloat(totalItems)}</span>
                            </div>
                            <div class="card-footer-total mb-2">
                                <span data-key-lang="sumofmoney" lang="US">Sum of money</span>
                                <span>${formatNumberWithComma(totalPrice.toFixed(2))}</span>
                            </div>
                            <div class="card-footer-total mb-2">
                                <span data-key-lang="vat" lang="US">VAT</span>
                                <span>${formatNumberWithComma(vat.toFixed(2))}</span>
                            </div>
                            <div class="card-footer-total mb-2">
                                <span data-key-lang="totalamount" lang="US">Total amount</span>
                                <span>${formatNumberWithComma(totalPriceWithVat.toFixed(2))} ${currency}</span>
                            </div>`);

                            htmlIsCart.push(`
                                <button 
                                    data-key-lang="proceed" lang="US"
                                    type="button" 
                                    class="btn btn-success" 
                                    onclick="${member_id ? 'directShipping(\'shipping.php\', \'\');' : 'getDetail(\'\', \'\');'}">
                                    ดำเนินการต่อ
                                </button>
                                <button 
                                    data-key-lang="cancel" lang="US"
                                    id="clear-cart" 
                                    class="btn btn-danger">
                                    Cancel
                                </button>
                            `);
    }else{
        htmlIsCart.push(`<div>There is no product information.</div>`);
    }

    $('#isCart').html(htmlIsCart.join(''));
    initializeLanguageSwitcher('#language-select', '[lang]', '../api/languages/langs.json');
    rebindEventListeners();
};

function rebindEventListeners() {

    $('.update-btn').off('input').on('input', debounce(async function(event) {
        const itemKey = $(this).data('key');
        let quantity = $(this).val().replace(/[^0-9]/g, ''); // ลบตัวอักษรที่ไม่ใช่ตัวเลข
        quantity = parseInt(quantity, 10);
    
        // ถ้าไม่มีค่าใดๆ หรือค่าเป็น 0 ให้ตั้งค่าเป็น 1
        if (isNaN(quantity) || quantity === 0) {
            quantity = 1;
        }
    
        $(this).val(quantity); // อัปเดตค่าใน input
    
        const actionCart = { action: 'update_quantity', item_key: itemKey, quantity: quantity };
        await handleCartAction(actionCart);
    }));

    $('.remove-btn').off('click').on('click', async function() {
        const itemKey = $(this).data('key');
        if (itemKey) {
            const actionCart = { action: 'remove_item', item_key: itemKey };
            await handleCartAction(actionCart);
        }
    });

    $('#clear-cart').off('click').on('click', async function() {
        const actionCart = { action: 'clear_cart' };
        await handleCartAction(actionCart);
    });
}

async function handleCartAction(actionCart) {
    try {
        await updateQuantity(actionCart);
        const response = await updateQuantity(actionCart);
        // console.log(`${actionCart.action} Response`, response);
        buildCart(response.data);
    } catch (error) {
        alert(`An error occurred while ${actionCart.action.replace('_', ' ')} the item.`);
    }
}

async function updateQuantity(actionCart) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: 'actions/process_cart.php',
            type: 'POST',
            data: actionCart,
            dataType: 'json',
            success: resolve,
            error: () => reject(new Error('Failed to update cart')),
        });
    });
}

const directShipping = (baseUrl, id) => {
    const url = baseUrl && id ? `${baseUrl}?id=${id}` : baseUrl;
    window.location.href = url;
};

// Debounce function to limit the rate of function execution
function debounce(func, delay = 300) {
    let timeoutId;
    return function(...args) {
        if (timeoutId) {
            clearTimeout(timeoutId);
        }
        timeoutId = setTimeout(() => {
            func.apply(this, args);
        }, delay);
    };
}
