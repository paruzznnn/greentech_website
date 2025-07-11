<?php
// header('Content-Type: application/javascript');
require_once("../api/PromptPay/lib/PromptPayQR.php");
require_once("../lib/base_directory.php");

function getSessionData($sessionName) {
    if (isset($_SESSION[$sessionName])) {
        return $_SESSION[$sessionName];
    }
    return array();
}

function getCookieData($cookieName) {
    if (isset($_COOKIE[$cookieName])) {
        $data = json_decode($_COOKIE[$cookieName], true);
        return is_array($data) ? $data : array();
    }
    return array();
}


/*************************Cookie*******************************/
// $cartContents = getCookieData('cart');
// $orderContents = getCookieData('orderArray');
// $compareContents = getCookieData('compare');


/*************************Session*******************************/
$cartContents = getSessionData('cart');
$compareContents = getSessionData('compare');


$itemCount = count($cartContents);
$itemCompareCount = count($compareContents);



// echo '<pre>';
// print_r($_SERVER);
// $ip_address = $_SERVER['REMOTE_ADDR'];
// echo "Your IP Address is: " . $ip_address;
// // print_r($_SESSION);
// // print_r($compareContents);
// echo '</pre>';

// echo '<pre>';
// print_r($_SESSION);
// echo '</pre>';



if (isset($_COOKIE['cookie_consent'])) {
    // หากคุกกี้ถูกตั้งค่า
    $cookieConsent = $_COOKIE['cookie_consent'];
    
    if ($cookieConsent === 'accepted') {

        // echo "ผู้ใช้ยอมรับคุกกี้";

    } elseif ($cookieConsent === 'declined') {
        
        // echo "ผู้ใช้ปฏิเสธคุกกี้";
    }

}

echo '<script>
    window.compareArr = '.json_encode($compareContents) .';
    window.cartArr = '.json_encode($cartContents).';
</script>';


$basePath = $base_path_admin;

?>
<div id="loading-overlay" class="hidden">
    <div class="spinner"></div>
</div>

<div id="background-blur"></div>
<div id="sidenav-blur"></div>


<div id="mySidenav" class="sidenav">
    <div class="sidenav-header">
        <h4>ส่วนลดและสิทธิประโยชน์</h4>
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    </div>

    <div class="sidenav-body">
        <form id="formCode">
            <div class="row">
                <div class="col-8">
                    <input type="text" class="form-control" id="discount" name="codeDiscount" value="">
                </div>
                <div class="col-4">
                    <button type="button" class="btn btn-primary">ใช้</button>
                </div>
            </div>
        </form>
        <br>
        <h6>คูปองจาก Trandar Store</h6>
        <div style="border-top: 3px dotted #5555;">
            <div id="showCoupon"></div>
        </div>
    </div>

    <div class="sidenav-footer">
        <button type="button" class="btn btn-success" onclick="closeNav()">ยืนยัน</button>
    </div>
</div>



<!-- <button type="button" id="myBtn-advertising" class="hidden"></button>
<div id="myModal-advertising" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="modal-close-advertising">&times;</span>
        </div>
        <div class="modal-body" style="background-color: #9e9e9e1f;">

            <div class="row" style="overflow: auto;">
                <div id="showAdvertising"></div>
            </div>

        </div>
    </div>
</div> -->

<div id="myModal-channel" class="modal">
    <!-- Modal content -->
    <div class="modal-content" style="width: 350px !important;">
        <div class="modal-header">
            <span class="modal-close-channel">&times;</span>
        </div>
        <div class="modal-body" style="background-color: #9e9e9e1f;">

            <div class="box-login-container">

                <div class="card">

                    <article class="card-body">

                        <div class="box-logo-container">

                            <a href="<?php echo $basePath .'login.php'?>">
                                <div class="box-logo">
                                    <div class="box-logo-image">
                                        <img src="../public/img/trandar_logo.png" alt="Trandar Logo">
                                    </div>
                                    <div data-key-lang="Trandar" lang="US" class="box-logo-text">
                                        Trandar
                                    </div>
                                </div>
                            </a>

                            <a href="https://www.origami.life//login.php#/">
                                <div class="box-logo">
                                    <div class="box-logo-image">
                                        <img src="../public/img/ogm_logo.png" alt="Origami Logo">
                                    </div>
                                    <div data-key-lang="Origami" lang="US" class="box-logo-text">
                                        Origami
                                    </div>
                                </div>
                            </a>

                        </div>

                        <br>
                        <h6 style="text-align: center; color: #555;" class="mt-2">
                            <span><i class="fas fa-unlock"></i></span>
                            <span data-key-lang="Pleaselogin" lang="US">Please log in</span>
                        </h6>
                        
                        <hr>

                        <form id="loginModal" action="" method="post">
                                
                            <div class="form-group mt-4">
                                <input id="email" type="text" class="emet-login input" placeholder="Email or login">
                            </div>

                            <!-- <div class="form-group mt-2">
                                <input id="password" type="password" class="emet-login input" data-type="password">
                            </div>   -->

                            <div class="form-group mt-2" style="position: relative;">
                                <input id="password" type="password" class="emet-login inpu" data-type="password">
                                <span class="" 
                                style="position: absolute; top: 10px; right: 20px; color: #555555;" 
                                id="togglePasswordPage">
                                    <i class="fas fa-eye-slash"></i>
                                </span>
                            </div>    

                            
                            <div class="row mt-4">

                                <div class="col-md-12 text-end">
                                    <a href="register.php">
                                        <span style="font-size: 13px !important;">
                                            สมัครสมาชิก
                                        </span>
                                    </a>
                                </div>
                        
                                <div class="col-md-12">
                                    <div class="d-inline-flex">
                                        <button type="submit" class="" 
                                        style="
                                        width: 260px;
                                        border: none;
                                        border-radius: 4px;
                                        padding: 10px;
                                        background: #ff8200;
                                        color: white;
                                        "
                                        > Login  </button>
                                    </div>
                                </div>
                            </div>

                        </form>

                    </article>
                </div>
                    

        </div>

        </div>
    </div>
</div>

<div id="myModal-compare" class="modal">
    <div class="modal-content" style="width: 90% !important;">
        <div class="modal-header">
            <span class="modal-close-compare">&times;</span>
            <h5><i class="fab fa-readme"></i> <span data-key-lang="" lang="US">Compare products</span></h5>
        </div>
        <div class="modal-body">

            <div class="row" style="overflow: auto;">
                <div id="compareTableContainer"></div>
            </div>

        </div>
    </div>
</div>

<div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <div class="modal-header">
            <span class="modal-close">&times;</span>
            
            <h5>
                <?php if(isset($_SESSION['user_pic'])){ ?>
                <img 
                src="actions/<?php echo $_SESSION['user_pic'] ?>" 
                style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;"
                alt="">
                <?php } else{ ?>

                    <i class="fas fa-user-cog"></i> 

                <?php } ?>
                <span data-key-lang="member" lang="US">Member information</span>
            </h5>
        </div>
        <div class="modal-body">

            <div class="row">

                <div class="col-md-6">

                <div style="margin: 10px;">
                    <a href="member.php?tab=profile" class="">
                        <i class="fas fa-id-card"></i>
                        <span data-key-lang="profile" lang="US"> profile</span>
                    </a>
                </div>

                <div style="margin: 10px;">
                    <a href="member.php?tab=shipping" class="">
                        <i class="fas fa-truck"></i>
                        <span data-key-lang="shipping" lang="US"> Shipping address</span>
                    </a>
                </div>

                </div>

                <div class="col-md-6">

                    <div style="margin: 10px;">
                        <a href="member.php?tab=pay" class="">
                            <i class="fas fa-clipboard-list"></i>
                            <span data-key-lang="purchase_history" lang="US"> Purchase history</span>
                        </a>
                    </div>

                    <div style="margin: 10px;">
                        <a href="member.php?tab=track" class="">
                            <i class="fas fa-tags"></i>
                            <span data-key-lang="track" lang="US"> Track products</span>
                        </a>
                    </div>

                </div>


            </div>

        </div>
        <div class="modal-footer">

        <?php if($_SESSION) { ?>

            <a href="admin/logout.php" class="display-link">
                <i class="fas fa-sign-out-alt"></i>
                <span data-key-lang="" lang="US">Log out</span>
            </a>

        <?php }else{ ?>

            <a href="admin/index.php" class="display-link">
                <i class="fas fa-sign-in-alt"></i>
                <span data-key-lang="" lang="US">Login</span>
            </a>

        <?php } ?>

        </div>
    </div>
</div>

<div id="myModal-shipping" class="modal">
    <!-- Modal content -->
    <div class="modal-content" style="width: 50%;">
        <div class="modal-header">
            <span class="modal-close-shipping">&times;</span>
            <h5><i class="fas fa-truck"></i> <span data-key-lang="shipping" lang="US">Add a shipping address<</span></h5>
        </div>
        <div class="modal-body" style="padding: 25px;">

            <form id="formShipment">
                <div class="row">
                    <div class="col-12 col-md-2 p-1 d-flex flex-column">
                        <span class="text-nowrap">
                            <label data-key-lang="prefix" lang="US">คำนำหน้า</label> <span>*</span>
                        </span>
                        <select class="form-select" id="prefix" name="prefix" required></select>
                    </div>
                    <div class="col-12 col-md-5 p-1 d-flex flex-column">
                        <span class="text-nowrap">
                            <label data-key-lang="first_name" lang="US">ชื่อ</label> <span>*</span>
                        </span>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="" required>
                    </div>
                    <div class="col-12 col-md-5 p-1 d-flex flex-column">
                        <span class="text-nowrap">
                            <label data-key-lang="last_name" lang="US">นามสกุล</label> <span>*</span>
                        </span>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="" required>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="col-12 col-md-4 p-1 d-flex flex-column">
                        <span class="text-nowrap">
                            <label data-key-lang="country" lang="US">ประเทศ</label> <span>*</span>
                        </span>
                        <select class="form-select" id="country" name="country" required></select>
                    </div>
                    <div class="col-12 col-md-4 p-1 d-flex flex-column">
                        <span class="text-nowrap">
                            <label data-key-lang="province" lang="US">จังหวัด</label> <span>*</span>
                        </span>
                        <select class="form-select" id="province" name="province" required></select>
                    </div>
                    <div class="col-12 col-md-4 p-1 d-flex flex-column">
                        <span class="text-nowrap">
                            <label data-key-lang="district" lang="US">เขต/อำเภอ</label>  <span>*</span>
                        </span>
                        <select class="form-select" id="district" name="district" required></select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-4 p-1 d-flex flex-column">
                        <span class="text-nowrap">
                            <label data-key-lang="subdistrict" lang="US">แขวง/ตำบล</label> <span>*</span>
                        </span>
                        <select class="form-select" id="subdistrict" name="subdistrict" required></select>
                    </div>
                    <div class="col-12 col-md-4 p-1 d-flex flex-column">
                        <span class="text-nowrap">
                            <label data-key-lang="postal_code" lang="US">รหัสไปรษณีย์</label> <span>*</span>
                        </span>
                        <select class="form-select" id="post_code" name="post_code" required></select>
                    </div>
                    <div class="col-12 col-md-4 p-1 d-flex flex-column">
                        <span class="text-nowrap">
                            <label data-key-lang="phone_number" lang="US">เบอร์โทรศัพท์</label> <span>*</span>
                        </span>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-md-12 p-1 d-flex flex-column">
                        <span class="text-nowrap">
                            <label data-key-lang="address" lang="US">ที่อยู่</label> <span>*</span>
                        </span>
                        <textarea id="address" class="form-control" name="address" required></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-md-4 p-1 d-flex flex-column">
                        <span class="text-nowrap">
                            <label data-key-lang="company" lang="US">บริษัท</label>
                        </span>
                        <input type="text" class="form-control" id="comp_name" name="comp_name" placeholder="">
                    </div>

                    <div class="col-12 col-md-8 p-1 d-flex flex-column">
                        <span class="text-nowrap">
                            <label data-key-lang="tax_id" lang="US">เลขประจําตัวผู้เสียภาษี</label> 
                        </span>
                        <input type="text" class="form-control" id="tax_number" name="tax_number" placeholder="">
                    </div>
                </div>

                <div class="row">

                    <div class="col-12 col-md-12 p-1 d-flex flex-column">
                        <span class="text-nowrap">
                            <label data-key-lang="map" lang="US">จุดสังเกต แผนที่</label> 
                            <span><i class="fas fa-map-marker-alt"></i></span>
                        </span>
                        <input type="text" class="form-control" id="searchInput" placeholder="" >
                    </div>

                    <div class="col-12 col-md-6 p-1 d-flex flex-column">
                        <span class="text-nowrap">
                            <label data-key-lang="" lang="US">ละติจูด</label>
                        </span>
                        <input type="text" class="form-control" id="inputLatitude" name="inputLatitude" readonly>
                    </div>

                    <div class="col-12 col-md-6 p-1 d-flex flex-column">
                        <span class="text-nowrap">
                            <label data-key-lang="" lang="US">ลองจิจูด</label>
                        </span>
                        <input type="text" class="form-control" id="inputLongitude" name="inputLongitude" readonly>
                    </div>

                </div>

                <div class="row">
                    <div id="googleMap"></div>
                </div>

            </form>

        </div>
        <div class="modal-footer">
            <div class="p-1 d-sm-flex">
                <button type="button" class="btn btn-success" id="submitAddShipping"><i class="fas fa-plus"></i> Add</button>
            </div>

        </div>
    </div>
</div>

<div id="myModal-pay" class="modal">
    <!-- Modal content -->
    <div class="modal-content" style="width: 350px !important;">
        <!-- <div class="modal-header">
            <span class="modal-close-pay">&times;</span>
            <h6 style="text-align: center; position: absolute; left: 35%;" class="mt-2">
            
            </h6>
        </div> -->
        <div class="modal-body" style="background-color: #9e9e9e1f;">
        <span class="modal-close-pay">&times;</span>

            <div id="showMypay"></div>
            
        </div>
        <div class="modal-footer" style="display: flex; flex-direction: column; justify-content: center;">

            <form id="uploadForm" enctype="multipart/form-data">
                <input id="qrCodeInput" name="qrCodeInput" type="text" hidden>
                <div class="file-loading"> 
                    <input id="input-b6b" name="input-b6b[]" type="file" multiple>
                </div>
                <br>
                <div style="display: flex; flex-direction: column;">
                    <button type="button" id="submitPay" class="btn btn-success" 
                    data-key-lang="order" lang="US">สั่งซื้อ</button>
                </div>
            </form>

        </div>
    </div>
</div>

<div id="myModal-purchase" class="modal">
    <!-- Modal content -->
    <div class="modal-content" style="width: 350px !important;">
        <!-- <div class="modal-header">
            <span class="modal-close-pay">&times;</span>
            <h6 style="text-align: center; position: absolute; left: 35%;" class="mt-2">
            
            </h6>
        </div> -->
        <div class="modal-body" style="background-color: #9e9e9e1f;">
        <span class="modal-close-purchase">&times;</span>
        </div>

        <div class="modal-footer" style="display: flex; flex-direction: column; justify-content: center;">

            <form id="uploadForm_evidence" enctype="multipart/form-data">
                <input id="numberOrder" name="numberOrder" type="text" hidden>
                <label for="input-b">แนบสลิป</label>
                <div class="file-loading"> 
                    <input id="input-b" name="input-b[]" type="file" multiple>
                </div>
                <br>
                <div style="display: flex;">
                    <button type="button" id="submitEvidence" class="btn btn-success">ส่งข้อมูล</button>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="row">
    <div class="head-store">
        <div class="display-in-head">
            <div class="col-md-2 col-sm-2 col-xs-2">
                <div style="display: flex; flex-direction: row-reverse; align-items: center;">
                    <div style="text-align: center;">
                        <span id="pageHome">
                            <img style="width: 50%;" src="../public/img/trandar_logo.png" alt="Logo">
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-7 col-sm-7 col-xs-7">
                <div class="box-info">
                    <div>
                        <span class="toggle-button">
                            <i id="toggleIcon" class="fas fa-bars"></i>
                        </span>
                        <span data-key-lang="category" lang="US" value="" style="font-size: 16px;"></span>
                    </div>
                    <input type="text" id="search_prod" class="form-element" placeholder="">
                    <button type="button" id="btn_search_prod" class="btn-warning">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-1 col-sm-1 col-xs-1">
                <div style="text-align: center;">
                    <a href="register.php" style="text-decoration: none; color: #ff8c0a;">
                        <span style="font-size: 13px !important;">
                            สมัครสมาชิก
                        </span>
                    </a>
                </div>
            </div>
            <div class="col-md-2 col-sm-2 col-xs-2">
                <div style="text-align: center; margin: 15px;">
                    <select id="language-select" class="form-element"></select>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="showTabContent" class="row row-tab"></div>

<div class="alert-cart"><i class="fa-solid fa-shop"></i>

    <div style="position: relative; border-bottom: 1px solid #f4f4f4; padding: 8px 0px;">
        <div class="display-info">
            <a type="button" id="<?php echo isset($_SESSION['user_id']) ? 'myBtn' : 'myBtn-channel';?>" class="display-link">
            
            <?php if(isset($_SESSION['user_pic'])){ ?>
            <img 
            src="actions/<?php echo $_SESSION['user_pic'] ?>" 
            style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;"
            alt="">
            <?php } else{ ?>

                <i class="fas fa-user-circle"></i>

            <?php } ?>
            </a>
        </div>
    </div>
    <?php if(isset($_SESSION['user_id'])){ ?>
    <div style="position: relative; border-bottom: 1px solid #f4f4f4; padding: 8px 0px;">
        <label for="" id="userNotify" class="cart-count">0</label>
        <div class="display-info">
            <a type="button" id="" class="display-link">
                <i class="fas fa-bell"></i>
            </a>
        </div>
    </div>
    <?php } ?>

    <div style="position: relative; border-bottom: 1px solid #f4f4f4; padding: 8px 0px;">
        <label for="" id="compareCount" class="cart-count"><?php echo $itemCompareCount;?></label>
        <div class="display-info">
            <a type="button" id="<?php echo isset($_SESSION['user_id']) ? 'myBtn-compare' : '';?>" class="display-link">
                <i class="fab fa-readme"></i>
            </a>
        </div>
    </div>

    <div style="position: relative; padding: 8px 0px;">
        <label for="" class="cart-count"><?php echo $itemCount;?></label>
        <div class="display-info">
            <a type="button" href="cart.php" class="display-link">
                <i class="fas fa-shopping-basket"></i>
            </a>
        </div>
    </div>
</div>





