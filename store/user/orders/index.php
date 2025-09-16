<?php
require_once '../../server/connect_sqli.php';
require_once __DIR__ . '/../../vendor/autoload.php';

$defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
$fontDirs = $defaultConfig['fontDir'];

$defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];

$mpdf = new \Mpdf\Mpdf([
    'fontDir' => array_merge($fontDirs, [
        __DIR__ . '/../../fonts/sarabun',
        __DIR__ . '/../../fonts/kanit'
    ]),
    'fontdata' => $fontData + [
        'sarabun' => [
            'R' => 'Sarabun-Regular.ttf',
            'B' => 'Sarabun-Bold.ttf',
            'I' => 'Sarabun-Italic.ttf',
        ],
        'kanit' => [
            'R' => 'Kanit-Regular.ttf',
            'B' => 'Kanit-Bold.ttf',
        ]
    ],
    'default_font' => 'sarabun' // ฟอนต์เริ่มต้น
]);

$html = '
<h1>ข้อความภาษาไทย Sarabun</h1>
<p>ข้อความภาษาไทย Kanit</p>
';

$mpdf->WriteHTML($html);
$mpdf->Output('multi_fonts.pdf', 'I');
