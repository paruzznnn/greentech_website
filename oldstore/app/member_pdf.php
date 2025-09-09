<?php
session_start();
date_default_timezone_set('Asia/Bangkok');
require_once '../lib/connect.php';
require '../vendor/autoload.php';
ini_set('display_errors', 0);
error_reporting(0);


// build mPDF
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

// Font setup
$defaultConfig = (new ConfigVariables())->getDefaults();
$fontDirs = $defaultConfig['fontDir'];

$defaultFontConfig = (new FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];

// Initialize mPDF with custom settings
$mpdf = new Mpdf([
    'format' => 'A4', // Paper size (can be 'Letter', 'A3', or custom sizes like [210, 297])
    'fontDir' => array_merge($fontDirs, [
        __DIR__ . '/fonts', // Path to your fonts
    ]),
    'fontdata' => $fontData + [
        'thsarabun' => [
            'R' => 'THSarabunNew.ttf',
        ]
    ],
    'default_font' => 'thsarabun',
]);

if(isset($_GET) && $_GET['dataID']){

    $member_id = $_SESSION['user_id'];
    $is_status = 0;
    $ids = $_GET['dataID']; // Assuming this is a comma-separated list of IDs or an array
    
    
    // Prepare SQL with placeholders for multiple IDs
    $sql = "
        SELECT
            od.order_id,
            od.is_status,
            GROUP_CONCAT(DISTINCT od.id) AS ids,
            GROUP_CONCAT(DISTINCT od.created_at) AS date_created,
            GROUP_CONCAT(DISTINCT od.order_key) AS order_keys,
            GROUP_CONCAT(DISTINCT od.order_code) AS order_codes,
            GROUP_CONCAT(od.pro_id) AS product_ids,
            GROUP_CONCAT(od.price) AS prices,
            GROUP_CONCAT(od.quantity) AS quantities,
            GROUP_CONCAT(od.total_price) AS total_prices,
            CONCAT(sp.first_name, ' ', sp.last_name) AS fullname,
            sp.address,
            CONCAT(sp.county, ' ', sp.district, ' ', sp.district, ' ', sp.post_code) AS shipping,
            pm.pay_channel,
            od.pay_type,
            od.qr_pp
        FROM
            ecm_orders od
        LEFT JOIN ord_payment pm ON
            od.order_id = pm.order_id
        LEFT JOIN ord_shipping sp ON
            od.order_id = sp.order_id
        WHERE
            od.member_id = ? AND od.is_status = ? AND od.id IN ($ids)
        GROUP BY
            od.order_id, 
            od.created_at,
            od.order_code, 
            sp.address, 
            pm.pay_channel, 
            od.pay_type, 
            od.qr_pp
    ";
    
    // Prepare the statement
    $stmt = $conn->prepare($sql);
    
    // Bind parameters
    $stmt->bind_param("ii", $member_id, $is_status);
    
    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);

    // Generate the HTML for PDF content
    $html = '
    <style>
        @page {
            margin: 20mm; /* Adjust page margins */
        }
        html, body {
            font-family: thsarabun, sans-serif;
            font-size: 16pt;
            margin: 0;
            padding: 0;
            color: #555;
        }
        .container {
            padding: 20px;
        }
        .header {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }
        .po-info {
            margin-bottom: 20px;
            width: 100%;
        }
        .po-info td {
            padding: 5px;
            vertical-align: top;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table th {
            background-color: #f4f4f4;
            font-weight: bold;
            text-align: center;
        }
        .table td {
            text-align: right;
        }
        .table .text-left {
            text-align: left;
        }
        .footer {
            margin-top: 30px;
            font-size: 14pt;
        }
        tfoot .text-right {
            text-align: right;
        }
        tfoot .text-left {
            text-align: left;
        }

        .qr-img{
            width: 150px !important;
        }

        .logo-img{
            width: 150px !important;
        }
        
        .table .text-center {
            text-align: center;
        }

    </style>
    <div class="container">
        <div class="header"><img src="../public/img/trandar_logo.png" class="logo-img"></div>
        <br>
        <table class="po-info">
            <tr>
                <td style="width: 50%;"><strong>ผู้ขาย:</strong> Trandar International Co., Ltd.</td>
                <td style="width: 50%;"><strong>ผู้ซื้อ:</strong> ' . $data[0]['fullname'] . '</td>
            </tr>
            <tr>
                <td style="width: 50%;"><strong>ที่อยู่ผู้ขาย:</strong> 
                102 Soi Pattanakarn 40,
                Pattanakarn Rd,
                Suanluang, Bangkok 10250, Thailand
                </td>
                <td style="width: 50%;"><strong>ที่อยู่ผู้ซื้อ:</strong> ' . $data[0]['shipping'] . '</td>
            </tr>
            <tr>
                <td style="width: 50%;"><strong>เลขที่ใบสั่งซื้อ:</strong> ' . $data[0]['order_codes'] . '</td>
                <td style="width: 50%;"><strong>วันที่:</strong> ' . $data[0]['date_created'] . '</td>
            </tr>
        </table>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th class="text-left">สินค้า</th>
                        <th>ราคา/หน่วย</th>
                        <th>จำนวน</th>
                        <th>ราคารวม</th>
                    </tr>
                </thead>
                <tbody>';
    $totalItem = 0;
    $totalQty = 0;
    $totalAmount = 0;
    $countItem = 0;
    foreach ($data as $index => $order) {

        $productIds = explode(',', $order['product_ids']);
        $prices = explode(',', $order['prices']);
        $quantities = explode(',', $order['quantities']);
        $totalPrices = explode(',', $order['total_prices']);
        $keyOrder = explode(',', $order['order_keys']);

        $payChannel = '';
        $transportChannel = '';
        $statusHtml = '';

        switch ($order['pay_channel']) {
            case 1:
                $payChannel = '<img src="../public/img/bankPay.png" class="" style="width: 25%;"><br>
                                <label>บจก.แทรนดาร์ อินเตอร์เนชั่นแนล</label>
                                <div>
                                    ธ.กรุงศรีอยุธยา 320-1-13702-8
                                </div>';
                break;
            case 2:
                $payChannel = '<img src="../public/img/prompt-pay-logo.png" class="" style="width: 25%;"></img><br>' .
                                $order['qr_pp'];
                break;
            default:
                break;
        }
        switch ($order['pay_type']) {
            case 1:
                $transportChannel = 'ตามที่อยู่ที่กำหนด';
                break;
            case 2:
                $transportChannel = 'รับหน้าร้าน';
                break;
            default:
                break;
        }
        switch ($order['is_status']) {
            case 0:
                $statusHtml = 'รอส่งหลักฐานการชำระเงิน';
                break;
            case 1:
                $statusHtml = '';
                break;
            case 2:
                $statusHtml = '';
                break;
            default:
                break;
        }
        
        foreach ($productIds as $i => $productId) {
            $countItem ++;
            $html .= '
            <tr>
                <td class="text-center">' . $countItem . '</td>
                <td class="text-left">' . $keyOrder[$i] . '</td>
                <td>' . number_format($prices[$i], 2) . '</td>
                <td>' . $quantities[$i] . '</td>
                <td>' . number_format($totalPrices[$i], 2) . '</td>
            </tr>';

            $totalItem += ($index + 1);
            $totalQty += $quantities[$i];
            $totalAmount += $totalPrices[$i];
            
        }
    }

    $vat = $totalAmount * 0.07;
    $grandTotal = $totalAmount + $vat;

    $html .= '
                </tbody>
                <tfoot>
                    <tr>
                        <th rowspan="6" colspan="2" style="width: 250px;">
                            ' . $payChannel . '
                            <br>
                            <div class=""></div>
                        </th>
                        <th class="text-right" colspan="2">จำนวนรายการ</th>
                        <th class="text-right">' . $totalItem . '</th>
                    </tr>
                    <tr>
                        <th class="text-right" colspan="2">จำนวนรายการสินค้า</th>
                        <th class="text-right">' . $totalQty . '</th>
                    </tr>
                    <tr>
                        <th class="text-right" colspan="2">รวมเป็นเงิน</th>
                        <th class="text-right">' . number_format($totalAmount, 2) . '</th>
                    </tr>
                    <tr>
                        <th class="text-right" colspan="2">ภาษีมูลค่าเพิ่ม(7%)</th>
                        <th class="text-right">' . number_format($vat, 2) . '</th>
                    </tr>
                    <tr>
                        <th class="text-right" colspan="2">จำนวนเงินทั้งสิ้น</th>
                        <th class="text-right">' . number_format($grandTotal, 2) . '</th>
                    </tr>
                    <tr>
                        <th class="text-right" colspan="3">สถานะ ' . $statusHtml . '</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="footer">
            <strong>จัดส่ง:</strong> '. $transportChannel .'<br>
            <strong>เงื่อนไขการชำระเงิน:</strong> ชำระเงินภายใน 7 วันหลังจากได้รับใบแจ้งหนี้<br>
            <strong>หมายเหตุ:</strong> ขอบคุณที่สั่งซื้อสินค้ากับเรา
        </div>
    </div>
';

}

// Use Mpdf to generate the PDF
$mpdf->WriteHTML($html);
$mpdf->Output($_GET['code'].'PO.pdf', 'I');
// 'I' = Inline (เปิดในเบราว์เซอร์), 'D' = Download, 'F' = Save to file

?>
