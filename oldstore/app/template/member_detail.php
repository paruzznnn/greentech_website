<div class="row" style="margin: 3%; min-height: 100vh;">
    <div class="container">
        <div class="card-detail">
            <div class="container-fluid">
                <div class="wrapper row">

                    <!-- ปุ่มต่างๆ จัดอยู่ด้านบน -->
                    <div class="nav nav-pills d-flex flex-row justify-content-start" id="member-tab" style="width: 100%; margin-bottom: 20px;">
                        <button class="nav-link active" data-target="#member-profile" style="flex: 1; text-align: start;">
                            <i class="fas fa-id-card"></i> <span data-key-lang="profile" lang="US">Profile</span>
                        </button>
                        <button class="nav-link" data-target="#member-shipping" style="flex: 1; text-align: start;">
                            <i class="fas fa-truck"></i> <span data-key-lang="shipping" lang="US">Shipping</span>
                        </button>
                        <button class="nav-link" data-target="#member-pay" style="flex: 1; text-align: start;">
                            <i class="fas fa-clipboard-list"></i> <span data-key-lang="purchase" lang="US">Purchase</span>
                        </button>
                        <!-- <button class="nav-link" data-target="#member-track" style="flex: 1; text-align: start;">
                            <i class="fas fa-tags"></i> <span data-key-lang="track" lang="US">Track Products</span>
                        </button> -->
                    </div>

                    <hr>

                    <!-- เนื้อหาของแท็บ -->
                    <div class="tab-content" id="member-tabContent" style="width: 100%; padding: 0 2%;">
                        <div class="tab-pane fade show active" id="member-profile">
                            <?php include 'member_profile.php'?>
                        </div>
                        <div class="tab-pane fade" id="member-shipping">
                            <?php include 'member_shipping.php'?>
                        </div>
                        <div class="tab-pane fade" id="member-pay">
                            <?php include 'member_pay.php'?>
                        </div>
                        <!-- <div class="tab-pane fade" id="member-track">
                            <?php //include 'member_track.php'?>
                        </div> -->
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
