<?php include '../../routes.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../../inc-meta.php'; ?>
    <link href="../../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php include '../../inc-cdn.php'; ?>
    <style>
        .rcp-card { background-color: #fff; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1),0 4px 6px -2px rgba(0,0,0,0.05); padding: 2.5rem; margin-bottom: 2rem; }
        
        /* Flexbox for the new combined card's content */
        .rcp-payment-container { display: flex; flex-direction: column; gap: 2rem; }
        
        .rcp-payment-section .rcp-bank-details {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: center;
        }

        .rcp-bank-details-info {
            text-align: center;
        }

        .rcp-bank-details-info p {
            margin: 0.25rem 0;
            font-size: 1rem;
        }

        .rcp-payment-proof-section .rcp-file-input-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .rcp-proof-preview {
            width: 100%;
            max-width: 400px;
            height: auto;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f9fafb;
            cursor: pointer;
        }

        .rcp-proof-img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 6px;
        }

        .rcp-footer-notes { text-align: center; font-size: 0.875rem; color: #6b7280; }
        .rcp-action-buttons { margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 1rem; }
        .rcp-btn { padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 700; text-align: center; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-decoration: none; cursor: pointer; border: none; }
        .rcp-btn-secondary { background-color: #e5e7eb; color: #374151; }
        .rcp-btn-secondary:hover { background-color: #d1d5db; }
        .rcp-btn-primary { background-color: #4f46e5; color: #fff; }
        .rcp-btn-primary:hover { background-color: #4338ca; }

        
        @media (min-width:640px){
            .rcp-action-buttons{flex-direction:row;}
            
            /* Responsive layout for the new combined container */
            .rcp-payment-container { flex-direction: row; }
            .rcp-payment-section, .rcp-payment-proof-section {
                 flex-basis: 50%; /* Each section takes up half the width */
            }
        }
    </style>
</head>
<body>
    <?php include '../../template/head-bar.php'; ?>
    <main>
        <div id="sections_root_payment" class="section-space">
            <div class="container">

                <div class="rcp-card">
                    <div class="rcp-payment-container">
                        <div class="rcp-payment-section">
                            <h3>วิธีการชำระเงิน</h3>
                            <p>กรุณาชำระเงินตามช่องทางด้านล่างนี้</p>
                            
                            <div class="rcp-bank-select-container">
                                <label for="bank-select">เลือกธนาคาร:</label>
                                <select id="bank-select">
                                    <option value="bbl">ธนาคารกรุงเทพ</option>
                                    <option value="kbank">ธนาคารกสิกรไทย</option>
                                    <option value="scb">ธนาคารไทยพาณิชย์</option>
                                    <option value="ktb">ธนาคารกรุงไทย</option>
                                </select>
                            </div>

                            <!-- <div class="rcp-bank-details">
                                <img id="bank-logo" src="https://placehold.co/120x120/004B8F/ffffff?text=BBL" alt="โลโก้ธนาคาร">
                                <div class="rcp-bank-details-info">
                                    <p id="bank-name">ธนาคารกรุงเทพ</p>
                                    <p><strong>ชื่อบัญชี:</strong> บริษัท ขายดี จำกัด</p>
                                    <p><strong>เลขที่บัญชี:</strong> <span id="account-number">123-4-56789-0</span></p>
                                </div>
                            </div> -->

                        </div>

                        <div class="rcp-payment-proof-section">
                            <h3>หลักฐานการชำระเงิน</h3>
                            <div class="rcp-file-input-container">
                                <div class="rcp-proof-preview" id="proof-preview-container">
                                    <img id="proof-img-preview" src="" alt="แสดงตัวอย่างหลักฐาน" class="rcp-proof-img" style="display:none;">
                                    <span id="proof-placeholder">กรุณาเลือกไฟล์รูปภาพ</span>
                                </div>
                                <input type="file" id="proof-file-input" accept="image/*" style="display:none;">
                            </div>
                        </div>
                    </div>
                    
                    <div class="rcp-footer-notes">
                        <p>ใบเสร็จนี้ถูกสร้างขึ้นโดยระบบอัตโนมัติ กรุณาเก็บไว้เป็นหลักฐาน</p>
                    </div>

                    <div class="rcp-action-buttons">
                        <button id="save-proof-btn" class="rcp-btn rcp-btn-primary">บันทึกการแนบไฟล์</button>
                        <a href="#" class="rcp-btn rcp-btn-secondary">กลับหน้าหลัก</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include '../../template/footer-bar.php'; ?>

    <script>
    
        document.addEventListener('DOMContentLoaded', ()=>{
            // Handle payment proof upload
            const fileInput = document.getElementById('proof-file-input');
            const previewContainer = document.getElementById('proof-preview-container');
            const previewImg = document.getElementById('proof-img-preview');
            const placeholder = document.getElementById('proof-placeholder');
            const saveButton = document.getElementById('save-proof-btn');

            // Trigger file input click when the preview container is clicked
            previewContainer.addEventListener('click', () => {
                fileInput.click();
            });

            fileInput.addEventListener('change', (event) => {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        previewImg.src = e.target.result;
                        previewImg.style.display = 'block';
                        placeholder.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Handle bank selection
            const bankSelect = document.getElementById('bank-select');
            const bankLogo = document.getElementById('bank-logo');
            const bankName = document.getElementById('bank-name');
            const accountNumber = document.getElementById('account-number');
            
            const bankData = {
                bbl: {
                    name: 'ธนาคารกรุงเทพ',
                    account: '123-4-56789-0',
                    logo: 'https://placehold.co/120x120/004B8F/ffffff?text=BBL'
                },
                kbank: {
                    name: 'ธนาคารกสิกรไทย',
                    account: '987-6-54321-0',
                    logo: 'https://placehold.co/120x120/00A950/ffffff?text=KBANK'
                },
                scb: {
                    name: 'ธนาคารไทยพาณิชย์',
                    account: '111-2-33344-5',
                    logo: 'https://placehold.co/120x120/4e2474/ffffff?text=SCB'
                },
                ktb: {
                    name: 'ธนาคารกรุงไทย',
                    account: '456-7-89012-3',
                    logo: 'https://placehold.co/120x120/194474/ffffff?text=KTB'
                }
            };

            bankSelect.addEventListener('change', (event) => {
                const selectedBank = event.target.value;
                const data = bankData[selectedBank];
                if (data) {
                    bankLogo.src = data.logo;
                    bankName.textContent = data.name;
                    accountNumber.textContent = data.account;
                }
            });

            // Handle save action
            saveButton.addEventListener('click', () => {
                if (fileInput.files.length > 0) {
                    // Simulate saving the file (in a real app, this would be an API call)
                    showMessage('หลักฐานการชำระเงินถูกบันทึกเรียบร้อยแล้ว');
                    setTimeout(() => {
                        window.location.href = 'myorder.html'; // Redirect to myorder page
                    }, 2000); // Wait for 2 seconds to show the message
                } else {
                    showMessage('กรุณาแนบไฟล์หลักฐานการชำระเงิน');
                }
            });
        });
    </script>
</body>
</html>
