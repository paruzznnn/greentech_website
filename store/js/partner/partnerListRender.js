
const StoreApp = {
    // stores: [{
    //     name: "แทรนดาร์ อะคูสติก",
    //     logo: "https://www.trandar.com//public/img/logo_688c431f30bf3.png",
    //     categories: ["Organic", "Groceries", "Butcher Shop"],
    //     delivery: "Pickup available",
    //     distance: "7.5 mi away"
    // }
    // ],

    stores: [],
    currentPage: 1,
    itemsPerPage: 8,
    containers: {
        storesContainer: document.getElementById('stores-container'),
        gridBtn: document.getElementById("gridViewBtn"),
        listBtn: document.getElementById("listViewBtn"),
        pagination: document.getElementById("pagination"),
        showingEntries: document.getElementById("showing-entries")
    },

    loadStores(){
        const url = pathConfig.BASE_WEB + "service/partner/partner-data.php?action=getPartnerStores";
        fetch(url, {
            method: "GET",
            headers: {
                'Authorization': 'Bearer my_secure_token_123',
                "Content-Type": "application/json"
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.data) {
                this.stores = data.data;
            } else {
                console.error('API response error:', data.message);
            }
        })
        .catch(err => {
            console.error('Failed to load orders:', err);
        });
    },

    renderStores() {
        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = start + this.itemsPerPage;
        const paginatedStores = this.stores.slice(start, end);

        this.containers.storesContainer.innerHTML = "";

        paginatedStores.forEach(store => {
            const categoriesHtml = store.categories.map(cat => `<span class="category-tag">${cat}</span>`).join('');
            const storeElement = document.createElement("div");
            storeElement.classList.add("stores-card");

            storeElement.innerHTML = `
                        <img src="${store.logo}" alt="${store.name}" class="store-logo" />
                        <div>
                            <h2 class="stores-name">${store.name}</h2>
                            <div class="category-container">${categoriesHtml}</div>
                            <div class="details-container">
                                <p class="delivery-info">${store.delivery}</p>
                                <p class="distance-info">${store.distance}</p>
                            </div>
                        </div>
                    `;

            // ใช้ฟังก์ชันแยก
            storeElement.addEventListener("click", () => this.handleCardClick(store));

            this.containers.storesContainer.appendChild(storeElement);
        });

        this.containers.showingEntries.innerText =
            `Showing ${start + 1} to ${Math.min(end, this.stores.length)} of ${this.stores.length} entries`;
        this.renderPagination();
    },

    handleCardClick(store) {
        const url = pathConfig.BASE_WEB + `partner/detail/?name=${encodeURIComponent(store.name)}`;
        window.location.href = url;
    },

    renderPagination() {
        this.containers.pagination.innerHTML = "";
        const totalPages = Math.ceil(this.stores.length / this.itemsPerPage);

        const prevBtn = `<button ${this.currentPage === 1 ? "disabled" : ""} onclick="StoreApp.changePage(${this.currentPage - 1})">Prev</button>`;
        this.containers.pagination.insertAdjacentHTML("beforeend", prevBtn);

        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = `<button class="${i === this.currentPage ? "active" : ""}" onclick="StoreApp.changePage(${i})">${i}</button>`;
            this.containers.pagination.insertAdjacentHTML("beforeend", pageBtn);
        }

        const nextBtn = `<button ${this.currentPage === totalPages ? "disabled" : ""} onclick="StoreApp.changePage(${this.currentPage + 1})">Next</button>`;
        this.containers.pagination.insertAdjacentHTML("beforeend", nextBtn);
    },

    changePage(page) {
        this.currentPage = page;
        this.renderStores();
    },

    switchView(mode) {
        if (mode === 'grid') {
            this.containers.storesContainer.classList.remove("list");
            this.containers.storesContainer.classList.add("grid");
            this.containers.gridBtn.classList.add("active");
            this.containers.listBtn.classList.remove("active");
            this.itemsPerPage = 8;
        } else {
            this.containers.storesContainer.classList.remove("grid");
            this.containers.storesContainer.classList.add("list");
            this.containers.listBtn.classList.add("active");
            this.containers.gridBtn.classList.remove("active");
            this.itemsPerPage = 6;
        }
        this.currentPage = 1;
        this.renderStores();
    },

    init() {
        this.containers.gridBtn.addEventListener("click", () => this.switchView('grid'));
        this.containers.listBtn.addEventListener("click", () => this.switchView('list'));
        this.renderStores();
    },

};

StoreApp.init();
