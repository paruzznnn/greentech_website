<?php
// เพิ่มโค้ดส่วนนี้เพื่อสร้าง URL ของหน้าปัจจุบัน
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$pageUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$subjectTitle = "Trandar - Website Title"; // กำหนดหัวข้อสำหรับแชร์ (สามารถเปลี่ยนได้)
?>
<style>
/* เพิ่มโค้ดส่วนนี้เข้าไป */
body, html {
    overflow-x: hidden;
}

/* สไตล์สำหรับ line-ref ที่แก้ไขแล้ว */
.line-ref, .line-ref1 {
    display: block;
    margin-bottom: 1.5rem;
    font-size: 1.8rem;
    font-weight: 600;
    color: #555;
    position: relative;
    text-align: left;
    width: fit-content;
    padding-left: 15px;
}
.line-ref:after, .line-ref1:after {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    transform: translateY(-50%);
    width: 3px;
    height: 2.5rem;
    background-color: #ff9900;
    border-radius: 2px;
}

.line-ref2 {
    display: block;
    margin-bottom: 1.5rem;
    font-size: 1.8rem;
    font-weight: 600;
    color: #555;
    position: relative;
    text-align: left;
    width: fit-content;
    padding-left: 15px;
}
.line-ref2:after {
    content: '';
    position: absolute;
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
    width: 120px;
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

/* สไตล์สำหรับบล็อกสินค้าเดิม */
.section.product-bg {
    background-color: #ffffffff;
    padding: 30px 0;
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

/* สไตล์สำหรับพื้นหลังสีส้มเต็มจอ */
.full-width-bg {
    background-color: #ff9900;
    width: 100vw;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    padding: 20px 0;
}
.full-width-content {
    max-width: 95%;
    margin: 0 auto;
}
.full-width-content .line-ref2 {
    color: #fff;
}

/* สไตล์สำหรับปุ่มคัดลอกลิงก์ที่ปรับปรุงใหม่ */
.copy-link-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 48px; /* ปรับขนาดให้เท่ากับไอคอนอื่น */
    height: 48px;
    border: 1px solid #ccc;
    background-color: #fff;
    color: #666;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 0;
    font-size: 1.5rem;
}

.copy-link-btn:hover {
    background-color: #f0f0f0;
    border-color: #999;
    color: #333;
}
</style>

<div class="content-sticky" id="" style=" margin: 0 auto;">
    <div style="max-width: 90%;">
        <div class="row">
            <div class="col-md-12 text-end" style="padding-top: 30px;">
                <div class="social-share" style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
                    <p data-translate="share" lang="th" style="margin: 0; font-size:18px; font-family: sans-serif;">แชร์หน้านี้:</p>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($pageUrl) ?>" target="_blank">
                            <img src="https://img.icons8.com/color/48/000000/facebook-new.png" alt="Share on Facebook">
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode($pageUrl) ?>&text=<?= urlencode($subjectTitle) ?>" target="_blank">
                             <img style="height: 37px; border-radius: 6px;"src="https://cdn.prod.website-files.com/5d66bdc65e51a0d114d15891/64cebdd90aef8ef8c749e848_X-EverythingApp-Logo-Twitter.jpg" alt="Share on Twitter">
                        </a>
                        <a href="https://social-plugins.line.me/lineit/share?url=<?= urlencode($pageUrl) ?>" target="_blank">
                            <img src="https://img.icons8.com/color/48/000000/line-me.png" alt="Share on Line">
                        </a>
                        <a href="https://pinterest.com/pin/create/button/?url=<?= urlencode($pageUrl) ?>" target="_blank">
                            <img src="https://img.icons8.com/color/48/000000/pinterest--v1.png" alt="Share on Pinterest">
                        </a>
                        <a href="https://www.instagram.com/" target="_blank">
                            <img src="https://img.icons8.com/fluency/48/instagram-new.png" alt="Share on Instagram">
                        </a>
                        <a href="https://www.tiktok.com/" target="_blank">
                            <img src="https://img.icons8.com/fluency/48/tiktok.png" alt="Share on TikTok">
                        </a>
                        <button class="copy-link-btn" onclick="copyLink()">
                            <i class="fas fa-link"></i> 
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-12 section bottom-shasow" >
                <h4 data-translate="WhatsNew" class="line-ref1" lang="th" >What's New</h4>
                <div class="box-content" >
                    <?php include 'template/news/news_home.php'; ?>
                </div>
            </div>

            <!-- <div class="col-md-12 section product-bg bottom-shasow" style="padding-top:40px;">
                <h4 data-translate="product1" lang="th" class="line-ref" >Product</h4>
                <div class="box-content">
                    <?php include 'template/product/shop_home.php'; ?>
                </div> -->
            </div>
        </div>
    </div>
</div>

<div class="full-width-bg">
    <div class="full-width-content">
        <div class="row">
            <div class="col-md-12 section">
                <div class="box-content-shop" style="background-color: transparent;">
                    <h4 data-translate="project1" lang="th" class="line-ref2">โครงการล่าสุด</h4>
                    <?php include 'template/project/project_home.php'; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-sticky" id="" style=" margin: 0 auto;">
    <div style="max-width: 90%;">
        <div class="row">
            <div class="col-md-12 section bottom-shasow "style="padding-top:3rem;">
                <h4 data-translate="blog" lang="th" class="line-ref">บทความ</h4>
                <div class="box-content">
                    <?php include 'template/Blog/Blog_home.php'; ?>
                </div>
            </div>

            <div class="col-md-12 section bottom-shasow">
                <div class="box-content">
                    <h4 data-translate="video" lang="th" class="line-ref">วิดีโอแนะนำ</h4>
                    <?php include 'template/video/video_home.php'; ?>
                </div>
            </div>

            <div class="col-md-12 text-start" style="padding-bottom:3em;">
                <p data-translate="share" lang="th" style="margin: 0; padding-bottom: 10px; font-size:18px; font-family: sans-serif;">แชร์หน้านี้:</p>
                <div class="social-share" style="display: flex; align-items: center; gap: 10px;">
                    <button class="copy-link-btn" onclick="copyLink()">
                        <i class="fas fa-link"></i>
                    </button>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($pageUrl) ?>" target="_blank">
                        <img src="https://img.icons8.com/color/48/000000/facebook-new.png" alt="Share on Facebook">
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode($pageUrl) ?>&text=<?= urlencode($subjectTitle) ?>" target="_blank">
                        <img style="height: 37px; border-radius: 6px;"src="https://cdn.prod.website-files.com/5d66bdc65e51a0d114d15891/64cebdd90aef8ef8c749e848_X-EverythingApp-Logo-Twitter.jpg" alt="Share on Twitter">
                    </a>
                    <a href="https://social-plugins.line.me/lineit/share?url=<?= urlencode($pageUrl) ?>" target="_blank">
                        <img src="https://img.icons8.com/color/48/000000/line-me.png" alt="Share on Line">
                    </a>
                    <a href="https://pinterest.com/pin/create/button/?url=<?= urlencode($pageUrl) ?>" target="_blank">
                        <img src="https://img.icons8.com/color/48/000000/pinterest--v1.png" alt="Share on Pinterest">
                    </a>
                    <a href="https://www.instagram.com/" target="_blank">
                        <img src="https://img.icons8.com/fluency/48/instagram-new.png" alt="Share on Instagram">
                    </a>
                    <a href="https://www.tiktok.com/" target="_blank">
                        <img src="https://img.icons8.com/fluency/48/tiktok.png" alt="Share on TikTok">
                    </a>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript for Copy Link functionality
    function copyLink() {
        const pageUrl = "<?= $pageUrl ?>";
        navigator.clipboard.writeText(pageUrl).then(function() {
            alert("คัดลอกลิงก์เรียบร้อยแล้ว");
        }, function() {
            alert("ไม่สามารถคัดลอกลิงก์ได้ กรุณาคัดลอกด้วยตนเอง");
        });
    }
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">