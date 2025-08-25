<!-- header 1 -->
<header class="header-store1">
  <div class="navbar-service">
    <div class="container">
        <div class="navbar-service-wrapper">
          <div class="nav-service-link-group nav-link-group-left ">
              <a href="#" class="nav-service-link">บริการ Trandar Store</a>
              <a href="#" class="nav-service-link">โปรโมชั่น</a>
              <a href="#" class="nav-service-link">การรับประกันสินค้าและคืนสินค้า</a>
              <a href="#" class="nav-service-link">วิธีการสั่งซื้อ</a>
              <a href="#" class="nav-service-link">ติดตามการจัดส่ง</a>
          </div>
          <div class="nav-service-link-group nav-link-group-right">
              <a href="#" class="nav-service-link">ศูนย์ช่วยเหลือ</a>
              <div id="langButtons" class="lang-buttons-container">
                  <i class="bi bi-globe"></i>
                  <button data-lang="en" type="button" class="btn-lang">English</button>
                  <button data-lang="th" type="button" class="btn-lang">Thai</button>
              </div>
          </div>
        </div>
    </div>
  </div>

  <nav class="pt-1">
    <div class="container">
      <div class="nav-store1">

        <div class="nav-store1-box-logo">
          <a href="<?php echo $BASE_WEB?>">
            <img src="<?php echo $BASE_WEB?>trandar_logo.png" alt="Logo">
          </a>
        </div>

        <div class="nav-store1-box-search">
          <form style="margin: 0px;">
            <div class="input-search-store1">
              <i class="fa fa-search icon-left"></i>
              <input type="text" id="input-search" class="input-search" placeholder="ค้นหา..." />
              <button class="btn-inside">
                <i class="bi bi-camera-fill"></i>
                <span>ค้นหาสินค้า</span>
              </button>
            </div>
          </form>
        </div>

        <div class="nav-store1-box-menu">
          <?php if(empty($_SESSION['user'])) { ?>
            <div>
                <button data-lang="" id="modal-auth-store1" type="button" class="nav-store1-btn btn-sm">
                  <i class="bi bi-person-circle"></i>
                  <span>เข้าสู่ระบบ / ลงทะเบียน</span>
                </button>
            </div>
            <?php } else if(!empty($_SESSION['user']) && $_SESSION['user']['role'] == "user") { ?>
            <div id="box-notify-panel" class="notify-panel">
              <!-- <div class="notify-item">
                <div class="notify-icon-wrapper">
                  <a href="<?php echo $BASE_WEB?>user/" class="notify-icon-button">
                    <i class="bi bi-person"></i>
                  </a>
                </div>
              </div> -->
              <div class="notify-item">
                <span id="cartCount" class="notify-count">0</span>
                <div class="notify-icon-wrapper">
                  <a href="<?php echo $BASE_WEB?>user/" class="notify-icon-button">
                    <i class="bi bi-cart3"></i>
                  </a>
                </div>
              </div>

              <div class="notify-item">
                <span id="wishlistCount" class="notify-count">0</span>
                <div class="notify-icon-wrapper">
                  <a href="<?php echo $BASE_WEB?>user/" class="notify-icon-button">
                    <i class="bi bi-clipboard-heart"></i>
                  </a>
                </div>
              </div>

            </div>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                  setInterval(() => {
                    const countCart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
                    const countWishlist = JSON.parse(localStorage.getItem('likedProducts')) || [];

                    document.querySelector("#wishlistCount").textContent = countWishlist.length;
                    document.querySelector("#cartCount").textContent = countCart.length;
                  }, 1000);
                });
            </script>
            <?php } ?>
            <div>
              <span id="menu-open-store1" style="font-size:20px;cursor:pointer">
                <i class="bi bi-three-dots-vertical"></i>
              </span>
            </div>
        </div>

      </div>
    </div>
  </nav>
  <nav class="mt-2">
    <div id="linkContainer" class="container"></div>
  </nav>
</header>

<div class="navbar-news">
  <div class="container">
    <marquee id="newsMarquee" scrollamount="4" behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();">
      <div style="display: inline;">
        <span style="padding: 0 50px;">
          <a id="newsMarquee-link" href="" style="text-decoration: none; color: inherit; font-size: 12px;">
            <img src="https://img.icons8.com/?size=100&id=6ER3rS2ZLjRQ&format=png&color=000000" alt="" width="18px">
            Trandar Acoustics หนึ่งในวัสดุจากกลุ่ม Harmony เปิดตัวที่ Acoustics Solution For WELL Standard 
            ที่ Harmony Club ในงาน INNOVATORX FORUM 2023 
          </a>
        </span>
      </div>
    </marquee>
  </div>
</div>

<!-- header 2 -->
<header class="header-store2">
  <nav class="pt-3 pb-3">
    <div class="container">
      <div class="nav-store2">
        <div id="menu-open-store2" class="nav-store2-box-menu">
          <i class="bi bi-border-width"></i>
        </div>
        <div id="menu-close-store2" class="nav-store2-box-menu hidden">
          <i class="fas fa-times"></i>
        </div>
        <div class="nav-store2-box-logo">
          <a href="<?php echo $BASE_WEB?>">
            <img src="<?php echo $BASE_WEB?>trandar_logo.png" alt="Logo">
          </a>
        </div>
        <div class="nav-store2-box-menu">
          <div id="menu1-open-store1">
            <i class="bi bi-person-circle"></i>
          </div>
        </div>
      </div>
    </div>
  </nav>
</header>

<!-- Modal -->
<div id="auth-modal" class="store-modal">
  <div class="store-modal-content">

    <span id="modal-auth-close-store1" class="store-close-modal">&times;</span>
    <img src="<?php echo $BASE_WEB?>trandar_logo.png" alt="" class="store-modal-title" />

    <div class="tab-navigation">
      <button id="login-tab" class="tab-button active">
        เข้าสู่ระบบ
      </button>
      <button id="register-tab" class="tab-button">
        ลงทะเบียน
      </button>
    </div>

    <!-- Tab Content: Login -->
    <div id="login-content" class="tab-content active">
      <form id="formLogin" class="form-space-y" data-url="<?php echo $BASE_WEB?>auth/check-login.php" data-redir="<?php echo $BASE_WEB?>user/" data-type="login">
        <input type="text" name="action" value="checkLogin" hidden>
        <div>
          <label for="login_email" class="form-label">อีเมล:</label>
          <input type="text" id="login_email" name="login_email" class="form-input" placeholder="email@example.com" required>
        </div>

        <div class="form-group">
          <label for="login_password" class="form-label">รหัสผ่าน:</label>
          <div class="input-wrapper">
            <input type="password" id="login_password" name="login_password"
              class="form-input"
              placeholder="••••••••" required>
            <button type="button" onclick="togglePassword('login_password')"
              class="toggle-btn">
              <i class="far fa-eye"></i>
            </button>
          </div>
        </div>

        <div class="auth-remember">
          <div>
            <i class="bi bi-lock"></i>
            <span>ข้อมูลของคุณทั้งหมดจะถูกเข้ารหัส เพื่อความปลอดภัย</span>
          </div>
          <div>
            <a href="<?php echo $BASE_WEB?>terms.php">ลืมรหัสผ่าน</a>
          </div>
        </div>

        <button type="submit" class="form-button login-button">
          <span>ดำเนินการต่อ</span>
        </button>
      </form>
      <div class="space-login-social">ช่องทางอื่น</div>
      <div class="box-login-social">
        <button type="button" id="loginGoogleBtn" class="btn-login-social">
          <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="48" height="48" viewBox="0 0 48 48">
            <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"></path>
            <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"></path>
            <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"></path>
            <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"></path>
          </svg>
        </button>
        <button type="button" id="loginLineBtn" class="btn-login-social">
          <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="48" height="48" viewBox="0 0 48 48">
            <path fill="#00c300" d="M12.5,42h23c3.59,0,6.5-2.91,6.5-6.5v-23C42,8.91,39.09,6,35.5,6h-23C8.91,6,6,8.91,6,12.5v23C6,39.09,8.91,42,12.5,42z"></path>
            <path fill="#fff" d="M37.113,22.417c0-5.865-5.88-10.637-13.107-10.637s-13.108,4.772-13.108,10.637c0,5.258,4.663,9.662,10.962,10.495c0.427,0.092,1.008,0.282,1.155,0.646c0.132,0.331,0.086,0.85,0.042,1.185c0,0-0.153,0.925-0.187,1.122c-0.057,0.331-0.263,1.296,1.135,0.707c1.399-0.589,7.548-4.445,10.298-7.611h-0.001C36.203,26.879,37.113,24.764,37.113,22.417z M18.875,25.907h-2.604c-0.379,0-0.687-0.308-0.687-0.688V20.01c0-0.379,0.308-0.687,0.687-0.687c0.379,0,0.687,0.308,0.687,0.687v4.521h1.917c0.379,0,0.687,0.308,0.687,0.687C19.562,25.598,19.254,25.907,18.875,25.907z M21.568,25.219c0,0.379-0.308,0.688-0.687,0.688s-0.687-0.308-0.687-0.688V20.01c0-0.379,0.308-0.687,0.687-0.687s0.687,0.308,0.687,0.687V25.219z M27.838,25.219c0,0.297-0.188,0.559-0.47,0.652c-0.071,0.024-0.145,0.036-0.218,0.036c-0.215,0-0.42-0.103-0.549-0.275l-2.669-3.635v3.222c0,0.379-0.308,0.688-0.688,0.688c-0.379,0-0.688-0.308-0.688-0.688V20.01c0-0.296,0.189-0.558,0.47-0.652c0.071-0.024,0.144-0.035,0.218-0.035c0.214,0,0.42,0.103,0.549,0.275l2.67,3.635V20.01c0-0.379,0.309-0.687,0.688-0.687c0.379,0,0.687,0.308,0.687,0.687V25.219z M32.052,21.927c0.379,0,0.688,0.308,0.688,0.688c0,0.379-0.308,0.687-0.688,0.687h-1.917v1.23h1.917c0.379,0,0.688,0.308,0.688,0.687c0,0.379-0.309,0.688-0.688,0.688h-2.604c-0.378,0-0.687-0.308-0.687-0.688v-2.603c0-0.001,0-0.001,0-0.001c0,0,0-0.001,0-0.001v-2.601c0-0.001,0-0.001,0-0.002c0-0.379,0.308-0.687,0.687-0.687h2.604c0.379,0,0.688,0.308,0.688,0.687s-0.308,0.687-0.688,0.687h-1.917v1.23H32.052z"></path>
          </svg>
        </button>
        <button type="button" id="loginFacebookBtn" class="btn-login-social">
          <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="48" height="48" viewBox="0 0 48 48">
            <linearGradient id="Ld6sqrtcxMyckEl6xeDdMa_uLWV5A9vXIPu_gr1" x1="9.993" x2="40.615" y1="9.993" y2="40.615" gradientUnits="userSpaceOnUse">
              <stop offset="0" stop-color="#2aa4f4"></stop>
              <stop offset="1" stop-color="#007ad9"></stop>
            </linearGradient>
            <path fill="url(#Ld6sqrtcxMyckEl6xeDdMa_uLWV5A9vXIPu_gr1)" d="M24,4C12.954,4,4,12.954,4,24s8.954,20,20,20s20-8.954,20-20S35.046,4,24,4z"></path>
            <path fill="#fff" d="M26.707,29.301h5.176l0.813-5.258h-5.989v-2.874c0-2.184,0.714-4.121,2.757-4.121h3.283V12.46 c-0.577-0.078-1.797-0.248-4.102-0.248c-4.814,0-7.636,2.542-7.636,8.334v3.498H16.06v5.258h4.948v14.452 C21.988,43.9,22.981,44,24,44c0.921,0,1.82-0.084,2.707-0.204V29.301z"></path>
          </svg>
        </button>
      </div>
      <p class="auth-policy">
        เมื่อเข้าสู่ระบบ ถือว่าคุณได้ยอมรับ
        <a href="<?php echo $BASE_WEB?>terms.php">เงื่อนไขการใช้บริการ</a>
        และรับทราบ
        <a href="<?php echo $BASE_WEB?>privacy-policy.php">นโยบายความเป็นส่วนตัว</a>
        ของ Trandar Store
      </p>
    </div>

    <!-- Tab Content: Register -->
    <div id="register-content" class="tab-content">
      <form id="formRegister" class="form-space-y" data-url="<?php echo $BASE_WEB?>auth/check-register.php" data-redir="<?php echo $BASE_WEB?>user/" data-type="register">
        <input type="text" name="action" value="checkRegister" hidden>
        <!-- <div>
          <label for="register-username" class="form-label">ชื่อผู้ใช้:</label>
          <input type="text" id="register-username" name="register-username" class="form-input" placeholder="ชื่อผู้ใช้ของคุณ" required>
        </div> -->
        <div>
          <label for="register_email" class="form-label">อีเมล:</label>
          <input type="email" id="register_email" name="register_email" class="form-input" placeholder="email@example.com" required>
        </div>

        <div class="form-group">
          <label for="register_password" class="form-label">รหัสผ่าน:</label>
          <div class="input-wrapper">
            <input type="password" id="register_password" name="register_password"
              class="form-input"
              placeholder="••••••••" required>
            <!-- <button type="button" onclick="togglePassword('register-password')"
              class="toggle-btn">
              <i class="far fa-eye"></i>
            </button> -->
          </div>
        </div>

        <div class="form-group">
          <label for="register_confirm_password" class="form-label">ยืนยันรหัสผ่าน:</label>
          <div class="input-wrapper">
            <input type="password" id="register_confirm_password" name="register_confirm_password"
              class="form-input"
              placeholder="••••••••" required>
            <!-- <button type="button" onclick="togglePassword('register-confirm-password')"
              class="toggle-btn">
              <i class="far fa-eye"></i>
            </button> -->
          </div>
          <div id="matchMessage" class="message"></div>
        </div>

        <div id="pwdRules">
          <ul>
            <li id="rule-length" class="rule-fail">
              <i class="far fa-circle"></i> At least <strong>8</strong> characters
            </li>
            <li id="rule-lower" class="rule-fail">
              <i class="far fa-circle"></i> Lowercase (a‑z)
            </li>
            <li id="rule-upper" class="rule-fail">
              <i class="far fa-circle"></i> Capital letters (A‑Z)
            </li>
            <li id="rule-digit" class="rule-fail">
              <i class="far fa-circle"></i> Numbers (0‑9)
            </li>
            <li id="rule-special" class="rule-fail">
              <i class="far fa-circle"></i> Special characters (!@#$…)
            </li>
          </ul>
        </div>

        <p class="auth-policy">
          <input class="form-check-input" type="checkbox" id="accept_policy" name="accept_policy" required>
          <span>
            การลงทะเบียนเข้าใช้งานหมายถึงฉันยอมรับ
            <a href="<?php echo $BASE_WEB?>terms.php">เงื่อนไขการใช้งาน</a>และ
            <a href="<?php echo $BASE_WEB?>privacy-policy.php">นโยบายความเป็นส่วนตัว</a>ของ Trandar Store
          </span>
        </p>

        <!-- <div class="g-recaptcha" data-sitekey="6LeEp5YrAAAAAE9gUav_bHzqkYrPpC5CAttb_xXv"></div> -->
        <button type="submit" id="submit-register" class="form-button register-button">
          ลงทะเบียน
        </button>

      </form>
    </div>

  </div>
</div>

<!-- Notify -->
<div id="notificationContainer" class="notification-container">
    <div id="notificationMessage" class="notification-message"></div>
</div>


<!-- sidenav 1 -->
<aside id="sidenav-store1" class="sidenav">
  <div class="login-box-store1">

    <?php if(empty($_SESSION['user'])) { ?>
    <img src="https://www.w3schools.com/howto/img_avatar.png" alt="Avatar" class="avatar-store1">
    <p>กรุณาเข้าสู่ระบบ</p>
    <?php } else { ?>
    <img src="https://www.w3schools.com/howto/img_avatar.png" alt="Avatar" class="avatar-store1">
    <p><?php echo $_SESSION['user']['username']?></p>
    <?php } ?>

    <a href="javascript:void(0)" id="menu-close-store1" class="closebtn-store1">&times;</a>
  </div>
  <div>
  </div>
</aside>

<!-- sidenav 2 -->
<aside id="sidenav-store2" class="sidenav">
  <!-- <a href="javascript:void(0)" class="closebtn-store2" >&times;</a> -->
  <div class="pt-2 ps-2 pe-2">
    <!-- <form action="">
      <div class="input-search-store2">
        <i class="fa fa-search icon-left"></i>
        <input type="text" placeholder="ค้นหา..." />
        <button class="btn-inside">
          <span>ค้าหาสินค้า</span>
        </button>
      </div>
    </form> -->
  </div>
  <div class="pt-1">
    <ul id="menuListContainer"></ul>
  </div>
</aside>
<div id="overlay-store2"></div>


<nav id="breadcrumb-box">
  <div class="container">
    <ul id="breadcrumb-list">
    </ul>
  </div>
</nav>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const breadcrumbList = document.getElementById('breadcrumb-list');
    const pathSegments = window.location.pathname.split('/').filter(segment => segment !== '');

    const isHomePage = (
      pathSegments.length === 1 && pathSegments[0] === 'store' ||
      pathSegments.length === 2 && pathSegments.includes('trandar_website') && pathSegments.includes('store')
    );

    const breadcrumbBox = document.getElementById('breadcrumb-box');
    breadcrumbBox.style.display = isHomePage ? "none" : "block";

    if (isHomePage) {
      
      return;
    }

    const filteredSegments = pathSegments.filter(
      segment => segment !== 'trandar_website' && segment !== 'store'
    );

    function createBreadcrumbItem(text, url = null, isLast = false) {
      const li = document.createElement('li');
      li.className = 'flex items-center';

      let link = '';
      if (url === "/store") {
        link = pathConfig.BASE_WEB;
      } else {
        link =  pathConfig.BASE_WEB + url;
      }

      if (url && !isLast) {
        const a = document.createElement('a');
        a.href = link;
        a.textContent = text;
        a.setAttribute('data-lang', text);
        li.appendChild(a);
      } else {
        const span = document.createElement('span');
        span.className = 'text-gray-500';
        span.textContent = text;
        span.setAttribute('data-lang', text);
        li.appendChild(span);
      }

      if (!isLast) {
        const separator = document.createElement('span');
        separator.className = 'mx-2';
        separator.innerHTML = '<i class="bi bi-chevron-right"></i>';
        li.appendChild(separator);
      }
      return li;
    }

    breadcrumbList.appendChild(
      createBreadcrumbItem('home', '/store', false)
    );

    let currentPath = '';
    filteredSegments.forEach((segment, index) => {
      currentPath += '/' + segment;
      const cleanPath = currentPath.replace(/^\/+/, ''); 

      const isLastSegment = (index === filteredSegments.length - 1);
      let displayTitle = segment.replace(/-/g, ' ');

      breadcrumbList.appendChild(
        createBreadcrumbItem(displayTitle, cleanPath, isLastSegment)
      );
    });

  });
</script>

<script type="module">
  const base = pathConfig.BASE_WEB;
  const timestamp = <?= time() ?>;

  Promise.all([
    import(`${base}js/formHandler.js?v=${timestamp}`),
    import(`${base}js/modalBuilder.js?v=${timestamp}`),
    import(`${base}js/menuBuilder.js?v=${timestamp}`)
  ])
  .then( async ([formHandler, modalBuilder, menuBuilder]) => {
    const { handleFormSubmit } = formHandler;
    const { setupAuthModal, setupPasswordValidation, exposeTogglePassword } = modalBuilder;
    const { fetchHeader, buildLinkmenu, buildLinkmenuSlide, resetPosition } = menuBuilder;

    // ============== DOM Elements ========================
    const loginTab = document.getElementById("login-tab");
    const registerTab = document.getElementById("register-tab");
    const loginContent = document.getElementById("login-content");
    const registerContent = document.getElementById("register-content");

    const passwordInput = document.getElementById("register_password");
    const confirmInput = document.getElementById("register_confirm_password");
    const matchMessage = document.getElementById("matchMessage");
    const submitRegister = document.getElementById("submit-register");

    const rules = {
      length: document.getElementById("rule-length"),
      lower: document.getElementById("rule-lower"),
      upper: document.getElementById("rule-upper"),
      digit: document.getElementById("rule-digit"),
      special: document.getElementById("rule-special"),
    };

    // ==================== Initialize Features ================
    if (loginTab && registerTab && loginContent && registerContent) {
      setupAuthModal(loginTab, registerTab, loginContent, registerContent);
    }

    if (passwordInput && confirmInput && matchMessage) {
      setupPasswordValidation(passwordInput, confirmInput, matchMessage, rules, submitRegister);
    }

    exposeTogglePassword(window);
    const formLogin = document.querySelector("#formLogin");
    const formRegister = document.querySelector("#formRegister");
    formLogin?.addEventListener("submit", handleFormSubmit);
    formRegister?.addEventListener("submit", handleFormSubmit);

    //====================== Build Menu ==========================
    const service = pathConfig.BASE_WEB + 'service/header-data.php?';
    const data = await fetchHeader("getMenuHeaderItems", service);
    const contentArray = await fetchHeader("getMenuHeaderBox", service);
    const menuData = await fetchHeader("getMenuHeaderSideItems", service);

    if(data || contentArray){
      buildLinkmenu(data, contentArray);
    }
    
    if(menuData){
      buildLinkmenuSlide(menuData);
    }
    

    // ============= Responsive ==================
    const checkDeviceSize = () => {
      const width = window.innerWidth;

      if (width > 480) leftSlideClose();

      document.body.classList.remove("is-mobile", "is-tablet", "is-desktop");

      if (width <= 480) {
        document.body.classList.add("is-mobile");
      } else if (width <= 768) {
        document.body.classList.add("is-tablet");
      } else {
        document.body.classList.add("is-desktop");
      }
    };

    // =============== Menu Slide Functions =======================
    const leftSlide = () => {
      document.getElementById("sidenav-store2")?.classList.add("open");
      document.getElementById("overlay-store2")?.classList.add("active");
      document.getElementById("menu-open-store2")?.classList.add("hidden");
      document.getElementById("menu-close-store2")?.classList.remove("hidden");
    };

    const leftSlideClose = () => {
      document.getElementById("sidenav-store2")?.classList.remove("open");
      document.getElementById("overlay-store2")?.classList.remove("active");
      document.getElementById("menu-open-store2")?.classList.remove("hidden");
      document.getElementById("menu-close-store2")?.classList.add("hidden");
    };

    const rightSlide = () => {
      document.getElementById("sidenav-store1")?.classList.add("open");
    };

    const rightSlideClose = () => {
      document.getElementById("sidenav-store1")?.classList.remove("open");
    };

    // ============ Right menu =================
    document.getElementById("menu-open-store1")?.addEventListener("click", rightSlide);
    document.getElementById("menu1-open-store1")?.addEventListener("click", rightSlide);
    document.getElementById("menu-close-store1")?.addEventListener("click", rightSlideClose);

    // ============ Left menu ===================
    document.getElementById("menu-open-store2")?.addEventListener("click", leftSlide);
    document.getElementById("menu-close-store2")?.addEventListener("click", leftSlideClose);
    document.getElementById("overlay-store2")?.addEventListener("click", leftSlideClose);

    checkDeviceSize();
    const handleResize = () => {
      checkDeviceSize();
      document.querySelectorAll(".toggle-box-store1").forEach(box => {
        box.classList.remove("show");
        resetPosition(box);
      });
    };

    window.addEventListener("resize", handleResize);

  })
  .catch((e) => {
    console.error("One or more module imports failed", e);
  });
</script>

<script>
  //================= SET TIME ZONE ================================
  const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
  fetch(`${pathConfig.BASE_WEB}/time_zone/set-timezone.php`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: 'tz=' + encodeURIComponent(timezone)
  });
</script>