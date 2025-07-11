<div class="row d-none d-sm-block">
    <div class="col-lg-12"> 

        <div class="step-wizard">
            <div class="step-wizard-row">
                <div id="home" class="step-wizard-step active">
                    <a href="index.php">
                        <div class="step-circle">
                            <i class="fas fa-home"></i>
                        </div>
                        <p data-key-lang="Home" lang="US">Home</p>
                    </a>
                </div>
                <div id="cart" class="step-wizard-step">
                    <a href="cart.php">
                        <div class="step-circle">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <p data-key-lang="cart" lang="US">cart</p>
                    </a>
                </div>
                <div id="payment-info" class="step-wizard-step">
                    <a href="shipping.php">
                        <div class="step-circle">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <p data-key-lang="Shipping" lang="US">Shipping</p>
                    </a>
                </div>
                <div id="payment" class="step-wizard-step">
                    <a href="member.php">
                        <div class="step-circle">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <p data-key-lang="Payment" lang="US">Payment</p>
                    </a>
                </div>
                <div id="tracking" class="step-wizard-step">
                    <a href="#">
                        <div class="step-circle">
                            <i class="fas fa-tags"></i>
                        </div>
                        <p data-key-lang="Track" lang="US">Track</p>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<button id="myBtn-pay" hidden></button>

<div class="row" style="margin: 0 4%; min-height: 100vh;" >
    <div class="container mt-3 mb-4">
        <div class="card-detail">
            <div class="container-fliud">
                <div class="wrapper row">
                    
                        <div class="preview col-md-7">
                            <div class="p-1">
                                <h4 data-key-lang="shipping" lang="US">ที่อยู่ในการจัดส่ง</h4>

                                <div class="row">
                                    <div class="col-md-5">
                                        <input type="radio" class="channel" name="channel" value="1" checked>
                                        <label data-key-lang="custom" lang="US">กำหนดเอง</label>
                                    </div>
                                    
                                    <div class="col-md-7">
                                        <input type="radio" class="channel" name="channel" value="2">
                                        <label data-key-lang="receive_items" lang="US">รับของหน้าร้าน</label>
                                    </div>
                                </div>
                                
                            </div>

                            <div id="shippingDetailCustom" class="customized box-pay-detail" style="display: none;"></div>
                            <div id="shippingDetailStore" class="storefront box-pay-detail" style="display: none;"></div>
                            
                        </div>

                        <div class="details col-md-5">

                            <div class="col-md-12 customized">
                                <div class="p-1">
                                    <h5 data-key-lang="" lang="US">ส่วนลดและสิทธิประโยชน์</h5>
                                </div>

                                <div class="box-pay-detail">

                                    <div>
                                        <button  type="button" id="" class="btn btn-outline-warning" onclick="openNav()">
                                            <i class="fas fa-ticket-alt"></i>
                                            ใช้ส่วนลดและสิทธิประโยชน์
                                        </button >
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-12 customized">
                                <div class="p-1">
                                    <h5 data-key-lang="" lang="US">การขนส่ง</h5>
                                </div>

                                <div class="box-pay-detail">
                                    <form id="formTMS">
                                        <?php
                                            $sqlTms = "SELECT * FROM `tms_vehicles` WHERE del = 0";
                                            $rsTms = mysqli_query($conn, $sqlTms);

                                            while ($rowTms = mysqli_fetch_assoc($rsTms)) {

                                                // $checkedTms = 'checked';
                                                
                                                echo '
                                                <div class="box-pay-order">
                                                <span>
                                                    <input type="radio" id="" class="vehicle" 
                                                    name="vehicle" data-vehicle="'.$rowTms['price'].'" 
                                                    value="'.$rowTms['vehicle_id'].'">
                                                    <label data-key-lang="" lang="US" for="">'.$rowTms['vehicle_type'].'</label>
                                                </span>
                                                <span>'.$rowTms['price'].'</span>
                                                </div>
                                                
                                                ';
                                            }
                                        
                                        ?>
                                        
                                    </form>

                                    <div style="
                                    border-top: 2px dashed #adadad;
                                    margin-top: 15px;
                                    font-size: 15px;
                                    ">
                                        (บริการจัดส่ง)
                                        <br>
                                        <small>
                                            อาจมีการคิดค่าบริการในการจัดส่งเพิ่ม
                                            เนื่องจากเราคำนวนจากความจุของขนาดรถและระยะทางการจัดส่ง
                                            เราจะให้แอดมินติดต่อไป
                                        </small>
                                        
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-12">
                                <div class="p-1">
                                    <h5 data-key-lang="summary_order" lang="US">สรุปข้อมูลคำสั่งซื้อ</h5>
                                </div>

                                <div id="show_summary_order"></div>

                            </div>

                            <div class="col-md-12">
                                <div class="p-1">
                                    <h5 data-key-lang="Payment" lang="US">วิธีการชำระเงิน</h5>
                                </div>

                                <div class="box-pay-detail">
                                <form id="formPay">
                                    <input type="radio" id="bankPay" class="pay_channel" name="pay_channel" value="1">
                                    <label data-key-lang="bank_transfer" lang="US" for="bankPay">โอนเงินผ่านธนาคาร</label><br>
                                    <input type="radio" id="promptPay" class="pay_channel" name="pay_channel" value="2">
                                    <label data-key-lang="promptpay_transfer" lang="US" for="promptPay">พร้อมเพย์</label><br>
                                </form>
                                </div>

                            </div>

                            <div class="col-md-12">

                                <div class="customized" style="display: none;">
                                    <div class="p-1 d-sm-flex">
                                        <button type="button" class="btn btn-success" id="submitA" data-key-lang="confirm" lang="US">ยืนยัน</button>
                                    </div>
                                </div>

                                <div class="storefront" style="display: none;">
                                    <div class="p-1 d-sm-flex">
                                        <button type="button" class="btn btn-success" id="submitB" data-key-lang="confirm" lang="US">ยืนยัน</button>
                                    </div>
                                </div>
                                

                            </div>

                        </div>
                    
                </div>
            </div>
            
        </div>
    </div>
</div>