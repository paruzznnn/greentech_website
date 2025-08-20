
<div class="row">

    <div class="footer">
        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-4 mt-3">
                
                <div style="padding: 10px;">
                    <h6>ติดต่อเรา</h6>
                    <p>102 พัฒนาการ40 ถนนพัฒนาการ สวนหลวง กรุงเทพฯ 10250</p>
                    <p>โทร : 02 722 7007</p>
                    <p>email : info@trandar.com</p>
                    <p>จันทร์-เสาร์ : 8:30 - 17:00</p>

                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 30px;">
                            <img src="../public/img/facebook_logo.png" style="width: 100%; height: auto;" alt="Facebook">
                        </div>
                        <div style="width: 30px;">
                            <img src="../public/img/line_logo.png" style="width: 100%; height: auto;" alt="Line">
                        </div>
                        <div style="width: 30px;">
                            <img src="../public/img/instagram_logo.png" style="width: 100%; height: auto;" alt="Instagram">
                        </div>
                        <div style="width: 30px;">
                            <img src="../public/img/youtube_logo.png" style="width: 100%; height: auto;" alt="YouTube">
                        </div>
                        <div style="width: 30px;">
                            <img src="../public/img/twitter_logo.png" style="width: 100%; height: auto;" alt="Twitter">
                        </div>
                    </div>


                </div>
                
                
            </div>
            <div class="col-md-4 col-sm-4 col-xs-4 mt-3">

                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6 mt-3">
                        <ul>
                            <li>
                                <a href="#" class="list-group-item">เกี่ยวกับเรา</a>
                            </li>
                            <li>
                                <a href="#" class="list-group-item">คำถามที่พบบ่อย</a>
                            </li>
                            <li>
                                <a href="#" class="list-group-item">ทำไมต้องซื้อกับเรา</a>
                            </li>
                            <li>
                                <a href="#" class="list-group-item">ข้อกำหนดและเงื่อนไข</a>
                            </li>
                            
                        </ul>  
                    </div>

                    <div class="col-md-6 col-sm-6 col-xs-6 mt-3">
                        <ul>
                            <li>
                                <a href="#" class="list-group-item">วิธีการสั่งซื้อสินค้า</a>
                            </li>
                            <li>
                                <a href="#" class="list-group-item">ยืนยันการชำระเงิน</a>
                            </li>
                            <li>
                                <a href="#" class="list-group-item">การรับประกัน และการคืนสินค้า</a>
                            </li>
                        </ul>  
                    </div>

                </div>

            </div>
            
            <div class="col-md-4 col-sm-4 col-xs-4 mt-3">

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 mt-3">
                        <ul>
                            <li>
                                <a href="#" class="list-group-item">นโยบายการจัดส่งสินค้า</a>
                            </li>
                            <li>
                                <a href="#" class="list-group-item">นโยบายการคืนและยกเลิกคำสั่งซื้อ</a>
                            </li>
                            <li>
                                <a href="#" class="list-group-item">นโยบายความเป็นส่วนตัว</a>
                            </li>
                            <li>
                                <a href="#" class="list-group-item">นโยบายเกี่ยวกับคุ้กกี้</a>
                            </li>
                        </ul>  
                    </div>
                </div>
                

            </div>
        </div>

        <hr>
        <p>
            trandar store© 2024. All Rights Reserved. Designed by Allable
        </p>

    </div>
    
    <?php if(!isset($_COOKIE['cookie_consent'])){ ?>

        <?php if(!isset($_SESSION['user_id'])){ ?>
        <div class="alert-cookie">
            <p style="font-size: 14px;">
                เราใช้คุกกี้เพื่อเพิ่มประสิทธิภาพและประสบการณ์ที่ดีในการใช้เว็ปไซต์ 
                ท่านสามารถศึกษารายละเอียดการใช้คุกกี้ได้ที่ นโยบายการใช้คุกกี้ 
                และสามารถเลือกตั้งค่ายินยอมการใช้คุกกี้ได้โดยคลิกที่การตั้งค่าคุกกี้
            </p>
            <div class="button-container">
            
                <button type="button" class="btn btn-outline-warning" onclick="setCookie('cookie_consent', 'declined', 365);">ปฏิเสธคุกกี้</button>
                <button type="button" class="btn-orange" onclick="setCookie('cookie_consent', 'accepted', 365);">
                    <i class="fas fa-cookie-bite" style="margin-right: 5px;"></i> ยอมรับคุกกี้
                </button>
            </div>
        </div>
        <?php } ?>

    <?php } ?>


</div>


