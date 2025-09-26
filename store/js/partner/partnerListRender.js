import { redirectGet } from "../centerHandler.js";
const StoresApp = {
    stores: [],
    currentPage: 1,
    itemsPerPage: 8,
    pageStart: 0,
    pageEnd: 0,

    containers: {
        storesContainer: document.getElementById('stores-container'),
        gridBtn: document.getElementById("gridViewBtn"),
        listBtn: document.getElementById("listViewBtn"),
        pagination: document.getElementById("pagination"),
        showingEntries: document.getElementById("showing-entries")
    },

    loadStores() {
        this.pageStart = (this.currentPage - 1) * this.itemsPerPage;
        this.pageEnd = this.pageStart + this.itemsPerPage;
        const params = new URLSearchParams({
            action: 'getPartnerStores',
            start: this.pageStart,
            end: this.pageEnd
        });
        const url = window.AppConfig.BASE_WEB + "service/partner/partner-data.php?" + params.toString();
        fetch(url, {
            method: "GET",
            headers: {
                'Authorization': 'Bearer my_secure_token_123',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.data) {
                this.stores = data.data;
                this.renderStores();
            } else {
                console.error('API response error:', data.message);
            }
        })
        .catch(err => {
            console.error('Failed to load stores:', err);
        });
    },

    renderStores() {
        this.containers.storesContainer.innerHTML = "";

        this.stores.forEach(store => {

            // console.log('store', store);
            // const categoriesHtml = store.categories.map(cat => `<span class="category-tag">${cat}</span>`).join('');
            const storeElement = document.createElement("div");
            storeElement.classList.add("stores-card");

            // <div class="category-container">${categoriesHtml}</div>
            // <div class="details-container">
            //     <p class="delivery-info">${store.delivery}</p>
            //     <p class="distance-info">${store.distance}</p>
            // </div>

            storeElement.innerHTML = `
                <img src="${store.logo_url}" alt="${store.name}" class="store-logo" />
                <div>
                    <h2 class="stores-name">${store.name}</h2>
                </div>
            `;

            storeElement.addEventListener("click", () => this.handleCardClick(store));
            this.containers.storesContainer.appendChild(storeElement);
        });

        const totalEntries = (this.pageEnd > this.pageStart + this.stores.length) ? this.pageStart + this.stores.length : this.pageEnd;
        this.containers.showingEntries.innerText =
            `Showing ${this.pageStart + 1} to ${totalEntries} entries`;

        this.renderPagination();
    },

    handleCardClick(store) {
        const url = window.AppConfig.BASE_WEB + `partner/detail/`;
        redirectGet(url,
            {
                store: encodeURIComponent(store.store_id),
                name: encodeURIComponent(store.name)
            }
        );
    },

    renderPagination() {
        this.containers.pagination.innerHTML = "";

        const hasNextPage = this.stores.length === this.itemsPerPage;
        const totalPages = hasNextPage ? this.currentPage + 1 : this.currentPage;

        const prevBtn = `<button ${this.currentPage === 1 ? "disabled" : ""} onclick="StoresApp.changePage(${this.currentPage - 1})">Prev</button>`;
        this.containers.pagination.insertAdjacentHTML("beforeend", prevBtn);

        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = `<button class="${i === this.currentPage ? "active" : ""}" onclick="StoresApp.changePage(${i})">${i}</button>`;
            this.containers.pagination.insertAdjacentHTML("beforeend", pageBtn);
        }

        const nextBtn = `<button ${!hasNextPage ? "disabled" : ""} onclick="StoresApp.changePage(${this.currentPage + 1})">Next</button>`;
        this.containers.pagination.insertAdjacentHTML("beforeend", nextBtn);
    },

    changePage(page) {
        this.currentPage = page;
        this.loadStores();
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
        this.loadStores();
    },

    init() {
        this.containers.gridBtn.addEventListener("click", () => this.switchView('grid'));
        this.containers.listBtn.addEventListener("click", () => this.switchView('list'));
        this.loadStores();
    },
};

document.addEventListener("DOMContentLoaded", () => {
    StoresApp.init();
});



