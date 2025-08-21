export async function fetchOrders(req, call) {
    try {
        const params = new URLSearchParams({ action: req });
        const url = call + params.toString();

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer my_secure_token_123',
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        return result.data || [];

    } catch (error) {
        console.error('Fetch error:', error);
        return [];
    }
}

export const OrderListUI = {
    getThaiStatus(status) {
        switch (status) {
            case 'Pending': return 'รอดำเนินการ';
            case 'Shipped': return 'กำลังจัดส่ง';
            case 'Delivered': return 'จัดส่งแล้ว';
            case 'Cancelled': return 'ยกเลิกแล้ว';
            case 'Finished': return 'สำเร็จแล้ว';
            case 'Return': return 'คืนเงิน/คืนสินค้า';
            default: return status;
        }
    },
    displayTabOrders(containerId) {
        const tabOrdersContainer = document.getElementById(containerId);
        tabOrdersContainer.innerHTML = `
            <button class="tab-button active" data-status="All">ทั้งหมด</button>
            <button class="tab-button" data-status="Pending">ที่ต้องชำระ</button>
            <button class="tab-button" data-status="Shipped">ที่ต้องจัดส่ง</button>
            <button class="tab-button" data-status="Delivered">ที่ต้องได้รับ</button>
            <button class="tab-button" data-status="Finished">สำเร็จแล้ว</button>
            <button class="tab-button" data-status="Cancelled">ยกเลิกแล้ว</button>
            <button class="tab-button" data-status="Return">คืนเงิน/คืนสินค้า</button>
        `;
    },
    displayOrders(status, containerId, allOrders) {
        const ordersListContainer = document.getElementById(containerId);
        ordersListContainer.innerHTML = '';

        const filteredOrders = status === 'All' ? allOrders : allOrders.filter(order => order.status === status);

        if (filteredOrders.length === 0) {
            ordersListContainer.innerHTML = '<p class="no-orders-message">ไม่พบคำสั่งซื้อในสถานะนี้</p>';
        } else {
            filteredOrders.forEach(order => {
                const card = this.createOrderCard(order);
                ordersListContainer.appendChild(card);
            });
        }
    },

    //===== template literals ==========
    createOrderCard(order) {
        const mainItem = order.items[0];

        const moreItemsText = order.items.length > 1
            ? `<p class="more-items-text">และสินค้าอื่นๆ อีก ${order.items.length - 1} ชิ้น</p>`
            : '';

        const itemDetailsHTML = order.items.map(item => `
            <div class="item-detail">
                <img src="${item.imageUrl}" alt="${item.name}">
                <div class="item-detail-info">
                    <p>${item.name}</p>
                    <p>จำนวน: ${item.quantity} x ฿${item.price.toFixed(2)}</p>
                </div>
                <p class="item-detail-price">฿${(item.quantity * item.price).toFixed(2)}</p>
            </div>
        `).join('');

        let primaryActionLabel = '';
        if (order.status === 'Delivered') {
            primaryActionLabel = 'ซื้ออีกครั้ง';
        } else if (order.status === 'Shipped' || order.status === 'Pending') {
            primaryActionLabel = 'ติดตามคำสั่งซื้อ';
        } else {
            primaryActionLabel = 'ติดต่อผู้ขาย';
        }

        const cardHTML = `
        <div class="order-card" data-order-id="${order.id}">
            <div class="order-card-header">
                <p>หมายเลขคำสั่งซื้อ: <span>${order.id}</span></p>
                <span class="status-badge status-${order.status}">
                    ${this.getThaiStatus(order.status)}
                </span>
            </div>

            <div class="main-items-display">
                <div class="item-summary">
                    <img src="${mainItem.imageUrl}" alt="${mainItem.name}">
                    <div class="item-info">
                        <p>${mainItem.name}</p>
                        <p>จำนวน: ${mainItem.quantity}</p>
                        <p>ราคา: ฿${mainItem.price.toFixed(2)}</p>
                    </div>
                    <p class="item-total-price">฿${(mainItem.quantity * mainItem.price).toFixed(2)}</p>
                </div>
                ${moreItemsText}
            </div>

            <div id="details-${order.id}" class="order-items-details">
                ${itemDetailsHTML}
            </div>

            <div class="order-card-footer">
                <div class="order-summary-info">
                    <p>วันที่สั่งซื้อ: <span>${order.date}</span></p>
                    <p>ยอดรวม: <span>฿${order.total.toFixed(2)}</span></p>
                </div>
                <div class="action-buttons">
                    <button class="toggle-details-button" data-target="${order.id}">ดูรายละเอียดทั้งหมด</button>
                    <button class="primary-action-button">${primaryActionLabel}</button>
                </div>
            </div>
        </div>
    `;

        const wrapper = document.createElement('div');
        wrapper.innerHTML = cardHTML;

        const cardElement = wrapper.firstElementChild;

        const toggleButton = cardElement.querySelector('.toggle-details-button');
        toggleButton.addEventListener('click', () => {
            const detail = cardElement.querySelector(`#details-${order.id}`);
            detail.classList.toggle('open');
            toggleButton.textContent = detail.classList.contains('open') ? 'ซ่อนรายละเอียด' : 'ดูรายละเอียดทั้งหมด';
        });

        const primaryButton = cardElement.querySelector('.primary-action-button');
        primaryButton.addEventListener('click', () => {
            alert(`คุณต้องการ ${primaryButton.textContent} สำหรับคำสั่งซื้อ ${order.id} ใช่หรือไม่?`);
        });

        return cardElement;
    }

}


