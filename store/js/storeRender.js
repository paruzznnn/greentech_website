import { redirectGet, showNotification } from './formHandler.js';
import { formatPrice } from './formatHandler.js';

// ---------- API -----------------------------
export async function fetchIndexData(req, call) {
  try {
    const params = new URLSearchParams({ action: req });
    const url = call + params.toString();

    const response = await fetch(url, {
      method: "GET",
      headers: {
        Authorization: "Bearer my_secure_token_123",
        "Content-Type": "application/json",
      },
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return await response.json();
  } catch (error) {
    console.error("Fetch error:", error);
    return { data: [] };
  }
}

// ---------- RENDER SECTIONS -----------------
export function renderSections(targetId, data) {
  const createContentHtml = (section) => {
    const titleHtml = section.title ? `<h5>${section.title}</h5>` : "";
    const detailHtml = section.detail ? `<p>${section.detail}</p>` : "";
    const baseContent = section.isSlide
      ? `<div id="${section.carouselId}" class="owl-carousel owl-theme"></div>`
      : `<div id="${section.carouselId}"></div>`;
    return `${titleHtml}${detailHtml}${baseContent}`;
  };

  const getWrapperStyle = (sectionId) => {
    // section ที่ต้องการกล่องสีขาว border-radius
    const styledSections = ["section_products", "section_brand"];
    return styledSections.includes(sectionId)
      ? `style="background:#fff;padding:0.5rem;border-radius:3px;"`
      : `style="background:#fff;padding:0.5rem;border-radius:3px;"`;
  };

  const html = data
    .map((section) => {
      const sectionClass = section.class || "";
      const contentHtml = createContentHtml(section);
      const wrapperStyle = getWrapperStyle(section.id);

      return `
        <section id="${section.id}" class="${sectionClass}">
          <div class="container">
            <div ${wrapperStyle}>${contentHtml}</div>
          </div>
        </section>
      `;
    })
    .join("");

  document.querySelector(targetId).innerHTML = html;
}


// ---------- RENDER INTRODUCE ----------------
export function renderIntroduce(containerId, items) {
  const container = document.querySelector(containerId);
  if (!container) {
    console.error(`Container with id "${containerId}" not found.`);
    return;
  }

  container.innerHTML = "";

  items.forEach((item) => {
    const intdDiv = document.createElement("div");
    const products = item.products || [];
    const itemsPerPage = 1;
    let currentPage = 1;
    const totalPages = Math.ceil(products.length / itemsPerPage);

    const carouselItemsHtml =
      item.carousel
        ?.map(
          (img, i) => `
      <div class="intd-carousel-item ${i === 0 ? "active" : "hidden"}">
        <img src="${img.image}" alt="${img.alt}" title="${img.label}">
      </div>
    `
        )
        .join("") || "";

    const categoryButtonsHtml =
      item.categories
        ?.map(
          (category) => `
      <a href="${category.link}" class="intd-option-button">
        ${category.icon} ${category.title}
      </a>
    `
        )
        .join("") || "";

    intdDiv.innerHTML = `
      <div class="intd-main-container">
        <div class="intd-left-column">
          <div class="intd-slideshow-wrapper">
            <div class="intd-carousel-container">
              ${carouselItemsHtml}
            </div>
            <button class="intd-nav-button intd-prev-btn" aria-label="Previous slide">
              <i class="fas fa-angle-left"></i>
            </button>
            <button class="intd-nav-button intd-next-btn" aria-label="Next slide">
              <i class="fas fa-angle-right"></i>
            </button>
            <div class="intd-dots-container"></div>
          </div>
          <div class="intd-option-buttons-section">
            ${categoryButtonsHtml}
          </div>
        </div>

        <div class="intd-right-column">
          <div class="intd-products-container"></div>
          <div class="intd-pagination-controls mt-3"></div>
        </div>
      </div>
    `;

    container.appendChild(intdDiv);

    const productListContainer = intdDiv.querySelector(
      ".intd-products-container"
    );
    const paginationControls = intdDiv.querySelector(
      ".intd-pagination-controls"
    );

    const handlePageChange = (newPage) => {
      currentPage = newPage;
      renderProductsPage({
        products,
        productListContainer,
        paginationControls,
        itemsPerPage,
        currentPage,
        totalPages,
        onPageChange: handlePageChange,
      });
    };

    handlePageChange(currentPage);
    setupCarousel(intdDiv);
  });
}

function renderProductsPage({
  products,
  productListContainer,
  paginationControls,
  itemsPerPage,
  currentPage,
  totalPages,
  onPageChange,
}) {
  const start = (currentPage - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  const currentItems = products.slice(start, end);

  const productItemsHtml = currentItems
    .map(
      (product) => `
    <div class="intd-product-card">
      <h2 class="intd-card-title">${product.title || ""}</h2>
      <p class="intd-card-subtitle">โดย: ${product.subtitle || ""}</p>
      <img src="${product.image || ""}" alt="${product.title || ""}">
      <div class="intd-price-info">
        <span class="intd-current-price">${product.price?.toLocaleString() || "0"
        }</span>
        <span class="intd-old-price">${product.oldPrice?.toLocaleString() || ""
        }</span>
        <span class="intd-discount-text">${product.discount || ""}</span>
      </div>
    </div>
  `
    )
    .join("");

  productListContainer.innerHTML = productItemsHtml;

  paginationControls.innerHTML = `
    <button class="intd-page-btn prev-btn" ${currentPage === 1 ? "disabled" : ""
    }>ก่อนหน้า</button>
    <span class="intd-page-info">หน้า ${currentPage} / ${totalPages}</span>
    <button class="intd-page-btn next-btn" ${currentPage === totalPages ? "disabled" : ""
    }>ถัดไป</button>
  `;

  paginationControls
    .querySelector(".prev-btn")
    ?.addEventListener("click", () => {
      if (currentPage > 1) {
        onPageChange(currentPage - 1);
      }
    });

  paginationControls
    .querySelector(".next-btn")
    ?.addEventListener("click", () => {
      if (currentPage < totalPages) {
        onPageChange(currentPage + 1);
      }
    });
}

function setupCarousel(container) {
  const carouselItems = Array.from(
    container.querySelectorAll(".intd-carousel-item")
  );
  const prevBtn = container.querySelector(".intd-prev-btn");
  const nextBtn = container.querySelector(".intd-next-btn");
  const dotsContainer = container.querySelector(".intd-dots-container");

  let currentIndex = 0;
  let autoPlayInterval;

  function showSlide(index) {
    carouselItems.forEach((item, i) => {
      item.classList.toggle("active", i === index);
      item.classList.toggle("hidden", i !== index);
    });
    updateDots(index);
  }

  function nextSlide() {
    currentIndex = (currentIndex + 1) % carouselItems.length;
    showSlide(currentIndex);
  }

  function prevSlide() {
    currentIndex =
      (currentIndex - 1 + carouselItems.length) % carouselItems.length;
    showSlide(currentIndex);
  }

  function generateDots() {
    dotsContainer.innerHTML = "";
    carouselItems.forEach((_, index) => {
      const dot = document.createElement("button");
      dot.classList.add("intd-carousel-dot");
      dot.addEventListener("click", () => {
        currentIndex = index;
        showSlide(index);
        resetAutoPlay();
      });
      dotsContainer.appendChild(dot);
    });
    updateDots(currentIndex);
  }

  function updateDots(index) {
    [...dotsContainer.children].forEach((dot, i) => {
      dot.classList.toggle("active", i === index);
    });
  }

  function startAutoPlay() {
    autoPlayInterval = setInterval(nextSlide, 5000);
  }

  function stopAutoPlay() {
    clearInterval(autoPlayInterval);
  }

  function resetAutoPlay() {
    stopAutoPlay();
    startAutoPlay();
  }

  prevBtn?.addEventListener("click", () => {
    prevSlide();
    resetAutoPlay();
  });

  nextBtn?.addEventListener("click", () => {
    nextSlide();
    resetAutoPlay();
  });

  generateDots();
  showSlide(currentIndex);
  startAutoPlay();
}

function addCart(product) {
  const existingCart =
    JSON.parse(localStorage.getItem("shoppingCart")) || [];
  const productIndex = existingCart.findIndex(
    (item) => item.id === product.productId
  );
  if (productIndex !== -1) {
    // existingCart[productIndex].quantity += 1;
    showNotification('เพิ่มสินค้าลงตะกร้าแล้ว', 'success');
  } else {
    existingCart.push({
      id: product.productId,
      name: product.productName,
      price: product.productPrice,
      quantity: 1,
      imageUrl: product.image,
    });
    showNotification('เพิ่มสินค้าลงตะกร้าแล้ว', 'success');
  }
  localStorage.setItem("shoppingCart", JSON.stringify(existingCart));
}

function addWishlist(product) {
  const existingWishlist =
    JSON.parse(localStorage.getItem("likedProducts")) || [];
  const productIndex = existingWishlist.findIndex(
    (item) => item.id === product.productId
  );
  if (productIndex !== -1) {
    showNotification('กดถูกใจไว้แล้ว', 'success');
  } else {
    existingWishlist.push({
      id: product.productId,
      name: product.productName,
      price: product.productPrice,
      imageUrl: product.image,
    });
    showNotification('กดถูกใจไว้แล้ว', 'success');
  }

  localStorage.setItem("likedProducts", JSON.stringify(existingWishlist));
}

// ---------- RENDER INTRODUCE ----------------

export function renderBanners(containerId, banners) {
  const container = document.querySelector(containerId);
  if (!container) {
    console.error(`Container with id "${containerId}" not found.`);
    return;
  }

  container.innerHTML = "";

  banners.forEach((item) => {
    const bannerDiv = document.createElement("div");
    bannerDiv.classList.add("banner");

    bannerDiv.innerHTML = `
      <img src="${item.image}" alt="">
      <div class="banner-text">
        <div class="banner-content">
          <h1>${item.title}</h1>
          <p>${item.description}</p>
          <a href="${item.path}" class="banner-button">
            <span style="padding: 5px 8px; background: #f18b20; border-radius: 2rem;">
              <i class="bi bi-box-seam"></i>
            </span>
            <span>${item.buttonText}</span>
          </a>
        </div>
      </div>
    `;
    container.appendChild(bannerDiv);
  });
}

export function renderCarouselSM(selector, items) {
  const container = document.querySelector(selector);
  items.forEach((item) => {
    const div = document.createElement("div");
    div.classList.add("item");
    div.innerHTML = `
      <div class="crs-sm-card">
        <div><img src="${item.image}" alt="" class="crs-sm-card-image"/></div>
        <div>${item.title}</div>
      </div>
    `;
    container.appendChild(div);
  });

  $(selector).owlCarousel({
    loop: false,
    margin: 10,
    nav: true,
    dots: false,
    autoWidth: true,
    touchDrag: true,
    mouseDrag: true,
    onInitialized: function(event) {
      $(event.target).removeClass("owl-drag");
    }
  });
}

export function renderCarouselMD(selector, items, config) {
  const container = document.querySelector(selector);
  items.forEach((item) => {
    const badgesWithIcon = item.productBadges
      .map((badge) => `<span class="badges-tag">${badge}</span> `)
      .join("");

    // <a href="#"><i class="fa fa-share-alt"></i> Share</a>
    // <a href="#"><i class="fas fa-copy"></i> Copy URL</a>
    // /e-store/partner/?id=${1}

    const div = document.createElement("div");
    div.classList.add("item");
    div.setAttribute("data-product-id", item.productId);
    div.innerHTML = `
      <div class="store-card">
        <div class="store-card-row">
          <div class="store-card-header">
            <div class="box-card-header">
              <div>
                <img class="store-card-logo" src="${item.compLogo}" alt="">
              </div>
              <div>
                <strong>${item.compName}</strong>
                <span>
                <i class="bi bi-globe-americas"></i>
                ${item.dateCreated}
                </span>
              </div>
              <div>
              <div class="store-card-dropdown">
                <i class="bi bi-three-dots-vertical"></i>
                <div class="store-card-dropdown-content">
                  <a href="https://www.trandar.com/app/index.php" target="_blank"><i class="fas fa-share"></i> เจ้าของสินค้า</a>
                </div>
              </div>
              </div>
            </div>
          </div>
          <div class="img-view-detail">
            <img src="${item.image}" alt="" class="store-card-image" />
          </div>
          <div class="store-card-body">
            <div>
              <span style="font-size: 12px;"><i class="bi bi-layers"></i><span>
              <small style="font-size: 12px;">${item.category}</small>
            </div>
            <strong>${item.productName}</strong>
          </div>
          <ul class="store-list-group">
            <li class="store-list-item">
              <div class="line-clamp">
                ${item.productDetail}
              </div>
            </li>
            <li class="store-list-item">
              <div>
                ${badgesWithIcon}
              </div>
            </li>
          </ul>
          <div class="store-card-footer">
            <div class="box-card-footer">
              <div>
                <span style="color: #ff9902; font-weight: bold; font-size: 1.2rem;">
                  ${formatPrice(item.productCurrency, item.productPrice)}
                </span>
              </div>
              <div class="box-card-footer-action">
                <div class="e-store-tooltip left btn-view-detail">
                  <i class="bi bi-file-text"></i>
                  <span class="e-store-tooltiptext">รายละเอียดเพิ่มเติม</span>
                </div>
                <span class="btn-add-wishlist"><i class="bi bi-heart"></i></span>
                <span class="btn-add-cart"><i class="bi bi-cart3"></i></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;

    container.appendChild(div);
  });

  container.addEventListener("click", (e) => {

    if (e.target.closest(".btn-add-wishlist")) {
      if (config.user) {
        const itemDiv = e.target.closest(".item");
        const productId = itemDiv.dataset.productId;
        const product = items.find((i) => i.productId === productId);
        if (product) addWishlist(product);
      } else {
        document.getElementById("auth-modal").style.display = "flex";
      }
    }

    else if (e.target.closest(".btn-add-cart")) {
      if (config.user) {
        const itemDiv = e.target.closest(".item");
        const productId = itemDiv.dataset.productId;
        const product = items.find((i) => i.productId === productId);
        if (product) addCart(product);
      } else {
        document.getElementById("auth-modal").style.display = "flex";
      }
    }

    else if (e.target.closest(".btn-view-detail")) {
      const itemDiv = e.target.closest(".item");
      const productId = itemDiv.dataset.productId;
      // redirectGet(`${config.BASE_WEB}product/detail/`, { id: productId }, '_blank');
      redirectGet(`${config.BASE_WEB}product/detail/`, { id: productId });
    }

    else if (e.target.closest(".img-view-detail")) {
      const itemDiv = e.target.closest(".item");
      const productId = itemDiv.dataset.productId;
      // redirectGet(`${config.BASE_WEB}product/detail/`, { id: productId }, '_blank');
      redirectGet(`${config.BASE_WEB}product/detail/`, { id: productId });
    }

  });

  $(selector).owlCarousel({
    loop: false,
    margin: 10,
    nav: true,
    dots: false,
    autoWidth: true,
    touchDrag: true,    
    mouseDrag: true,
    onInitialized: function(event) {
      $(event.target).removeClass("owl-drag");
    }
  });

}

export function renderCarouselLG(selector, items) {
  const container = document.querySelector(selector);
  items.forEach((item) => {
    const div = document.createElement("div");
    div.classList.add("item");
    div.innerHTML = `
      <div class="news-card">
        <div class="news-card-row">
          <div class="news-card-image">
            <img src="${item.image}" alt="">
          </div>
          <div class="news-card-content">
            <h5 class="news-card-title">Card title</h5>
            <p class="news-card-text">
              This is a wider card with supporting text below as a natural lead-in to additional content.
            </p>
            <p class="news-card-updated">Last updated 3 mins ago</p>
          </div>
        </div>
      </div>
    `;
    container.appendChild(div);
  });

  $(selector).owlCarousel({
    loop: false,
    margin: 10,
    nav: true,
    dots: false,
    autoWidth: true,
    touchDrag: true,
    mouseDrag: true,
    onInitialized: function(event) {
      $(event.target).removeClass("owl-drag");
    }
  });
}

export function renderGridCardSM(selector, items) {

  // <div>${item.title}</div>
  const container = document.querySelector(selector);
  items.forEach((item) => {
    const div = document.createElement("div");
    div.classList.add("item");
    div.innerHTML = `
    <a href="${item.compLink}" target="_blank">
      <div class="cb-sm-grid">
        <div><img src="${item.image}" alt="" class="cb-sm-grid-image"/></div>
      </div>
    </a>
    `;
    container.appendChild(div);
  });

  $(selector).owlCarousel({
    loop: false,
    margin: 10,
    nav: true,
    dots: false,
    autoWidth: true,
    touchDrag: true,
    mouseDrag: true,
    onInitialized: function(event) {
      $(event.target).removeClass("owl-drag");
    }
  });

}


export function renderGridCardMD(selector, items, config) {
  const container = document.querySelector(selector);
  container.classList.add("cpd-md-grid");

  let currentIndex = 0;
  const batchSize = 8;

  // render เฉพาะ batch (ไม่ render ทั้งหมดทีเดียว)
  function renderBatch() {
    const nextItems = items.slice(currentIndex, currentIndex + batchSize);
    nextItems.forEach((item) => {
      const badgesWithIcon = item.productBadges
        .map((badge) => `<span class="badges-tag">${badge}</span>`)
        .join("");

      const div = document.createElement("div");
      div.classList.add("cpd-md-card");
      div.setAttribute("data-product-id", item.productId);

      // ${true ? `<div class="cpd-md-card-discount">-${10}%</div>` : ""}

      div.innerHTML = `
        <div class="cpd-md-card-row img-view-detail">
          <img src="${item.image}" alt="${item.productName}" class="cpd-md-card-image" />
        </div>

        <div class="cpd-md-card-body">
          <div class="cpd-md-card-category">
            <i class="bi bi-layers"></i> ${item.category}
          </div>
          <strong class="cpd-md-card-name">${item.productName}</strong>
          <p class="cpd-md-card-detail">${item.productDetail}</p>
          <div class="cpd-md-card-badges">${badgesWithIcon}</div>
        </div>

        <div class="cpd-md-card-footer">
          <span class="cpd-md-card-price">
            ${formatPrice(item.productCurrency, item.productPrice)}
          </span>
          <div class="cpd-md-card-footer-action">
            <div class="e-store-tooltip left btn-view-detail">
              <i class="bi bi-file-text"></i>
              <span class="e-store-tooltiptext">รายละเอียดเพิ่มเติม</span>
            </div>
            <button class="btn-add-wishlist"><i class="bi bi-heart"></i></button>
            <button class="btn-add-cart"><i class="bi bi-cart3"></i></button>
          </div>
        </div>
      `;

      container.appendChild(div);
    });

    currentIndex += batchSize;

    // ถ้าแสดงครบแล้ว → ซ่อนปุ่ม
    if (currentIndex >= items.length) {
      loadMoreBtn.style.display = "none";
    }
  }

  // ปุ่ม "ดูเพิ่มเติม"
  const loadMoreBtn = document.createElement("button");
  loadMoreBtn.textContent = "ดูเพิ่มเติม";
  loadMoreBtn.classList.add("cpd-md-loadmore");
  loadMoreBtn.style.margin = "1rem auto";
  loadMoreBtn.style.display = "block";

  loadMoreBtn.addEventListener("click", () => {
    renderBatch();
  });

  // แทรกปุ่มหลัง container
  container.insertAdjacentElement("afterend", loadMoreBtn);

  // render รอบแรก
  renderBatch();

  // event delegation (wishlist, cart, detail)
  container.addEventListener("click", (e) => {
    if (e.target.closest(".btn-add-wishlist")) {
      if (config.user) {
        const itemDiv = e.target.closest(".cpd-md-card");
        const productId = itemDiv.dataset.productId;
        const product = items.find((i) => i.productId === productId);
        if (product) addWishlist(product);
      } else {
        document.getElementById("auth-modal").style.display = "flex";
      }
    }

    else if (e.target.closest(".btn-add-cart")) {
      if (config.user) {
        const itemDiv = e.target.closest(".cpd-md-card");
        const productId = itemDiv.dataset.productId;
        const product = items.find((i) => i.productId === productId);
        if (product) addCart(product);
      } else {
        document.getElementById("auth-modal").style.display = "flex";
      }
    }

    else if (e.target.closest(".btn-view-detail") || e.target.closest(".img-view-detail")) {
      const itemDiv = e.target.closest(".cpd-md-card");
      const productId = itemDiv.dataset.productId;
      redirectGet(`${config.BASE_WEB}product/detail/`, { id: productId });
    }
  });
}


