<style>
/* สไตล์สำหรับ line-ref ที่แก้ไขแล้ว */
.line-ref, .line-ref1 {
    display: block;
    margin-bottom: 1.5rem;
    /* padding-bottom: 0.5rem; */
    font-size: 1.8rem;
    font-weight: 600;
    color: #555;
    position: relative;
    /* เพิ่มการจัดชิดซ้ายและปรับความกว้าง */
    text-align: left;
    width: fit-content;
    padding-left: 15px; /* เพิ่ม padding เพื่อเว้นช่องว่างให้เส้น */
}
.line-ref:after, .line-ref1:after {
    content: '';
    position: absolute;
    /* เปลี่ยนจากแนวนอนเป็นแนวตั้ง */
    top: 50%;
    left: 0;
    transform: translateY(-50%);
    width: 3px;
    height: 2.5rem;
    background-color: #555;
    border-radius: 2px;
}

.line-ref2 {
    display: block;
    margin-bottom: 1.5rem;
    /* padding-bottom: 0.5rem; */
    font-size: 1.8rem;
    font-weight: 600;
    color: #555;
    position: relative;
    /* เพิ่มการจัดชิดซ้ายและปรับความกว้าง */
    text-align: left;
    width: fit-content;
    padding-left: 15px; /* เพิ่ม padding เพื่อเว้นช่องว่างให้เส้น */
}
.line-ref2:after {
    content: '';
    position: absolute;
    /* เปลี่ยนจากแนวนอนเป็นแนวตั้ง */
    top: 50%;
    left: 0;
    transform: translateY(-50%);
    width: 3px;
    height: 2.5rem;
    background-color: #fff;
    border-radius: 2px;
}
/* สไตล์สำหรับส่วน "อะไรใหม่" ที่ปรับปรุงใหม่ */
.news-wrapper {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 40px;
    display: grid;
    /* แบ่งคอลัมน์หลักเป็น 2 ส่วน: ส่วนใหญ่ 2/3 และส่วนเล็ก 1/3 */
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
}

.news-main-card {
    display: flex;
    flex-direction: column;
    border-radius: 8px;
    overflow: hidden;
    background-color: #fff;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.news-main-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
}

.news-main-image-wrapper {
    position: relative;
    /* อัตราส่วน 16:9 */
    padding-top: 56.25%;
    overflow: hidden;
}

.news-main-img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.news-main-card:hover .news-main-img {
    transform: scale(1.05);
}

.news-main-body {
    padding: 1.5rem;
}

.news-main-title {
    font-weight: bold;
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    color: #222;
}

.news-main-info {
    font-size: 0.9rem;
    color: #777;
}

.news-side-grid {
    display: grid;
    grid-template-columns: 1fr;
    grid-template-rows: repeat(auto-fill, minmax(100px, 1fr));
    gap: 1.5rem;
}

.news-side-card {
    display: flex;
    background-color: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    transition: box-shadow 0.3s ease;
}

.news-side-card:hover {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
}

.news-side-img-wrapper {
    flex-shrink: 0;
    width: 120px; /* ความกว้างของรูปภาพด้านข้าง */
    height: 100%;
    position: relative;
}

.news-side-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.news-side-body {
    padding: 1rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.news-side-title {
    font-weight: 600;
    font-size: 0.9rem;
    margin: 0;
    line-height: 1.3em;
    color: #333;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Responsive */
@media (max-width: 992px) {
    .news-wrapper {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 576px) {
    .news-wrapper {
        padding: 0 20px;
    }
    .news-side-img-wrapper {
        width: 100px;
    }
}

/* สไตล์สำหรับบล็อกสินค้าเดิม (ไม่ได้แก้ไข) */
.section.product-bg {
    background-color: #ffffffff;
    padding: 30px 0;
    /* border-radius: 10px; */
}
.box-content-shop{
    background-color: #ffead0;
    padding: 40px 20px 20px 20px;
    border-radius: 8px;
    color: #555;
}
.article-luxury-section {
    background-color: #f8f9fa;
    padding: 40px 0;
    border-radius: 6px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
}
/* .bottom-shasow{
    padding: 2em 3em;
    border-bottom: 4px solid #bfbfbf;
    box-shadow: 0px 4px 0px 0px #bfbfbfa8;
} */
</style>
<div class="content-sticky" id="" style=" margin: 0 auto;">
    <div style="max-width: 95%;">
        <div class="row">

            <div class="col-md-12 section bottom-shasow" >
                 <h4 data-translate="WhatsNew" class="line-ref1" lang="th" >What's New</h4>
                <div class="box-content" >
                    <?php include 'template/news/news_home.php'; ?>
                </div>
            </div>

           <style>
/* เพิ่มสไตล์สำหรับบล็อกสินค้า */
.section.product-bg {
    background-color: #ffffffff; /* ใช้สีส้มอ่อนๆ */
    padding: 30px 0; /* เพิ่ม padding เพื่อให้มีพื้นที่รอบๆ เนื้อหา */
    /* border-radius: 0px; เพิ่มขอบมนเล็กน้อย */
}
.box-content-shop{
    background-color: #ff9900;
    padding: 40px 20px 50px 20px;
    border-radius: 8px;
    color: #555;
}
.article-luxury-section {
    background-color: #f8f9fa; /* สีพื้นหลังอ่อนๆ เพื่อให้การ์ดดูเด่นขึ้น */
    padding: 40px 0; /* เพิ่มพื้นที่ด้านบนและล่าง */
    border-radius: 6px; /* ขอบมนเพื่อให้ดูหรูหรา */
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); /* เงาบางๆ ที่บล็อกทั้งหมด */
}
.shadow-divider {
    border: none;
    height: 5px; /* ทำให้เส้นหนาขึ้น */
    background-color: #000000; /* กำหนดสีเส้นเป็นสีดำเข้มสุดๆ */
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.75); /* ปรับเงาให้เข้มและฟุ้งลงมาด้านล่าง */
    margin: 2.5rem 0; /* เพิ่มระยะห่างด้านบนและล่างให้ชัดเจน */
    width: 100%;
}
</style>
<!-- <hr class="shadow-divider">  -->
<div class="col-md-12 section product-bg bottom-shasow" style="padding-top:40px;">
    <h4 class="line-ref" >Product</h4>
    <div class="box-content">
        
        <?php include 'template/product/shop_home.php'; ?>
    </div>
</div>

<!-- <hr class="shadow-divider">  -->

            <div class="col-md-12 section bottom-shasow">
                <div class="box-content-shop">
                    <h4 class="line-ref2" style="color:#fff;">โครงการล่าสุด</h4>
                    <?php include 'template/project/project_home.php'; ?>
                </div>
            </div>

            <!-- <hr class="shadow-divider">  -->

            <div class="col-md-12 section bottom-shasow "style="padding-top:3rem;">
                <h4 class="line-ref">บทความ</h4>
    <div class="box-content">
        
        <?php include 'template/Blog/Blog_home.php'; ?>
    </div>
</div>

<!-- <hr class="shadow-divider">  -->

            <div class="col-md-12 section bottom-shasow">
                <div class="box-content">
                    <h4 class="line-ref">วิดีโอแนะนำ</h4>
                    <?php include 'template/video/video_home.php'; ?>
                </div>
            </div>
           
            </div>
             <div class="social-share">
                    <p>แชร์หน้านี้:</p>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($pageUrl) ?>" target="_blank">
                        <img src="https://img.icons8.com/color/48/000000/facebook-new.png" alt="Share on Facebook">
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode($pageUrl) ?>&text=<?= urlencode($subjectTitle) ?>" target="_blank">
                        <img src="https://img.icons8.com/color/48/000000/twitter--v1.png" alt="Share on Twitter">
                    </a>
                    <a href="https://social-plugins.line.me/lineit/share?url=<?= urlencode($pageUrl) ?>" target="_blank">
                        <img src="https://img.icons8.com/color/48/000000/line-me.png" alt="Share on Line">
                    </a>
                    <a href="https://pinterest.com/pin/create/button/?url=<?= urlencode($pageUrl) ?>&description=<?= urlencode($subjectTitle) ?>" target="_blank">
                        <img src="https://img.icons8.com/color/48/000000/pinterest--v1.png" alt="Share on Pinterest">
                    </a>
                    <a href="https://www.instagram.com/" target="_blank">
                        <img src="https://img.icons8.com/fluency/48/instagram-new.png" alt="Share on Instagram">
                    </a>
                    <a href="https://www.tiktok.com/" target="_blank">
                        <img src="https://img.icons8.com/fluency/48/tiktok.png" alt="Share on TikTok">
                    </a>
                    <button class="copy-link-btn" onclick="copyLink()">คัดลอกลิงก์</button>
                </div>


    </div>
</div>