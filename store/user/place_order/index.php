<?php
include '../../routes.php'; 

$order_id = "123456";

// รายการสินค้า
$items = [
    ["name" => "สินค้า A", "qty" => 1, "price" => 50],
    ["name" => "สินค้า B", "qty" => 2, "price" => 21.775]
];

// ค่าต่างๆ
$discount = 0;       // ส่วนลด
$shipping = 0;       // ค่าจัดส่ง
$extra_service = 0;  // บริการเสริม
$tax_rate = 0.07;    // ภาษี 7%

// คำนวณยอดรวม
$subtotal = 0;
foreach($items as $item){
    $subtotal += $item['price'] * $item['qty'];
}

$tax = ($subtotal - $discount + $shipping + $extra_service) * $tax_rate;
$total_amount = $subtotal - $discount + $shipping + $extra_service + $tax;

// ตัวอย่างวิธีชำระเงิน
$payment_method = "krungsri_card";
$payment_methods = [
    "krungsri_card" => "โอนเงินผ่าน ธ.กรุงศรีอยุธยา",
    "promptpay" => "สแกนคิวอาโค้ด (พร้อมเพย์)",
    "paypal" => "PayPal"
];
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-STORE</title>
    <?php include '../../inc-meta.php'; ?>
    <link href="../../css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
    <!-- <link href="../../css/user/template-cart.css?v=<?php echo time(); ?>" rel="stylesheet"> -->
    <?php include '../../inc-cdn.php'; ?>
    <style>
        .success-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }

        .success-container {
            background: #fff;
            padding: 2rem;
            border-radius: 6px;
            border: 1px solid #eaeaea;
            width: 100%;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            font-size: 3rem;
            color: #ffffff;
            border-radius: 50%;
            background: #4caf50;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .success-actions {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .success-actions .btn {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: 0.2s;
        }

        .btn-primary {
            background-color: #4caf50;
            color: #fff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #45a049;
        }

        .btn-outline-primary {
            background-color: transparent;
            border: 1px solid #4caf50;
            color: #4caf50;
        }

        .btn-outline-primary:hover {
            background-color: #4caf50;
            color: #fff;
        }

        .btn-outline-secondary {
            background-color: transparent;
            border: 1px solid #ccc;
            color: #333;
        }

        .btn-outline-secondary:hover {
            background-color: #ccc;
            color: #fff;
        }

        .order-summary {
            border-top: 1px solid #eaeaea;
            padding-top: 15px;
            text-align: left;
        }

        .order-summary h4 {
            margin-bottom: 0.5rem;
        }

        .order-summary ul {
            list-style: none;
            padding-left: 0;
        }

        .order-summary li {
            margin-bottom: 0.25rem;
        }

        .bank-info {
            text-align: left;
            margin-top: 1rem;
        }

        .bank-info ul {
            list-style: none;
            padding-left: 0;
        }

        .bank-info li {
            margin-bottom: 0.5rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <?php include '../../template/head-bar.php'; ?>
    <main>
        <div class="section-space">
            <div class="container">
                <div class="success-wrapper">
                    <div class="success-container">
                        <div class="success-icon">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <h2>การสั่งซื้อสำเร็จ</h2>
                        <p>
                            ขอบคุณที่สั่งซื้อสินค้ากับเรา<br>
                            หมายเลขออเดอร์: <strong>#<?php echo $order_id; ?></strong>
                        </p>

                        <div class="order-summary">
                            <h4>สรุปคำสั่งซื้อ</h4>
                            <ul>
                                <?php foreach($items as $item): ?>
                                    <li><?php echo $item['name']; ?> x<?php echo $item['qty']; ?> - <?php echo number_format($item['price'] * $item['qty'], 2); ?> บาท</li>
                                <?php endforeach; ?>
                            </ul>
                            <ul>
                                <li>รวม <span><?php echo number_format($subtotal, 2); ?></span></li>
                                <li>ส่วนลด <span><?php echo number_format(-$discount, 2); ?></span></li>
                                <li>จัดส่ง <span><?php echo number_format($shipping, 2); ?></span></li>
                                <li>บริการเสริม <span><?php echo number_format($extra_service, 2); ?></span></li>
                                <li>ภาษีมูลค่าเพิ่ม <?php echo ($tax_rate*100); ?>% <span><?php echo number_format($tax, 2); ?></span></li>
                                <li><strong>ทั้งหมด <span><?php echo number_format($total_amount, 2); ?></span></strong></li>
                                <?php if(isset($payment_methods[$payment_method])): ?>
                                    <li>วิธีชำระเงิน: <strong><?php echo $payment_methods[$payment_method]; ?></strong></li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <div class="success-actions">
                            <a href="/" class="btn btn-primary">กลับไปหน้าหลัก</a>
                            <a href="/user/orders" class="btn btn-outline-primary">ไปที่คำสั่งซื้อของฉัน</a>
                            <a href="/user/upload-slip.php?order_id=<?php echo $order_id; ?>" class="btn btn-outline-secondary">แนบสลิปการชำระเงิน</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include '../../template/footer-bar.php'; ?>
</body>

</html>
