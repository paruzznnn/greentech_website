<?php include '../../routes.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../../inc-meta.php'; ?>
    <link href="../../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../../css/user/template-track.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../../inc-cdn.php'; ?>

</head>

<body>
    <?php include '../../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_track" class="section-space">
            <div class="container">
                <div class="large-steps" id="large-steps-container"></div>
                <hr class="dashed-line">
                <div class="section-header">
                    <h2 style="font-size: 18px; color: #1f2937; font-weight: bold;">ที่อยู่ในการจัดส่ง</h2>
                    <div class="text-right small-text">
                        <p>ขนส่งบริษัทแทรนดาร์</p>
                        <p>TH253025994610B</p>
                        <p>คนขับรถ: เปรมชัย, <a href="">0970727598</a></p>
                    </div>
                </div>
                <hr class="dashed-line">
                <div class="shipping-info">
                    <div class="address-box">
                        <p class="font-semibold" style="color:#1f2937;">กิตตินันท์ธนัช สีแก้วน้ำใส</p>
                        <p class="small-text">0838945256</p>
                        <p class="small-text">โครงการลาดกระบังประชา 114/14 แขวงลำผักชี เขตหนองจอก จังหวัดกรุงเทพมหานคร 10530</p>
                    </div>
                    <div class="timeline-container-wrapper">
                        <div class="timeline" id="timeline-container"></div>
                        <div id="view-more-timeline"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include '../../template/footer-bar.php'; ?>

    <script>
        const trackApp = {
            viewAll: false,
            largeStepsData: [{
                    title: "มีคำสั่งซื้อใหม่",
                    date: "02-08-2025 16:27",
                    icon: '<i class="bi bi-box-seam"></i>',
                    active: true
                },
                {
                    title: "ชำระเงินแล้ว (B145)",
                    date: "02-08-2025 16:27",
                    icon: '<i class="bi bi-credit-card"></i>',
                    active: true
                },
                {
                    title: "จัดส่งแล้ว",
                    date: "03-08-2025 11:38",
                    icon: '<i class="bi bi-truck"></i>',
                    active: true
                },
                {
                    title: "ตรวจสอบและยอมรับ",
                    date: "04-08-2025 21:27",
                    icon: '<i class="bi bi-check2-circle"></i>',
                    active: true
                },
                {
                    title: "คำสั่งซื้อสำเร็จ",
                    date: "03-09-2025 23:59",
                    icon: '<i class="bi bi-star"></i>',
                    active: false
                }
            ],

            timelineData: [{
                    status: "การจัดส่งสำเร็จ",
                    detail: "พัสดุถูกจัดส่งสำเร็จเรียบร้อย",
                    recipient: "กิตตินันท์ธนัช สีแก้วน้ำใส",
                    driver: "เปรมชัย",
                    phone: "0970727598",
                    date: "04-08-2025 16:52",
                    active: true
                },
                {
                    status: "อยู่ระหว่างการขนส่ง",
                    detail: "พัสดุอยู่ระหว่างการนำส่ง",
                    date: "04-08-2025 11:35"
                },
                {
                    status: "พัสดุถูกส่งมอบให้พนักงานขนส่ง",
                    date: "04-08-2025 11:35"
                },
                {
                    status: "ถึงสาขาปลายทาง: หนองจอก",
                    date: "04-08-2025 04:47"
                },
                {
                    status: "ออกจากศูนย์คัดแยก",
                    date: "03-08-2025 23:34"
                },
                {
                    status: "เข้ารับพัสดุเรียบร้อยแล้ว",
                    date: "03-08-2025 14:15"
                },
                {
                    status: "เตรียมพัสดุ",
                    detail: "ผู้ขายกำลังเตรียมพัสดุ",
                    date: "02-08-2025 16:40"
                },
                {
                    status: "สั่งซื้อสินค้า",
                    detail: "คำสั่งซื้อสำเร็จ",
                    date: "02-08-2025 16:27",
                    link: ""
                }
            ],

            renderLargeSteps() {
                const activeSteps = this.largeStepsData.filter(step => step.active).length;
                const progress = (activeSteps / this.largeStepsData.length) * 100;

                const largeStepsContainer = document.getElementById('large-steps-container');
                largeStepsContainer.style.setProperty('--progress', `${progress}%`);

                largeStepsContainer.innerHTML = this.largeStepsData.map(step => `
                    <div class="large-step-item ${step.active ? 'active':''}">
                        <div class="large-step-icon">${step.icon}</div>
                        <span class="large-step-text">${step.title}</span>
                        <span class="large-step-date">${step.date}</span>
                    </div>
                `).join('');
            },

            renderTimeline() {
                const timelineContainer = document.getElementById('timeline-container');
                const viewMoreContainer = document.getElementById('view-more-timeline');
                
                let itemsToShow = this.timelineData;
                if (!this.viewAll && this.timelineData.length > 5) {
                    itemsToShow = this.timelineData.slice(0, 5);
                    viewMoreContainer.innerHTML = `<button class="view-more-btn">ดูทั้งหมด</button>`;
                } else {
                    viewMoreContainer.innerHTML = ''; // Hide the button when all are shown
                }

                timelineContainer.innerHTML = itemsToShow.map(item => `
                    <div class="timeline-item ${item.active?'active':''}">
                        <div class="timeline-item-icon"></div>
                        <div class="timeline-item-detail">
                            <div class="date-time">${item.date}</div>
                            <h4>${item.status}</h4>
                            ${item.detail ? `<p>${item.detail}</p>` : ''}
                            ${item.recipient ? `<p class="font-semibold">ผู้รับ: ${item.recipient}</p>` : ''}
                            ${item.driver ? `<p>คนขับรถ: ${item.driver}, <a href="tel:${item.phone}">${item.phone}</a></p>` : ''}
                            ${item.link ? `<a>${item.link}</a>` : ''}
                        </div>
                    </div>
                `).join('');
                
                if (!this.viewAll && this.timelineData.length > 5) {
                    document.querySelector('.view-more-btn').addEventListener('click', () => {
                        this.viewAll = true;
                        this.renderTimeline();
                    });
                }
            },

            init() {
                this.renderLargeSteps();
                this.renderTimeline();
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            trackApp.init();
        });
    </script>
</body>

</html>