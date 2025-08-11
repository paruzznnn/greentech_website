export async function fetchOrders(req) {
    try {
        const params = new URLSearchParams({ action: req });
        const url = '/newstore/service/user/user-data.php?' + params.toString();

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


export function getThaiStatus(status) {
    switch (status) {
        case 'Pending': return 'รอดำเนินการ';
        case 'Shipped': return 'กำลังจัดส่ง';
        case 'Delivered': return 'จัดส่งแล้ว';
        case 'Cancelled': return 'ยกเลิกแล้ว';
        case 'Finished': return 'สำเร็จแล้ว';
        case 'Return': return 'คืนเงิน/คืนสินค้า';
        default: return status;
    }
}

export function createOrderCard(order) {
    const orderCard = document.createElement('div');
    orderCard.className = 'order-card';

    const headerDiv = document.createElement('div');
    headerDiv.className = 'order-card-header';
    headerDiv.innerHTML = `
        <p>หมายเลขคำสั่งซื้อ: <span>${order.id}</span></p>
        <span class="status-badge status-${order.status.toLowerCase()}">
            ${getThaiStatus(order.status)}
        </span>
    `;
    orderCard.appendChild(headerDiv);

    const mainItemsDiv = document.createElement('div');
    mainItemsDiv.className = 'main-items-display';

    order.items.slice(0, 1).forEach(item => {
        const itemDiv = document.createElement('div');
        itemDiv.className = 'item-summary';
        itemDiv.innerHTML = `
            <img src="${item.imageUrl}" alt="${item.name}">
            <div class="item-info">
                <p>${item.name}</p>
                <p>จำนวน: ${item.quantity}</p>
                <p>ราคา: ฿${item.price.toFixed(2)}</p>
            </div>
            <p class="item-total-price">฿${(item.quantity * item.price).toFixed(2)}</p>
        `;
        mainItemsDiv.appendChild(itemDiv);
    });

    if (order.items.length > 1) {
        const moreItemsText = document.createElement('p');
        moreItemsText.className = 'more-items-text';
        moreItemsText.textContent = `และสินค้าอื่นๆ อีก ${order.items.length - 1} ชิ้น`;
        mainItemsDiv.appendChild(moreItemsText);
    }
    orderCard.appendChild(mainItemsDiv);

    const detailsContainer = document.createElement('div');
    detailsContainer.id = `details-${order.id}`;
    detailsContainer.className = 'order-items-details';

    order.items.forEach(item => {
        const itemDetailDiv = document.createElement('div');
        itemDetailDiv.className = 'item-detail';
        itemDetailDiv.innerHTML = `
            <img src="${item.imageUrl}" alt="${item.name}">
            <div class="item-detail-info">
                <p>${item.name}</p>
                <p>จำนวน: ${item.quantity} x ฿${item.price.toFixed(2)}</p>
            </div>
            <p class="item-detail-price">฿${(item.quantity * item.price).toFixed(2)}</p>
        `;
        detailsContainer.appendChild(itemDetailDiv);
    });
    orderCard.appendChild(detailsContainer);

    const footerDiv = document.createElement('div');
    footerDiv.className = 'order-card-footer';
    footerDiv.innerHTML = `
        <div class="order-summary-info">
            <p>วันที่สั่งซื้อ: <span>${order.date}</span></p>
            <p>ยอดรวม: <span>฿${order.total.toFixed(2)}</span></p>
        </div>
    `;

    const actionButtonsDiv = document.createElement('div');
    actionButtonsDiv.className = 'action-buttons';

    const toggleDetailsButton = document.createElement('button');
    toggleDetailsButton.className = 'toggle-details-button';
    toggleDetailsButton.textContent = 'ดูรายละเอียดทั้งหมด';
    toggleDetailsButton.onclick = () => {
        const targetDetails = document.getElementById(`details-${order.id}`);
        targetDetails.classList.toggle('open');
        toggleDetailsButton.textContent = targetDetails.classList.contains('open')
            ? 'ซ่อนรายละเอียด'
            : 'ดูรายละเอียดทั้งหมด';
    };
    actionButtonsDiv.appendChild(toggleDetailsButton);

    const primaryActionButton = document.createElement('button');
    primaryActionButton.className = 'primary-action-button';
    if (order.status === 'Delivered') {
        primaryActionButton.textContent = 'ซื้ออีกครั้ง';
    } else if (order.status === 'Shipped' || order.status === 'Pending') {
        primaryActionButton.textContent = 'ติดตามคำสั่งซื้อ';
    } else {
        primaryActionButton.textContent = 'ติดต่อผู้ขาย';
    }
    primaryActionButton.onclick = () => {
        alert(`คุณต้องการ ${primaryActionButton.textContent} สำหรับคำสั่งซื้อ ${order.id} ใช่หรือไม่?`);
    };
    actionButtonsDiv.appendChild(primaryActionButton);

    footerDiv.appendChild(actionButtonsDiv);
    orderCard.appendChild(footerDiv);

    return orderCard;
}

export function displayTabOrders(containerId) {
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
}

export function displayOrders(status, containerId, allOrders) {
    const ordersListContainer = document.getElementById(containerId);
    ordersListContainer.innerHTML = '';

    const filteredOrders = status === 'All'
        ? allOrders
        : allOrders.filter(order => order.status === status);

    if (filteredOrders.length === 0) {
        ordersListContainer.innerHTML = '<p class="no-orders-message">ไม่พบคำสั่งซื้อในสถานะนี้</p>';
    } else {
        filteredOrders.forEach(order => {
            const card = createOrderCard(order);
            ordersListContainer.appendChild(card);
        });
    }
}
