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
                </div>
            </div> -->
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





<?php
// เพิ่มโค้ดส่วนนี้เพื่อสร้าง URL ของหน้าปัจจุบัน
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$pageUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$subjectTitle = "Greentech AI - Sustainable Technology Innovation"; 

// Mock Data สำหรับข่าว
$mockNews = [
    [
        'title' => 'AI-Powered Solar Panel Optimization Increases Efficiency by 35%',
        'date' => '2025-09-28',
        'category' => 'Innovation',
        'image' => 'https://images.unsplash.com/photo-1509391366360-2e959784a276?w=800',
        'excerpt' => 'Revolutionary machine learning algorithm optimizes solar panel angles in real-time based on weather patterns and sun position.'
    ],
    [
        'title' => 'Smart Grid Technology Reduces Energy Waste in Urban Areas',
        'date' => '2025-09-25',
        'category' => 'Smart City',
        'image' => 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=800',
        'excerpt' => 'New AI system manages city-wide energy distribution, cutting waste by 40%.'
    ],
    [
        'title' => 'Green Data Centers Now 100% Carbon Neutral',
        'date' => '2025-09-22',
        'category' => 'Sustainability',
        'image' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800',
        'excerpt' => 'Latest cooling technologies and renewable energy integration achieve zero emissions.'
    ],
    [
        'title' => 'AI Predicts Crop Yields with 98% Accuracy',
        'date' => '2025-09-20',
        'category' => 'AgriTech',
        'image' => 'https://images.unsplash.com/photo-1625246333195-78d9c38ad449?w=800',
        'excerpt' => 'Farmers leverage AI for sustainable agriculture practices.'
    ]
];

// Mock Data สำหรับโครงการ
$mockProjects = [
    [
        'title' => 'Smart Forest Monitoring System',
        'description' => 'AI-powered drone network monitoring forest health and preventing illegal logging across 50,000 hectares',
        'image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=800',
        'status' => 'Active',
        'impact' => '2.5M trees protected'
    ],
    [
        'title' => 'Ocean Plastic Collection AI',
        'description' => 'Autonomous vessels using computer vision to identify and collect ocean plastic waste efficiently',
        'image' => 'https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=800',
        'status' => 'Active',
        'impact' => '150 tons collected'
    ],
    [
        'title' => 'Carbon Capture Optimization',
        'description' => 'Machine learning algorithms optimizing industrial carbon capture processes to maximize efficiency',
        'image' => 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=800',
        'status' => 'Pilot Phase',
        'impact' => '10K tons CO2 captured'
    ],
    [
        'title' => 'Green Building AI Assistant',
        'description' => 'Smart system managing building energy consumption, reducing costs by 45% through predictive analytics',
        'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800',
        'status' => 'Deployed',
        'impact' => '500 buildings optimized'
    ]
];

// Mock Data สำหรับบทความ
$mockArticles = [
    [
        'title' => 'The Future of Renewable Energy: How AI is Revolutionizing Solar and Wind Power',
        'author' => 'Dr. Sarah Chen',
        'date' => '2025-09-27',
        'image' => 'https://images.unsplash.com/photo-1509391366360-2e959784a276?w=600',
        'readTime' => '8 min read'
    ],
    [
        'title' => 'Machine Learning for Climate Change: Predictive Models That Could Save Our Planet',
        'author' => 'Prof. Michael Rodriguez',
        'date' => '2025-09-24',
        'image' => 'https://images.unsplash.com/photo-1611273426858-450d8e3c9fce?w=600',
        'readTime' => '12 min read'
    ],
    [
        'title' => 'Smart Cities 2025: Integration of Green Technology and Artificial Intelligence',
        'author' => 'Emily Thompson',
        'date' => '2025-09-21',
        'image' => 'https://images.unsplash.com/photo-1480714378408-67cf0d13bc1b?w=600',
        'readTime' => '10 min read'
    ],
    [
        'title' => 'Sustainable Agriculture: AI-Powered Farming for a Greener Tomorrow',
        'author' => 'James Park',
        'date' => '2025-09-18',
        'image' => 'https://images.unsplash.com/photo-1625246333195-78d9c38ad449?w=600',
        'readTime' => '7 min read'
    ]
];

// Mock Data สำหรับวิดีโอ
$mockVideos = [
    [
        'title' => 'How AI is Transforming Renewable Energy',
        'thumbnail' => 'https://images.unsplash.com/photo-1497435334941-8c899ee9e8e9?w=600',
        'duration' => '12:45',
        'views' => '2.5M views'
    ],
    [
        'title' => 'Inside Our Smart Forest Monitoring Project',
        'thumbnail' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=600',
        'duration' => '8:30',
        'views' => '1.8M views'
    ],
    [
        'title' => 'Green Data Centers: The Future of Cloud Computing',
        'thumbnail' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=600',
        'duration' => '10:15',
        'views' => '1.2M views'
    ],
    [
        'title' => 'AI for Ocean Conservation: Our Mission',
        'thumbnail' => 'https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=600',
        'duration' => '15:20',
        'views' => '3.1M views'
    ]
];
?>
<style>
/* เพิ่มโค้ดส่วนนี้เข้าไป */
body, html {
    overflow-x: hidden;
    background-color: #f5f7fa;
}

/* สไตล์สำหรับ line-ref ที่แก้ไขแล้ว */
.line-ref, .line-ref1 {
    display: block;
    margin-bottom: 2rem;
    font-size: 2rem;
    font-weight: 700;
    color: #1a3a2e;
    position: relative;
    text-align: left;
    width: fit-content;
    padding-left: 20px;
}
.line-ref:after, .line-ref1:after {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    transform: translateY(-50%);
    width: 4px;
    height: 3rem;
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(34, 197, 94, 0.3);
}

.line-ref2 {
    display: block;
    margin-bottom: 2rem;
    font-size: 2rem;
    font-weight: 700;
    color: #fff;
    position: relative;
    text-align: left;
    width: fit-content;
    padding-left: 20px;
}
.line-ref2:after {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    transform: translateY(-50%);
    width: 4px;
    height: 3rem;
    background-color: #fff;
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(255, 255, 255, 0.3);
}

/* สไตล์สำหรับส่วน "อะไรใหม่" */
.news-wrapper {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 20px;
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
}

.news-main-card {
    display: flex;
    flex-direction: column;
    border-radius: 16px;
    overflow: hidden;
    background-color: #fff;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    transition: transform 0.4s ease, box-shadow 0.4s ease;
}

.news-main-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(34, 197, 94, 0.15);
}

.news-main-image-wrapper {
    position: relative;
    padding-top: 56.25%;
    overflow: hidden;
}

.news-category-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: white;
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    z-index: 10;
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
}

.news-main-img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.news-main-card:hover .news-main-img {
    transform: scale(1.08);
}

.news-main-body {
    padding: 2rem;
}

.news-main-title {
    font-weight: 700;
    font-size: 1.6rem;
    margin-bottom: 0.8rem;
    color: #1a3a2e;
    line-height: 1.4;
}

.news-main-excerpt {
    color: #6b7280;
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.news-main-info {
    font-size: 0.9rem;
    color: #16a34a;
    font-weight: 500;
}

.news-side-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

.news-side-card {
    display: flex;
    background-color: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.news-side-card:hover {
    transform: translateX(5px);
    box-shadow: 0 8px 25px rgba(34, 197, 94, 0.12);
}

.news-side-img-wrapper {
    flex-shrink: 0;
    width: 140px;
    height: 140px;
    position: relative;
    overflow: hidden;
}

.news-side-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.news-side-card:hover .news-side-img {
    transform: scale(1.1);
}

.news-side-body {
    padding: 1.2rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.news-side-category {
    color: #16a34a;
    font-size: 0.8rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
}

.news-side-title {
    font-weight: 600;
    font-size: 1rem;
    margin: 0 0 0.5rem 0;
    line-height: 1.4em;
    color: #1a3a2e;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.news-side-date {
    font-size: 0.85rem;
    color: #9ca3af;
}

/* สไตล์สำหรับโครงการ */
.projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.project-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    transition: transform 0.4s ease, box-shadow 0.4s ease;
    position: relative;
}

.project-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 16px 40px rgba(34, 197, 94, 0.15);
}

.project-image-wrapper {
    position: relative;
    padding-top: 66.67%;
    overflow: hidden;
}

.project-status-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255, 255, 255, 0.95);
    color: #16a34a;
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
    z-index: 10;
    backdrop-filter: blur(10px);
}

.project-img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.project-card:hover .project-img {
    transform: scale(1.1);
}

.project-body {
    padding: 1.8rem;
}

.project-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1a3a2e;
    margin-bottom: 0.8rem;
    line-height: 1.3;
}

.project-description {
    color: #6b7280;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.project-impact {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #16a34a;
    font-weight: 600;
    font-size: 0.9rem;
}

.project-impact i {
    font-size: 1.1rem;
}

/* สไตล์สำหรับบทความ */
.articles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.article-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    transition: transform 0.4s ease, box-shadow 0.4s ease;
}

.article-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 16px 40px rgba(34, 197, 94, 0.15);
}

.article-image-wrapper {
    position: relative;
    padding-top: 60%;
    overflow: hidden;
}

.article-img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.article-card:hover .article-img {
    transform: scale(1.08);
}

.article-body {
    padding: 1.8rem;
}

.article-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    font-size: 0.85rem;
}

.article-author {
    color: #16a34a;
    font-weight: 600;
}

.article-date {
    color: #9ca3af;
}

.article-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1a3a2e;
    line-height: 1.4;
    margin-bottom: 0.8rem;
}

.article-read-time {
    color: #6b7280;
    font-size: 0.9rem;
}

/* สไตล์สำหรับวิดีโอ */
.videos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.video-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    transition: transform 0.4s ease, box-shadow 0.4s ease;
    cursor: pointer;
}

.video-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 16px 40px rgba(34, 197, 94, 0.15);
}

.video-thumbnail-wrapper {
    position: relative;
    padding-top: 56.25%;
    overflow: hidden;
}

.video-play-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 70px;
    height: 70px;
    background: rgba(34, 197, 94, 0.95);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    transition: all 0.3s ease;
}

.video-card:hover .video-play-overlay {
    background: rgba(22, 163, 74, 1);
    transform: translate(-50%, -50%) scale(1.1);
}

.video-play-overlay i {
    color: white;
    font-size: 2rem;
    margin-left: 4px;
}

.video-duration {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
}

.video-thumbnail {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.video-card:hover .video-thumbnail {
    transform: scale(1.08);
}

.video-body {
    padding: 1.5rem;
}

.video-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1a3a2e;
    line-height: 1.4;
    margin-bottom: 0.8rem;
}

.video-views {
    color: #6b7280;
    font-size: 0.9rem;
}

/* สไตล์สำหรับพื้นหลัง */
.section.product-bg {
    background-color: #ffffff;
    padding: 40px 0;
}

.box-content-shop {
    background-color: transparent;
    padding: 20px;
}

/* สไตล์สำหรับพื้นหลังสีเขียวเต็มจอ */
.full-width-bg {
    background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
    width: 100vw;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    padding: 60px 0;
    box-shadow: 0 10px 40px rgba(22, 163, 74, 0.2);
}

.full-width-content {
    max-width: 95%;
    margin: 0 auto;
}

/* สไตล์สำหรับปุ่มแชร์ */
.copy-link-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border: 2px solid #22c55e;
    background-color: #fff;
    color: #16a34a;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 0;
    font-size: 1.3rem;
}

.copy-link-btn:hover {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    border-color: #16a34a;
    color: #fff;
    transform: scale(1.05);
}

.social-share a img {
    transition: transform 0.3s ease;
    border-radius: 12px;
}

.social-share a:hover img {
    transform: scale(1.1);
}

/* Responsive */
@media (max-width: 992px) {
    .news-wrapper {
        grid-template-columns: 1fr;
    }
    .projects-grid, .articles-grid, .videos-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }
}

@media (max-width: 576px) {
    .news-wrapper {
        padding: 0 15px;
    }
    .news-side-img-wrapper {
        width: 100px;
        height: 100px;
    }
    .line-ref, .line-ref1, .line-ref2 {
        font-size: 1.5rem;
    }
}
</style>

<div class="content-sticky" id="" style="margin: 0 auto;">
    <div style="max-width: 90%;">
        <div class="row">
            <div class="col-md-12 text-end" style="padding-top: 30px;">
                <div class="social-share" style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
                    <p data-translate="share" lang="th" style="margin: 0; font-size:18px; font-family: sans-serif; color: #1a3a2e; font-weight: 600;">Share this page:</p>
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
            
            <div class="col-md-12 section" style="padding-top: 40px;">
                <h4 data-translate="WhatsNew" class="line-ref1" lang="th">What's New</h4>
                <div class="box-content">
                    <div class="news-wrapper">
                        <!-- Main News Card -->
                        <div class="news-main-card">
                            <div class="news-main-image-wrapper">
                                <span class="news-category-badge"><?= $mockNews[0]['category'] ?></span>
                                <img src="<?= $mockNews[0]['image'] ?>" alt="<?= $mockNews[0]['title'] ?>" class="news-main-img">
                            </div>
                            <div class="news-main-body">
                                <h3 class="news-main-title"><?= $mockNews[0]['title'] ?></h3>
                                <p class="news-main-excerpt"><?= $mockNews[0]['excerpt'] ?></p>
                                <p class="news-main-info"><?= date('M d, Y', strtotime($mockNews[0]['date'])) ?></p>
                            </div>
                        </div>
                        
                        <!-- Side News Grid -->
                        <div class="news-side-grid">
                            <?php for($i = 1; $i < count($mockNews); $i++): ?>
                            <div class="news-side-card">
                                <div class="news-side-img-wrapper">
                                    <img src="<?= $mockNews[$i]['image'] ?>" alt="<?= $mockNews[$i]['title'] ?>" class="news-side-img">
                                </div>
                                <div class="news-side-body">
                                    <span class="news-side-category"><?= $mockNews[$i]['category'] ?></span>
                                    <h4 class="news-side-title"><?= $mockNews[$i]['title'] ?></h4>
                                    <p class="news-side-date"><?= date('M d, Y', strtotime($mockNews[$i]['date'])) ?></p>
                                </div>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="full-width-bg">
    <div class="full-width-content">
        <div class="row">
            <div class="col-md-12 section">
                <div class="box-content-shop">
                    <h4 data-translate="project1" lang="th" class="line-ref2">Latest Projects</h4>
                    <div class="projects-grid">
                        <?php foreach($mockProjects as $project): ?>
                        <div class="project-card">
                            <div class="project-image-wrapper">
                                <span class="project-status-badge"><?= $project['status'] ?></span>
                                <img src="<?= $project['image'] ?>" alt="<?= $project['title'] ?>" class="project-img">
                            </div>
                            <div class="project-body">
                                <h3 class="project-title"><?= $project['title'] ?></h3>
                                <p class="project-description"><?= $project['description'] ?></p>
                                <div class="project-impact">
                                    <i class="fas fa-chart-line"></i>
                                    <span><?= $project['impact'] ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-sticky" id="" style="margin: 0 auto;">
    <div style="max-width: 90%;">
        <div class="row">
            <div class="col-md-12 section" style="padding-top: 60px;">
                <h4 data-translate="blog" lang="th" class="line-ref">Articles</h4>
                <div class="box-content">
                    <div class="articles-grid">
                        <?php foreach($mockArticles as $article): ?>
                        <div class="article-card">
                            <div class="article-image-wrapper">
                                <img src="<?= $article['image'] ?>" alt="<?= $article['title'] ?>" class="article-img">
                            </div>
                            <div class="article-body">
                                <div class="article-meta">
                                    <span class="article-author"><?= $article['author'] ?></span>
                                    <span class="article-date"><?= date('M d, Y', strtotime($article['date'])) ?></span>
                                </div>
                                <h3 class="article-title"><?= $article['title'] ?></h3>
                                <p class="article-read-time"><i class="far fa-clock"></i> <?= $article['readTime'] ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-12 section" style="padding-top: 40px;">
                <div class="box-content">
                    <h4 data-translate="video" lang="th" class="line-ref">Featured Videos</h4>
                    <div class="videos-grid">
                        <?php foreach($mockVideos as $video): ?>
                        <div class="video-card">
                            <div class="video-thumbnail-wrapper">
                                <div class="video-play-overlay">
                                    <i class="fas fa-play"></i>
                                </div>
                                <span class="video-duration"><?= $video['duration'] ?></span>
                                <img src="<?= $video['thumbnail'] ?>" alt="<?= $video['title'] ?>" class="video-thumbnail">
                            </div>
                            <div class="video-body">
                                <h3 class="video-title"><?= $video['title'] ?></h3>
                                <p class="video-views"><i class="far fa-eye"></i> <?= $video['views'] ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-12 text-start" style="padding: 40px 0 60px 0;">
                <p data-translate="share" lang="th" style="margin: 0; padding-bottom: 10px; font-size:18px; font-family: sans-serif; color: #1a3a2e; font-weight: 600;">Share this page:</p>
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
            // Create success notification
            const notification = document.createElement('div');
            notification.textContent = '✓ Link copied successfully!';
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
                color: white;
                padding: 15px 25px;
                border-radius: 12px;
                font-weight: 600;
                box-shadow: 0 8px 24px rgba(34, 197, 94, 0.3);
                z-index: 9999;
                animation: slideIn 0.3s ease;
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 2000);
        }, function() {
            alert("Unable to copy link. Please copy manually.");
        });
    }
    
    // Add animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">