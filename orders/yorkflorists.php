<?php

$ts = !empty($_GET['date']) ? strtotime($_GET['date']) : time();
$date = date('d/m/Y', $ts);
$url = 'https://www.yorkflorists.co.uk/wp-json/wc/v3/orders?status=completed&delivery_date='.$date;
$auth = 'key:secret';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERPWD, $auth);
$data = curl_exec($ch); 

$orders = json_decode($data, true);
$rows = array();
foreach ($orders as $order) {
    $meta_data =& $order['line_items'][0]['meta_data'];
    foreach ((array) $meta_data as $meta) {
        $key =& $meta['key'];
        if ($key == 'Delivery Date' && $meta['value'] != $date) {
            continue 2;
        }
    }
    $rows[] = array(
        $order['id'],
        // more details
    );
}

if (!empty($rows)) {
    $filename = sprintf('yorkflorists.co.uk_%s', $date);
    header("Content-type: application/csv");
    header("Content-Disposition: attachment; filename={$filename}.csv");
    header("Pragma: no-cache");
    header("Expires: 0");

    $f = fopen('php://output', 'w');
    foreach ($rows as $row) {
        fputcsv($f, $row, ',');
    }
}
