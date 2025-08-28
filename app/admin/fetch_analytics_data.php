<?php
// fetch_analytics_data.php
require __DIR__ . '/../../vendor/autoload.php';

use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\OrderBy;
use Google\Analytics\Data\V1beta\OrderBy\DimensionOrderBy;
use Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy;


// Replace with your GA4 Property ID
$property_id = '497553877';

// Path to your JSON key file
$credentials_path = __DIR__ . '/keydatatrandarwebsite/spatial-vision-470405-u2-1572283a8a60.json';

try {
    $client = new BetaAnalyticsDataClient(['credentials' => $credentials_path]);

    // 1. ดึงข้อมูลผู้ใช้งานรายวัน (Daily Active Users)
$dailyUsersRequest = (new RunReportRequest())
    ->setProperty('properties/' . $property_id)
    ->setDateRanges([
        new DateRange([
            'start_date' => '30daysAgo',
            'end_date' => 'today',
        ]),
    ])
    ->setDimensions([
        new Dimension(['name' => 'date']),
    ])
    ->setMetrics([
        new Metric(['name' => 'activeUsers']),
    ])
    ->setOrderBys([
        (new OrderBy())->setDimension((new DimensionOrderBy())->setDimensionName('date'))->setDesc(false),
    ]);
    $dailyUsersResponse = $client->runReport($dailyUsersRequest);
    $dailyUsersData = [];
    $dailyUsersLabels = [];
    foreach ($dailyUsersResponse->getRows() as $row) {
        $dailyUsersLabels[] = date('d M', strtotime($row->getDimensionValues()[0]->getValue()));
        $dailyUsersData[] = (int) $row->getMetricValues()[0]->getValue();
    }

   // 2. ดึงข้อมูลหน้ายอดนิยม (Top Pages)
    $topPagesRequest = (new RunReportRequest())
        ->setProperty('properties/' . $property_id)
        ->setDateRanges([
            new DateRange([
                'start_date' => '7daysAgo',
                'end_date' => 'today',
            ]),
        ])
        ->setDimensions([
            new Dimension(['name' => 'pagePath']),
        ])
        ->setMetrics([
            new Metric(['name' => 'activeUsers']),
        ])
        ->setOrderBys([
            (new OrderBy())->setMetric((new MetricOrderBy())->setMetricName('activeUsers'))->setDesc(true),
        ])
        ->setLimit(5); // ดึงข้อมูล 5 อันดับแรก
    $topPagesResponse = $client->runReport($topPagesRequest);
    $topPagesData = [];
    $topPagesLabels = [];
    foreach ($topPagesResponse->getRows() as $row) {
        $topPagesLabels[] = $row->getDimensionValues()[0]->getValue();
        $topPagesData[] = (int) $row->getMetricValues()[0]->getValue();
    }
    
   // 3. ดึงข้อมูลแหล่งที่มา (Source)
    $sourceRequest = (new RunReportRequest())
        ->setProperty('properties/' . $property_id)
        ->setDateRanges([
            new DateRange([
                'start_date' => '7daysAgo',
                'end_date' => 'today',
            ]),
        ])
        ->setDimensions([
            new Dimension(['name' => 'sessionSource']),
        ])
        ->setMetrics([
            new Metric(['name' => 'activeUsers']),
        ])
        ->setOrderBys([
            (new OrderBy())->setMetric((new MetricOrderBy())->setMetricName('activeUsers'))->setDesc(true),
        ]);
    $sourceResponse = $client->runReport($sourceRequest);
    $sourceData = [];
    $sourceLabels = [];
    foreach ($sourceResponse->getRows() as $row) {
        $sourceLabels[] = $row->getDimensionValues()[0]->getValue();
        $sourceData[] = (int) $row->getMetricValues()[0]->getValue();
    }

   // 4. ดึงข้อมูลจากประเทศ (Country)
    $countryRequest = (new RunReportRequest())
        ->setProperty('properties/' . $property_id)
        ->setDateRanges([
            new DateRange([
                'start_date' => '7daysAgo',
                'end_date' => 'today',
            ]),
        ])
        ->setDimensions([
            new Dimension(['name' => 'country']),
        ])
        ->setMetrics([
            new Metric(['name' => 'activeUsers']),
        ])
        ->setOrderBys([
            (new OrderBy())->setMetric((new MetricOrderBy())->setMetricName('activeUsers'))->setDesc(true),
        ]);
    $countryResponse = $client->runReport($countryRequest);
    $countryData = [];
    $countryLabels = [];
    foreach ($countryResponse->getRows() as $row) {
        $countryLabels[] = $row->getDimensionValues()[0]->getValue();
        $countryData[] = (int) $row->getMetricValues()[0]->getValue();
    }

    // รวมข้อมูลทั้งหมดใน Array เดียวแล้วส่งกลับเป็น JSON
    $response = [
        'daily_users' => [
            'labels' => $dailyUsersLabels,
            'data' => $dailyUsersData,
        ],
        'top_pages' => [
            'labels' => $topPagesLabels,
            'data' => $topPagesData,
        ],
        'source' => [
            'labels' => $sourceLabels,
            'data' => $sourceData,
        ],
        'country' => [
            'labels' => $countryLabels,
            'data' => $countryData,
        ],
    ];

    header('Content-Type: application/json');
    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
